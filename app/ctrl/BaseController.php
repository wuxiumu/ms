<?php

namespace app\ctrl;

use core\lib\Model;

class BaseController extends \core\phpmsframe
{	
    public function __construct(){
		session_start();
	}
}