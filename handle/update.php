<?php
require_once '../inc/conn.php';
session_start();

if(!isset($_SESSION['user_id'])):
header(header: "location:../Login.php");
exit;
endif;
// form , catch , filter
if(isset($_POST['submit']) && isset($_GET['id'])){
    $id = $_GET['id'];

    // select old image
    $query = "select image from posts where id=$id";
    $result = mysqli_query($conn , $query);

    if(mysqli_num_rows($result)==1){
        $oldImage = mysqli_fetch_assoc($result)['image'];

        // filter data
        $title = trim(htmlspecialchars($_POST['title']));
        $body = trim(htmlspecialchars($_POST['body']));

        // validation
        $errors = [];

        // title
        if(empty($title)){
            $errors[] = "title is required";
        }elseif(is_numeric($title)){
            $errors[] = "title must be string";
        }

        // body
        if(empty($body)){
            $errors[] = "body is required";
        }elseif(is_numeric($body)){
            $errors[] = "body must be string";
        }

        // image optional
        if(isset($_FILES['image']) && $_FILES['image']['name']){

            // image data
            $image = $_FILES['image'];
            $image_name = $image['name'];
            $image_error = $image['error'];
            $image_tmpname = $image['tmp_name'];
            $image_size_MB= $image['size']/(1024*1024);
            $image_ext = strtolower(pathinfo($image_name , PATHINFO_EXTENSION));
            $newName = uniqid().".$image_ext";
            $ext = ["png" , "jpg" , "jpeg"];

            // image validation
            if($image_error != 0){
                $errors[] = "image not correct";
            }elseif($image_size_MB > 1){
                $errors[] = "image large size";
            }elseif(!in_array($image_ext, $ext)){
                $errors[] = "choose correct image";
            }

        }else{
            // لو مفيش صورة جديدة
            $newName = $oldImage;
        }

        // لو مفيش أخطاء - update
        if(empty($errors)){
            $query = "UPDATE posts SET title = '$title', body = '$body', image = '$newName' WHERE id=$id";
            $result = mysqli_query($conn , $query);

            if($result){
                // لو في صورة جديدة - نحذف القديمة ونرفع الجديدة
                if(isset($_FILES['image']) && $_FILES['image']['name']){
                    // delete old image
                    if(file_exists("../uploads/$oldImage")){
                        unlink("../uploads/$oldImage");
                    }
                    // move new image
                    move_uploaded_file($image_tmpname , "../uploads/$newName");
                }

                // success msg
                $_SESSION['success'] = ["post updated successfully"];
                header("location:../viewPost.php?id=$id");
                exit;
            }else{
                // error in query
                $_SESSION['errors'] = ['error while update'];
                header("location:../editPost.php?id=$id");
                exit;
            }

        }else{
            // validation errors
            $_SESSION['errors'] = $errors;
            header("location:../editPost.php?id=$id");
            exit;
        }

    }else{
        // post not found
        $_SESSION['errors'] = ['post not found'];
        header("location:../editPost.php?id=$id");
        exit;
    }

}else{
    // wrong access
    header("location:../404.php");
    exit;
}
