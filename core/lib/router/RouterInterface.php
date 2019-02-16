<?php

namespace core\lib\router;

use core\lib\router\Router;

Interface RouterInterface
{
    public function route(Router $entrance);
}
