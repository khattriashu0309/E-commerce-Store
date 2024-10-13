<?php

use Phalcon\Mvc\Controller;

session_start();
class LoginController extends Controller
{
    public function indexAction()
    {
        $this->view->data = "";
        if (!empty($this->request->getPost())) {
            $var = $this->session;
            $var = $this->container->getSession();
            $this->view->data = "Wrong Credentials!!";
            $result = Users::find();
            foreach ($result as $user) {

                if (($user->email == $this->request->get('emailfield')) && ($user->userpassword == $this->request->get('pass')) && ($user->status == 'Approved') && ($user->role == 'customer')) {

                    $var->set('userdata', array("ID" => $user->id, "fname" => $user->firstname, "lname" => $user->lastname, "email" => $_POST['emailfield'], "password" => $_POST['pass'], "role" => $user->role));
                    if ($var->has('cart')) {
                        header('Location:http://localhost:8080/user/checkout');
                    } else {
                        header('Location:http://localhost:8080/user/profile');
                    }
                } else if (($user->email == $this->request->get('emailfield')) && ($user->userpassword == $this->request->get('pass')) && ($user->status == 'Approved') && ($user->role == 'admin')) {
                    $var->set('userdata', array("ID" => $user->id, "fname" => $user->firstname, "lname" => $user->lastname, "email" => $_POST['emailfield'], "password" => $_POST['pass'], "role" => $user->role));
                    header('Location:http://localhost:8080/admin/dashboard');
                } else if (($user->email == $this->request->get('emailfield')) && ($user->userpassword == $this->request->get('pass')) && ($user->status == 'Pending') && ($user->role == 'customer')) {
                    $this->view->data = "User is not Approved!!";
                }
            }
        }
    }
}
