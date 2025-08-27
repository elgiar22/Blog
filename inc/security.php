<?php
// Security helper functions

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Sanitize input data
 */
function sanitizeInput($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate name (letters and spaces only)
 */
function validateName($name) {
    return preg_match('/^[a-zA-Z\s]{2,50}$/', $name);
}

/**
 * Validate phone number
 */
function validatePhone($phone) {
    return preg_match('/^[0-9]{10,15}$/', $phone);
}

/**
 * Validate password strength
 */
function validatePassword($password) {
    return strlen($password) >= 6 && strlen($password) <= 128;
}

/**
 * Secure file upload validation
 */
function validateFileUpload($file) {
    $errors = [];
    
    // Check if file was uploaded
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "File upload error";
        return $errors;
    }
    
    // Check file size (1MB max)
    $maxSize = 1024 * 1024; // 1MB
    if ($file['size'] > $maxSize) {
        $errors[] = "File size too large (max 1MB)";
    }
    
    // Check file type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    
    if (!in_array($mimeType, $allowedTypes)) {
        $errors[] = "Invalid file type. Only JPEG, PNG, and GIF allowed";
    }
    
    // Check file extension
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($extension, $allowedExtensions)) {
        $errors[] = "Invalid file extension";
    }
    
    return $errors;
}

/**
 * Generate secure filename
 */
function generateSecureFilename($originalName) {
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    return uniqid('img_', true) . '.' . $extension;
}

/**
 * Check if user owns the post
 */
function userOwnsPost($conn, $postId, $userId) {
    $stmt = $conn->prepare("SELECT user_id FROM posts WHERE id = ?");
    $stmt->bind_param("i", $postId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $post = $result->fetch_assoc();
        return $post['user_id'] == $userId;
    }
    
    return false;
}

/**
 * Log security events
 */
function logSecurityEvent($event, $details = '') {
    $logMessage = date('Y-m-d H:i:s') . " - " . $event . " - " . $details . " - IP: " . $_SERVER['REMOTE_ADDR'] . "\n";
    error_log($logMessage, 3, 'security.log');
}
?>
