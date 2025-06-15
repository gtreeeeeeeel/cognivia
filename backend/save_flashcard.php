<?php
session_start();
require '../config/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'rate') {
    $flashcard_id = $_POST['flashcard_id'];
    $confidence = $_POST['confidence'];
    $intervals = ['easy' => 4, 'medium' => 2, 'hard' => 1];
    $next_review = date('Y-m-d', strtotime("+{$intervals[$confidence]} days"));

    $stmt = $conn->prepare("INSERT INTO flashcard_reviews (user_id, flashcard_id, confidence_level, next_review, review_count) VALUES (?, ?, ?, ?, 1) ON DUPLICATE KEY UPDATE confidence_level = ?, next_review = ?, review_count = review_count + 1");
    $stmt->bind_param("iissss", $user_id, $flashcard_id, $confidence, $next_review, $confidence, $next_review);
    $stmt->execute();
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $deck_title = $_POST['deck_title'];

    $stmt = $conn->prepare("INSERT INTO flashcard_decks (title, user_id) VALUES (?, ?)");
    $stmt->bind_param("si", $deck_title, $user_id);
    $stmt->execute();
    $deck_id = $conn->insert_id;

    foreach ($_POST['cards'] as $card) {
        $front = $card['front'];
        $back = $card['back'];
        $image = !empty($card['image']) ? $card['image'] : null;

        $c_stmt = $conn->prepare("INSERT INTO flashcards (deck_id, front_text, back_text, image_url) VALUES (?, ?, ?, ?)");
        $c_stmt->bind_param("isss", $deck_id, $front, $back, $image);
        $c_stmt->execute();
    }

    header("Location: ../pages/dashboard.php"); // Adjusted path
}
?>