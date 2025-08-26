<?php
require_once "../inc/conn.php";
session_start();

//form , catch , filter
if(isset($_POST['submit'])){

$title = trim(htmlspecialchars($_POST['title']));
$body = trim(htmlspecialchars($_POST['body']));
//validation
$errors = [];

//title
if(empty($title)){
    $errors[] = "title is required";
}elseif(is_numeric($title)){
    $errors[] = "title must be string";
}

//body
if(empty($body)){
    $errors[] = "body is required";
}elseif(is_numeric($body)){
    $errors[] = "body must be string";
}


//image validation

$image = $_FILES['image'];
$image_name = $image['name'];
$image_error = $image['error'];
$image_tmpname = $image['tmp_name'];
$image_size_MB= $image['size']/(1024*1024);
$image_ext = strtolower(pathinfo($image_name , PATHINFO_EXTENSION));
$newName = uniqid().".$image_ext";
$ext =["png" , "jpg" , "jpeg"];
if($image_error != 0){
    $errors[] = "image not correct";
}elseif($image_size_MB > 1)
{
    $errors[] = "image large size";
}elseif(!in_array($image_ext,$ext)){
    $errors[] = "choose correct image";
}

// end validation ------------------------------------------------------------------------

//errors -> store in session -> redirect form
if(empty($errors)){

//insert -> move image

$query = "INSERT INTO posts(`title`, `body`, `image`, `user_id`) VALUES('$title', '$body', '$newName', 1)";
$result = mysqli_query($conn , $query);
if($result){
    //move
    move_uploaded_file($image_tmpname , "../uploads/$newName");
    //session success
    $_SESSION['success'] = ["post inserted successfuly"];
    //head index
    header("location:../index.php");

}else{
    $_SESSION['errors'] = ["error while insert"];
    header("location:../addPost.php");
}

//message , header
}else{
    $_SESSION['title'] = $title;
    $_SESSION['body'] = $body;
    $_SESSION['errors'] = $errors;
    header("location:../addPost.php");
}

}
else{
    header("location:../addPost.php");
}
