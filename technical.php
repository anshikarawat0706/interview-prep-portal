<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

require_once "db.php";

$sql = "SELECT * FROM questions 
        WHERE category = 'Technical' 
        ORDER BY id DESC 
        LIMIT 15";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Technical Interview</title>

    <link rel="stylesheet" href="css/style.css">

    <style>

        /* =========================================
           TECHNICAL - LIGHT MODE
           ========================================= */

        html:not(.dark-mode) .category-card {
            background: #ffffff !important;
            color: #1f2937 !important;
        }

        html:not(.dark-mode) .category-card h3 {
            color: #1f2937 !important;
        }

        html:not(.dark-mode) .category-card label {
            background: #ffffff !important;
            color: #1f2937 !important;
            border: 1px solid #e5e7eb !important;
        }

        html:not(.dark-mode) .category-card label:hover {
            background: #f3f6ff !important;
            color: #1f2937 !important;
        }

        html:not(.dark-mode) .category-card input[type="radio"] {
            accent-color: #2563eb;
        }


        /* =========================================
           TECHNICAL - DARK MODE
           ========================================= */

        html.dark-mode .category-card {
            background: #1e293b !important;
            color: #ffffff !important;
        }

        html.dark-mode .category-card h3 {
            color: #ffffff !important;
        }

        html.dark-mode .category-card label {
            background: #0f172a !important;
            color: #e2e8f0 !important;
            border: 1px solid #475569 !important;
        }

        html.dark-mode .category-card label:hover {
            background: #334155 !important;
            color: #ffffff !important;
        }

        html.dark-mode .category-card input[type="radio"] {
            accent-color: #60a5fa;
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

        <a href="logout.php">Logout</a>

    </div>

</nav>


<main class="dashboard">

    <div class="welcome-section">

        <h1>💻 Technical Interview</h1>

        <p>Test your technical knowledge for placement interviews.</p>

    </div>


    <form method="POST" action="submit-technical.php">

        <?php

        $question_number = 1;

        while ($row = $result->fetch_assoc()):

        ?>

        <div class="category-card" style="margin-bottom: 25px;">

            <div style="display:flex; justify-content:space-between; align-items:center;">

                <h3>
                    Q<?php echo $question_number; ?>.
                    <?php echo htmlspecialchars($row["question"]); ?>
                </h3>

                <a
                    href="bookmark.php?question_id=<?php echo $row['id']; ?>"
                    style="
                        text-decoration:none;
                        font-size:24px;
                    "
                    title="Bookmark this question"
                >
                    ⭐
                </a>

            </div>

            <br>

            <label>
                <input
                    type="radio"
                    name="answer[<?php echo $row["id"]; ?>]"
                    value="<?php echo htmlspecialchars($row["option_a"]); ?>"
                    required
                >

                <?php echo htmlspecialchars($row["option_a"]); ?>

            </label>

            <br><br>

            <label>
                <input
                    type="radio"
                    name="answer[<?php echo $row["id"]; ?>]"
                    value="<?php echo htmlspecialchars($row["option_b"]); ?>"
                >

                <?php echo htmlspecialchars($row["option_b"]); ?>

            </label>

            <br><br>

            <label>
                <input
                    type="radio"
                    name="answer[<?php echo $row["id"]; ?>]"
                    value="<?php echo htmlspecialchars($row["option_c"]); ?>"
                >

                <?php echo htmlspecialchars($row["option_c"]); ?>

            </label>

            <br><br>

            <label>
                <input
                    type="radio"
                    name="answer[<?php echo $row["id"]; ?>]"
                    value="<?php echo htmlspecialchars($row["option_d"]); ?>"
                >

                <?php echo htmlspecialchars($row["option_d"]); ?>

            </label>

        </div>

        <?php

        $question_number++;

        endwhile;

        ?>


        <button
            type="submit"
            style="
                background-color: #2563eb !important;
                color: #ffffff !important;
                border: 2px solid #60a5fa !important;
                padding: 14px 30px !important;
                border-radius: 10px !important;
                font-size: 16px !important;
                font-weight: 700 !important;
                cursor: pointer !important;
                opacity: 1 !important;
                visibility: visible !important;
                display: inline-block !important;
                box-shadow: 0 4px 12px rgba(0,0,0,0.3) !important;
            "
        >
            Submit Answers
        </button>

    </form>

</main>

<script src="js/theme.js"></script>

</body>

</html>