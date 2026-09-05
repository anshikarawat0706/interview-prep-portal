<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

require_once "db.php";


/*
|--------------------------------------------------------------------------
| Selected Topic
|--------------------------------------------------------------------------
*/

$selected_topic = isset($_GET["topic"])
    ? trim($_GET["topic"])
    : "";


/*
|--------------------------------------------------------------------------
| Get Topic Counts
|--------------------------------------------------------------------------
|
| COUNT(DISTINCT question) is used because the database contains
| duplicate records of some questions.
|
*/

$topic_counts = [];

$count_sql = "
    SELECT
        CASE
            WHEN topic IS NULL OR TRIM(topic) = ''
            THEN 'General Aptitude'
            ELSE TRIM(topic)
        END AS display_topic,
        COUNT(DISTINCT question) AS total
    FROM questions
    WHERE category = 'Aptitude'
    GROUP BY
        CASE
            WHEN topic IS NULL OR TRIM(topic) = ''
            THEN 'General Aptitude'
            ELSE TRIM(topic)
        END
    ORDER BY display_topic
";

$count_result = $conn->query($count_sql);

if ($count_result) {

    while ($count_row = $count_result->fetch_assoc()) {

        $topic_counts[$count_row["display_topic"]] =
            (int) $count_row["total"];
    }
}


/*
|--------------------------------------------------------------------------
| Topic Icons
|--------------------------------------------------------------------------
*/

$topic_icons = [

    "Average" => "🧮",

    "Mixture & Alligation" => "🧪",

    "Percentage" => "📊",

    "Profit & Loss" => "💰",

    "General Aptitude" => "🧠"

];


/*
|--------------------------------------------------------------------------
| Topic Descriptions
|--------------------------------------------------------------------------
*/

$topic_descriptions = [

    "Average" =>
        "Practice questions based on averages, mean, marks, ages and related problems.",

    "Mixture & Alligation" =>
        "Practice mixture, ratio, replacement and alligation problems.",

    "Percentage" =>
        "Practice percentage, increase, decrease and related problems.",

    "Profit & Loss" =>
        "Practice cost price, selling price, profit, loss and discount problems.",

    "General Aptitude" =>
        "Practice other aptitude questions that are not assigned to a specific topic."

];


/*
|--------------------------------------------------------------------------
| Available Topics
|--------------------------------------------------------------------------
*/

$topics = [

    "Average",

    "Mixture & Alligation",

    "Percentage",

    "Profit & Loss",

    "General Aptitude"

];


/*
|--------------------------------------------------------------------------
| PRACTICE MODE
|--------------------------------------------------------------------------
*/

$questions = [];

if ($selected_topic !== "") {

    /*
    |--------------------------------------------------------------------------
    | Validate topic
    |--------------------------------------------------------------------------
    */

    if (!in_array($selected_topic, $topics, true)) {

        $selected_topic = "";
    }

}


/*
|--------------------------------------------------------------------------
| Load Questions For Selected Topic
|--------------------------------------------------------------------------
|
| IMPORTANT:
| No LIMIT 15 here.
|
| The category card shows the actual number of unique questions,
| so the practice page must load ALL unique questions for that topic.
|
| MIN(id) keeps only one database row for duplicate questions.
|
*/

