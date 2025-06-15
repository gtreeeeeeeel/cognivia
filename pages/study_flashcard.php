<?php
session_start();
require '../config/db_connection.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: ../login.php");
    exit();
}

$deck_id = $_GET['id'];
$stmt = $conn->prepare("SELECT title, user_id FROM flashcard_decks WHERE id = ?");
$stmt->bind_param("i", $deck_id);
$stmt->execute();
$deck = $stmt->get_result()->fetch_assoc();

if (!$deck || $deck['user_id'] != $_SESSION['user_id']) {
    header("Location: dashboard.php");
    exit();
}

$cards = $conn->prepare("SELECT id, front_text, back_text, image_url FROM flashcards WHERE deck_id = ?");
$cards->bind_param("i", $deck_id);
$cards->execute();
$cards_result = $cards->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Study Flashcards - Cognivia</title>
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
        <section class="flashcard-study">
            <h2><?php echo htmlspecialchars($deck['title']); ?></h2>
            <?php if ($cards_result->num_rows == 0): ?>
                <p class="no-content">No flashcards in this deck.</p>
                <a href="../pages/create_flashcard.php" class="btn btn-primary">Create Flashcards</a>
            <?php else: ?>
                <div class="study-controls">
                    <select id="study-mode">
                        <option value="flip">Flip Mode</option>
                        <option value="quiz">Quiz Mode</option>
                        <option value="match">Match Game</option>
                    </select>
                </div>
                <div id="flip-mode" class="study-mode">
                    <div class="flashcard" id="flashcard">
                        <div class="front"></div>
                        <div class="back" style="display: none;"></div>
                    </div>
                    <div class="flashcard-controls">
                        <button class="btn btn-secondary" onclick="prevCard()">Previous</button>
                        <button class="btn btn-primary" onclick="flipCard()">Flip</button>
                        <button class="btn btn-secondary" onclick="nextCard()">Next</button>
                    </div>
                    <div class="confidence-buttons">
                        <button class="btn btn-outline" onclick="rateCard('hard')">Hard</button>
                        <button class="btn btn-outline" onclick="rateCard('medium')">Medium</button>
                        <button class="btn btn-outline" onclick="rateCard('easy')">Easy</button>
                    </div>
                </div>
                <div id="quiz-mode" class="study-mode" style="display: none;">
                    <div id="quiz-question"></div>
                    <div id="quiz-options"></div>
                    <button class="btn btn-primary" onclick="checkQuizAnswer()">Submit</button>
                </div>
                <div id="match-mode" class="study-mode" style="display: none;">
                    <div id="match-grid"></div>
                </div>
                <script>
                    const cards = <?php
                        $card_array = [];
                        while ($card = $cards_result->fetch_assoc()) {
                            $card_array[] = $card;
                        }
                        echo json_encode($card_array);
                    ?>;
                    let currentCard = 0;
                    let flipped = false;

                    function initStudy() {
                        if (cards.length === 0) return;
                        document.getElementById('study-mode').addEventListener('change', switchMode);
                        switchMode();
                        displayCard();
                    }

                    function switchMode() {
                        const mode = document.getElementById('study-mode').value;
                        document.querySelectorAll('.study-mode').forEach(m => m.style.display = 'none');
                        document.getElementById(`${mode}-mode`).style.display = 'block';
                        if (mode === 'flip') {
                            displayCard();
                        } else if (mode === 'quiz') {
                            startQuiz();
                        } else if (mode === 'match') {
                            startMatch();
                        }
                    }

                    function displayCard() {
                        const front = document.querySelector('.flashcard .front');
                        const back = document.querySelector('.flashcard .back');
                        front.innerHTML = `<p>${cards[currentCard].front_text}</p>${cards[currentCard].image_url ? `<img src="${cards[currentCard].image_url}" alt="Card Image" class="card-image">` : ''}`;
                        back.innerHTML = `<p>${cards[currentCard].back_text}</p>`;
                        front.style.display = flipped ? 'none' : 'block';
                        back.style.display = flipped ? 'block' : 'none';
                    }

                    function flipCard() {
                        flipped = !flipped;
                        displayCard();
                    }

                    function prevCard() {
                        currentCard = (currentCard - 1 + cards.length) % cards.length;
                        flipped = false;
                        displayCard();
                    }

                    function nextCard() {
                        currentCard = (currentCard + 1) % cards.length;
                        flipped = false;
                        displayCard();
                    }

                    function rateCard(level) {
                        fetch('../backend/save_flashcard.php', { // Adjusted path
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: `action=rate&flashcard_id=${cards[currentCard].id}&confidence=${level}`
                        }).then(() => nextCard());
                    }

                    function startQuiz() {
                        const question = document.getElementById('quiz-question');
                        const options = document.getElementById('quiz-options');
                        question.innerHTML = cards[currentCard].front_text;
                        const correct = cards[currentCard].back_text;
                        const allBacks = cards.map(c => c.back_text);
                        const choices = [correct, ...getRandom(allBacks.filter(b => b !== correct), 3)];
                        shuffle(choices);
                        options.innerHTML = choices.map(c => `<label><input type="radio" name="quiz-answer" value="${c}"> ${c}</label>`).join('');
                    }

                    function checkQuizAnswer() {
                        const selected = document.querySelector('input[name="quiz-answer"]:checked');
                        if (selected && selected.value === cards[currentCard].back_text) {
                            alert('Correct!');
                            rateCard('easy');
                        } else {
                            alert('Try again!');
                            rateCard('hard');
                        }
                    }

                    function startMatch() {
                        const grid = document.getElementById('match-grid');
                        const pairs = cards.map(c => [{text: c.front_text, type: 'front'}, {text: c.back_text, type: 'back'}]).flat();
                        shuffle(pairs);
                        grid.innerHTML = pairs.map((p, i) => `<div class="match-card" data-index="${i}" onclick="selectMatch(${i})">${p.text}</div>`).join('');
                    }

                    let firstMatch = null;
                    function selectMatch(index) {
                        const card = document.querySelector(`.match-card[data-index="${index}"]`);
                        if (card.classList.contains('matched')) return;
                        card.classList.add('selected');
                        if (!firstMatch) {
                            firstMatch = { index, card };
                        } else {
                            const firstCard = firstMatch.card;
                            const firstText = pairs[firstMatch.index].text;
                            const secondText = pairs[index].text;
                            const firstIsFront = pairs[firstMatch.index].type === 'front';
                            const match = firstIsFront ? cards.find(c => c.front_text === firstText && c.back_text === secondText) : cards.find(c => c.back_text === firstText && c.front_text === secondText);
                            if (match) {
                                card.classList.add('matched');
                                firstCard.classList.add('matched');
                                rateCard('easy');
                            } else {
                                setTimeout(() => {
                                    card.classList.remove('selected');
                                    firstCard.classList.remove('selected');
                                }, 1000);
                                rateCard('hard');
                            }
                            firstMatch = null;
                        }
                    }

                    function shuffle(array) {
                        for (let i = array.length - 1; i > 0; i--) {
                            const j = Math.floor(Math.random() * (i + 1));
                            [array[i], array[j]] = [array[j], array[i]];
                        }
                    }

                    function getRandom(array, n) {
                        const shuffled = array.sort(() => 0.5 - Math.random());
                        return shuffled.slice(0, n);
                    }

                    initStudy();
                </script>
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