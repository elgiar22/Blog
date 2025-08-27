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
        logSecurityEvent("CSRF token mismatch", "Post deletion attempt");
        $_SESSION['errors'] = ["Security token mismatch. Please try again."];
        header("location:../index.php");
        exit;
    }

    // Check if user owns the post
    if(!userOwnsPost($conn, $postId, $_SESSION['user_id'])){
        logSecurityEvent("Unauthorized post deletion attempt", "User ID: " . $_SESSION['user_id'] . ", Post ID: " . $postId);
        $_SESSION['errors'] = ["You are not authorized to delete this post"];
        header("location:../index.php");
        exit;
    }

    try {
        // Get post image before deletion
        $stmt = $conn->prepare("SELECT image FROM posts WHERE id = ?");
        $stmt->bind_param("i", $postId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result->num_rows === 1){
            $post = $result->fetch_assoc();
            $imageName = $post['image'];
            
            // Delete post from database
            $stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
            $stmt->bind_param("i", $postId);
            
            if($stmt->execute()){
                // Delete associated image file
                $imagePath = "../uploads/" . $imageName;
                if(file_exists($imagePath)){
                    unlink($imagePath);
                }
                
                logSecurityEvent("Post deleted successfully", "User ID: " . $_SESSION['user_id'] . ", Post ID: " . $postId);
                $_SESSION['success'] = ["Post deleted successfully!"];
                header("location:../index.php");
                exit;
            } else {
                throw new Exception("Database deletion failed");
            }
        } else {
            $_SESSION['errors'] = ["Post not found"];
            header("location:../index.php");
            exit;
        }
        
    } catch (Exception $e) {
        logSecurityEvent("Post deletion error", $e->getMessage());
        $_SESSION['errors'] = ["Failed to delete post. Please try again."];
        header("location:../index.php");
        exit;
    } finally {
        if(isset($stmt)){
            $stmt->close();
        }
    }
} else {
    header("location:../index.php");
    exit;
}