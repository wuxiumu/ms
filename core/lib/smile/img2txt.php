<?php
//IMG2TXT：简单的ASCII艺术！
//提示：图像越小越好。尺寸大于640x480的图像将无法使用。处理和打印只需要很长时间。
//https://www.degraeve.com/img2txt.php

function tochars($r, $g, $b, $px = 256, $char = "#ABCDEFGHIJKLMNOPQRSTUVWXYZ@$%??__ff--++~~''  ::..  ``  ")
{
    if ($px == 0)
        return '';
    $len = mb_strlen($char);
    //灰度值
    $gray = floor(($r + $g + $b) / 3);
    //将256个像素平均分配给字符
    $unit = ceil($px / $len);
    //获取当前像素对应的字符
    $index = floor($gray / $unit);
    if ($index >= $len) {
        $index = $len - 1;
    }
    return $char[(int)$index];
}

function tosmallim($const = 100, $width, $height, $image)
{
    if ($width > $const) {
        $times = floor($width / $const);
        $smwidth = $const;
        $smheight = floor($height / $times);
        $im = imagecreatetruecolor($smwidth, $smheight);
        imagecopyresampled($im, $image, 0, 0, 0, 0, $smwidth, $smheight, $width, $height);
        return [$im, $smwidth, $smheight];
    }
    return [$image, $width, $height];
}


$imname = 'https://ms.meiyoufan.com/public/img/posts/896x428.png';

//返回一图像标识符，代表了从给定的文件名取得的图像
$image = ImageCreateFromPng($imname);
//$im = ImageCreateFromJpeg($imname);

$size = getimagesize($imname);
$width = $size[0];
$height = $size[1];

list($image, $width, $height) = tosmallim(100, $width, $height, $image);


$arr = [];
for ($i = 0; $i < $height; $i++) {
    for ($j = 0; $j < $width; $j++) {
        $rgb = ImageColorat($image, $j, $i);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;
        $arr[] = floor(($r + $g + $b) / 3);
    }
}
$num = count(array_unique($arr));
$str = '<span style="font-size: 8pt;
    letter-spacing: 4px;
    line-height: 8pt;
    font-weight: bold;display: block;
    font-family: monospace;
    white-space: pre;
    margin: 1em 0;">';
for ($i = 0; $i < $height; $i++) {
    for ($j = 0; $j < $width; $j++) {
        $rgb = ImageColorat($image, $j, $i);
        $r = ($rgb >> 16) & 0xFF;
        $g = ($rgb >> 8) & 0xFF;
        $b = $rgb & 0xFF;
        $str .= tochars($r, $g, $b, $num);
    }
    $str .= '<br>';
}
$str .= '</span>';