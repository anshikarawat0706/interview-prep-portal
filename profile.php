<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

require_once "db.php";

$user_id = $_SESSION["user_id"];


/* ============================
   GET USER INFORMATION
   ============================ */

$stmt = $conn->prepare(
    "SELECT * FROM users WHERE id = ?"
);

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->close();


if (!$user) {

    session_destroy();

    header("Location: login.php");
    exit();
}


$user_name = $user["name"] ?? "";
$user_email = $user["email"] ?? "";
$user_dob = $user["date_of_birth"] ?? "";
$user_bio = $user["bio"] ?? "";
$profile_image = $user["profile_image"] ?? "";

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>Profile - Interview Prep</title>

<link rel="stylesheet"
      href="css/style.css">


<style>

/* =====================================
   PROFILE PAGE
   ===================================== */

.profile-container {

    max-width: 950px;

    margin: 30px auto;

}


/* =====================================
   PROFILE HEADER
   ===================================== */

.profile-header {

    background: #ffffff;

    padding: 35px;

    border-radius: 20px;

    text-align: center;

    box-shadow:
        0 6px 20px rgba(0,0,0,0.08);

    transition: 0.3s;

}


.profile-header h1 {

    margin: 15px 0 5px;

}


.profile-email {

    color: #666;

    margin: 0;

}


/* =====================================
   PROFILE PHOTO
   ===================================== */

.profile-photo-wrapper {

    position: relative;

    width: 120px;

    height: 120px;

    margin: auto;

}


.profile-image,
.profile-avatar {

    width: 110px;

    height: 110px;

    border-radius: 50%;

}


.profile-image {

    object-fit: cover;

    border: 4px solid #2563eb;

}


.profile-avatar {

    background: #e8f0ff;

    border: 4px solid #2563eb;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 50px;

}


/* =====================================
   CAMERA BUTTON
   ===================================== */

.camera-btn {

    position: absolute;

    right: 0;

    bottom: 5px;

    width: 38px;

    height: 38px;

    border-radius: 50%;

    border: 3px solid white;

    background: #2563eb;

    color: white;

    cursor: pointer;

    font-size: 17px;

    display: flex;

    align-items: center;

    justify-content: center;

}


/* =====================================
   PHOTO MENU
   ===================================== */

.photo-menu {

    display: none;

    position: absolute;

    top: 125px;

    left: 50%;

    transform: translateX(-50%);

    width: 180px;

    background: white;

    padding: 8px;

    border-radius: 12px;

    box-shadow:
        0 8px 25px rgba(0,0,0,0.18);

    z-index: 100;

}


.photo-menu button,
.photo-menu a {

    width: 100%;

    display: block;

    box-sizing: border-box;

    padding: 11px;

    border: none;

    background: transparent;

    text-align: left;

    text-decoration: none;

    color: #333;

    border-radius: 8px;

    cursor: pointer;

    font-size: 14px;

}


.photo-menu button:hover,
.photo-menu a:hover {

    background: #f1f5ff;

}


/* =====================================
   PROFILE SECTION
   ===================================== */

.profile-section {

    background: #ffffff;

    margin-top: 25px;

    padding: 30px;

    border-radius: 20px;

    box-shadow:
        0 6px 20px rgba(0,0,0,0.08);

}


/* =====================================
   PERSONAL INFORMATION
   ===================================== */

.profile-form {

    display: grid;

    grid-template-columns:
        repeat(2, 1fr);

    gap: 20px;

}


.form-group {

    display: flex;

    flex-direction: column;

}


.form-group.full {

    grid-column: 1 / -1;

}


.form-group label {

    font-weight: 600;

    margin-bottom: 7px;

}


.form-group input,
.form-group textarea {

    padding: 12px;

    border: 1px solid #ddd;

    border-radius: 10px;

    font-size: 15px;

    box-sizing: border-box;

}


.form-group textarea {

    min-height: 110px;

    resize: vertical;

}


/* =====================================
   SAVE BUTTON
   ===================================== */

.save-btn {

    width: fit-content;

    padding: 12px 25px;

    border: none;

    border-radius: 10px;

    background: #2563eb;

    color: white;

    cursor: pointer;

    font-size: 15px;

}


/* =====================================
   SETTINGS
   ===================================== */

