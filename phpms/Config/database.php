<?php
	return [
		'mysql_default'=>[
			'DSN'=>'101.200.50.155',
			'PORT'=>'3306',
			'USERNAME'=>'phpms',
			'PASSWORD'=>'phpms',
			'DBNAME'=>'phpms',
		],
		'mysql_company_phpms01'=>[
			'database_type' => 'mysql',
			'database_name' => 'phpms01',
			'server' => '101.200.50.155',			
			'username' => 'phpms',
			'password' => 'phpms',
		],	
	    'mysql_medoo_conf'=>[
			'database_type' => 'mysql',
			'database_name' => '91shop',
			'server' => '101.200.50.155',			
			'username' => 'phpms',
			'password' => 'phpms',
		],			
		'mysql_localhost'=>[
			'database_type' => 'mysql',
			'database_name' => '91shop',
			'server' => '47.92.207.106',			
			'username' => 'root',
			'password' => '123456',
		],
		'redis_localhost'=>[
			'database_type' => 'redis',
			'server' => '127.0.0.1',
			'PORT'=>'6379',
			'username' => 'root',
			'password' => '',
		],
	];