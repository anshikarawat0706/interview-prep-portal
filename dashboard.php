<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

require_once "db.php";

$user_id = $_SESSION["user_id"];
$user_name = $_SESSION["user_name"];


/* ==============================
   GET CATEGORY SCORES
   ============================== */

$categories = [
    "Aptitude",
    "Technical",
    "HR"
];

$scores = [];

foreach ($categories as $category) {

    $stmt = $conn->prepare(
        "SELECT score
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

        $scores[$category] = (float)$row["score"];

    } else {

        $scores[$category] = 0;

    }

    $stmt->close();
}


/* ==============================
   OVERALL PROGRESS
   ============================== */

$overall_progress = round(
    (
        $scores["Aptitude"] +
        $scores["Technical"] +
        $scores["HR"]
    ) / 3
);


/* ==============================
   MOCK INTERVIEW COUNT
   ============================== */

$stmt = $conn->prepare(
    "SELECT COUNT(*) AS total
     FROM sessions
     WHERE user_id = ?"
);

$stmt->bind_param(
    "i",
    $user_id
);

$stmt->execute();

$result = $stmt->get_result();

$row = $result->fetch_assoc();

$mock_interviews = $row["total"] ?? 0;

$stmt->close();

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>
    Dashboard - Interview Prep Portal
</title>

<link rel="stylesheet"
      href="css/style.css">


<style>

/* =================================
   DASHBOARD STATISTICS
   ================================= */

.dashboard-stats {

    display: grid;

    grid-template-columns:
        repeat(4, 1fr);

    gap: 20px;

    margin-top: 25px;

}


/* =================================
   STAT CARD
   ================================= */

.stat-card {

    background: white;

    padding: 25px 15px;

    border-radius: 18px;

    text-align: center;

    box-shadow:
        0 6px 20px rgba(0,0,0,0.07);

    transition:
        transform 0.2s ease,
        box-shadow 0.2s ease;

}


.stat-card:hover {

    transform:
        translateY(-5px);

    box-shadow:
        0 10px 28px rgba(0,0,0,0.10);

}


/* =================================
   STAT ICON
   ================================= */

.stat-icon {

    font-size: 38px;

    margin-bottom: 8px;

}


/* =================================
   STAT NUMBER
   ================================= */

.stat-number {

    font-size: 30px;

    font-weight: bold;

    color: #2563eb;

    margin: 8px 0;

}


/* =================================
   STAT TEXT
   ================================= */

.stat-card p {

    margin: 0;

    font-size: 15px;

    color: #555;

}


/* =================================
   OVERALL PROGRESS CIRCLE
   ================================= */

.progress-circle {

    width: 100px;

    height: 100px;

    border-radius: 50%;

    background: #e8f0ff;

    color: #2563eb;

    display: flex;

    justify-content: center;

    align-items: center;

    font-size: 23px;

    font-weight: bold;

    flex-shrink: 0;

}


/* =================================
   PROGRESS VALUE
   ================================= */

.progress-value {

    display: block;

    color: #2563eb;

    font-size: 23px;

    font-weight: 800;

    line-height: 1;

    opacity: 1;

    visibility: visible;

}


/* =================================
   DARK MODE
   ================================= */

/* Overall Progress Card */

html.dark-mode .progress-card {

    background: #1e293b !important;

    color: #f8fafc !important;

}


/* Overall Progress Heading */

html.dark-mode .progress-card h2 {

    color: #ffffff !important;

}


/* Overall Progress Description */

html.dark-mode .progress-card p {

    color: #cbd5e1 !important;

}


/* 91% Circle */

html.dark-mode .progress-circle {

    background: #334155 !important;

    color: #ffffff !important;

    border: 2px solid #60a5fa !important;

    opacity: 1 !important;

    visibility: visible !important;

}


/* 91% Number */

html.dark-mode .progress-circle .progress-value {

    color: #ffffff !important;

    font-size: 23px !important;

    font-weight: 800 !important;

    opacity: 1 !important;

    visibility: visible !important;

}


/* =================================
   DARK MODE STAT CARDS
   ================================= */

