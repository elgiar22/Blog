<?php
require_once '../inc/conn.php';
session_start();
if(isset($_SESSION['user_id'])){
    header("location:../login.php");
    exit;
}
if(isset($_POST['submit'])){

    $email = trim(htmlspecialchars($_POST['email']));
    $password = trim(htmlspecialchars($_POST['password']));
    //validation
    $errors = [];
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
//---------------------end validation----------------------------------------------
    //compare email ->
    if(empty($errors)){
    $query = "select id, name , password from users where email ='$email' ";
    $result = mysqli_query($conn,$query);
    if(mysqli_num_rows($result)==1){     //email founded
        
        $user = mysqli_fetch_assoc($result);
        $oldPassword = $user['password'];
        $user_id = $user['id'];
        $user_name = $user["name"];

        $_SESSION['email']=$email;

        $is_verify = password_verify($password , $oldPassword);

       if($is_verify){
        $_SESSION['user_id'] = $user_id; //login
        $_SESSION['success'] = ["welcome $user_name"];
        header("location:../index.php");

        
       }else {
        $_SESSION['errors'] = ["email or password not correct"];
        header("location:../login.php");
       }

    }else{
        $_SESSION['errors'] = ["email or password not correct"];
        header("location:../login.php");
    }
    }else{
    $_SESSION['errors'] = $errors;
    header("location:../login.php");
}

}else{
    header("location:../login.php");
}

