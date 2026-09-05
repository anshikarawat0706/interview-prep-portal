<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

require_once "db.php";

$user_id = $_SESSION["user_id"];
$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $current_password = $_POST["current_password"] ?? "";
    $new_password = $_POST["new_password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $message = "Please fill in all password fields.";
        $message_type = "error";

    } elseif ($new_password !== $confirm_password) {
        $message = "New password and confirm password do not match.";
        $message_type = "error";

    } elseif (strlen($new_password) < 6) {
        $message = "New password must be at least 6 characters long.";
        $message_type = "error";

    } else {

        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if (!$user || !password_verify($current_password, $user["password"])) {

            $message = "Current password is incorrect.";
            $message_type = "error";

        } elseif (password_verify($new_password, $user["password"])) {

            $message = "New password must be different from your current password.";
            $message_type = "error";

        } else {

            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            $update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $update->bind_param("si", $hashed_password, $user_id);

            if ($update->execute()) {
                $message = "Password changed successfully!";
                $message_type = "success";
            } else {
                $message = "Something went wrong. Please try again.";
                $message_type = "error";
            }

            $update->close();
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Change Password - Interview Prep Portal</title>

    <link rel="stylesheet" href="css/style.css">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f7fb;
            color: #222;
            transition: background 0.3s, color 0.3s;
        }

        .password-page {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 30px 15px;
        }

        .password-card {
            width: 100%;
            max-width: 500px;
            background: white;
            padding: 35px;
            border-radius: 16px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.10);
            transition: background 0.3s, color 0.3s;
        }

        .password-card h1 {
            margin: 0 0 8px;
            font-size: 26px;
        }

        .password-card .subtitle {
            margin: 0 0 28px;
            color: #777;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 14px;
        }

        .password-wrapper {
            position: relative;
            width: 100%;
        }

        .password-wrapper input {
            width: 100%;
            padding: 13px 48px 13px 14px;
            border: 1px solid #d5d9e2;
            border-radius: 9px;
            font-size: 15px;
            outline: none;
            background: white;
            color: #222;
            transition: border 0.2s, background 0.3s, color 0.3s;
        }

        .password-wrapper input:focus {
            border-color: #4f46e5;
        }

        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            cursor: pointer;
            font-size: 19px;
            padding: 5px;
            color: #666;
            line-height: 1;
        }

        .toggle-password:hover {
            color: #222;
        }

        .message {
            padding: 12px 14px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .message.success {
            background: #e8f7ee;
            color: #187a42;
            border: 1px solid #b9e6ca;
        }

        .message.error {
            background: #fff0f0;
            color: #c62828;
            border: 1px solid #f0bcbc;
        }

        .button-row {
            display: flex;
            gap: 12px;
            margin-top: 25px;
        }

        .btn {
            flex: 1;
            padding: 13px 16px;
            border-radius: 9px;
            text-decoration: none;
            text-align: center;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            border: none;
        }

        .btn-primary {
            background: #4f46e5;
            color: white;
        }

        .btn-primary:hover {
            background: #4338ca;
        }

        .btn-secondary {
            background: #e9ebf1;
            color: #333;
        }

        .btn-secondary:hover {
            background: #dfe2e9;
        }

        /* DARK MODE */
        html.dark-mode body,
        body.dark-mode {
            background: #111827;
            color: #f3f4f6;
        }

        html.dark-mode .password-card,
        body.dark-mode .password-card {
            background: #1f2937;
            color: #f3f4f6;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.35);
        }

        html.dark-mode .password-card .subtitle,
        body.dark-mode .password-card .subtitle {
            color: #aeb6c4;
        }

        html.dark-mode .password-wrapper input,
        body.dark-mode .password-wrapper input {
            background: #111827;
            color: #f3f4f6;
            border-color: #374151;
        }

        html.dark-mode .password-wrapper input:focus,
        body.dark-mode .password-wrapper input:focus {
            border-color: #818cf8;
        }

        html.dark-mode .toggle-password,
        body.dark-mode .toggle-password {
            color: #aeb6c4;
        }

        html.dark-mode .toggle-password:hover,
        body.dark-mode .toggle-password:hover {
            color: white;
        }

        html.dark-mode .btn-secondary,
        body.dark-mode .btn-secondary {
            background: #374151;
            color: #f3f4f6;
        }

        html.dark-mode .btn-secondary:hover,
        body.dark-mode .btn-secondary:hover {
            background: #4b5563;
        }

        html.dark-mode .message.success,
        body.dark-mode .message.success {
            background: #123522;
            color: #7ee2a8;
            border-color: #245c3b;
        }

        html.dark-mode .message.error,
        body.dark-mode .message.error {
            background: #3a1717;
            color: #ff9b9b;
            border-color: #673030;
        }

        @media (max-width: 500px) {
            .password-card {
                padding: 25px 20px;
            }

            .button-row {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>

<div class="password-page">

    <div class="password-card">

        <h1>🔑 Change Password</h1>

        <p class="subtitle">
            Update your account password securely.
        </p>

        <?php if (!empty($message)): ?>
            <div class="message <?php echo htmlspecialchars($message_type); ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">

            <!-- Current Password -->
            <div class="form-group">
                <label for="current_password">Current Password</label>

                <div class="password-wrapper">
                    <input
                        type="password"
                        id="current_password"
                        name="current_password"
                        placeholder="Enter current password"
                        autocomplete="current-password"
                        required
                    >

                    <button
                        type="button"
                        class="toggle-password"
                        onclick="togglePassword('current_password', this)"
                        aria-label="Show current password"
                    >👁️</button>
                </div>
            </div>

            <!-- New Password -->
            <div class="form-group">
                <label for="new_password">New Password</label>

                <div class="password-wrapper">
                    <input
                        type="password"
                        id="new_password"
                        name="new_password"
                        placeholder="Enter new password"
                        autocomplete="new-password"
                        required
                    >

                    <button
                        type="button"
                        class="toggle-password"
                        onclick="togglePassword('new_password', this)"
                        aria-label="Show new password"
                    >👁️</button>
                </div>
            </div>

            <!-- Confirm Password -->
            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>

                <div class="password-wrapper">
                    <input
                        type="password"
                        id="confirm_password"
                        name="confirm_password"
                        placeholder="Confirm new password"
                        autocomplete="new-password"
                        required
                    >

                    <button
                        type="button"
                        class="toggle-password"
                        onclick="togglePassword('confirm_password', this)"
                        aria-label="Show confirm password"
                    >👁️</button>
                </div>
            </div>

            <div class="button-row">

                <a href="profile.php" class="btn btn-secondary">
                    ← Back to Profile
                </a>

                <button type="submit" class="btn btn-primary">
                    Change Password
                </button>

            </div>

        </form>

    </div>

</div>

<script src="js/theme.js"></script>

<script>
function togglePassword(inputId, button) {

    const input = document.getElementById(inputId);

    if (input.type === "password") {
        input.type = "text";
        button.textContent = "🙈";
        button.setAttribute("aria-label", "Hide password");
    } else {
        input.type = "password";
        button.textContent = "👁️";
        button.setAttribute("aria-label", "Show password");
    }
}
</script>

</body>
</html>