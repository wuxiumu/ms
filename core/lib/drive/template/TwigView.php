<?php

namespace core\lib\drive\Template;

class TwigView{   

    public function view($file,$assign)
    {
        $loader = new \Twig_Loader_Filesystem(APP.'/views');
        $twig = new \Twig_Environment($loader, array(
            'cache' => PHPMSFRAME.'/cache',
            'debug'=>DEBUG,
        ));
        $template = $twig->load($file);			
        $template->display($assign?$assign:'');
    }
}