.setting-row {

    display: flex;

    justify-content: space-between;

    align-items: center;

    padding: 20px 0;

    border-bottom: 1px solid #e5e7eb;

}


.setting-row:last-child {

    border-bottom: none;

}


.setting-info h3 {

    margin: 0 0 5px;

}


.setting-info p {

    margin: 0;

    color: #777;

    font-size: 14px;

}


/* =====================================
   NOTIFICATION SWITCH
   ===================================== */

.toggle {

    position: relative;

    width: 50px;

    height: 26px;

}


.toggle input {

    display: none;

}


.slider {

    position: absolute;

    inset: 0;

    background: #ccc;

    border-radius: 30px;

    cursor: pointer;

}


.slider:before {

    content: "";

    position: absolute;

    width: 20px;

    height: 20px;

    left: 3px;

    top: 3px;

    background: white;

    border-radius: 50%;

    transition: 0.2s;

}


.toggle input:checked + .slider {

    background: #2563eb;

}


.toggle input:checked + .slider:before {

    transform: translateX(24px);

}


/* =====================================
   THEME OPTIONS
   ===================================== */

.theme-options {

    display: flex;

    gap: 10px;

    flex-wrap: wrap;

}


.theme-option {

    padding: 10px 14px;

    border: 1px solid #ddd;

    border-radius: 10px;

    cursor: pointer;

}


/* =====================================
   HELP & SUPPORT
   ===================================== */

.help-card {

    border-radius: 14px;

    background: #f8f9ff;

    margin-bottom: 12px;

    overflow: hidden;

    border: 1px solid #e5e7eb;

    transition: 0.25s;

}


.help-card:last-child {

    margin-bottom: 0;

}


/* Clickable heading */

.help-header {

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 15px;

    padding: 18px;

    cursor: pointer;

    user-select: none;

}


.help-header-left {

    display: flex;

    align-items: center;

    gap: 15px;

}


.help-icon {

    width: 42px;

    height: 42px;

    border-radius: 10px;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 25px;

    background: #e8efff;

    flex-shrink: 0;

}


.help-title {

    margin: 0;

    font-size: 17px;

}


.help-arrow {

    font-size: 18px;

    transition: transform 0.25s;

    flex-shrink: 0;

}


/* Expanded state */

.help-card.active .help-arrow {

    transform: rotate(180deg);

}


/* Hidden content */

.help-content {

    display: none;

    padding: 0 20px 20px 75px;

}


.help-card.active .help-content {

    display: block;

}


.help-content p {

    margin: 0 0 12px;

    line-height: 1.6;

}


.help-content ul {

    margin: 8px 0 0;

    padding-left: 20px;

}


.help-content li {

    margin-bottom: 8px;

    line-height: 1.5;

}


/* =====================================
   ACCOUNT
   ===================================== */

.account-actions {

    display: flex;

    gap: 15px;

    flex-wrap: wrap;

}


.account-btn {

    padding: 12px 20px;

    border-radius: 10px;

    background: #2563eb;

    color: white !important;

    text-decoration: none;

}


.logout-btn {

    background: #dc2626;

}


/* =====================================
   DARK MODE
   ===================================== */

html.dark-mode,
body.dark-mode {

    background: #111827 !important;

    color: #f3f4f6 !important;

}


html.dark-mode body {

    background: #111827 !important;

}


/* Navbar */

html.dark-mode .navbar,
body.dark-mode .navbar {

    background: #1f2937 !important;

}


html.dark-mode .logo,
body.dark-mode .logo {

    color: white !important;

}


html.dark-mode .nav-links a,
body.dark-mode .nav-links a {

    color: white !important;

}


/* Profile cards */

html.dark-mode .profile-header,
html.dark-mode .profile-section,
body.dark-mode .profile-header,
body.dark-mode .profile-section {

    background: #1f2937 !important;

    color: #f3f4f6 !important;

}


html.dark-mode .profile-header h1,
html.dark-mode .profile-section h2,
html.dark-mode .profile-section h3,
body.dark-mode .profile-header h1,
body.dark-mode .profile-section h2,
body.dark-mode .profile-section h3 {

    color: white !important;

}


/* Paragraph */

