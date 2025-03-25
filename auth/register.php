<?php
include '../includes/config.php';

$errors = [];
// Error array
$username = $email = "";
// Preserve input values

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    // Validate username
    if (empty($username)) {
        $errors['username'] = "⚠ Please enter a username.";
    } elseif (!preg_match('/\d/', $username)) {
        $errors['username'] = "⚠ Username must contain at least one number.";
    }

    //Validate email format
    if (empty($email)) {
        $errors['email'] = "⚠ Please enter an email.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "⚠ Invalid email format. Please enter a valid email (e.g., example@email.com).";
    }

    // Check if password meets complexity requirements
    if (strlen($password) < 6 || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password) || !preg_match('/[^a-zA-Z0-9]/', $password)) {
        $errors['password'] = "⚠ Password must be at least 6 characters long, contain one uppercase letter, one number, and one special character.";
    }

    // Validate password confirmation
    if ($password !== $confirmPassword) {
        $errors['confirm_password'] = "⚠ Password confirmation does not match.";
    }

    // Check if email already exists
    if (empty($errors)) {
        $checkQuery = "SELECT id FROM users WHERE email = :email";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->execute([':email' => $email]);

        if ($checkStmt->fetch()) {
            $errors['email'] = "⚠ This email is already in use. Please choose another email!";
        }
    }

    // If no errors, proceed with registration
    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
        $stmt = $conn->prepare($query);
        $stmt->execute([
            ':username' => htmlspecialchars($username),
            ':email' => $email,
            ':password' => $hashedPassword
        ]);

        header("Location: login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
    <link rel="stylesheet" href="./../assets/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="d-flex justify-content-center align-items-center vh-100 bg-light">
    <div class="card shadow p-4" style="max-width: 400px; width: 100%;">
        <h2 class="text-center">Sign Up</h2>
        <form method="POST" novalidate>
            <div class="mb-3">
                <input type="text" name="username" class="form-control" placeholder="Username" value="<?= htmlspecialchars($username) ?>">
                <?php if (isset($errors['username'])): ?>
                    <div class="text-danger"> <?= $errors['username'] ?> </div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <input type="email" name="email" id="email" class="form-control" placeholder="Email" value="<?= htmlspecialchars($email) ?>" oninput="validateEmail()">
                <div id="email-error" class="text-danger"></div>
                <?php if (isset($errors['email'])): ?>
                    <div class="text-danger"> <?= $errors['email'] ?> </div>
                <?php endif; ?>
            </div>

            <div class="mb-3 position-relative">
                <input type="password" id="password" name="password" class="form-control" placeholder="Password">
                <span class="position-absolute top-50 end-0 translate-middle-y px-2" onclick="togglePassword('password')" style="cursor: pointer;">👁️</span>
                <?php if (isset($errors['password'])): ?>
                    <div class="text-danger"> <?= $errors['password'] ?> </div>
                <?php endif; ?>
            </div>

            <div class="mb-3 position-relative">
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirm Password">
                <span class="position-absolute top-50 end-0 translate-middle-y px-2" onclick="togglePassword('confirm_password')" style="cursor: pointer;">👁️</span>
                <?php if (isset($errors['confirm_password'])): ?>
                    <div class="text-danger"> <?= $errors['confirm_password'] ?> </div>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-primary w-100">Sign Up</button>
        </form>

        <p class="mt-3 text-center">Already have an account? <a href="login.php">Log in</a></p>
    </div>

    <script>
        document.querySelector("form").addEventListener("submit", function(event) {
            let emailField = document.getElementById("email");
            let emailError = document.getElementById("emailError");
            let emailValue = emailField.value.trim();
            let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // Email Format

            emailError.textContent = ""; // Remove old error

            if (!emailPattern.test(emailValue)) {
                emailError.textContent = "⚠ Please enter a valid email address!";
                event.preventDefault(); // Prevent login if email error format
            }
        });

        function togglePassword(fieldId) {
            let passwordField = document.getElementById(fieldId);
            let toggleIcon = passwordField.nextElementSibling;

            if (passwordField.type === "password") {
                passwordField.type = "text";
                toggleIcon.textContent = "👁️‍🗨️"; 
            } else {
                passwordField.type = "password";
                toggleIcon.textContent = "👁️"; 
            }
        }
    </script>
</body>

</html>