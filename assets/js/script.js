// NAVIGATION
const menuToggle = document.getElementById('menu-toggle');
const rightNav = document.getElementById('rightNav');

menuToggle.addEventListener('click', () => {
    rightNav.classList.toggle('show');
});

//LOGOUT MODAL
const logoutModal = document.getElementById('logout-modal');
const logoutTrigger = document.querySelector('.logout-btn'); // changed to class
const cancelLogout = document.getElementById('cancel-logout');

// Make sure all elements exist before adding event listeners
if (logoutTrigger) {
    logoutTrigger.addEventListener('click', function (e) {
        e.preventDefault(); // prevent navigation to logout.php
        showModal();
    });
}

if (cancelLogout) {
    cancelLogout.addEventListener('click', function () {
        hideModal();
    });
}

if (logoutModal) {
    logoutModal.addEventListener('click', function (e) {
        if (e.target === logoutModal) {
            hideModal();
        }
    });
}

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && logoutModal && logoutModal.classList.contains('show')) {
        hideModal();
    }
});

function showModal() {
    logoutModal.classList.add('show');
    document.body.style.overflow = 'hidden';
}

function hideModal() {
    logoutModal.classList.remove('show');
    document.body.style.overflow = 'auto';
}














let questionCount = 1;
let cardCount = 1;

function addQuestion() {
    const questionsDiv = document.getElementById('questions');
    const questionBlock = document.createElement('div');
    questionBlock.className = 'question-block';
    questionBlock.innerHTML = `
        <h3>Question ${questionCount + 1}</h3>
        <label>Question Text</label>
        <input type="text" name="questions[${questionCount}][text]" required>
        <label>Question Type</label>
        <select name="questions[${questionCount}][type]" onchange="toggleChoices(this)">
            <option value="true_false">True/False</option>
            <option value="multiple_choice">Multiple Choice</option>
            <option value="identification">Identification</option>
        </select>
        <div class="choices" style="display: none;">
            <label>Number of Choices</label>
            <input type="number" min="2" max="10" value="4" onchange="updateChoices(this)">
            <div class="choice-list">
                <div><input type="text" name="questions[${questionCount}][choices][0][text]" placeholder="Choice 1"><label><input type="checkbox" name="questions[${questionCount}][choices][0][is_correct]"> Correct</label></div>
                <div><input type="text" name="questions[${questionCount}][choices][1][text]" placeholder="Choice 2"><label><input type="checkbox" name="questions[${questionCount}][choices][1][is_correct]"> Correct</label></div>
                <div><input type="text" name="questions[${questionCount}][choices][2][text]" placeholder="Choice 3"><label><input type="checkbox" name="questions[${questionCount}][choices][2][is_correct]"> Correct</label></div>
                <div><input type="text" name="questions[${questionCount}][choices][3][text]" placeholder="Choice 4"><label><input type="checkbox" name="questions[${questionCount}][choices][3][is_correct]"> Correct</label></div>
            </div>
        </div>
        <div class="answer" style="display: none;">
            <label>Correct Answer</label>
            <input type="text" name="questions[${questionCount}][answer]">
        </div>
        <button type="button" onclick="removeQuestion(this)" class="btn btn-outline remove-btn">Remove</button>
    `;
    questionsDiv.appendChild(questionBlock);
    questionCount++;
    updateQuestionNumbers();
}

function removeQuestion(button) {
    if (questionCount > 1) {
        button.parentElement.remove();
        questionCount--;
        updateQuestionNumbers();
    }
}

function updateQuestionNumbers() {
    const questionBlocks = document.querySelectorAll('.question-block');
    questionBlocks.forEach((block, index) => {
        block.querySelector('h3').textContent = `Question ${index + 1}`;
        const inputs = block.querySelectorAll('input, select');
        inputs.forEach(input => {
            if (input.name) {
                input.name = input.name.replace(/questions\[\d+\]/, `questions[${index}]`);
            }
        });
    });
}

function toggleChoices(select) {
    const questionBlock = select.parentElement;
    const choicesDiv = questionBlock.querySelector('.choices');
    const answerDiv = questionBlock.querySelector('.answer');
    const index = Array.from(document.querySelectorAll('.question-block')).indexOf(questionBlock);
    if (select.value === 'multiple_choice' || select.value === 'true_false') {
        choicesDiv.style.display = 'block';
        answerDiv.style.display = 'none';
        if (select.value === 'true_false') {
            const choiceList = choicesDiv.querySelector('.choice-list');
            choiceList.innerHTML = `
                <div><input type="text" value="True" readonly name="questions[${index}][choices][0][text]"><label><input type="checkbox" name="questions[${index}][choices][0][is_correct]"> Correct</label></div>
                <div><input type="text" value="False" readonly name="questions[${index}][choices][1][text]"><label><input type="checkbox" name="questions[${index}][choices][1][is_correct]"> Correct</label></div>
            `;
            choicesDiv.querySelector('input[type="number"]').style.display = 'none';
            choicesDiv.querySelector('label[for="number"]').style.display = 'none';
        } else {
            choicesDiv.querySelector('input[type="number"]').style.display = 'block';
            choicesDiv.querySelector('label[for="number"]').style.display = 'block';
        }
    } else {
        choicesDiv.style.display = 'none';
        answerDiv.style.display = 'block';
    }
}

function updateChoices(input) {
    const numChoices = Math.min(Math.max(parseInt(input.value), 2), 10);
    const choiceList = input.parentElement.querySelector('.choice-list');
    const questionBlock = input.closest('.question-block');
    const index = Array.from(document.querySelectorAll('.question-block')).indexOf(questionBlock);
    choiceList.innerHTML = '';
    for (let i = 0; i < numChoices; i++) {
        const div = document.createElement('div');
        div.innerHTML = `<input type="text" name="questions[${index}][choices][${i}][text]" placeholder="Choice ${i + 1}"><label><input type="checkbox" name="questions[${index}][choices][${i}][is_correct]"> Correct</label>`;
        choiceList.appendChild(div);
    }
}

function addCard() {
    const cardsDiv = document.getElementById('cards');
    const cardBlock = document.createElement('div');
    cardBlock.className = 'card-block';
    cardBlock.innerHTML = `
        <h3>Card ${cardCount + 1}</h3>
        <label>Front</label>
        <input type="text" name="cards[${cardCount}][front]" required>
        <label>Back</label>
        <input type="text" name="cards[${cardCount}][back]" required>
        <label>Image (URL)</label>
        <input type="url" name="cards[${cardCount}][image]">
        <button type="button" onclick="removeCard(this)" class="btn btn-outline remove-btn">Remove</button>
    `;
    cardsDiv.appendChild(cardBlock);
    cardCount++;
    updateCardNumbers();
}

function removeCard(button) {
    if (cardCount > 1) {
        button.parentElement.remove();
        cardCount--;
        updateCardNumbers();
    }
}

function updateCardNumbers() {
    const cardBlocks = document.querySelectorAll('.card-block');
    cardBlocks.forEach((block, index) => {
        block.querySelector('h3').textContent = `Card ${index + 1}`;
        const inputs = block.querySelectorAll('input');
        inputs.forEach(input => {
            if (input.name) {
                input.name = input.name.replace(/cards\[\d+\]/, `cards[${index}]`);
            }
        });
    });
}