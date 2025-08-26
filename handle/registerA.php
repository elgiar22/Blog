<?php

require_once '../inc/conn.php';
session_start();

if(isset($_SESSION['user_id'])){
    header("location:../index.php");
    exit;
}
if(isset($_POST['submit'])){

    $name = trim(htmlspecialchars($_POST['name']));
    $email = trim(htmlspecialchars($_POST['email']));
    $password = trim(htmlspecialchars($_POST['password']));
    $phone = trim(htmlspecialchars($_POST['phone']));

    $errors = [];
    // validation
    if(empty($name)){
        $errors[] = "name is requried";
    }   elseif(is_numeric($name)){
        $errors[] = "name must be string";
    }

    if(empty($email)){
        $errors[] = "email is requried";
    }   elseif(! filter_var($email , FILTER_VALIDATE_EMAIL)){
        $errors[] = "email not correct";
    }

    if(empty($password)){
        $errors[] = "password is requried";
    }   elseif(strlen($password)<6){
        $errors[] = "password less than 6";
    }

    if(empty($phone)){
        $errors[] = "phone is requried";
    }   elseif(! is_numeric($phone)){
        $errors[] = "phone must be number";
    }

    if(empty($errors)){
        // hash 
        $password = password_hash($password , PASSWORD_DEFAULT);


         $query = "insert into users(`name`,`email`,`password`,`phone`) values('$name','$email','$password','$phone')";
        $result = mysqli_query($conn , $query);
        if($result){
            //login
            $_SESSION['success'] = ["you register successfuly"];
            header("location:../login.php");

        }else{
        $_SESSION["errors"] = ["error while register"];
        header("location:../register.php");
        }



    }else{
        $_SESSION['name'] = $name;
        $_SESSION['email'] = $email;
        $_SESSION['phone'] = $phone;
        $_SESSION['errors'] = $errors;
        header("location:../register.php");
    }


}else{
    header("location:../register.php");
}