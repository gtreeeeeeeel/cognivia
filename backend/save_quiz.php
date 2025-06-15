<?php
session_start();
require '../config/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $quiz_title = $_POST['quiz_title'];

    $stmt = $conn->prepare("INSERT INTO quizzes (title, user_id) VALUES (?, ?)");
    $stmt->bind_param("si", $quiz_title, $user_id);
    $stmt->execute();
    $quiz_id = $conn->insert_id;

    foreach ($_POST['questions'] as $index => $q) {
        $question_text = $q['text'];
        $question_type = $q['type'];
        $answer = isset($q['answer']) ? $q['answer'] : '';

        $q_stmt = $conn->prepare("INSERT INTO questions (quiz_id, question_text, question_type) VALUES (?, ?, ?)");
        $q_stmt->bind_param("iss", $quiz_id, $question_text, $question_type);
        $q_stmt->execute();
        $question_id = $conn->insert_id;

        if ($question_type == 'multiple_choice' || $question_type == 'true_false') {
            foreach ($q['choices'] as $choice) {
                if (!empty($choice['text'])) {
                    $choice_text = $choice['text'];
                    $is_correct = isset($choice['is_correct']) ? 1 : 0;
                    $c_stmt = $conn->prepare("INSERT INTO choices (question_id, choice_text, is_correct) VALUES (?, ?, ?)");
                    $c_stmt->bind_param("isi", $question_id, $choice_text, $is_correct);
                    $c_stmt->execute();
                }
            }
        } else {
            $c_stmt = $conn->prepare("INSERT INTO choices (question_id, choice_text, is_correct) VALUES (?, ?, 1)");
            $c_stmt->bind_param("is", $question_id, $answer);
            $c_stmt->execute();
        }
    }

    header("Location: ../pages/dashboard.php"); 
}
?>