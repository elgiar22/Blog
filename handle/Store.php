<?php
require_once "../inc/conn.php";
require_once "../inc/security.php";

// Check if user is logged in
if(!isset($_SESSION['user_id'])){
    header("location:../Login.php");
    exit;
}

if(isset($_POST['submit'])){
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        logSecurityEvent("CSRF token mismatch", "Post creation attempt");
        $_SESSION['errors'] = ["Security token mismatch. Please try again."];
        header("location:../addPost.php");
        exit;
    }

    // Sanitize input
    $title = sanitizeInput($_POST['title']);
    $body = sanitizeInput($_POST['body']);

    $errors = [];
    
    // Enhanced validation
    if(empty($title)){
        $errors[] = "Title is required";
    } elseif(strlen($title) < 3 || strlen($title) > 255){
        $errors[] = "Title must be between 3 and 255 characters";
    }

    if(empty($body)){
        $errors[] = "Body is required";
    } elseif(strlen($body) < 10){
        $errors[] = "Body must be at least 10 characters long";
    }

    // Image validation
    if(!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE){
        $errors[] = "Image is required";
    } else {
        $imageErrors = validateFileUpload($_FILES['image']);
        if(!empty($imageErrors)){
            $errors = array_merge($errors, $imageErrors);
        }
    }

    if(empty($errors)){
        try {
            // Generate secure filename
            $newFileName = generateSecureFilename($_FILES['image']['name']);
            $uploadPath = "../uploads/" . $newFileName;
            
            // Move uploaded file
            if(move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)){
                // Insert post with prepared statement
                $stmt = $conn->prepare("INSERT INTO posts(title, body, image, user_id) VALUES(?, ?, ?, ?)");
                $stmt->bind_param("sssi", $title, $body, $newFileName, $_SESSION['user_id']);
                
                if($stmt->execute()){
                    logSecurityEvent("Post created successfully", "User ID: " . $_SESSION['user_id']);
                    $_SESSION['success'] = ["Post created successfully!"];
                    header("location:../index.php");
                    exit;
                } else {
                    // Delete uploaded file if database insert fails
                    if(file_exists($uploadPath)){
                        unlink($uploadPath);
                    }
                    throw new Exception("Database insert failed");
                }
            } else {
                throw new Exception("File upload failed");
            }
            
        } catch (Exception $e) {
            logSecurityEvent("Post creation error", $e->getMessage());
            $_SESSION['errors'] = ["Failed to create post. Please try again."];
            header("location:../addPost.php");
            exit;
        } finally {
            if(isset($stmt)){
                $stmt->close();
            }
        }
    } else {
        // Store form data for redisplay
        $_SESSION['title'] = $title;
        $_SESSION['body'] = $body;
        $_SESSION['errors'] = $errors;
        header("location:../addPost.php");
        exit;
    }
} else {
    header("location:../addPost.php");
    exit;
}