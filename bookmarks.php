<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

require_once "db.php";

$user_id = $_SESSION["user_id"];


/* Remove bookmark */

if (isset($_GET["remove"])) {

    $question_id = (int)$_GET["remove"];

    $stmt = $conn->prepare(
        "DELETE FROM bookmarks
         WHERE user_id = ? AND question_id = ?"
    );

    $stmt->bind_param(
        "ii",
        $user_id,
        $question_id
    );

    $stmt->execute();
    $stmt->close();

    header("Location: bookmarks.php");
    exit();
}


/* Get bookmarked questions */

$sql = "
    SELECT 
        q.id,
        q.question,
        q.option_a,
        q.option_b,
        q.option_c,
        q.option_d,
        q.category
    FROM bookmarks b
    INNER JOIN questions q
        ON b.question_id = q.id
    WHERE b.user_id = ?
    ORDER BY b.id DESC
";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "i",
    $user_id
);

$stmt->execute();

$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>My Bookmarks</title>

<link rel="stylesheet"
      href="css/style.css">

<style>

.bookmark-wrapper {
    max-width: 1000px;
    margin: auto;
}

.bookmark-card {
    background: white;
    padding: 25px;
    border-radius: 16px;
    margin-bottom: 20px;
    box-shadow: 0 5px 18px rgba(0,0,0,0.08);
}

.bookmark-top {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 15px;
}

.category-badge {
    display: inline-block;
    background: #e8eefc;
    color: #2563eb;
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 13px;
    margin-bottom: 10px;
}

.remove-bookmark {
    color: #dc2626;
    text-decoration: none;
    font-weight: 600;
    white-space: nowrap;
}

.remove-bookmark:hover {
    text-decoration: underline;
}

.options {
    margin-top: 18px;
    line-height: 2;
}

.empty-bookmarks {
    background: white;
    padding: 50px 25px;
    text-align: center;
    border-radius: 18px;
    box-shadow: 0 5px 18px rgba(0,0,0,0.08);
}

.empty-bookmarks .icon {
    font-size: 55px;
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


<div class="bookmark-wrapper">


<div class="welcome-section">

    <h1>
        ⭐ My Bookmarks
    </h1>

    <p>
        Your saved interview questions are here.
    </p>

</div>


<?php if ($result->num_rows === 0): ?>


<div class="empty-bookmarks">

    <div class="icon">
        ⭐
    </div>

    <h2>
        No Bookmarks Yet
    </h2>

    <p>
        Save important questions while practicing
        and they will appear here.
    </p>

    <br>

    <a href="aptitude.php">
        Start Practicing →
    </a>

</div>


<?php else: ?>


<?php while ($row = $result->fetch_assoc()): ?>


<div class="bookmark-card">


<div class="bookmark-top">

    <div>

        <span class="category-badge">

            <?php echo htmlspecialchars($row["category"]); ?>

        </span>

        <h3>

            <?php echo htmlspecialchars($row["question"]); ?>

        </h3>

    </div>


    <a
        class="remove-bookmark"
        href="bookmarks.php?remove=<?php echo $row["id"]; ?>"
        onclick="return confirm('Remove this bookmark?');"
    >
        Remove ⭐
    </a>

</div>


<div class="options">

    <div>
        A. <?php echo htmlspecialchars($row["option_a"]); ?>
    </div>

    <div>
        B. <?php echo htmlspecialchars($row["option_b"]); ?>
    </div>

    <div>
        C. <?php echo htmlspecialchars($row["option_c"]); ?>
    </div>

    <div>
        D. <?php echo htmlspecialchars($row["option_d"]); ?>
    </div>

</div>


</div>


<?php endwhile; ?>


<?php endif; ?>


</div>

</main>
<script src="js/theme.js"></script>
</body>

</html>