<?php
namespace Demo\Controllers;

class HiController{

    public function sayAction() {
        echo "hello world!" . PHP_EOL;

        return ['code'=>1, 'message'=>"method: say"];
        
    }

}