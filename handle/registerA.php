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
        logSecurityEvent("CSRF token mismatch", "Registration attempt");
        $_SESSION['errors'] = ["Security token mismatch. Please try again."];
        header("location:../register.php");
        exit;
    }

    // Sanitize input
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password']; // Don't sanitize password
    $phone = sanitizeInput($_POST['phone']);

    $errors = [];
    
    // Enhanced validation
    if(empty($name)){
        $errors[] = "Name is required";
    } elseif(!validateName($name)){
        $errors[] = "Name must contain only letters and spaces (2-50 characters)";
    }

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

    if(empty($phone)){
        $errors[] = "Phone number is required";
    } elseif(!validatePhone($phone)){
        $errors[] = "Phone number must contain only numbers (10-15 digits)";
    }

    if(empty($errors)){
        try {
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Check if email already exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if($result->num_rows > 0){
                $_SESSION["errors"] = ["Email already exists"];
                header("location:../register.php");
                exit;
            }
            
            // Insert new user
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $hashedPassword, $phone);
            
            if($stmt->execute()){
                logSecurityEvent("User registration successful", "Email: " . $email);
                $_SESSION['success'] = ["Registration successful! Please login."];
                header("location:../Login.php");
                exit;
            } else {
                throw new Exception("Database insert failed");
            }
            
        } catch (Exception $e) {
            logSecurityEvent("Registration error", $e->getMessage());
            $_SESSION["errors"] = ["Registration failed. Please try again."];
            header("location:../register.php");
            exit;
        } finally {
            $stmt->close();
        }
    } else {
        // Store form data for redisplay (except password)
        $_SESSION['name'] = $name;
        $_SESSION['email'] = $email;
        $_SESSION['phone'] = $phone;
        $_SESSION['errors'] = $errors;
        header("location:../register.php");
        exit;
    }
} else {
    header("location:../register.php");
    exit;
}