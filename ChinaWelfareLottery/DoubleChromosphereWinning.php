<?php
include "simple_html_dom.php";
class DoubleChromosphereWinning
{
    const URL = 'http://kaijiang.zhcw.com/zhcw/html/ssq/list_[[index]].html';

    // 获取某一页的双色球中奖组合信息
    public static function getIndexWinningResultList(int $index) : array
    {
        if (!is_numeric($index) || $index < 0) {
            throw new Exception('非法的索引');
        }
        $list = [];
        $url = str_replace('[[index]]', $index, DoubleChromosphereWinning::URL);

        $html = file_get_html($url);

        $i = 1;
        foreach ($html->find('tr') as $tr) {
            $tmp = [];
            foreach ($tr->find('td') as $td) {
                if ($td->find('p') || $i > 3) {
                    continue;
                }
                if ($i == 1) {
                    $tmp['date'] = $td->innertext;
                }
                if ($i == 2) {
                    $tmp['issue'] = $td->innertext;
                }
                $num = 1;
                foreach ($td->find('em') as $em) {
                    if ($num < 7) {
                        $tmp['combination']['red'][] = $em->innertext;
                    } else {
                        $tmp['combination']['blue'][] = $em->innertext;
                    }
                    $num ++;
                }
                $i++;
            }
            $i = 1;
            if (!empty($tmp)) {
                $list[] = $tmp;
            }
        }
        return $list;
    }

    // 获取最新一期的双色球中奖组合信息
    public static function getLatestWinningResult() : array
    {
        $list = DoubleChromosphereWinning::getIndexWinningResultList(1);
        return $list[0];
    }

    // 获取某一期的双色球中奖组合信息
    public static function getWinningResultByIssue(string $issue) : array
    {
        $index = 1;
        do {
            $list = DoubleChromosphereWinning::getIndexWinningResultList($index);
            $count = count($list);
            if ($issue >= $list[$count-1]['issue']) {
                $key = $count - (intval($issue) - intval($list[$count-1]['issue'])) - 1;
                if ($key < 0) {
                    throw new Exception('未开奖');
                } else {
                    return $list[$key];
                }
                break;
            } else {
                $index++;
            }
        } while(true);
    }
    
}
