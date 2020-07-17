<?php

include 'DoubleChromosphere.php';

$filePath = __DIR__ . DIRECTORY_SEPARATOR . '1.jpg';
$doubleChromosphere = new DoubleChromosphere(realpath($filePath));
// print_r($doubleChromosphere->getResult());

// echo "\n";

$doubleChromosphere->printResult();

echo "\n";

include 'DoubleChromosphereWinning.php';

// $ret = DoubleChromosphereWinning::getIndexWinningResultList(1);
// $ret = DoubleChromosphereWinning::getLatestWinningResult();
// print_r(DoubleChromosphereWinning::getWinningResultByIssue('2020013'));
$ret = DoubleChromosphereWinning::getWinningResultByIssue($doubleChromosphere->getIssue());
// print_r($ret);
echo '开奖结果：' . "\n";
echo '期号：' . $ret['issue'] . "\n";
echo '红球：' . implode(' ', $ret['combination']['red']) . "\n";
echo '蓝球：' . $ret['combination']['blue'][0] . "\n";

echo "\n";

include 'Redemption.php';

echo '中奖结果：' . "\n";
foreach ($doubleChromosphere->getResult() as $item) {
    $info = Redemption::DoubleChromosphereRedemption($item, $ret['combination']);
    echo $info . "\n";
}
// $my = [
//     'red' => ['10', '14', '17', '22', '26', '27'],
//     'blue' => ['05'],
// ];
// $info = Redemption::DoubleChromosphereRedemption($my, $ret['combination']);
// echo $info . "\n";

