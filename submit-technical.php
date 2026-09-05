<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

require_once "db.php";

$user_id = $_SESSION["user_id"];

$answers = $_POST["answer"] ?? [];

$score = 0;
$total = count($answers);

foreach ($answers as $question_id => $selected_answer) {

    $stmt = $conn->prepare(
        "SELECT correct_answer FROM questions WHERE id = ?"
    );

    $stmt->bind_param("i", $question_id);

    $stmt->execute();

    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {

        if ($selected_answer === $row["correct_answer"]) {
            $score++;
        }

    }

    $stmt->close();
}

$percentage = $total > 0
    ? ($score / $total) * 100
    : 0;

$percentage = round($percentage, 2);


/* Update Technical Progress */

$check = $conn->prepare(
    "SELECT id FROM progress
     WHERE user_id = ? AND category = 'Technical'"
);

$check->bind_param("i", $user_id);

$check->execute();

$result = $check->get_result();


if ($result->num_rows > 0) {

    $update = $conn->prepare(
        "UPDATE progress
         SET attempted = attempted + ?,
             correct = correct + ?,
             score = ?
         WHERE user_id = ? AND category = 'Technical'"
    );

    $update->bind_param(
        "iidi",
        $total,
        $score,
        $percentage,
        $user_id
    );

    $update->execute();

    $update->close();

} else {

    $insert = $conn->prepare(
        "INSERT INTO progress
        (user_id, category, attempted, correct, score)
        VALUES (?, 'Technical', ?, ?, ?)"
    );

    $insert->bind_param(
        "iiid",
        $user_id,
        $total,
        $score,
        $percentage
    );

    $insert->execute();

    $insert->close();
}


/* Save Session */

$session = $conn->prepare(
    "INSERT INTO sessions
    (user_id, questions_attempted, score)
    VALUES (?, ?, ?)"
);

$session->bind_param(
    "iid",
    $user_id,
    $total,
    $percentage
);

$session->execute();

$session->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Technical Result</title>

<link rel="stylesheet"
      href="css/style.css">

</head>

<body>

<nav class="navbar">

    <div class="logo">
        Interview Prep
    </div>

    <div class="nav-links">

        <a href="dashboard.php">Dashboard</a>

        <a href="logout.php">Logout</a>

    </div>

</nav>


<main class="dashboard">

    <div class="progress-card">

        <div>

            <h2>Technical Test Completed 🎉</h2>

            <p>
                You answered <?php echo $total; ?> questions.
            </p>

        </div>

        <div class="progress-circle">

            <?php echo $percentage; ?>%

        </div>

    </div>


    <div class="category-card">

        <h2>Your Score</h2>

        <br>

        <h1>
            <?php echo $score; ?>
            /
            <?php echo $total; ?>
        </h1>

        <br>

        <a href="technical.php">
            Try Again
        </a>

        &nbsp;&nbsp;

        <a href="dashboard.php">
            Dashboard
        </a>

    </div>

</main>
<script src="js/theme.js"></script>
</body>

</html>