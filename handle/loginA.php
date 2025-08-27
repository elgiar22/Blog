<?php
require_once '../inc/conn.php';
require_once '../inc/security.php';

// Redirect if already logged in
if(isset($_SESSION['user_id'])){
    header("location:../index.php");
    exit;
}

if(isset($_POST['submit'])){
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        logSecurityEvent("CSRF token mismatch", "Login attempt");
        $_SESSION['errors'] = ["Security token mismatch. Please try again."];
        header("location:../Login.php");
        exit;
    }

    // Sanitize input
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password']; // Don't sanitize password

    $errors = [];
    
    // Enhanced validation
    if(empty($email)){
        $errors[] = "Email is required";
    } elseif(!validateEmail($email)){
        $errors[] = "Please enter a valid email address";
    }

    if(empty($password)){
        $errors[] = "Password is required";
    } elseif(!validatePassword($password)){
        $errors[] = "Password must be between 6 and 128 characters";
    }

    if(empty($errors)){
        try {
            // Use prepared statement to prevent SQL injection
            $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if($result->num_rows === 1){
                $user = $result->fetch_assoc();
                $storedPassword = $user['password'];
                $userId = $user['id'];
                $userName = $user['name'];

                // Verify password
                if(password_verify($password, $storedPassword)){
                    // Regenerate session ID for security
                    session_regenerate_id(true);
                    
                    // Set session variables
                    $_SESSION['user_id'] = $userId;
                    $_SESSION['user_name'] = $userName;
                    
                    logSecurityEvent("User login successful", "User ID: " . $userId);
                    $_SESSION['success'] = ["Welcome back, " . htmlspecialchars($userName) . "!"];
                    header("location:../index.php");
                    exit;
                } else {
                    logSecurityEvent("Failed login attempt", "Invalid password for email: " . $email);
                    $_SESSION['errors'] = ["Invalid email or password"];
                    header("location:../Login.php");
                    exit;
                }
            } else {
                logSecurityEvent("Failed login attempt", "Email not found: " . $email);
                $_SESSION['errors'] = ["Invalid email or password"];
                header("location:../Login.php");
                exit;
            }
            
        } catch (Exception $e) {
            logSecurityEvent("Login error", $e->getMessage());
            $_SESSION['errors'] = ["Login failed. Please try again."];
            header("location:../Login.php");
            exit;
        } finally {
            $stmt->close();
        }
    } else {
        $_SESSION['errors'] = $errors;
        header("location:../Login.php");
        exit;
    }
} else {
    header("location:../Login.php");
    exit;
}