html.dark-mode .stat-card {

    background: #1e293b !important;

    color: #ffffff !important;

    box-shadow:
        0 6px 20px rgba(0,0,0,0.35);

}


html.dark-mode .stat-number {

    color: #93c5fd !important;

}


html.dark-mode .stat-card p {

    color: #cbd5e1 !important;

}


/* =================================
   DARK MODE CATEGORY CARDS
   ================================= */

html.dark-mode .category-card {

    background: #1e293b !important;

    color: #ffffff !important;

}


html.dark-mode .category-card h3 {

    color: #ffffff !important;

}


html.dark-mode .category-card p {

    color: #cbd5e1 !important;

}


html.dark-mode .category-card a {

    color: #60a5fa !important;

}


/* =================================
   DARK MODE QUICK ACCESS
   ================================= */

html.dark-mode .quick-card {

    background: #1e293b !important;

    color: #ffffff !important;

}


html.dark-mode .quick-card span {

    color: #ffffff !important;

}


/* =================================
   DARK MODE NAVBAR
   ================================= */

html.dark-mode .navbar {

    background: #111827 !important;

}


html.dark-mode .navbar .logo {

    color: #60a5fa !important;

}


html.dark-mode .navbar .nav-links a {

    color: #ffffff !important;

}


html.dark-mode .navbar .nav-links a:hover {

    color: #93c5fd !important;

}
/* =================================
   COMPLETE DASHBOARD DARK MODE
   ================================= */

html.dark-mode,
html.dark-mode body {
    background: #0f172a !important;
    color: #f8fafc !important;
}


/* Main dashboard area */

html.dark-mode .dashboard {
    background: transparent !important;
    color: #f8fafc !important;
}


/* Welcome */

html.dark-mode .welcome-section {
    background: transparent !important;
}

html.dark-mode .welcome-section h1 {
    color: #ffffff !important;
}

html.dark-mode .welcome-section p {
    color: #cbd5e1 !important;
}


/* Overall Progress */

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


/* 91% */

html.dark-mode .progress-circle {
    background: #334155 !important;
    color: #ffffff !important;
    border: 2px solid #60a5fa !important;
}

html.dark-mode .progress-circle .progress-value {
    color: #ffffff !important;
}


/* Section headings */

html.dark-mode .section-title {
    color: #ffffff !important;
}


/* Performance cards */

html.dark-mode .stat-card {
    background: #1e293b !important;
    color: #ffffff !important;
}

html.dark-mode .stat-number {
    color: #93c5fd !important;
}

html.dark-mode .stat-card p {
    color: #cbd5e1 !important;
}


/* Practice cards */

html.dark-mode .category-card {
    background: #1e293b !important;
    color: #ffffff !important;
}

html.dark-mode .category-card h3 {
    color: #ffffff !important;
}

html.dark-mode .category-card p {
    color: #cbd5e1 !important;
}

html.dark-mode .category-card a {
    color: #60a5fa !important;
}


/* Quick Access */

html.dark-mode .quick-card {
    background: #1e293b !important;
    color: #ffffff !important;
}

