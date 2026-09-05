<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

require_once "db.php";

$user_id = $_SESSION["user_id"];
$question_id = (int)($_GET["question_id"] ?? 0);

if ($question_id > 0) {

    $check = $conn->prepare(
        "SELECT id FROM bookmarks
         WHERE user_id = ? AND question_id = ?"
    );

    $check->bind_param(
        "ii",
        $user_id,
        $question_id
    );

    $check->execute();

    $result = $check->get_result();

    if ($result->num_rows > 0) {

        $delete = $conn->prepare(
            "DELETE FROM bookmarks
             WHERE user_id = ? AND question_id = ?"
        );

        $delete->bind_param(
            "ii",
            $user_id,
            $question_id
        );

        $delete->execute();
        $delete->close();

    } else {

        $insert = $conn->prepare(
            "INSERT INTO bookmarks
             (user_id, question_id)
             VALUES (?, ?)"
        );

        $insert->bind_param(
            "ii",
            $user_id,
            $question_id
        );

        $insert->execute();
        $insert->close();
    }

    $check->close();
}

$back = $_SERVER["HTTP_REFERER"] ?? "dashboard.php";

header("Location: " . $back);
exit();

?>