<?php

namespace app\ctrl;

//header('Content-Type:application/json');  //此声明非常重要
class apiCtrl extends \core\phpmsframe
{	

    //接口版本1
    public function v1(){      
        $model = new \app\model\goModel();  
      p($model);
        // $result = $this->createRange(10); // 这里调用上面我们创建的函数

        // foreach($result as $value){

        //     sleep(1);//这里停顿1秒，我们后续有用

        //     echo $value.'<br />';

        // }
        // echo 1;
        // if(isset($_GET['db'])){
   
        //     $arr = $_GET;
        //     $this->posts($arr);
        // }else{
        //     echo '接口版本1';
        // }    
    }

    private function posts($arr=[]){
        $model = new \app\model\postModel();
		$page = 0;//数组以0起始
		if(isset($arr['paged'])){
			$page = $arr['paged'];
        }
        
		$limit = 10;
        $data['previous'] = $page - $limit;				
        $data['curpage']  = $page - 1;
		$data['next']     = $page + $limit;
		$id = $model->select("posts",
								"id", 
								[
									"display" => 1,
									"LIMIT" => [$page , $limit],
									"ORDER" => ["id" => "DESC"]
								]
							);//根据分页获取ID
		$list = $model->select("posts",
								   "*",
								   [	
									   "id" => $id,
									   "ORDER" => ["id" => "DESC"]
								   ]
								);//请求数据库数据
		$re = $list;
        if($re){
            
        }else{
            header('HTTP/1.1 404 Not Found');
        }
        $data['data'] = $re;
        echo json_encode($data,JSON_UNESCAPED_UNICODE);
    }

    // function createRange($number){

    //     $data = [];
    
    //     for($i=0;$i<$number;$i++){
    
    //         $data[] = time();
    
    //     }
    
    //     return $data;
    
    // }

    function createRange($number){

        for($i=0;$i<$number;$i++){
    
            yield time();
    
        }
    
    }

    function newSymlink($path){
        $manual = $path;  // 原路径
        $manualLink = './uploadSymlink/'.date('Y-m-d H:i:s');   // 软连接路径
        $isExistFile = true;    // 原文件是否存在的标识
        if(is_file($manual) && !is_file($manualLink)){  // 原文件存在且软连接不存在时，创建软连接    
          symlink($manual, $manualLink);              // 创建软连接    
        }    
        if(!is_file($manualLink))  {    
           $isExistFile = false;    
        }elseif(!is_file($manual)){ // 原文件不存在时    
           $isExistFile = false;    
        }    
        return array('isExistFile'=>$isExistFile,'manual'=>$manualLink);
    }

    /*
    *  @param  createfile //创建文件夹
    *  @param  createpath  // 创建的路径
    *  @param  file_exists() // 查看是否文件夹有同样的目录
    *  @param  file // 创建的的路径 基于文件夹 ./Public/Uploads/  下创建修改
    *  @param  mkdir // 创建文件夹的函数
    *  @param 2017/11/20  8:57
    */
    function createfile($file){

        $createpath = './Public/Uploads/' . $file;

        $_createpath = iconv('utf-8', 'gb2312', $createpath);

        if (file_exists($_createpath) == false)

        {

            //检查是否有该文件夹，如果没有就创建，并给予最高权限

            if (mkdir($_createpath, 0700, true)) {

                $value['file'] ='文件夹创建成功';

                $value['success']='success';

            } else {

                $value['file'] ='文件夹创建失败';

                $value['fail']='fail';

            }

        }

        else

        {

            $value['file'] ='文件夹已存在';

            $value['fail']='fail';

        }

        return $value;

    }
}