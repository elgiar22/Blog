<?php
require_once '../inc/conn.php';
session_start();
if(!isset($_SESSION['user_id'])):
header(header: "location:../Login.php");
exit;
endif;
if(isset($_POST['submit']) && isset($_GET['id'])){
    $id = $_GET['id'];

    // select old image
    $query = "select image from posts where id=$id";
    $result = mysqli_query($conn , $query);

    if(mysqli_num_rows($result)==1){
        $oldImage = mysqli_fetch_assoc($result)['image'];

        $query = "delete from posts where id = $id";
        $result = mysqli_query($conn , $query);
        if($result){
            if(file_exists("../uploads/$oldImage")){
                        unlink("../uploads/$oldImage");
                    }
            $_SESSION['success'] = ["post deleted successfuly"];
            header("location:../index.php");

        }else{
            $_SESSION['errors'] = ["error while deleted"];
            header("location:../index.php");

        }



    }else{
     header("location:../404.php");
    }









}else{
        // wrong access
    header("location:../404.php");
    exit;
}
