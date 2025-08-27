<?php
require_once '../inc/conn.php';
require_once '../inc/security.php';

// Check if user is logged in
if(!isset($_SESSION['user_id'])){
    header("location:../Login.php");
    exit;
}

if(isset($_POST['submit']) && isset($_GET['id'])){
    $postId = (int)$_GET['id']; // Cast to integer for safety
    
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        logSecurityEvent("CSRF token mismatch", "Post update attempt");
        $_SESSION['errors'] = ["Security token mismatch. Please try again."];
        header("location:../editPost.php?id=" . $postId);
        exit;
    }

    // Check if user owns the post
    if(!userOwnsPost($conn, $postId, $_SESSION['user_id'])){
        logSecurityEvent("Unauthorized post update attempt", "User ID: " . $_SESSION['user_id'] . ", Post ID: " . $postId);
        $_SESSION['errors'] = ["You are not authorized to edit this post"];
        header("location:../index.php");
        exit;
    }

    // Get current post data
    $stmt = $conn->prepare("SELECT image FROM posts WHERE id = ?");
    $stmt->bind_param("i", $postId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows !== 1){
        $_SESSION['errors'] = ["Post not found"];
        header("location:../index.php");
        exit;
    }
    
    $currentPost = $result->fetch_assoc();
    $oldImage = $currentPost['image'];

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

    // Handle image upload (optional)
    $newImageName = $oldImage; // Keep old image by default
    
    if(isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE){
        $imageErrors = validateFileUpload($_FILES['image']);
        if(!empty($imageErrors)){
            $errors = array_merge($errors, $imageErrors);
        } else {
            $newImageName = generateSecureFilename($_FILES['image']['name']);
        }
    }

    if(empty($errors)){
        try {
            // Update post with prepared statement
            $stmt = $conn->prepare("UPDATE posts SET title = ?, body = ?, image = ? WHERE id = ?");
            $stmt->bind_param("sssi", $title, $body, $newImageName, $postId);
            
            if($stmt->execute()){
                // Handle new image upload if provided
                if(isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE){
                    $newImagePath = "../uploads/" . $newImageName;
                    
                    // Move new image
                    if(move_uploaded_file($_FILES['image']['tmp_name'], $newImagePath)){
                        // Delete old image if it's different
                        if($oldImage !== $newImageName && file_exists("../uploads/" . $oldImage)){
                            unlink("../uploads/" . $oldImage);
                        }
                    } else {
                        // Revert to old image if upload fails
                        $stmt = $conn->prepare("UPDATE posts SET image = ? WHERE id = ?");
                        $stmt->bind_param("si", $oldImage, $postId);
                        $stmt->execute();
                        throw new Exception("New image upload failed");
                    }
                }
                
                logSecurityEvent("Post updated successfully", "User ID: " . $_SESSION['user_id'] . ", Post ID: " . $postId);
                $_SESSION['success'] = ["Post updated successfully!"];
                header("location:../viewPost.php?id=" . $postId);
                exit;
            } else {
                throw new Exception("Database update failed");
            }
            
        } catch (Exception $e) {
            logSecurityEvent("Post update error", $e->getMessage());
            $_SESSION['errors'] = ["Failed to update post. Please try again."];
            header("location:../editPost.php?id=" . $postId);
            exit;
        } finally {
            if(isset($stmt)){
                $stmt->close();
            }
        }
    } else {
        $_SESSION['errors'] = $errors;
        header("location:../editPost.php?id=" . $postId);
        exit;
    }
} else {
    header("location:../index.php");
    exit;
}