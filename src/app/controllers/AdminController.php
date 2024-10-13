<?php

use Phalcon\Mvc\Controller;

class AdminController extends Controller
{
    public function dashboardAction()
    {
        $var = $this->session;
        $var = $this->container->getSession();
        if ($var->has('userdata')) {
            $this->view->data = Users::find(array("role" => 'customer', "limit" => 3));
        } else {
            header('Location:http://localhost:8080/login/index');
        }
    }


    public function adduserAction()
    {
        $user = new Users();
        if (!empty($this->request->getPost())) {

            $user->assign(
                $this->request->getPost(),
                [
                    'firstname',
                    'lastname',
                    'email',
                    'userpassword',
                    'status',
                    'role'
                ]
            );
            $user->save();
        }
    }

    public function productsAction()
    {
        if (!empty($this->request->getPost())) {
            $v = $this->request->get('id');
            $res = Product::findFirst($v);
            unlink("../public/images/$res->pimage");
            $res->delete();
        }
        $this->view->data = Product::find();
    }
    public function addproductAction()
    {
        if (!empty($this->request->getPost())) {


            $img  = $this->request->getUploadedFiles();

            $imgName = $img[0]->getname();
            $res["product_image"] = $imgName;
            $temp = new Product();
            $temp->id = $this->request->get('pid');
            $temp->pname = $this->request->get('pname');
            $temp->pcategory = $this->request->get('category');
            $temp->price = $this->request->get('price');
            $temp->pcity = $this->request->get('city');
            $temp->pstate = $this->request->get('state');
            $temp->pzip = $this->request->get('zip');
            $temp->pimage = $imgName;
            try {
                move_uploaded_file($img[0]->getTempname(), "../public/images/$imgName");
            } catch (Exception $e) {
                echo $e;
            }
            try {
                $temp->save();

                header('Location:products');
            } catch (Exception $e) {
                echo "Duplicate ID Not Allowed";
                die();
            }
        }
    }

    public function updateproductAction()
    {
        if (isset($_POST['changestatus'])) {
            $v = $this->request->get('change');
            $this->view->data = Product::find("id IN (" . $v . ")");
        }
        if (isset($_POST['update'])) {

            $res = $this->request->getPost();
            $img = $this->request->getUploadedFiles();
            $imgName = $img[0]->getname();

            $id = $this->request->get('pid');
            $result = Product::find("id IN (" . $id . ")");
            foreach ($result as $temp) {
                $temp->pname = $this->request->get('pname');
                $temp->pcategory = $this->request->get('pcategory');
                $temp->price = $this->request->get('price');
                $temp->pcity = $this->request->get('city');
                $temp->pstate = $this->request->get('state');
                $temp->pzip = $this->request->get('zip');
                if ($imgName != "") {
                    if (move_uploaded_file($img[0]->getTempname(), "../public/images/$imgName")) {
                        unlink("../public/images/$temp->pimage");
                        $temp->pimage = $imgName;
                    }
                }
                $temp->save();
                header('Location:products');
            }
        }
    }

    public function ordersAction()
    {
        if (!empty($this->request->getPost())) {
            $r = '';
            $value = $this->request->get('change');
            $result = Userorders::find("orderid IN (" . $value . ")");
            foreach ($result as $user) {
                $r = ($user->pstatus);
            }

            if ($r == "Packed") {
                $result = Userorders::find("orderid IN (" . $value . ")");
                foreach ($result as $user) {
                    $user->pstatus = "In Transit";
                    $user->save();
                }
            } else if ($r == "In Transit") {
                $result = Userorders::find("orderid IN (" . $value . ")");
                foreach ($result as $user) {
                    $user->pstatus = "Delivered";
                    $user->save();
                }
            } else if ($r == "Delivered") {
                $result = Userorders::find("orderid IN (" . $value . ")");
                foreach ($result as $user) {
                    $user->pstatus = "Packed";
                    $user->save();
                }
            }
        }
        $this->view->data = Userorders::find();
    }
    public function customerAction()
    {

        if (isset($_POST['delete'])) {
            $v = $this->request->get('id');
            $res = Users::find("id IN (" . $v . ")");
            $res->delete();
        }

        if (isset($_POST['changestatus'])) {

            $r = '';
            $value = $this->request->get('change');
            $result = Users::find("id IN (" . $value . ")");
            foreach ($result as $user) {
                $r = ($user->status);
            }

            if ($r == "Approved") {
                $result = Users::find("id IN (" . $value . ")");
                foreach ($result as $user) {
                    $user->status = "Pending";
                    $user->save();
                }
            } else if ($r == "Pending") {
                $result = Users::find("id IN (" . $value . ")");
                foreach ($result as $user) {
                    $user->status = "Approved";
                    $user->save();
                }
            }
        }
        $this->view->data = Users::find("role IN ('customer')");
    }
}
