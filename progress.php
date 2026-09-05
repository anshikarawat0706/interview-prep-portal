<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

require_once "db.php";

$user_id = $_SESSION["user_id"];


/* ==============================
   GET CATEGORY PROGRESS
   ============================== */

$categories = [
    "Aptitude",
    "Technical",
    "HR"
];

$progress = [];

foreach ($categories as $category) {

    $stmt = $conn->prepare(
        "SELECT attempted, correct, score
         FROM progress
         WHERE user_id = ? AND category = ?"
    );

    $stmt->bind_param(
        "is",
        $user_id,
        $category
    );

    $stmt->execute();

    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {

        $progress[$category] = $row;

    } else {

        $progress[$category] = [
            "attempted" => 0,
            "correct" => 0,
            "score" => 0
        ];

    }

    $stmt->close();
}


/* ==============================
   GET MOCK INTERVIEW SESSIONS
   ============================== */

$stmt = $conn->prepare(
    "SELECT COUNT(*) AS total_sessions,
            AVG(score) AS average_score
     FROM sessions
     WHERE user_id = ?"
);

$stmt->bind_param(
    "i",
    $user_id
);

$stmt->execute();

$result = $stmt->get_result();

$mock = $result->fetch_assoc();

$stmt->close();

$total_sessions = $mock["total_sessions"] ?? 0;

$average_mock_score = $mock["average_score"] ?? 0;

$average_mock_score = round($average_mock_score);


/* ==============================
   OVERALL PROGRESS
   ============================== */

$total_scores =
    $progress["Aptitude"]["score"] +
    $progress["Technical"]["score"] +
    $progress["HR"]["score"];

$overall_progress =
    round($total_scores / 3);

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>My Progress</title>

<link rel="stylesheet"
      href="css/style.css">


<style>

/* =================================
   PROGRESS CONTAINER
   ================================= */

.progress-container {

    max-width: 1000px;

    margin: auto;

}


/* =================================
   CATEGORY GRID
   ================================= */

.progress-grid {

    display: grid;

    grid-template-columns:
        repeat(3, 1fr);

    gap: 20px;

}


/* =================================
   PROGRESS BOX
   ================================= */

.progress-box {

    background: white;

    padding: 25px;

    border-radius: 18px;

    box-shadow:
        0 6px 20px rgba(0,0,0,0.07);

}


/* =================================
   ICON
   ================================= */

.progress-icon {

    font-size: 40px;

}


/* =================================
   SCORE
   ================================= */

.progress-score {

    font-size: 35px;

    font-weight: bold;

    color: #2563eb;

    margin: 10px 0;

}


/* =================================
   PROGRESS BAR
   ================================= */

.progress-bar {

    height: 10px;

    background: #e5e7eb;

    border-radius: 10px;

    overflow: hidden;

}


.progress-fill {

    height: 100%;

    background: #2563eb;

    border-radius: 10px;

}


/* =================================
   SUMMARY GRID
   ================================= */

.summary-grid {

    display: grid;

    grid-template-columns:
        repeat(3, 1fr);

    gap: 20px;

    margin-top: 25px;

}


/* =================================
   SUMMARY BOX
   ================================= */

.summary-box {

    background: white;

    padding: 25px;

    text-align: center;

    border-radius: 18px;

    box-shadow:
        0 6px 20px rgba(19,20,19,0.07);

}


/* =================================
   SUMMARY NUMBER
   ================================= */

.summary-number {

    font-size: 32px;

    font-weight: bold;

    color: #2563eb;

}


/* =================================
   DARK MODE
   ================================= */

/* Entire progress page */

html.dark-mode body {

    background: #0f172a !important;

    color: #f8fafc !important;

}


/* Progress container */

html.dark-mode .progress-container {

    color: #f8fafc !important;

}


/* Category cards */

html.dark-mode .progress-box {

    background: #1e293b !important;

    color: #ffffff !important;

    box-shadow:
        0 6px 20px rgba(0,0,0,0.35);

}


/* Aptitude / Technical / HR */

html.dark-mode .progress-box h3 {

    color: #ffffff !important;

    font-weight: 700 !important;

}


/* Questions attempted */

html.dark-mode .progress-box p {

    color: #cbd5e1 !important;

}


/* Percentage */

html.dark-mode .progress-score {

    color: #93c5fd !important;

}


/* Progress bar background */

html.dark-mode .progress-bar {

    background: #475569 !important;

}


/* Progress fill */

html.dark-mode .progress-fill {

    background: #3b82f6 !important;

}


/* Summary cards */

html.dark-mode .summary-box {

    background: #1e293b !important;

    color: #ffffff !important;

    box-shadow:
        0 6px 20px rgba(0,0,0,0.35);

}


/* Summary numbers */

html.dark-mode .summary-number {

    color: #93c5fd !important;

}


/* Summary text */

html.dark-mode .summary-box p {

    color: #cbd5e1 !important;

}


/* Section headings */

html.dark-mode .section-title {

    color: #ffffff !important;

}


/* Welcome section */