html.dark-mode p,
body.dark-mode p {

    color: #cbd5e1;

}


html.dark-mode .profile-email,
body.dark-mode .profile-email {

    color: #cbd5e1 !important;

}


/* Inputs */

html.dark-mode input,
html.dark-mode textarea,
html.dark-mode select,
body.dark-mode input,
body.dark-mode textarea,
body.dark-mode select {

    background: #111827 !important;

    color: white !important;

    border-color: #374151 !important;

}


/* Labels */

html.dark-mode label,
body.dark-mode label {

    color: #e5e7eb !important;

}


/* Settings */

html.dark-mode .setting-row,
body.dark-mode .setting-row {

    border-color: #374151 !important;

}


html.dark-mode .setting-info p,
body.dark-mode .setting-info p {

    color: #9ca3af !important;

}


/* Theme buttons */

html.dark-mode .theme-option,
body.dark-mode .theme-option {

    color: white !important;

    border-color: #4b5563 !important;

}


/* =====================================
   DARK MODE - HELP
   ===================================== */

html.dark-mode .help-card,
body.dark-mode .help-card {

    background: #111827 !important;

    border-color: #374151 !important;

}


html.dark-mode .help-icon,
body.dark-mode .help-icon {

    background: #1f2937 !important;

}


html.dark-mode .help-title,
body.dark-mode .help-title {

    color: #ffffff !important;

}


html.dark-mode .help-content p,
html.dark-mode .help-content li,
body.dark-mode .help-content p,
body.dark-mode .help-content li {

    color: #cbd5e1 !important;

}


html.dark-mode .help-arrow,
body.dark-mode .help-arrow {

    color: #ffffff !important;

}


/* Photo menu */

html.dark-mode .photo-menu,
body.dark-mode .photo-menu {

    background: #1f2937 !important;

}


html.dark-mode .photo-menu button,
html.dark-mode .photo-menu a,
body.dark-mode .photo-menu button,
body.dark-mode .photo-menu a {

    color: white !important;

}


/* =====================================
   MOBILE
   ===================================== */

@media (max-width: 700px) {

    .profile-form {

        grid-template-columns: 1fr;

    }


    .form-group.full {

        grid-column: auto;

    }


    .setting-row {

        flex-direction: column;

        align-items: flex-start;

        gap: 15px;

    }


    .help-content {

        padding: 0 18px 18px 18px;

    }


    .help-header {

        padding: 15px;

    }

}

</style>

</head>


<body>


<!-- =====================================
     NAVBAR
     ===================================== -->

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


<!-- =====================================
     MAIN
     ===================================== -->

<main class="dashboard">

<div class="profile-container">


<!-- =====================================
     PROFILE HEADER
     ===================================== -->

<div class="profile-header">

    <div class="profile-photo-wrapper">

        <?php if (!empty($profile_image)): ?>

            <img
                src="<?php echo htmlspecialchars($profile_image); ?>"
                class="profile-image"
                alt="Profile Photo">

        <?php else: ?>

            <div class="profile-avatar">
                👤
            </div>

        <?php endif; ?>


        <button
            type="button"
            class="camera-btn"
            onclick="togglePhotoMenu()">

            📷

        </button>


        <div
            id="photoMenu"
            class="photo-menu">

            <button
                type="button"
                onclick="choosePhoto()">

                📷 Change Photo

            </button>


            <?php if (!empty($profile_image)): ?>

                <a
                    href="remove-profile-picture.php"
                    onclick="return confirm('Remove your profile picture?');">

                    🗑️ Remove Photo

                </a>

            <?php endif; ?>

        </div>


        <form
            id="photoForm"
            action="upload-profile-picture.php"
            method="POST"
            enctype="multipart/form-data">

            <input
                type="file"
                id="photoInput"
                name="profile_image"
                accept="image/jpeg,image/png,image/webp"
                style="display:none;">

        </form>

    </div>


    <h1>
        <?php echo htmlspecialchars($user_name); ?>
    </h1>


    <p class="profile-email">
        <?php echo htmlspecialchars($user_email); ?>
    </p>

</div>


<!-- =====================================
     PERSONAL INFORMATION
     ===================================== -->

