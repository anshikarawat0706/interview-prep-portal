<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

require_once "db.php";

/* Get 5 random Technical + HR questions */

$sql = "SELECT id, question
        FROM questions
        WHERE category IN ('Technical', 'HR')
        ORDER BY RAND()
        LIMIT 5";

$result = $conn->query($sql);

$questions = [];

while ($row = $result->fetch_assoc()) {
    $questions[] = $row;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Mock Interview - Interview Prep</title>

<link rel="stylesheet"
      href="css/style.css">

<style>

.mock-container {
    max-width: 800px;
    margin: auto;
}

.interview-card {
    background: white;
    color: #1f2937;
    padding: 35px;
    border-radius: 20px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    text-align: center;
}

.interview-card h2 {
    color: #1f2937;
}

.interview-card p {
    color: #666666;
}

.interview-icon {
    font-size: 60px;
    margin-bottom: 15px;
}

.question-box {
    text-align: left;
    margin-top: 25px;
}

.question-number {
    color: #2563eb;
    font-weight: bold;
    font-size: 18px;
}

.question-text {
    color: #1f2937;
    font-size: 21px;
    line-height: 1.5;
    margin-top: 12px;
}

.answer-box {
    width: 100%;
    min-height: 130px;
    margin-top: 20px;
    padding: 15px;
    border: 1px solid #ddd;
    border-radius: 12px;
    resize: vertical;
    font-size: 16px;
    box-sizing: border-box;
    background: #ffffff;
    color: #1f2937;
}

.answer-box::placeholder {
    color: #777777;
}

.start-btn,
.next-btn,
.finish-btn {
    margin-top: 20px;
    padding: 12px 25px;
    border: none;
    border-radius: 10px;
    background: #2563eb;
    color: white;
    font-size: 16px;
    cursor: pointer;
}

.start-btn:hover,
.next-btn:hover,
.finish-btn:hover {
    opacity: 0.9;
}

#interviewArea {
    display: none;
}

.progress-text {
    margin-top: 15px;
    color: #666;
}


/* =========================================
   DARK MODE - MOCK INTERVIEW
   ========================================= */

html.dark-mode .interview-card {
    background: #1e293b !important;
    color: #f8fafc !important;
    box-shadow: 0 8px 25px rgba(0,0,0,0.4);
}

html.dark-mode .interview-card h2 {
    color: #ffffff !important;
}

html.dark-mode .interview-card p {
    color: #cbd5e1 !important;
}

html.dark-mode .question-text {
    color: #ffffff !important;
}

html.dark-mode .question-number {
    color: #60a5fa !important;
}

html.dark-mode .answer-box {
    background: #0f172a !important;
    color: #f8fafc !important;
    border: 1px solid #475569 !important;
}

html.dark-mode .answer-box::placeholder {
    color: #94a3b8 !important;
}

html.dark-mode .progress-text {
    color: #cbd5e1 !important;
}


/* =========================================
   LIGHT MODE - MOCK INTERVIEW
   ========================================= */

html:not(.dark-mode) .interview-card {
    background: #ffffff !important;
    color: #1f2937 !important;
}

html:not(.dark-mode) .interview-card h2 {
    color: #1f2937 !important;
}

html:not(.dark-mode) .interview-card p {
    color: #666666 !important;
}

html:not(.dark-mode) .question-text {
    color: #1f2937 !important;
}

html:not(.dark-mode) .answer-box {
    background: #ffffff !important;
    color: #1f2937 !important;
    border-color: #d1d5db !important;
}

html:not(.dark-mode) .answer-box::placeholder {
    color: #777777 !important;
}

html:not(.dark-mode) .progress-text {
    color: #666666 !important;
}

</style>

</head>


<body>


<nav class="navbar">

    <div class="logo">
        Interview Prep
    </div>

    <div class="nav-links">

        <a href="dashboard.php">
            Dashboard
        </a>

        <a href="profile.php">
            Profile
        </a>

        <a href="logout.php">
            Logout
        </a>

    </div>

</nav>


<main class="dashboard">

<div class="mock-container">


<div class="welcome-section">

    <h1>
        🎯 Mock Interview
    </h1>

    <p>
        Simulate a real interview and test your preparation.
    </p>

</div>


<!-- START SCREEN -->

<div class="interview-card"
     id="startScreen">

    <div class="interview-icon">
        🎤
    </div>

    <h2>
        Ready for your interview?
    </h2>

    <p>
        You will face 5 random Technical and HR questions.
    </p>

    <p>
        Answer each question as if you were sitting in a real interview.
    </p>

    <button
        class="start-btn"
        onclick="startInterview()">

        Start Mock Interview

    </button>

</div>


<!-- INTERVIEW SCREEN -->

<div class="interview-card"
     id="interviewArea">


    <div class="question-box">

        <div class="question-number"
             id="questionNumber">
        </div>


        <div class="question-text"
             id="questionText">
        </div>


        <textarea
            class="answer-box"
            id="answerBox"
            placeholder="Type your answer here...">
        </textarea>

    </div>


    <div class="progress-text"
         id="progressText">
    </div>


    <button
        class="next-btn"
        id="nextButton"
        onclick="nextQuestion()">

        Next Question →

    </button>


    <button
        class="finish-btn"
        id="finishButton"
        onclick="finishInterview()"
        style="display:none;">

        Finish Interview ✓

    </button>


</div>


</div>

</main>


<script>

/* Questions from PHP */

const questions =
    <?php echo json_encode($questions); ?>;


let currentQuestion = 0;


/* Start Interview */

function startInterview() {

    if (questions.length === 0) {

        alert("No Technical or HR questions available.");

        return;

    }


    document.getElementById("startScreen")
        .style.display = "none";


    document.getElementById("interviewArea")
        .style.display = "block";


    showQuestion();

}


/* Show Current Question */

function showQuestion() {

    const question =
        questions[currentQuestion];


    document.getElementById("questionNumber")
        .innerText =
        "Question " +
        (currentQuestion + 1);


    document.getElementById("questionText")
        .innerText =
        question.question;


    document.getElementById("answerBox")
        .value = "";


    document.getElementById("progressText")
        .innerText =
        "Question " +
        (currentQuestion + 1) +
        " of " +
        questions.length;


    if (
        currentQuestion ===
        questions.length - 1
    ) {

        document.getElementById("nextButton")
            .style.display = "none";


        document.getElementById("finishButton")
            .style.display = "inline-block";

    }

    else {

        document.getElementById("nextButton")
            .style.display = "inline-block";


        document.getElementById("finishButton")
            .style.display = "none";

    }

}


/* Next Question */

function nextQuestion() {

    const answer =
        document.getElementById("answerBox")
        .value.trim();


    if (answer === "") {

        alert(
            "Please write your answer before continuing."
        );

        return;

    }


    currentQuestion++;


    showQuestion();

}


/* Finish Interview */

function finishInterview() {

    const answer =
        document.getElementById("answerBox")
        .value.trim();


    if (answer === "") {

        alert(
            "Please write your answer before finishing."
        );

        return;

    }


    /*
     * Send completed question count
     * to result page.
     */

    window.location.href =
        "mock-result.php?total=" +
        (currentQuestion + 1);

}

</script>

<script src="js/theme.js"></script>

</body>

</html>