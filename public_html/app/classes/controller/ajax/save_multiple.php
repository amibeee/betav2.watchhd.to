<?php
// save_multiple.php

// Ensure this script can only be accessed via AJAX
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
    die('Direct access not permitted');
}

// Start the session if it hasn't been started already
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the multiple parameter is set in the POST data
if (isset($_POST['multiple'])) {
    // Convert the string 'true' or 'false' to boolean
    $multiple = $_POST['multiple'] === 'true';
    
    // Save the multiple value to the session
    $_SESSION['multiple'] = $multiple;
    
    // Send a success response
    echo json_encode(['success' => true]);
} else {
    // Send an error response if the multiple parameter is not set
    echo json_encode(['success' => false, 'message' => 'Multiple parameter not set']);
}