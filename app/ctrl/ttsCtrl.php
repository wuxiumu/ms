<?php

namespace App\Ctrl;
use Core\phpmsframe as phpms;

class ttsCtrl extends phpms
{		
	// 图片转文字
	public function Imag_text($data=[]){ 			 
		$this->assign('data',$data);
		$this->display('tts/Imag_text.html');
	}

	// ajax 返回json
	public function ttsImage(){
		require PHPMSFRAME.'/lib/OCR/api.php';
		$client = new \AipOcr('15465241', 'yLMHVyCWg64RRbVVlRI8GGP5', 'PyGbkuDvmFvn1hDctxpfgfwOkBPLCPr9');
		$url = $_POST['url'];
		// 调用通用文字识别, 图片参数为远程url图片
		$client->basicGeneralUrl($url);
		// 如果有可选参数
		$options = array();
		$options["language_type"] = "CHN_ENG";
		$options["detect_direction"] = "true";
		$options["detect_language"] = "true";
		$options["probability"] = "true";
		// 带参数调用通用文字识别, 图片参数为远程url图片
		$re = $client->basicGeneralUrl($url, $options);
		$str = '';
		foreach($re['words_result'] as $k=>$v){
			$str .= $v['words'];
		}
		echo json_encode($str,JSON_UNESCAPED_UNICODE);
	}

	// 文字转语音 
	public function Text_speech($data=[]){ 	
		$file_path = PHPMSFRAME."/public/posts/tts.md";
		if(file_exists($file_path)){
			$fp = fopen($file_path,"r");
			$str = fread($fp,filesize($file_path)); 
			fclose($fp);	 
		}
		$data['tts'] = $str;
		$this->assign('data',$data);
		$this->display('tts/Text_speech.html');
	}

	// ajax api
	public function ttsText(){
		// 一次最多2048个汉字
		$content = $_POST['content'];
		require PHPMSFRAME.'/lib/yuyin/tts.php';
		$id = $_POST['id']; 
		$_num = floor(mb_strlen($_POST['content'])/2048);    
		$content= mb_substr($_POST['content'],0,2048,'utf-8'); 
		$file = PHPMSFRAME."/public/posts/mp3/tts.$id.mp3";
		$re = baidu_tts($content,$file);    
		echo json_encode($re);  
	}

	// 清空文件夹
	public function ttsDel(){
		$file_path = PHPMSFRAME."/public/posts/mp3/";
		$re = $this->deldir($file_path);
		$url='/tts/ttsList';
		js_redirect_time_msg($url,$time=2,$msg='清空成功');		
	}

	// list mp3
	public function ttsList($data=[])
	{
		$file_path = PHPMSFRAME."/public/posts/tts.md";
		if(file_exists($file_path)){
			$fp = fopen($file_path,"r");
			$str = fread($fp,filesize($file_path));  
			$str = str_replace("\r\n","<br />",$str);
			fclose($fp);	 
		}
		$Parsedown = new \Parsedown();
		$data['tts'] = $Parsedown->text($str);		
		$con = PHPMSFRAME."/public/posts/mp3";
		$filename = scandir($con);
		$conname = array();
		foreach($filename as $k=>$v){			
			if($v=="." || $v==".."){continue;}
			$conname[] = '/public/posts/mp3/'.$v;
		}
		$data['mp3'] = $conname;
		$this->assign('data',$data);
		$this->display('tts/ttsList.html');
	}

	// 跟新文字
	public function textUpdate()
	{
		$tts = $_POST['tts'];
		$file_path = PHPMSFRAME."/public/posts/tts.md";
		$myfile = fopen($file_path, "w") or die("Unable to open file!");	 
		fwrite($myfile,$tts); 
		$re = fclose($myfile);
		$data = [
			'code'=>500,
			'msg'=>'error'
		];
		if($re){
			$data = [
				'code'=>200,
				'msg'=>'success'
			];
		}
		echo json($data);
	}
	
	public function deldir($path)
	{
		//如果是目录则继续
		if (is_dir($path)) {
			//扫描一个文件夹内的所有文件夹和文件并返回数组
			$p = scandir($path);
			foreach ($p as $val) {
				//排除目录中的.和..
				if ($val != "." && $val != "..") {
					//如果是目录则递归子目录，继续操作
					if (is_dir($path . $val)) {
						//子目录中操作删除文件夹和文件
						$this->deldir($path . $val . '/');
						//目录清空后删除空文件夹
						@rmdir($path . $val . '/');
					} else {
						//如果是文件直接删除
						unlink($path . $val);
					}
				}
			}
		}
	}	 

}