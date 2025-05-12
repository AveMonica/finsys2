<?php
session_start();
include("dbcon.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST["username"]);
    $password = mysqli_real_escape_string($conn, $_POST["password"]);

    $query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $_SESSION["user"] = $username;
        header("Location: dashboard.php");
        exit();
    } else {
        echo "<script>alert('Invalid username or password!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FINSYS | LOGIN</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="assets/bootstrap-icons-1.11.3/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/styles.css">
</head>

<body>
    <div class="split-screen">
        <!-- Left Side: Visual Section -->
        <div class="left-side">
            <div class="overlay">
                <!-- Logo Image -->
                <img src="assets/img/2020-nia-logo.svg" alt="Logo" class="logo">
                <h1 class="display-4 fw-bold">Welcome Back!</h1>
                <p class="lead">Login to access your dashboard</p>
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="right-side">
            <div class="login-form">
                <h2 class="mb-4">Login</h2>
                <form method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label text-start w-100">Username</label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" required>
                    </div>
                    <div class="mb-3 position-relative">
                        <label for="password" class="form-label text-start w-100">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                        <!-- Toggle Eye Icon -->
                        <i class="bi bi-eye-slash toggle-password" id="togglePassword"></i>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
            </div>
        </div>
    </div>

    <script src="assets/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS for Toggle Password -->
    <script>
        const togglePassword = document.querySelector("#togglePassword");
        const password = document.querySelector("#password");

        togglePassword.addEventListener("click", function() {
            // Toggle the type attribute
            const type = password.getAttribute("type") === "password" ? "text" : "password";
            password.setAttribute("type", type);

            // Toggle the eye icon
            this.classList.toggle("bi-eye");
            this.classList.toggle("bi-eye-slash");
        });
    </script>
</body>

</html>