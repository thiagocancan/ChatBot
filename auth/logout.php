<?php
require_once __DIR__ . '/../auth/Auth.php';

// Initialize authentication
$auth = new Auth();

// Logout user
$auth->logout();

// Redirect to login page
header('Location: login.php');
exit;
