<?php

namespace app\model;

use core\lib\Model;

class ColumntModel extends Model
{
	public $table = 'column';

	public function add($data)
	{
		return $this->insert($this->table,$data);		 		 
	}

	public function find($where)
	{
		$columns = ['id','post_id','from_uid','nickname','content','created_at'];
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