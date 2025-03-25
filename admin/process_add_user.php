<?php
session_start();
include '../includes/config.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $role = trim($_POST["role"]);

    $errors = [];

    if (empty($username)) {
        $errors[] = "Username is required.";
    } elseif (!preg_match("/^[a-zA-Z0-9]{3,}$/", $username)) {
        $errors[] = "Username must be at least 3 characters long and contain only letters and numbers.";
    }

    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@!#$%^&*()-_+=])[A-Za-z\d@!#$%^&*()-_+=]{6,}$/", $password)) {
        $errors[] = "Password must be at least 6 characters long, include at least one uppercase letter, one lowercase letter, one number, and one special character (@!#$%^&*()-_+=).";
    }


    if (empty($role)) {
        $errors[] = "Role selection is required.";
    }

    if (!empty($errors)) {
        echo json_encode(["status" => "error", "messages" => $errors]);
        exit();
    }

    try {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email");
        $stmt->execute([":email" => $email]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(["status" => "error", "messages" => ["This email is already registered."]]);
            exit();
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)");
        $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':password' => $hashed_password,
            ':role' => $role
        ]);

        echo json_encode(["status" => "success", "message" => "User added successfully!"]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "messages" => ["Database error: " . $e->getMessage()]]);
    }
}
