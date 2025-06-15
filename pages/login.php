<?php
session_start();
require '../config/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ? OR username = ?");
    $stmt->bind_param("ss", $email, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            header("Location: dashboard.php");
        } else {
            $error = "Invalid email/username or password.";
        }
    } else {
        $error = "User not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Cognivia</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="shortcut icon" href="../assets/images/logo.png" type="image/x-icon">
</head>

<body class="login-body">
    <header id="index-header">
        <div class="container">
            <div id="leftNav">
                <h1 class="logo">Cognivia</h1>
            </div>
            <div id="rightNav">
                <ul>
                    <li><a href="pages/login.php">Login</a></li>
                </ul>
            </div>
        </div>
    </header>
    <main class="login-main">
        <section class="login-content">
            <div class="login-left">
                <h2>Smash sets in your sweats.</h2>
                <img class="logo" src="../assets/images/logo.png" alt="logo">
            </div>
            <div class="login-right">
                <div class="login-form">
                    <div class="error" style="display: none;">Error message placeholder</div>
                    <form method="POST" class="auth-form">
                        <label>Email / Username</label>
                        <input type="text" name="email" placeholder="Enter your email or username" required>

                        <label>Password</label>
                        <div class="password-container">
                            <input type="password" name="password" id="password" placeholder="Enter your password" required>
                            <button type="button" class="password-toggle" onclick="togglePassword()">👁️</button>
                        </div>

                        <div class="forgot-password">
                            <a href="forgot-password.php">Forgot password?</a>
                        </div>

                        <button type="submit" class="btn btn-primary">Log in</button>
                    </form>
                    <p class="terms">By clicking Log in, you accept Cognivia's Terms of Service and Privacy Policy</p>
                    <a href="register.php" class="btn btn-outline">New to Cognivia? Create an account</a>
                </div>
            </div>
        </section>
    </main>
    <footer>
        <div class="footer-content">
            <div class="footer-section about">
                <h1>Cognivia</h1>
                <p>Creating smarter learning through quizzes and flashcards.</p>
            </div>

            <div class="footer-section links">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="#">Home</a></li>
                    <li><a href="#">Create Quiz</a></li>
                    <li><a href="#">Flashcards</a></li>
                    <li><a href="#">Profile</a></li>
                </ul>
            </div>

            <div class="footer-section socials">
                <h4>Follow Us</h4>
                <div class="social-icons">
                    <a href="#"><img src="assets/images/facebook.png" alt="Facebook" /></a>
                    <a href="#"><img src="assets/images/twitter.png" alt="Twitter" /></a>
                    <a href="#"><img src="assets/images/instagram.png" alt="Instagram" /></a>
                    <a href="#"><img src="assets/images/youtube.png" alt="Youtube" /></a>
                    <a href="#"><img src="assets/images/tiktok.png" alt="Tiktok" /></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© 2025 Cognivia. All rights reserved.</p>
        </div>
    </footer>
</body>

<script>
    //LOGIN SHOW PASSWORD
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const toggleButton = document.querySelector('.password-toggle');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleButton.textContent = '🙈';
        } else {
            passwordInput.type = 'password';
            toggleButton.textContent = '👁️';
        }
    }
</script>

</html>