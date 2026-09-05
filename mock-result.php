<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

require_once "db.php";

$user_id = $_SESSION["user_id"];

$total = isset($_GET["total"]) ? (int)$_GET["total"] : 0;

if ($total < 0) {
    $total = 0;
}

if ($total > 5) {
    $total = 5;
}

$percentage = $total > 0
    ? round(($total / 5) * 100)
    : 0;


/* Save Mock Interview session */

$stmt = $conn->prepare(
    "INSERT INTO sessions
    (user_id, questions_attempted, score)
    VALUES (?, ?, ?)"
);

$stmt->bind_param(
    "iid",
    $user_id,
    $total,
    $percentage
);

$stmt->execute();

$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Mock Interview Result</title>

<link rel="stylesheet" href="css/style.css">

<style>

.result-container {
    max-width: 800px;
    margin: auto;
}

.result-card {
    background: white;
    padding: 40px;
    border-radius: 20px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    text-align: center;
}

.result-icon {
    font-size: 60px;
}

.result-score {
    font-size: 60px;
    font-weight: bold;
    color: #2563eb;
    margin: 15px 0;
}

.result-buttons {
    margin-top: 30px;
}

.result-buttons a {
    display: inline-block;
    margin: 8px;
    padding: 12px 22px;
    border-radius: 10px;
    background: #2563eb;
    color: white;
    text-decoration: none;
}

.result-buttons a:hover {
    opacity: 0.9;
}

</style>

</head>

<body>

<nav class="navbar">

    <div class="logo">
        Interview Prep
    </div>

    <div class="nav-links">

        <a href="dashboard.php">Dashboard</a>

        <a href="profile.php">Profile</a>

        <a href="logout.php">Logout</a>

    </div>

</nav>


<main class="dashboard">

<div class="result-container">

<div class="welcome-section">

    <h1>🎯 Mock Interview Completed</h1>

    <p>
        Your interview session has been recorded.
    </p>

</div>


<div class="result-card">

    <div class="result-icon">
        🏆
    </div>

    <h2>
        Interview Completion
    </h2>

    <div class="result-score">
        <?php echo $percentage; ?>%
    </div>

    <p>
        You completed
        <strong><?php echo $total; ?>/5</strong>
        questions.
    </p>

    <?php if ($percentage == 100): ?>

        <p>
            🔥 Excellent! You completed the full mock interview.
        </p>

    <?php elseif ($percentage >= 60): ?>

        <p>
            👍 Good effort! Keep practicing.
        </p>

    <?php else: ?>

        <p>
            📚 Keep practicing to improve your interview confidence.
        </p>

    <?php endif; ?>


    <div class="result-buttons">

        <a href="mock-interview.php">
            🔄 Try Again
        </a>

        <a href="dashboard.php">
            🏠 Back to Dashboard
        </a>

    </div>

</div>

</div>

</main>
<script src="js/theme.js"></script>
</body>

</html>