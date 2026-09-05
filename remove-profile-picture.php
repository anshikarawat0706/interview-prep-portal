<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

require_once "db.php";

$user_id = $_SESSION["user_id"];


/* Get current profile image */

$stmt = $conn->prepare(
    "SELECT profile_image
     FROM users
     WHERE id = ?"
);

$stmt->bind_param("i", $user_id);

$stmt->execute();

$result = $stmt->get_result();

$profile_image = "";

if ($row = $result->fetch_assoc()) {
    $profile_image = $row["profile_image"] ?? "";
}

$stmt->close();


/* Remove image from database */

$stmt = $conn->prepare(
    "UPDATE users
     SET profile_image = NULL
     WHERE id = ?"
);

$stmt->bind_param("i", $user_id);

$stmt->execute();

$stmt->close();


/* Delete image file */

if (
    !empty($profile_image) &&
    file_exists($profile_image)
) {

    unlink($profile_image);

}


/* Go back to profile */

echo "<script>

    alert('Profile picture removed successfully! 🗑️');

    window.location.href = 'profile.php';

</script>";

?>