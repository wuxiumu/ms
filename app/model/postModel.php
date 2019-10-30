<?php

namespace App\Model;

use Core\Lib\model;

class postModel extends model
{
	public $table = 'posts';

	public function addpost($data)
	{
		return $this->insert($this->table,$data);		 		 
	}

	public function findpost($where)
	{
		$columns = ['id','pid','title','uid','contnet','description','created_at','updated_at'];
		return $this->get($this->table,$columns,$where);		 		 
	}

	public function lists()
	{
		$ret = $this->select($this->table,"*");
		return $ret;
	}

	public function getOne($id){
		$ret = $this->get($this->table,'*',['id'=>$id]);
		return $ret;
	}

	public function updateOne($id,$data){
		$ret = $this->update($this->table,$data,['id'=>$id]);
		return $ret;
	}

	public function delOne($id){
		$ret = $this->delete($this->table,['id'=>$id]);
		return $ret;
	}
}