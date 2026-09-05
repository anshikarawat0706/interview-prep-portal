<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

require_once "db.php";

$user_id = $_SESSION["user_id"];


/* Check file */

if (!isset($_FILES["profile_image"]) ||
    $_FILES["profile_image"]["error"] !== UPLOAD_ERR_OK) {

    echo "<script>
            alert('Please select an image.');
            window.location.href='profile.php';
          </script>";

    exit();
}


$file = $_FILES["profile_image"];

$allowed_types = [
    "image/jpeg",
    "image/png",
    "image/webp"
];


if (!in_array($file["type"], $allowed_types)) {

    echo "<script>
            alert('Only JPG, PNG and WEBP images are allowed.');
            window.location.href='profile.php';
          </script>";

    exit();
}


/* Maximum 2 MB */

if ($file["size"] > 2 * 1024 * 1024) {

    echo "<script>
            alert('Image size must be less than 2 MB.');
            window.location.href='profile.php';
          </script>";

    exit();
}


/* Create uploads folder */

$upload_folder = "uploads/profile/";

if (!is_dir($upload_folder)) {

    mkdir(
        $upload_folder,
        0777,
        true
    );
}


/* File extension */

$extension = strtolower(
    pathinfo(
        $file["name"],
        PATHINFO_EXTENSION
    )
);


/* Unique filename */

$file_name =
    "profile_" .
    $user_id .
    "_" .
    time() .
    "." .
    $extension;


$file_path =
    $upload_folder .
    $file_name;


/* Move uploaded file */

if (!move_uploaded_file(
    $file["tmp_name"],
    $file_path
)) {

    echo "<script>
            alert('Failed to upload image.');
            window.location.href='profile.php';
          </script>";

    exit();
}


/* Get old image */

$stmt = $conn->prepare(
    "SELECT profile_image
     FROM users
     WHERE id = ?"
);

$stmt->bind_param(
    "i",
    $user_id
);

$stmt->execute();

$result = $stmt->get_result();

$old_image = "";

if ($row = $result->fetch_assoc()) {

    $old_image =
        $row["profile_image"] ?? "";

}

$stmt->close();


/* Save new image */

$stmt = $conn->prepare(
    "UPDATE users
     SET profile_image = ?
     WHERE id = ?"
);

$stmt->bind_param(
    "si",
    $file_path,
    $user_id
);

$stmt->execute();

$stmt->close();


/* Delete old image */

if (
    !empty($old_image) &&
    file_exists($old_image) &&
    $old_image !== $file_path
) {

    unlink($old_image);

}


echo "<script>

        alert('Profile picture updated successfully! 📸');

        window.location.href='profile.php';

      </script>";

?>