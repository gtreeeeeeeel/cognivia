<?php
session_start();
require '../config/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch quizzes
$stmt_quizzes = $conn->prepare("SELECT id, title FROM quizzes WHERE user_id = ?");
$stmt_quizzes->bind_param("i", $user_id);
$stmt_quizzes->execute();
$quizzes = $stmt_quizzes->get_result();

// Fetch flashcard decks
$stmt_decks = $conn->prepare("SELECT id, title FROM flashcard_decks WHERE user_id = ?");
$stmt_decks->bind_param("i", $user_id);
$stmt_decks->execute();
$decks = $stmt_decks->get_result();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Cognivia</title>
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
                        <li><a href="../pages/dashboard.php" class="active">Dashboard</a></li>
                        <li><a href="../pages/create_quiz.php">Create Quiz</a></li>
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
        <section class="dashboard">
            <h2>Your Quizzes</h2>
            <?php if ($quizzes->num_rows == 0): ?>
                <div style="margin-bottom: 48px;">
                    <p class="no-content">No quizzes found.</p>
                    <div style="text-align: center;">
                        <a href="create_quiz.php" class="btn btn-primary">Create Quiz</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="quiz-grid">
                    <?php while ($quiz = $quizzes->fetch_assoc()): ?>
                        <div class="quiz-card">
                            <h3><?php echo htmlspecialchars($quiz['title']); ?></h3>
                            <a href="take_quiz.php?id=<?php echo $quiz['id']; ?>" class="btn btn-primary">Take Quiz</a>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>

            <h2>Your Flashcard Decks</h2>
            <?php if ($decks->num_rows == 0): ?>
                <div style="margin-bottom: 48px;">
                    <p class="no-content">No flashcard decks found.</p>
                    <div style="text-align: center;">
                        <a href="create_flashcard.php" class="btn btn-primary">Create Flashcard Deck</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="card-grid">
                    <?php while ($deck = $decks->fetch_assoc()): ?>
                        <div class="card">
                            <h3><?php echo htmlspecialchars($deck['title']); ?></h3>
                            <a href="study_flashcard.php?id=<?php echo $deck['id']; ?>" class="btn btn-primary">Study Deck</a>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>
        </section>
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