html.dark-mode .welcome-section h1 {

    color: #ffffff !important;

}


html.dark-mode .welcome-section p {

    color: #cbd5e1 !important;

}


/* Overall progress */

html.dark-mode .progress-card {

    background: #1e293b !important;

    color: #ffffff !important;

}


html.dark-mode .progress-card h2 {

    color: #ffffff !important;

}


html.dark-mode .progress-card p {

    color: #cbd5e1 !important;

}


/* Overall progress circle */

html.dark-mode .progress-circle {

    background: #334155 !important;

    color: #ffffff !important;

    border: 2px solid #60a5fa !important;

}


/* Navbar */

html.dark-mode .navbar {

    background: #111827 !important;

}


html.dark-mode .navbar .logo {

    color: #60a5fa !important;

}


html.dark-mode .navbar .nav-links a {

    color: #ffffff !important;

}


/* Back to dashboard */

html.dark-mode .progress-container > div:last-child a {

    color: #60a5fa !important;

}


/* =================================
   RESPONSIVE
   ================================= */

@media (max-width: 700px) {

    .progress-grid,
    .summary-grid {

        grid-template-columns: 1fr;

    }

}

</style>

</head>


<body>


<!-- =================================
     NAVBAR
     ================================= -->

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


<!-- =================================
     MAIN
     ================================= -->

<main class="dashboard">

<div class="progress-container">


<!-- =================================
     WELCOME
     ================================= -->

<div class="welcome-section">

    <h1>
        📊 My Progress
    </h1>

    <p>
        Track your interview preparation and improve your weak areas.
    </p>

</div>


<!-- =================================
     OVERALL PROGRESS
     ================================= -->

<div class="progress-card">

    <div>

        <h2>
            Overall Progress
        </h2>

        <p>
            Your average performance across all categories.
        </p>

    </div>


    <div class="progress-circle">

        <?php echo $overall_progress; ?>%

    </div>

</div>


<br>


<!-- =================================
     CATEGORY PERFORMANCE
     ================================= -->

<h2 class="section-title">

      Category Performance

</h2>


<div class="progress-grid">


<!-- =================================
     APTITUDE
     ================================= -->

<div class="progress-box">

    <div class="progress-icon">
        🧠
    </div>


    <h3>
        Aptitude
    </h3>


    <div class="progress-score">

        <?php echo $progress["Aptitude"]["score"]; ?>%

    </div>


    <div class="progress-bar">

        <div
            class="progress-fill"
            style="width:
            <?php echo $progress["Aptitude"]["score"]; ?>%;">
        </div>

    </div>


    <p>

        <?php echo $progress["Aptitude"]["attempted"]; ?>

        questions attempted

    </p>

</div>


<!-- =================================
     TECHNICAL
     ================================= -->

<div class="progress-box">

    <div class="progress-icon">
        💻
    </div>


    <h3>
        Technical
    </h3>


    <div class="progress-score">

        <?php echo $progress["Technical"]["score"]; ?>%

    </div>


    <div class="progress-bar">

        <div
            class="progress-fill"
            style="width:
            <?php echo $progress["Technical"]["score"]; ?>%;">
        </div>

    </div>


    <p>

        <?php echo $progress["Technical"]["attempted"]; ?>

        questions attempted

    </p>

</div>


<!-- =================================
     HR
     ================================= -->

<div class="progress-box">

    <div class="progress-icon">
        🗣️
    </div>


    <h3>
        HR Interview
    </h3>


    <div class="progress-score">

        <?php echo $progress["HR"]["score"]; ?>%

    </div>


    <div class="progress-bar">

        <div
            class="progress-fill"
            style="width:
            <?php echo $progress["HR"]["score"]; ?>%;">
        </div>

    </div>


    <p>

        <?php echo $progress["HR"]["attempted"]; ?>

        questions attempted

    </p>

</div>


</div>


<br>


<!-- =================================
     INTERVIEW SUMMARY
     ================================= -->

<h2 class="section-title">

    Interview Summary

</h2>


<div class="summary-grid">


<!-- MOCK INTERVIEWS -->

<div class="summary-box">

    <div style="font-size:35px;">
        🎯
    </div>


    <div class="summary-number">

        <?php echo $total_sessions; ?>

    </div>


    <p>
        Mock Interviews
    </p>

</div>


<!-- AVERAGE SCORE -->

<div class="summary-box">

    <div style="font-size:35px;">
        📈
    </div>


    <div class="summary-number">

        <?php echo $average_mock_score; ?>%

    </div>


    <p>
        Average Mock Score
    </p>

</div>


<!-- OVERALL PERFORMANCE -->

<div class="summary-box">

    <div style="font-size:35px;">
        🚀
    </div>


    <div class="summary-number">

        <?php echo $overall_progress; ?>%

    </div>


    <p>
        Overall Performance
    </p>

</div>


</div>


<br>


<!-- BACK -->

<div style="text-align:center;">

    <a href="dashboard.php">
        ← Back to Dashboard
    </a>

</div>


</div>

</main>


<script src="js/theme.js"></script>

</body>

</html>