<?php

use Phalcon\Mvc\Controller;


class UserController extends Controller
{
  public function profileAction()
  {
    $var = $this->session;
    $var = $this->container->getSession();
    $temp = $var->get('userdata');
    if (!empty($this->request->getPost())) {

      $id = $_SESSION['userdata']['ID'];
      $result = Users::find("id IN (" . $id . ")");
      $temp['lname'] = $this->request->get('lname');
      $temp['email'] = $this->request->get('email');
      $temp['password'] = $this->request->get('pass');
      foreach ($result as $user) {
        $user->firstname = $this->request->get('fname');
        $user->lastname = $this->request->get('lname');
        $user->email = $this->request->get('email');
        $user->userpassword = $this->request->get('pass');
        $user->save();
      }
    }

    $id = $temp['ID'];
    $this->view->data = Users::find("id IN (" . $id . ")");
  }

  public function shopAction()
  {

    if (!empty($this->request->getPost())) {
      $v = $this->request->get('search');
      $d = $this->request->get('select');

      if ($v == "") {
        $this->view->data = Product::find(array("order" => "$d"));
      } else if ($v != "" && $d != "Sort By") {

        $this->view->data = Product::find(
          [

            'columns' => '*',
            'conditions' => 'id LIKE ?1 OR pname LIKE ?1 OR pcategory LIKE ?1',
            'bind' => [
              1 => $v . "%"

            ],
            'order' => $d,
          ]

        );
      } else {

        $this->view->data = Product::find(
          [

            'columns' => '*',
            'conditions' => 'id LIKE ?1 OR pname LIKE ?1 OR pcategory LIKE ?1',
            'bind' => [
              1 => $v . "%",

            ]
          ]
        );
      }
    } else {

      $this->view->data = Product::find();
    }
  }
  public function singleproductAction()
  {
    if (!empty($this->request->getPost())) {
      $v = $this->request->get('item');
      $this->view->data = Product::find("id IN (" . $v . ")");
    }
  }
  public function cartAction()
  {
    $f = 0;
    $var = $this->session;
    $var = $this->container->getSession();
    $temp = $var->get('cart');


    if (isset($_POST['Add'])) {
      $v = $_POST['cartval'];
      $result = Product::findFirst($v);
      $result = json_decode(json_encode($result));
      $res = ['id' => $result->id, 'name' => $result->pname, 'price' => $result->price, 'quantity' => 1];
      if (!$var->has('cart')) {
        $r[0] = $res;
        $var->set('cart', $r);
      } else if ($var->has('cart')) {
        $myarray = $var->get('cart');
        foreach ($myarray as $key => $value) {
          if ($v == $value['id']) {
            $myarray[$key]['quantity']++;
            $f = 1;
            $var->set('cart', $myarray);
          }
        }
        if ($f == 0) {
          array_push($myarray, $res);
          $var->set('cart', $myarray);
        }
      }
      $this->view->t = $var->get('cart');
    } else if (isset($_POST['deleterecord'])) {
      $id = $_POST['delval'];
      $t = $var->get('cart');
      for ($i = 0; $i < count($t); $i++) {
        if ($t[$i]['id'] == $id) {
          array_splice($t, $i, 1);
          $var->set('cart', $t);
        }
      }
      $this->view->t = $var->get('cart');
    } else  if (isset($_POST['update'])) {
      $t = $var->get('cart');
      $quan = $_POST['quantity'];
      $id = $_POST['id'];
      for ($i = 0; $i < count($t); $i++) {
        if ($t[$i]['id'] == $id) {
          $t[$i]['quantity'] = $quan;
          $var->set('cart', $t);
        }
      }
      $this->view->t = $var->get('cart');
    } else {
      $this->view->t = $this->session->cart;
    }
  }

  public function checkoutAction()
  {
    $var = $this->session;
    $var = $this->container->getSession();
    $t = $var->get('userdata');
    if (isset($_POST['checkout'])) {
      if (isset($t)) {
        header('Location:checkout');
      } else {
        header('Location:http://localhost:8080/login/index');
      }
    }
    if (isset($_POST['check'])) {
      $uid = $t['ID'];
      $umail = $t['email'];
      $detail = json_encode($_SESSION['cart']);
      $status = "Packed";
      $temp = new Userorders();
      $temp->userid = $uid;
      $temp->email = $umail;
      $temp->pdetails = $detail;
      $temp->pstatus = $status;
      $temp->save();
      unset($_SESSION['cart']);

      $this->response->redirect("http://localhost:8080/user/myorders");
    }
  }
  public function myordersAction()
  {
    $var = $this->session;
    $var = $this->container->getSession();
    $t = $var->get('userdata');
    $v = $t['ID'];
    $this->view->data = Userorders::find("userid IN (" . $v . ")");
  }
}
