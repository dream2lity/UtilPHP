<?php
/**
 * 给定双色球彩票图像，利用百度文字识别接口，解析出图像中的红蓝组合
 */
class DoubleChromosphere
{
    private $imgPath;

    private $clientID = '*******';
    private $clientSecret = '*******';

    private $issue = '';
    private $date = '';
    private $list = [];

    /**
     * 初始化
     *
     * @param string $imgPath   图像文件路径
     */
    function __construct(string $imgPath)
    {
        if (empty($imgPath) || !is_file($imgPath)) {
            throw new Exception('非法的文件路径');
        }
        $this->imgPath = $imgPath;
        $this->analysisImage();
    }

    // 获取token
    protected function getToken(): string
    {
        $url = 'https://aip.baidubce.com/oauth/2.0/token';
        $post_data['grant_type'] = 'client_credentials';
        $post_data['client_id'] = $this->clientID;
        $post_data['client_secret'] = $this->clientSecret;
        $o = "";
        foreach ($post_data as $k => $v) {
            $o .= "$k=" . urlencode($v) . "&";
        }
        $post_data = substr($o, 0, -1);

        $res = $this->request_post($url, $post_data);

        $token = json_decode($res, true)['access_token'];
        return $token;
    }

    // 解析图像并记录结果
    protected function analysisImage()
    {
        $url = 'https://aip.baidubce.com/rest/2.0/ocr/v1/general_basic?access_token=' . $this->getToken();
        $img = file_get_contents($this->imgPath);
        $img = base64_encode($img);
        $bodys = array(
            'image' => $img
        );
        $res = $this->request_post($url, $bodys);
        $wordsArr = json_decode($res, true)['words_result'];
        // var_dump($res);
        for ($i = 0;$i < count($wordsArr);$i++) {
            if (strpos($wordsArr[$i]['words'], '.红球')) {
                $redStr = $wordsArr[$i]['words'];
                $blueStr = $wordsArr[$i+1]['words'];
                $i++;
                if (strpos($blueStr, '机选')) {
                    $blueStr = $wordsArr[$i+1]['words'];
                    $i++;
                }
                $this->list[] = [
                    'red' => str_split(
                        mb_substr($redStr, mb_strpos($redStr, '.红球') + 3, 12), 
                        2
                    ),
                    'blue' => [
                        mb_strpos($blueStr, '蓝球') ? 
                        mb_substr($blueStr, mb_strpos($blueStr, '蓝球') + 2, 2) :
                        mb_substr($blueStr, mb_strpos($blueStr, '球') + 1, 2)
                    ],
                ];
            }
            if ($wordsArr[$i]['words'] == '期号') {
                $this->issue = $wordsArr[$i+1]['words'];
            }
            if ($wordsArr[$i]['words'] == '开奖日期') {
                $this->date = $wordsArr[$i+1]['words'];
            }
        }
    }

    // 获取解析结果中的双色球红蓝组合列表
    public function getResult() 
    {
        return $this->list;
    }

    // 获取期号
    public function getIssue()
    {
        return $this->issue;
    }

    // 获取开奖日期
    public function getDate()
    {
        return $this->date;
    }

    // 打印解析结果
    public function printResult()
    {
        echo "结果如下：\n";
        echo '期号：' . $this->issue . "\n";
        echo '开奖日期：' . $this->date . "\n";
        foreach($this->list as $info) {
            echo '红球：' . implode(' ', $info['red']) . "\n";
            echo '篮球：' . $info['blue'][0] . "\n";
        }
    }

    /**
     * 发起http post请求(REST API), 并获取REST请求的结果
     * @param string $url
     * @param string $param
     * @return - http response body if succeeds, else false.
     */
    protected function request_post($url = '', $param = '')
    {
        if (empty($url) || empty($param)) {
            return false;
        }

        $postUrl = $url;
        $curlPost = $param;
        // 初始化curl
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $postUrl);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        // 要求结果为字符串且输出到屏幕上
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        // post提交方式
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
        // 运行curl
        $data = curl_exec($curl);
        curl_close($curl);

        return $data;
    }
}