if ($selected_topic !== "") {

    if ($selected_topic === "General Aptitude") {

        $sql = "
            SELECT q.*
            FROM questions q

            INNER JOIN (

                SELECT MIN(id) AS question_id

                FROM questions

                WHERE category = 'Aptitude'

                AND (
                    topic IS NULL
                    OR TRIM(topic) = ''
                    OR TRIM(topic) = 'General Aptitude'
                )

                GROUP BY question

            ) unique_questions

            ON q.id = unique_questions.question_id

            ORDER BY q.id ASC
        ";

        $result = $conn->query($sql);

    } else {

        $stmt = $conn->prepare("
            SELECT q.*
            FROM questions q

            INNER JOIN (

                SELECT MIN(id) AS question_id

                FROM questions

                WHERE category = 'Aptitude'

                AND TRIM(topic) = ?

                GROUP BY question

            ) unique_questions

            ON q.id = unique_questions.question_id

            ORDER BY q.id ASC
        ");

        $stmt->bind_param("s", $selected_topic);

        $stmt->execute();

        $result = $stmt->get_result();
    }


    if ($result) {

        while ($row = $result->fetch_assoc()) {

            $questions[] = $row;
        }
    }

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        <?php
        echo $selected_topic !== ""
            ? htmlspecialchars($selected_topic) . " - Aptitude Practice"
            : "Aptitude Practice";
        ?>
    </title>

    <link
        rel="stylesheet"
        href="css/style.css"
    >

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

        <a href="logout.php">
            Logout
        </a>

    </div>

</nav>



<main class="dashboard">


<?php if ($selected_topic === ""): ?>


    <!-- =========================================================
         TOPIC SELECTION
         ========================================================= -->


    <div class="welcome-section">

        <h1>
            🧠 Aptitude Practice
        </h1>

        <p>
            Choose a topic to start practicing.
        </p>

    </div>



    <div
        class="category-grid"
        style="
            display:grid;
            grid-template-columns:
                repeat(auto-fit, minmax(300px, 1fr));
            gap:20px;
        "
    >


        <?php foreach ($topics as $topic): ?>


            <?php

            $count = $topic_counts[$topic] ?? 0;

            $icon = $topic_icons[$topic] ?? "🧠";

            $description =
                $topic_descriptions[$topic]
                ?? "Practice aptitude questions.";

            ?>


            <a
                href="aptitude.php?topic=<?php echo urlencode($topic); ?>"
                style="
                    text-decoration:none;
                    color:inherit;
                "
            >

                <div
                    class="category-card"
                    style="
                        min-height:150px;
                        cursor:pointer;
                    "
                >

                    <div
                        style="
                            font-size:32px;
                            margin-bottom:10px;
                        "
                    >
                        <?php echo $icon; ?>
                    </div>


                    <h2
                        style="
                            margin:0 0 10px 0;
                        "
                    >
                        <?php
                        echo htmlspecialchars($topic);
                        ?>
                    </h2>


                    <p>
                        <?php
                        echo htmlspecialchars($description);
                        ?>
                    </p>


                    <div
                        style="
                            margin-top:15px;
                            display:inline-block;
                            padding:6px 10px;
                            border-radius:6px;
                            font-size:13px;
                            font-weight:700;
                        "
                    >

                        <?php echo $count; ?> Questions

                    </div>

                </div>

            </a>


        <?php endforeach; ?>


    </div>


<?php else: ?>


    <!-- =========================================================
         PRACTICE MODE
         ========================================================= -->


    <div class="welcome-section">


        <div>

    <a href="aptitude.php"
       style="
           display:inline-block;
           margin-bottom:18px;
           padding:10px 16px;
           background:rgba(255,255,255,0.10);
           border:1px solid rgba(255,255,255,0.25);
           border-radius:10px;
           color:#ffffff;
           text-decoration:none;
           font-size:16px;
           font-weight:700;
           letter-spacing:0.2px;
       ">
        ← Back to Topics
    </a>

    <h1 style="margin:0;">
        
        <?php
        echo $topic_icons[$selected_topic] ?? "🧠";
        ?>

        <?php
        echo htmlspecialchars($selected_topic);
        ?>

        Practice

    </h1>

</div>


        <p>

            <?php echo count($questions); ?>

            questions available in this topic.

        </p>

    </div>



    <?php if (count($questions) > 0): ?>


        <form
            method="POST"
            action="submit-aptitude.php"
        >


            <?php

            $question_number = 1;

            foreach ($questions as $row):

            ?>


                <div
                    class="category-card"
                    style="
                        margin-bottom:25px;
                    "
                >


                    <div
                        style="
                            display:flex;
                            justify-content:space-between;
                            align-items:flex-start;
                            gap:15px;
                        "
                    >


                        <h3>

                            Q<?php
                            echo $question_number;
                            ?>.

                            <?php
                            echo htmlspecialchars(
                                $row["question"]
                            );
                            ?>

                        </h3>


                        <a
                            href="bookmark.php?question_id=<?php
                                echo $row["id"];
                            ?>"
                            style="
                                text-decoration:none;
                                font-size:24px;
                                flex-shrink:0;
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
                            name="answer[<?php
                                echo $row["id"];
                            ?>]"
                            value="<?php
                                echo htmlspecialchars(
                                    $row["option_a"]
                                );
                            ?>"
                            required
                        >

                        <?php
                        echo htmlspecialchars(
                            $row["option_a"]
                        );
                        ?>

                    </label>


                    <br><br>


                    <label>

                        <input
                            type="radio"
                            name="answer[<?php
                                echo $row["id"];
                            ?>]"
                            value="<?php
                                echo htmlspecialchars(
                                    $row["option_b"]
                                );
                            ?>"
                        >

                        <?php
                        echo htmlspecialchars(
                            $row["option_b"]
                        );
                        ?>

                    </label>


                    <br><br>


                    <label>

                        <input
                            type="radio"
                            name="answer[<?php
                                echo $row["id"];
                            ?>]"
                            value="<?php
                                echo htmlspecialchars(
                                    $row["option_c"]
                                );
                            ?>"
                        >

                        <?php
                        echo htmlspecialchars(
                            $row["option_c"]
                        );
                        ?>

                    </label>


                    <br><br>


                    <label>

                        <input
                            type="radio"
                            name="answer[<?php
                                echo $row["id"];
                            ?>]"
                            value="<?php
                                echo htmlspecialchars(
                                    $row["option_d"]
                                );
                            ?>"
                        >

                        <?php
                        echo htmlspecialchars(
                            $row["option_d"]
                        );
                        ?>

                    </label>


                </div>


            <?php

                $question_number++;

            endforeach;

            ?>



            <button
                type="submit"
                style="
                    background-color:#2563eb !important;
                    color:#ffffff !important;
                    border:2px solid #60a5fa !important;
                    padding:14px 30px !important;
                    border-radius:10px !important;
                    font-size:16px !important;
                    font-weight:700 !important;
                    cursor:pointer !important;
                    opacity:1 !important;
                    visibility:visible !important;
                    display:inline-block !important;
                    box-shadow:
                        0 4px 12px
                        rgba(0,0,0,0.3) !important;
                "
            >
                Submit Answers
            </button>


        </form>


    <?php else: ?>


        <div class="category-card">

            <h2>
                No questions found.
            </h2>

            <p>
                There are currently no questions in this topic.
            </p>


            <a
                href="aptitude.php"
                style="
                    text-decoration:none;
                    font-weight:700;
                "
            >
                ← Back to Topics
            </a>

        </div>


    <?php endif; ?>


<?php endif; ?>


</main>


<script src="js/theme.js"></script>


</body>

</html>