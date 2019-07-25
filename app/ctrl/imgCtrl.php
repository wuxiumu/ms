<?php

namespace app\ctrl;

use core\lib\model;

class imgCtrl extends \core\phpmsframe
{	
    public function s(){
        echo 1;
    }
    //自动抓取列表
    public function spiderauto()
    {        
        $url = "http://www.shunva.com/?m=vod-type-id-16.html";
        if(isset($_GET['page'])){
            $page = $_GET['page']+1;
        }else{
            $page = 1;
        }       
        $i = explode('-',$url);
        $tmp = $i;
        $tmp['3']= rtrim($i['3'],".html");
        $tmp[]='pg';
        $tmp[]= $page;
        $j = implode("-",$tmp);
        $nexturl = $j.".html";  
        $this->spider($nexturl,$page); 
        $data['page'] = $page;
        $data['nexturl'] = $nexturl;
        dump($data);
        $this->assign('data',$data);
        $this->display('img.html');
    }
    //抓取列表方法
    public function spider($url,$page=0)
    {        
        $html = file_get_contents($url);
        // 通过 php 的 file_get_contents 函数获取百度首页源码，并传给 $html 变量
        // 通过 preg_replace 函数使页面源码由多行变单行
        $htmlOneLine = preg_replace("/\r|\n|\t/","",$html);
        // 通过 preg_match 函数提取获取页面的标题信息
        preg_match("/<title>(.*)<\/title>/iU",$htmlOneLine,$titleArr);
        // 由于 preg_match 函数的结果是数组的形式        
        $title = $titleArr[1];

        $ulpreg = "/<ul>(.*?)<\/ul>/";
        preg_match($ulpreg,$htmlOneLine,$srcArr);
        $t_img_src_preg="#<a href=\"(.*?)\" target=\"_blank\"><img src=\"(.*?)\" /><h3>(.*?)<\/h3>#";
        preg_match_all($t_img_src_preg,$srcArr['1'],$srcA);

        $www = "http://www.shunva.com";
        $tmp = [];
        foreach($srcA['0'] as $k=>$v){
            $tmp[$k]['title'] = $srcA['3'][$k];
            $tmp[$k]['img']   = $srcA['2'][$k];
            $i =  explode('-',$srcA['1'][$k]);            
            $tmp[$k]['href']  = $www.$srcA['1'][$k];
            $tmp[$k]['id']    = rtrim($i['3'],".html");
        }        
        $newfile = PHPMSFRAME."/nosql/json/".mb_substr($title,0,4,'utf-8').'-'.$page.".json";
        $myfile = fopen($newfile, "w") or die("Unable to open file!");
        $txt = json_encode($tmp);;
        fwrite($myfile, $txt);
        fclose($myfile);        
        //echo $title;
        return $page+1;
    }
    //获取数据列表
    public function save(){
        // $data = PHPMSFRAME."/nosql/json/国产自拍-1.json";
        // $myfile = fopen($data, "r") or die("Unable to open file!");
        // $json = fread($myfile,filesize($data));
        // fclose($myfile);

        // $arr = json_decode($json,true);
        // dump($arr);
        $url = 'http://www.shunva.com/?m=vod-detail-id-24657.html';
        $html = file_get_contents($url);
        echo $html;
    }

    public function list()
    {
        echo 'list';
    }

    public function tochars($r, $g, $b, $px = 256, $char = "#ABCDEFGHIJKLMNOPQRSTUVWXYZ@$%??__ff--++~~''  ::..  ``  ")
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

    public function tosmallim($const = 100, $width, $height, $image)
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

    public function index(){        
        $imname = 'https://ms.meiyoufan.com/public/img/posts/896x428.png';
        //返回一图像标识符，代表了从给定的文件名取得的图像
        $image = ImageCreateFromPng($imname);
        //$im = ImageCreateFromJpeg($imname);
    
        $size = getimagesize($imname);
        $width = $size[0];
        $height = $size[1];
    
        list($image, $width, $height) = $this->tosmallim(100, $width, $height, $image);
        
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
                $str .= $this->tochars($r, $g, $b, $num);
            }
            $str .= '<br>';
        }
        echo $str . '</span>';
    }
}