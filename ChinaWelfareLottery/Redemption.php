<?php

class Redemption
{
    public static function DoubleChromosphereRedemption(array $yourCombination, array $combination)
    {
        // 参数校验

        $red = count(array_intersect($combination['red'], $yourCombination['red']));
        $blue = count(array_intersect($combination['blue'], $yourCombination['blue']));
        $result = $red . '+' . $blue;
        switch ($result) {
            case '6+1':
                return '一等奖';
            case '6+0':
                return '二等奖';
            case '5+1':
                return '三等奖';
            case '5+0':
            case '4+1':
                return '四等奖';
            case '4+0':
            case '3+1':
                return '五等奖';
            case '2+1':
            case '1+1':
            case '0+1':
                return '六等奖';
            default:
                return '未中奖';
        }
    }
}
