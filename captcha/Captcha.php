<?php

class Captcha
{

    private $res;                               //图像资源
    private $width;                             //图像资源宽度
    private $height;                            //图像资源高度

    private $fontType;                          //验证码字体
    private static $fontSize = 30;              //验证码字体大小
    private static $fontAngleMin = -30;         //验证码字体旋转最小角度
    private static $fontAngleMax = 30;          //验证码字体旋转最大角度
    private static $fontColorWeight = 0.3;      //验证码字体颜色和其他（干扰线等）颜色的深度比例
    private $text = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ1234567890';
    private $code = '';                         //生成的验证码

    //干扰点、干扰线的颜色设置
    private static $redColorMin = 0;            // R 最小值
    private static $redColorMax = 255;          // R 最大值
    private static $greenColorMin = 0;          // G 最小值
    private static $greenColorMax = 255;        // G 最大值
    private static $blueColorMin = 0;           // B 最小值
    private static $blueColorMax = 255;         // B 最大值

    private static $thicknessMin = 1;           //画线最小宽度
    private static $thichnessMax = 3;           //画线最大宽度

    function __construct(int $width = 150, int $height = 50)
    {
        $this->fontType = [
            realpath('HYLeMiaoTiW.ttf'),            //喵体
            realpath('FZLTCXHJW.TTF'),              //方正兰亭超细黑简体字体 常规
            realpath('fangzheng-cuyuan-jt.ttf'),    //方正粗圆简体
            realpath('fzzjblybjt.TTF'),             //方正字迹-百乐硬笔简体
            realpath('hyjqf.ttf'),                  //汉仪粗篆繁
        ];
        $this->width = $width;
        $this->height = $height;
        $this->res = @imagecreatetruecolor($width, $height) or die('Cannot Initialize new GD image stream');
        $white = imagecolorallocate($this->res, 255, 255, 255);
        imagefill($this->res, 0, 0, $white);
    }

    function __destruct()
    {
        imagedestroy($this->res);
    }

    /**
     * 渲染图像
     *
     * @param integer $codeNum      验证码位数
     * @param integer $pixelNum     像素点个数
     * @param integer $lineNum      干扰线条数
     * @param string $textExtend    验证码文字扩展
     * @return void
     */
    public function render(int $codeNum = 4, int $pixelNum = 300, int $lineNum = 10, string $textExtend = '')
    {
        $this->drawPixel($pixelNum);
        $this->drawLine($lineNum);
        if (!empty($textExtend) && is_string($textExtend)) {
            $textArr = $this->setText($textExtend);
            $this->drawCode($codeNum, $textArr);
        } else {
            $this->drawCode($codeNum);
        }
    }

    /**
     * 向浏览器展示图像
     *
     * @param boolean $needSave 是否需要保持图像，true-是；false-否
     * @param string $path      图像保存路径
     * @return void
     */
    public function show(bool $needSave = false, string $path = '')
    {
        if ($needSave) {
            imagepng($this->res, $path);
        }
        header('Content-Type: image/png');
        imagepng($this->res);
    }

    /**
     * 获取生成的验证码
     *
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    //绘制像素点
    protected function drawPixel(int $num)
    {
        for ($i = 0; $i < $num; $i++) {
            imagesetpixel(
                $this->res,
                mt_rand(0, $this->width - 1),
                mt_rand(0, $this->height - 1),
                $this->getColor()
            );
        }
    }

    //绘制干扰线
    protected function drawLine(int $num)
    {
        for ($i = 0; $i < $num; $i++) {
            $this->setThickness();
            imageline(
                $this->res,
                mt_rand(0, $this->width - 1),
                mt_rand(0, $this->height - 1),
                mt_rand(0, $this->width - 1),
                mt_rand(0, $this->height - 1),
                $this->getColor()
            );
        }
    }

    //绘制验证码文字
    protected function drawCode(int $num, array $textArr = [])
    {
        $xOffset = tan(deg2rad(self::$fontAngleMax)) * self::$fontSize;
        $xSpace = ($this->width - $xOffset * 2 - $num * self::$fontSize) / ($num + 1);
        $ySpace = ($this->height - self::$fontSize) / 2 + self::$fontSize;
        for ($i = 0; $i < $num; $i++) {
            if (!empty($textArr) && is_array($textArr)) {
                $char = $textArr[mt_rand(0, count($textArr) - 1)];
            } else {
                $char = $this->text[mt_rand(0, strlen($this->text) - 1)];
            }
            $this->code .= $char;
            $x = ($i + 1) * $xSpace + $i * self::$fontSize - 1 + $xOffset;
            $y = $ySpace - 1;
            imagettftext(
                $this->res,
                self::$fontSize,
                mt_rand(self::$fontAngleMin, self::$fontAngleMax),
                $x,
                $y,
                $this->getColor(0),
                $this->getFont(),
                $char
            );
        }
    }

    //随机设置一种画线宽度
    protected function setThickness()
    {
        imagesetthickness(
            $this->res,
            mt_rand(self::$thicknessMin, self::$thichnessMax)
        );
    }

    //随机选取一种字体
    protected function getFont()
    {
        return $this->fontType[mt_rand(0, count($this->fontType) - 1)];
    }

    //随机选取一种颜色, 0-字体;1-其他
    protected function getColor(int $type = 1)
    {
        switch ($type) {
            case 0:
                return imagecolorallocate(
                    $this->res,
                    mt_rand(self::$redColorMin, intval(self::$redColorMax * self::$fontColorWeight)),
                    mt_rand(self::$greenColorMin, intval(self::$greenColorMax * self::$fontColorWeight)),
                    mt_rand(self::$blueColorMin, intval(self::$blueColorMax * self::$fontColorWeight))
                );
            case 1:
                return imagecolorallocate(
                    $this->res,
                    mt_rand(self::$redColorMin, self::$redColorMax),
                    mt_rand(self::$greenColorMin, self::$greenColorMax),
                    mt_rand(self::$blueColorMin, self::$blueColorMax)
                );
        }
    }

    /**
     * 设置字符集
     *
     * @param string $textExtend 自定义字符
     * @return array
     */
    protected function setText(string $textExtend) : array
    {
        $arr = [];
        $tmp = $this->text . $textExtend;
        for ($i = 0;$i < mb_strlen($tmp);$i++) {
            $arr[] = mb_substr($tmp, $i, 1);
        }
        return $arr;
    }
}
