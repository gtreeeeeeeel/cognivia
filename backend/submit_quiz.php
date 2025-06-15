<?php
session_start();
require '../config/db_connection.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['quiz_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$quiz_id = $_POST['quiz_id'];
$score = 0;
$total_questions = 0;

$questions = $conn->prepare("SELECT id, question_type FROM questions WHERE quiz_id = ?");
$questions->bind_param("i", $quiz_id);
$questions->execute();
$questions_result = $questions->get_result();

while ($q = $questions_result->fetch_assoc()) {
    $total_questions++;
    if (isset($_POST['answers'][$q['id']])) {
        $user_answer = $_POST['answers'][$q['id']];
        $correct_stmt = $conn->prepare("SELECT choice_text FROM choices WHERE question_id = ? AND is_correct = 1");
        $correct_stmt->bind_param("i", $q['id']);
        $correct_stmt->execute();
        $correct_result = $correct_stmt->get_result();
        $correct_answer = $correct_result->fetch_assoc()['choice_text'];

        if ($q['question_type'] == 'identification') {
            if (strtolower(trim($user_answer)) == strtolower(trim($correct_answer))) {
                $score++;
            }
        } else {
            if ($user_answer == $correct_answer) {
                $score++;
            }
        }
    }
}

$stmt = $conn->prepare("INSERT INTO results (user_id, quiz_id, score, total_questions) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iiii", $user_id, $quiz_id, $score, $total_questions);
$stmt->execute();

// Calculate percentage for styling
$percentage = ($total_questions > 0) ? ($score / $total_questions) * 100 : 0;
$score_class = '';
if ($percentage >= 80) {
    $score_class = 'excellent';
} elseif ($percentage >= 60) {
    $score_class = 'good';
} elseif ($percentage >= 40) {
    $score_class = 'fair';
} else {
    $score_class = 'poor';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Result - QuizMaster</title>
    <link rel="stylesheet" href="../assets/css/style.css">
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
        <div class="result-card">
            <h1 class="result-title">Quiz Result</h1>

            <div class="score-display <?php echo $score_class; ?>">
                <?php echo $score; ?> / <?php echo $total_questions; ?>
                <div class="percentage"><?php echo round($percentage, 1); ?>%</div>
            </div>

            <div class="message <?php echo $score_class; ?>">
                <?php
                if ($percentage >= 80) {
                    echo "Excellent work! Outstanding performance! 🎉";
                } elseif ($percentage >= 60) {
                    echo "Good job! Well done! 👍";
                } elseif ($percentage >= 40) {
                    echo "Fair attempt. Keep practicing! 📚";
                } else {
                    echo "Don't give up! Practice makes perfect! 💪";
                }
                ?>
            </div>

            <a href="../pages/dashboard.php" class="back-btn">Back to Dashboard</a>
        </div>
    </main>
</body>

</html>