<div class="profile-section">

    <h2>
        ✏️ Personal Information
    </h2>


    <form
        class="profile-form"
        method="POST"
        action="update-profile.php">


        <div class="form-group">

            <label>
                Full Name
            </label>

            <input
                type="text"
                name="name"
                value="<?php echo htmlspecialchars($user_name); ?>"
                required>

        </div>


        <div class="form-group">

            <label>
                Email
            </label>

            <input
                type="email"
                name="email"
                value="<?php echo htmlspecialchars($user_email); ?>"
                required>

        </div>


        <div class="form-group">

            <label>
                Date of Birth
            </label>

            <input
                type="date"
                name="date_of_birth"
                value="<?php echo htmlspecialchars($user_dob); ?>">

        </div>


        <div class="form-group">

            <label>
                Account Type
            </label>

            <input
                type="text"
                value="Student"
                disabled>

        </div>


        <div class="form-group full">

            <label>
                About Me
            </label>

            <textarea
                name="bio"
                placeholder="Tell something about yourself..."><?php
                echo htmlspecialchars($user_bio);
                ?></textarea>

        </div>


        <div class="form-group full">

            <button
                type="submit"
                class="save-btn">

                💾 Save Personal Information

            </button>

        </div>

    </form>

</div>


<!-- =====================================
     SETTINGS
     ===================================== -->

<div class="profile-section">

    <h2>
        ⚙️ Settings
    </h2>


    <!-- Notifications -->

    <div class="setting-row">

        <div class="setting-info">

            <h3>
                🔔 Notifications
            </h3>

            <p>
                Receive reminders and interview practice notifications.
            </p>

        </div>


        <label class="toggle">

            <input
                type="checkbox"
                id="notifications">

            <span class="slider"></span>

        </label>

    </div>


    <!-- Appearance -->

    <div class="setting-row">

        <div class="setting-info">

            <h3>
                🎨 Appearance
            </h3>

            <p>
                Choose your preferred theme.
            </p>

        </div>


        <div class="theme-options">

            <label class="theme-option">

                <input
                    type="radio"
                    name="theme"
                    value="light">

                ☀️ Light

            </label>


            <label class="theme-option">

                <input
                    type="radio"
                    name="theme"
                    value="dark">

                🌙 Dark

            </label>


            <label class="theme-option">

                <input
                    type="radio"
                    name="theme"
                    value="system">

                🖥️ Default

            </label>

        </div>

    </div>

</div>


<!-- =====================================
     HELP & SUPPORT
     ===================================== -->

<div class="profile-section">

    <h2>
        ❓ Help & Support
    </h2>


    <!-- =================================
         HOW TO USE
         ================================= -->

    <div class="help-card">

        <div
            class="help-header"
            onclick="toggleHelp(this)">

            <div class="help-header-left">

                <div class="help-icon">
                    📖
                </div>

                <h3 class="help-title">
                    How to use Interview Prep?
                </h3>

            </div>


            <span class="help-arrow">
                ▼
            </span>

        </div>


        <div class="help-content">

            <p>
                Interview Prep Portal helps you prepare for different
                stages of an interview from one place.
            </p>

            <ul>

                <li>
                    Go to <strong>Dashboard</strong> to access all practice sections.
                </li>

                <li>
                    In <strong>Aptitude</strong>, choose a topic and solve the available questions.
                </li>

                <li>
                    Use <strong>Technical</strong> to practice technical interview questions.
                </li>

                <li>
                    Use <strong>HR</strong> to practice common HR interview questions.
                </li>

                <li>
                    Use <strong>Mock Interview</strong> to attempt a complete interview practice session.
                </li>

                <li>
                    Use <strong>Bookmarks</strong> to revisit questions you have saved.
                </li>

                <li>
                    Check <strong>Progress</strong> to review your practice performance.
                </li>

            </ul>

        </div>

    </div>


    <!-- =================================
         NEED HELP
         ================================= -->

    <div class="help-card">

        <div
            class="help-header"
            onclick="toggleHelp(this)">

            <div class="help-header-left">

                <div class="help-icon">
                    💬
                </div>

                <h3 class="help-title">
                    Need Help?
                </h3>

            </div>


            <span class="help-arrow">
                ▼
            </span>

        </div>


        <div class="help-content">

            <p>
                If something is not working correctly, check the following:
            </p>

            <ul>

                <li>
                    <strong>Questions not loading:</strong>
                    make sure the database connection is working.
                </li>

                <li>
                    <strong>Answer not submitting:</strong>
                    make sure you have selected an answer for every required question.
                </li>

                <li>
                    <strong>Bookmark issue:</strong>
                    check that you are logged into your account.
                </li>

                <li>
                    <strong>Profile issue:</strong>
                    use Personal Information to update your details.
                </li>

                <li>
                    <strong>Profile photo issue:</strong>
                    use the camera button beside your profile picture to change or remove it.
                </li>

            </ul>

        </div>

    </div>


    <!-- =================================
         ACCOUNT & SECURITY
         ================================= -->

    <div class="help-card">

        <div
            class="help-header"
            onclick="toggleHelp(this)">

            <div class="help-header-left">

                <div class="help-icon">
                    🔒
                </div>

                <h3 class="help-title">
                    Account & Security
                </h3>

            </div>


            <span class="help-arrow">
                ▼
            </span>

        </div>


        <div class="help-content">

            <p>
                Keep your Interview Prep account secure by following these practices:
            </p>

            <ul>

                <li>
                    Keep your account information updated.
                </li>

                <li>
                    Do not share your account password with anyone.
                </li>

                <li>
                    Use <strong>Change Password</strong> when you want to update your password.
                </li>

                <li>
                    Always use <strong>Logout</strong> after finishing your session on a shared computer.
                </li>

                <li>
                    Keep your profile information accurate so your account remains up to date.
                </li>

            </ul>

        </div>

    </div>