html.dark-mode .quick-card span {
    color: #ffffff !important;
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

/* =================================
   RESPONSIVE
   ================================= */

@media (max-width: 950px) {

    .dashboard-stats {

        grid-template-columns:
            repeat(2, 1fr);

    }

}


@media (max-width: 600px) {

    .dashboard-stats {

        grid-template-columns:
            1fr;

    }

}
/* =================================
   LIGHT MODE - DASHBOARD CARDS
   ================================= */

html:not(.dark-mode) .category-card {
    background: #ffffff !important;
    color: #1f2937 !important;
}

html:not(.dark-mode) .category-card h3 {
    color: #1f2937 !important;
}

html:not(.dark-mode) .category-card p {
    color: #666666 !important;
}

html:not(.dark-mode) .category-card a {
    color: #2563eb !important;
}

html:not(.dark-mode) .quick-card {
    background: #ffffff !important;
    color: #1f2937 !important;
}

html:not(.dark-mode) .quick-card span {
    color: #1f2937 !important;
}

</style>

</head>


<body>


<!-- =================================
     NAVIGATION BAR
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
     MAIN DASHBOARD
     ================================= -->

<main class="dashboard">


    <!-- =================================
         WELCOME SECTION
         ================================= -->

    <section class="welcome-section">


        <h1>

            Welcome back,
            <?php
            echo htmlspecialchars($user_name);
            ?>
            👋

        </h1>


        <p>

            Keep practicing and get ready
            for your next interview.

        </p>


    </section>



    <!-- =================================
         OVERALL PROGRESS
         ================================= -->

    <section class="progress-card">


        <div>

            <h2>
                Overall Progress
            </h2>


            <p>

                Your preparation progress
                across all categories.

            </p>

        </div>


        <div class="progress-circle">

            <span class="progress-value">

                <?php
                echo $overall_progress;
                ?>%

            </span>

        </div>


    </section>



    <!-- =================================
         PERFORMANCE CARDS
         ================================= -->

    <h2 class="section-title">

        Your Performance

    </h2>


    <section class="dashboard-stats">


        <!-- APTITUDE -->

        <div class="stat-card">


            <div class="stat-icon">
                🧠
            </div>


            <div class="stat-number">

                <?php
                echo $scores["Aptitude"];
                ?>%

            </div>


            <p>
                Aptitude
            </p>


        </div>



        <!-- TECHNICAL -->

        <div class="stat-card">


            <div class="stat-icon">
                💻
            </div>


            <div class="stat-number">

                <?php
                echo $scores["Technical"];
                ?>%

            </div>


            <p>
                Technical
            </p>


        </div>



        <!-- HR -->

        <div class="stat-card">


            <div class="stat-icon">
                🗣️
            </div>


            <div class="stat-number">

                <?php
                echo $scores["HR"];
                ?>%

            </div>


            <p>
                HR Interview
            </p>


        </div>



        <!-- MOCK INTERVIEW -->

        <div class="stat-card">


            <div class="stat-icon">
                🎯
            </div>


            <div class="stat-number">

                <?php
                echo $mock_interviews;
                ?>

            </div>


            <p>
                Mock Interviews
            </p>


        </div>


    </section>



    <!-- =================================
         PRACTICE CATEGORIES
         ================================= -->

    <h2 class="section-title">

        Practice Categories

    </h2>


    <section class="category-container">


        <!-- APTITUDE -->

        <div class="category-card">


            <div class="category-icon">
                🧠
            </div>


            <h3>
                Aptitude
            </h3>


            <p>

                Practice quantitative aptitude,
                logical reasoning and
                problem solving.

            </p>


            <a href="aptitude.php">

                Start Practice →

            </a>


        </div>



        <!-- TECHNICAL -->

        <div class="category-card">


            <div class="category-icon">
                💻
            </div>


            <h3>
                Technical
            </h3>


            <p>

                Prepare technical interview
                questions related to programming
                and CS concepts.

            </p>


            <a href="technical.php">

                Start Practice →

            </a>


        </div>



        <!-- HR -->

        <div class="category-card">


            <div class="category-icon">
                🗣️
            </div>


            <h3>
                HR Interview
            </h3>


            <p>

                Practice common HR questions
                and improve your interview
                communication.

            </p>


            <a href="hr.php">

                Start Practice →

            </a>


        </div>


    </section>



    <!-- =================================
         QUICK ACCESS
         ================================= -->

    <h2 class="section-title">

        Quick Access

    </h2>


    <section class="quick-container">


        <!-- BOOKMARKS -->

        <a href="bookmarks.php"
           class="quick-card">

            ⭐

            <span>
                Bookmarks
            </span>

        </a>



        <!-- PROGRESS -->

        <a href="progress.php"
           class="quick-card">

            📊

            <span>
                My Progress
            </span>

        </a>



        <!-- MOCK INTERVIEW -->

        <a href="mock-interview.php"
           class="quick-card">

            🎯

            <span>
                Mock Interview
            </span>

        </a>


    </section>


</main>


<!-- =================================
     THEME JS
     ================================= -->

<script src="js/theme.js"></script>


</body>

</html>