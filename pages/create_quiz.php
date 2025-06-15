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
    <title>Create Quiz - Cognivia</title>
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
                        <li><a href="../pages/create_quiz.php" class="active">Create Quiz</a></li>
                        <li><a href="../pages/create_flashcard.php">Create Flashcards</a></li>
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
        <form id="create-quiz-form" action="../backend/save_quiz.php" method="POST" class="form"> 
            <h2>Create Quiz</h2>
            <label>Quiz Title</label>
            <input type="text" name="quiz_title" required>
            <div id="questions">
                <div class="question-block">
                    <h3>Question 1</h3>
                    <label>Question Text</label>
                    <input type="text" name="questions[0][text]" required>
                    <label>Question Type</label>
                    <select name="questions[0][type]" onchange="toggleChoices(this)">
                        <option value="true_false">True/False</option>
                        <option value="multiple_choice">Multiple Choice</option>
                        <option value="identification">Identification</option>
                    </select>
                    <div class="choices" style="display: none;">
                        <label>Number of Choices</label>
                        <input type="number" min="2" max="10" value="4" onchange="updateChoices(this)">
                        <div class="choice-list">
                            <div><input type="text" name="questions[0][choices][0][text]" placeholder="Choice 1"><label><input type="checkbox" name="questions[0][choices][0][is_correct]"> Correct</label></div>
                            <div><input type="text" name="questions[0][choices][1][text]" placeholder="Choice 2"><label><input type="checkbox" name="questions[0][choices][1][is_correct]"> Correct</label></div>
                            <div><input type="text" name="questions[0][choices][2][text]" placeholder="Choice 3"><label><input type="checkbox" name="questions[0][choices][2][is_correct]"> Correct</label></div>
                            <div><input type="text" name="questions[0][choices][3][text]" placeholder="Choice 4"><label><input type="checkbox" name="questions[0][choices][3][is_correct]"> Correct</label></div>
                        </div>
                    </div>
                    <div class="answer" style="display: none;">
                        <label>Correct Answer</label>
                        <input type="text" name="questions[0][answer]">
                    </div>
                    <button type="button" onclick="removeQuestion(this)" class="btn btn-outline remove-btn">Remove</button>
                </div>
            </div>
            <button type="button" onclick="addQuestion()" class="btn btn-secondary">Add Question</button>
            <button type="submit" class="btn btn-primary">Save Quiz</button>
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