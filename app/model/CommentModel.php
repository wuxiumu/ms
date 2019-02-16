<?php

namespace app\model;

use core\lib\Model;

class CommentModel extends Model
{
	public $table = 'comment';

	public function addcomment($data)
	{
		return $this->insert($this->table,$data);		 		 
	}

	public function findcomment($where)
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