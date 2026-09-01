<?php

session_start();
require_once "db.php";

// Pull any error left by a previous POST (see bottom of file), then clear it
// so it only shows once.
$error = $_SESSION["admin_login_error"] ?? "";
$oldEmail = $_SESSION["admin_login_email"] ?? "";
unset($_SESSION["admin_login_error"], $_SESSION["admin_login_email"]);

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email === "" || $password === "") {

        $_SESSION["admin_login_error"] = "Please enter your email and password.";
        $_SESSION["admin_login_email"] = $email;
        header("Location: admin_login.php");
        exit;

    }

    $stmt = $conn->prepare("
        SELECT AdminID, Name, Email, Password
        FROM portfolio_admin
        WHERE Email = ?
        LIMIT 1
    ");

    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 1) {

        $admin = $result->fetch_assoc();

        if (password_verify($password, $admin["Password"])) {

            $_SESSION["admin_id"] = $admin["AdminID"];
            $_SESSION["admin_name"] = $admin["Name"];
            $_SESSION["admin_email"] = $admin["Email"];

            $stmt->close();
            $conn->close();

            header("Location: admin_dashboard.php");
            exit;

        }
    }

    $stmt->close();

    // Invalid email or password: flash the error and redirect, same as above,
    // so refreshing never resubmits the login attempt.
    $_SESSION["admin_login_error"] = "Invalid email or password.";
    $_SESSION["admin_login_email"] = $email;
    header("Location: admin_login.php");
    exit;
}

$conn->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Admin Login</title>

    <script>
        (function () {
            const savedTheme = localStorage.getItem("portfolioTheme");
            const isLight = savedTheme === "light";

            document.documentElement.setAttribute("data-theme", isLight ? "light" : "dark");

            if (isLight) {
                document.documentElement.classList.add("theme-light-loading");
            }
        })();
    </script>

    <link
        rel="stylesheet"
        href="css/style.css"
    >

</head>

<body>

<section class="contact">

    <h2 class="heading">
        Admin <span>Login</span>
    </h2>

    <?php if ($error !== ""): ?>

        <p class="error-message">
            <?php echo htmlspecialchars($error); ?>
        </p>

    <?php endif; ?>

    <form
        action="admin_login.php"
        method="POST"
        autocomplete="off"
    >

        <div class="input-box">

            <input
                type="email"
                name="email"
                id="admin-email"
                placeholder="Admin Email"
                value="<?php echo htmlspecialchars($oldEmail); ?>"
                autocomplete="off"
                readonly
                onfocus="this.removeAttribute('readonly')"
                required
            >

        </div>

        <div class="input-box">

            <input
                type="password"
                name="password"
                id="admin-password"
                placeholder="Password"
                autocomplete="new-password"
                readonly
                onfocus="this.removeAttribute('readonly')"
                required
            >

        </div>

        <input
            class="btn"
            type="submit"
            value="Login"
        >

        <div style="margin-top: 1.5rem; text-align: center;">
            <a href="index.php" class="btn" style="display: inline-block;">Back to Homepage</a>
        </div>

    </form>

</section>

</body>

</html>