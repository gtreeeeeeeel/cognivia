<?php
session_start();
require '../config/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$quizzes = $conn->prepare("SELECT id, title FROM quizzes WHERE user_id = ?");
$quizzes->bind_param("i", $user_id);
$quizzes->execute();
$quizzes_result = $quizzes->get_result();

$decks = $conn->prepare("SELECT id, title FROM flashcard_decks WHERE user_id = ?");
$decks->bind_param("i", $user_id);
$decks->execute();
$decks_result = $decks->get_result();

$results = $conn->prepare("SELECT r.score, r.total_questions, r.taken_at, q.title FROM results r JOIN quizzes q ON r.quiz_id = q.id WHERE r.user_id = ?");
$results->bind_param("i", $user_id);
$results->execute();
$results_result = $results->get_result();

$reviews = $conn->prepare("SELECT fr.review_count, fr.last_reviewed, f.front_text, fd.title FROM flashcard_reviews fr JOIN flashcards f ON fr.flashcard_id = f.id JOIN flashcard_decks fd ON f.deck_id = fd.id WHERE fr.user_id = ? ORDER BY fr.last_reviewed DESC LIMIT 10");
$reviews->bind_param("i", $user_id);
$reviews->execute();
$reviews_result = $reviews->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Cognivia</title>
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
                        <li><a href="../pages/create_flashcard.php">Create Flashcards</a></li>
                        <li><a href="../pages/profile.php" class="active">Profile</a></li>
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
        <section class="profile">            
            <h3>Your Quizzes</h3>
            <?php if ($quizzes_result->num_rows == 0): ?>
                <p class="no-content">No quizzes found.</p>
                <a href="create_quiz.php" class="btn btn-primary">Create Quiz</a>
            <?php else: ?>
                <div class="card-grid">
                    <?php while ($quiz = $quizzes_result->fetch_assoc()): ?>
                        <div class="card">
                            <h4><?php echo htmlspecialchars($quiz['title']); ?></h4>
                            <a href="take_quiz.php?id=<?php echo $quiz['id']; ?>" class="btn btn-primary">Take Quiz</a>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>

            <h3>Your Flashcard Decks</h3>
            <?php if ($decks_result->num_rows == 0): ?>
                <p class="no-content">No flashcard decks found.</p>
                <a href="create_flashcard.php" class="btn btn-primary">Create Flashcard Deck</a>
            <?php else: ?>
                <div class="card-grid">
                    <?php while ($deck = $decks_result->fetch_assoc()): ?>
                        <div class="card">
                            <h4><?php echo htmlspecialchars($deck['title']); ?></h4>
                            <a href="study_flashcard.php?id=<?php echo $deck['id']; ?>" class="btn btn-primary">Study Deck</a>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>

            <h3>Quiz History</h3>
            <?php if ($results_result->num_rows == 0): ?>
                <p class="no-content">No quiz history found.</p>
            <?php else: ?>
                <div class="card-grid">
                    <?php while ($result = $results_result->fetch_assoc()): ?>
                        <div class="card">
                            <h4><?php echo htmlspecialchars($result['title']); ?></h4>
                            <p>Score: <?php echo $result['score'] . '/' . $result['total_questions']; ?></p>
                            <p>Taken: <?php echo $result['taken_at']; ?></p>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>

            <h3>Recent Flashcard Reviews</h3>
            <?php if ($reviews_result->num_rows == 0): ?>
                <p class="no-content">No flashcard reviews found.</p>
            <?php else: ?>
                <div class="card-grid">
                    <?php while ($review = $reviews_result->fetch_assoc()): ?>
                        <div class="card">
                            <h4><?php echo htmlspecialchars($review['title']); ?></h4>
                            <p>Card: <?php echo htmlspecialchars(substr($review['front_text'], 0, 50)) . '...'; ?></p>
                            <p>Reviewed: <?php echo $review['last_reviewed']; ?></p>
                            <p>Times Reviewed: <?php echo $review['review_count']; ?></p>
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