<?php
session_start();
require '../config/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $password);

    if ($stmt->execute()) {
        header("Location: login.php");
    } else {
        $error = "Username or email already exists.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Cognivia</title>
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
    <main class="register-main">
        <section class="register-content">
            <div class="register-left">
                <h2>Smash sets in your sweats.</h2>
                <img class="logo" src="../assets/images/logo.png" alt="logo">
            </div>
            <div class="register-right">
                <div class="register-form">
                    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
                    <form method="POST" class="auth-form">
                        <label>Email</label>
                        <input type="email" name="email" placeholder="Enter your email address" required>
                        <label>Username</label>
                        <input type="text" name="username" placeholder="Enter your username" required>
                        <label>Password</label>
                        <input type="text" name="password" placeholder="Enter your password" required>
                        <button type="submit" class="btn btn-primary">Sign Up</button>
                    </form>
                    <p class="terms">
                        By clicking Sign Up, you accept Cognivia's Terms of Service and Privacy Policy
                    </p>
                    <a href="login.php" class="btn btn-outline">Already have an account? Log in</a>
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

</html>