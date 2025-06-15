<?php
session_start();
require '../config/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Flashcards - Cognivia</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="shortcut icon" href="../assets/images/logo.png" type="image/x-icon">
</head>

<body>
    <header id="dashboard-header">
        <div class="container">
            <div class="top-bar">
                <h1 class="logo">Cognivia</h1>
                <nav id="rightNav">
                    <ul>
                        <li><a href="../pages/dashboard.php">Dashboard</a></li>
                        <li><a href="../pages/create_quiz.php">Create Quiz</a></li>
                        <li><a href="../pages/create_flashcard.php" class="active">Create Flashcards</a></li>
                        <li><a href="../pages/profile.php">Profile</a></li>
                        <li><a href="logout.php" class="logout-btn">Logout</a></li>
                    </ul>
                </nav>
                <div id="menu-toggle" class="hamburger">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>
    </header>
    <main>
        <form id="create-flashcard-form" action="../backend/save_flashcard.php" method="POST" class="form"> <!-- Adjusted path -->
            <h2>Create Flashcard Deck</h2>
            <label>Deck Title</label>
            <input type="text" name="deck_title" required>
            <div id="cards">
                <div class="card-block">
                    <h3>Card 1</h3>
                    <label>Front</label>
                    <input type="text" name="cards[0][front]" required>
                    <label>Back</label>
                    <input type="text" name="cards[0][back]" required>
                    <label>Image (URL)</label>
                    <input type="url" name="cards[0][image]">
                    <button type="button" onclick="removeCard(this)" class="btn btn-outline remove-btn">Remove</button>
                </div>
            </div>
            <button type="button" onclick="addCard()" class="btn btn-secondary">Add Card</button>
            <button type="submit" class="btn btn-primary">Save Deck</button>
        </form>
    </main>

    <div id="logout-modal" class="modal-overlay">
        <div class="modal">
            <div class="modal-header">
                <h3>Confirm Logout</h3>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to logout? You will be redirected to the login page.</p>
            </div>
            <div class="modal-actions">
                <button class="btn btn-cancel" id="cancel-logout">Cancel</button>
                <a href="logout.php" class="btn btn-confirm" id="confirm-logout">Logout</a>
            </div>
        </div>
    </div>

</body>

<script src="../assets/js/script.js"></script>

</html>