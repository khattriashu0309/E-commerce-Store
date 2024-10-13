<?php

use Phalcon\Mvc\Controller;

class IndexController extends Controller
{
    public function indexAction()
    {
        header('Location:http://localhost:8080/login/index');
    }
    public function ShopAction()
    {
        header('Location:http://localhost:8080/user/shop');
    }
}
