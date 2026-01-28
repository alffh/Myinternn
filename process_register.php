<?php
include 'db_connect.php';

$username = $_POST['username'];
$email    = $_POST['email'];
$password = $_POST['password'];
$role     = $_POST['role'];

// Prevent admin registration
if ($role === 'admin') {
    die("Invalid role");
}

// Check email exists
$check = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    die("Email already registered");
}

// Hash password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insert user
$stmt = $conn->prepare("
    INSERT INTO users (username, email, password, role, status, created_at)
    VALUES (?, ?, ?, ?, 'active', NOW())
");
$stmt->bind_param("ssss", $username, $email, $hashed_password, $role);

if ($stmt->execute()) {
    header("Location: login.php?registered=success");
} else {
    echo "Registration failed";
}
