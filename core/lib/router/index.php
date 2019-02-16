<?php

require 'Ms.php';

use core\lib\router\Ms;

Ms::get('/', function() {
  echo "Welcome";
});

Ms::get('/name/(:all)', function($name) {
  echo 'Your name is '.$name;
});

Ms::error(function() {
  echo '404';
});

Ms::dispatch();

//php -S localhost:8000