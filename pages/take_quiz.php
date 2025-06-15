<?php
session_start();
require '../config/db_connection.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: ../login.php");
    exit();
}

$quiz_id = $_GET['id'];
$stmt = $conn->prepare("SELECT title, user_id FROM quizzes WHERE id = ?");
$stmt->bind_param("i", $quiz_id);
$stmt->execute();
$quiz = $stmt->get_result()->fetch_assoc();

if ($quiz['user_id'] != $_SESSION['user_id']) {
    header("Location: ../dashboard.php");
    exit();
}

$questions = $conn->prepare("SELECT id, question_text, question_type FROM questions WHERE quiz_id = ?");
$questions->bind_param("i", $quiz_id);
$questions->execute();
$questions_result = $questions->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Quiz - QuizMaster</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
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
        <form action="../backend/submit_quiz.php" method="POST" class="take-quiz-form">
            <h2><?php echo htmlspecialchars($quiz['title']); ?></h2>
            <input type="hidden" name="quiz_id" value="<?php echo $quiz_id; ?>">
            <?php $i = 0; while ($q = $questions_result->fetch_assoc()): ?>
                <div class="question-block">
                    <h3>Question <?php echo $i+1; ?>: <?php echo htmlspecialchars($q['question_text']); ?></h3>
                    <?php if ($q['question_type'] == 'true_false'): ?>
                        <label><input type="radio" name="answers[<?php echo $q['id']; ?>]" value="True" required> True</label>
                        <label><input type="radio" name="answers[<?php echo $q['id']; ?>]" value="False"> False</label>
                    <?php elseif ($q['question_type'] == 'multiple_choice'): ?>
                        <?php
                        $choices = $conn->prepare("SELECT choice_text FROM choices WHERE question_id = ?");
                        $choices->bind_param("i", $q['id']);
                        $choices->execute();
                        $choices_result = $choices->get_result();
                        while ($c = $choices_result->fetch_assoc()): ?>
                            <label><input type="radio" name="answers[<?php echo $q['id']; ?>]" value="<?php echo htmlspecialchars($c['choice_text']); ?>" required> <?php echo htmlspecialchars($c['choice_text']); ?></label>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <input type="text" name="answers[<?php echo $q['id']; ?>]" required>
                    <?php endif; ?>
                </div>
            <?php $i++; endwhile; ?>
            <button type="submit" class="btn">Submit Quiz</button>
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