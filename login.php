<?php

// Keep the login session active for 30 days,
// even if the browser is closed and opened again.
session_set_cookie_params([
    "lifetime" => 60 * 60 * 24 * 30,
    "path" => "/",
    "secure" => false,
    "httponly" => true,
    "samesite" => "Lax"
]);

session_start();

require_once "db.php";

$message = "";

// If user is already logged in, directly open dashboard.
if (isset($_SESSION["user_id"])) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (empty($email) || empty($password)) {

        $message = "Please enter email and password.";

    } else {

        $stmt = $conn->prepare(
            "SELECT id, name, email, password FROM users WHERE email = ?"
        );

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows == 1) {

            $user = $result->fetch_assoc();

            if (password_verify($password, $user["password"])) {

                // Regenerate session ID after successful login
                // for better security.
                session_regenerate_id(true);

                $_SESSION["user_id"] = $user["id"];
                $_SESSION["user_name"] = $user["name"];
                $_SESSION["user_email"] = $user["email"];

                header("Location: dashboard.php");
                exit();

            } else {

                $message = "Incorrect password.";
            }

        } else {

            $message = "No account found with this email.";
        }

        $stmt->close();
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

    <title>Login - Interview Prep Portal</title>

</head>

<body>

    <h1>Interview Prep Portal</h1>

    <h2>Login</h2>

    <?php if (!empty($message)): ?>

        <p>
            <?php echo htmlspecialchars($message); ?>
        </p>

    <?php endif; ?>

    <form method="POST" action="">

        <label>Email</label><br>

        <input
            type="email"
            name="email"
            required
        >

        <br><br>

        <label>Password</label><br>

        <input
            type="password"
            name="password"
            required
        >

        <br><br>

        <button type="submit">
            Login
        </button>

    </form>

    <p>

        Don't have an account?

        <a href="register.php">
            Register
        </a>

    </p>

    <script src="js/theme.js"></script>

</body>

</html>