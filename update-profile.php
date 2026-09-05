<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

require_once "db.php";

$user_id = $_SESSION["user_id"];


/* Get submitted data */

$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$date_of_birth = $_POST["date_of_birth"] ?? "";
$bio = trim($_POST["bio"] ?? "");


/* Basic validation */

if ($name === "" || $email === "") {

    echo "<script>
            alert('Name and Email are required.');
            window.location.href='profile.php';
          </script>";

    exit();
}


/* Check whether email is already used */

$stmt = $conn->prepare(
    "SELECT id
     FROM users
     WHERE email = ? AND id != ?"
);

$stmt->bind_param(
    "si",
    $email,
    $user_id
);

$stmt->execute();

$result = $stmt->get_result();


if ($result->num_rows > 0) {

    $stmt->close();

    echo "<script>
            alert('This email is already registered with another account.');
            window.location.href='profile.php';
          </script>";

    exit();

}

$stmt->close();


/* Update profile */

$stmt = $conn->prepare(
    "UPDATE users
     SET name = ?,
         email = ?,
         date_of_birth = NULLIF(?, ''),
         bio = ?
     WHERE id = ?"
);

$stmt->bind_param(
    "ssssi",
    $name,
    $email,
    $date_of_birth,
    $bio,
    $user_id
);


if ($stmt->execute()) {

    /* Update session name */

    $_SESSION["user_name"] = $name;

    $stmt->close();

    echo "<script>
            alert('Profile updated successfully! ✅');
            window.location.href='profile.php';
          </script>";

    exit();

} else {

    $stmt->close();

    echo "<script>
            alert('Something went wrong. Please try again.');
            window.location.href='profile.php';
          </script>";

    exit();

}

?>