</div>


<!-- =====================================
     ACCOUNT
     ===================================== -->

<div class="profile-section">

    <h2>
        🔐 Account
    </h2>


    <div class="account-actions">

        <a
            href="change-password.php"
            class="account-btn">

            🔑 Change Password

        </a>


        <a
            href="logout.php"
            class="account-btn logout-btn">

            🚪 Logout

        </a>

    </div>

</div>


</div>

</main>


<!-- =====================================
     THEME JS
     ===================================== -->

<script src="js/theme.js"></script>


<script>

/* =====================================
   PHOTO MENU
   ===================================== */

function togglePhotoMenu() {

    const menu =
        document.getElementById("photoMenu");

    if (menu.style.display === "block") {

        menu.style.display = "none";

    } else {

        menu.style.display = "block";

    }

}


function choosePhoto() {

    document
        .getElementById("photoInput")
        .click();

}


document
    .getElementById("photoInput")
    .addEventListener(
        "change",
        function () {

            if (this.files.length > 0) {

                document
                    .getElementById("photoForm")
                    .submit();

            }

        }
    );


document.addEventListener(
    "click",
    function (event) {

        const wrapper =
            document.querySelector(
                ".profile-photo-wrapper"
            );

        const menu =
            document.getElementById(
                "photoMenu"
            );


        if (
            wrapper &&
            !wrapper.contains(event.target)
        ) {

            menu.style.display = "none";

        }

    }
);


/* =====================================
   NOTIFICATIONS
   ===================================== */

const notificationToggle =
    document.getElementById(
        "notifications"
    );


const NOTIFICATION_KEY =
    "interviewPrepNotifications";


let savedNotification =
    localStorage.getItem(
        NOTIFICATION_KEY
    );


/* First time = ON */

if (savedNotification === null) {

    savedNotification = "on";

    localStorage.setItem(
        NOTIFICATION_KEY,
        "on"
    );

}


notificationToggle.checked =
    savedNotification === "on";


notificationToggle.addEventListener(
    "change",
    function () {

        localStorage.setItem(
            NOTIFICATION_KEY,
            this.checked
                ? "on"
                : "off"
        );

    }
);


/* =====================================
   HELP ACCORDION
   ===================================== */

function toggleHelp(header) {

    const card =
        header.closest(".help-card");


    const allCards =
        document.querySelectorAll(".help-card");


    /*
       Close other help cards
       so only one opens at a time.
    */

    allCards.forEach(function(otherCard) {

        if (otherCard !== card) {

            otherCard.classList.remove("active");

        }

    });


    /*
       Toggle clicked card
    */

    card.classList.toggle("active");

}

</script>


</body>

</html>