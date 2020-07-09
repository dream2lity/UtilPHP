<?php
include 'Captcha.php';

$cap = new Captcha();

// 渲染一张验证码图像，4位验证码， 500个干扰点， 8条干扰线， 验证码文字扩充字符 我爱你喵喵喵
$cap->render(4, 500, 8, '我爱你喵喵喵');

// 向浏览器渲染
$cap->show();

// 向浏览器渲染，并保存到a.png
// $cap->show(true, 'a.png');

// 验证码保存到a.txt
// file_put_contents('a.txt',$cap->getCode());