<?php

require_once "db.php";
require_once "visitor_tracker.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["admin_id"])) {
    track_visit($conn, "Readme");
}

$heading = "About";
$content = "";

try {
    $result = $conn->query("SELECT Heading, Content FROM portfolio_readme ORDER BY ReadmeID ASC LIMIT 1");
    if ($result && $row = $result->fetch_assoc()) {
        $heading = $row["Heading"];
        $content = $row["Content"];
    }
} catch (\mysqli_sql_exception $e) {
    // Fall back to the defaults above if the table isn't ready yet.
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <script>
        (function () {
            const savedTheme = localStorage.getItem("portfolioTheme");
            if (savedTheme === "light") {
                document.documentElement.classList.add("theme-light-loading");
            }
        })();
    </script>
    <link href="css/boxicons.min.css" rel="stylesheet" />
    <link href="css/style.css" rel="stylesheet" />
    <title><?php echo htmlspecialchars($heading); ?></title>
</head>

<body>

    <header class="header">
        <a class="logo" href="index.php">Noel</a>

        <nav class="navbar">
            <a href="index.php#home">Home</a>
            <a href="index.php#about">About</a>
            <a href="index.php#services">Services</a>
            <a href="index.php#projects">Projects</a>
            <a href="index.php#contact">Contact</a>
        </nav>

        <div class="header-controls">
            <i class="bx bx-menu" id="menu-icon"></i>

            <button class="theme-toggle" id="theme-toggle" aria-label="Toggle theme">
                <i class="bx bx-moon"></i>
            </button>
        </div>
    </header>

    <div class="readme">
        <h1><?php echo htmlspecialchars($heading); ?></h1>
        <p><?php echo nl2br(htmlspecialchars($content)); ?></p>
        <a class="btn" href="index.php#about" style="margin-top: 2rem; display: inline-block;">← Back to portfolio</a>
    </div>

    <script src="js/script.js"></script>
</body>

</html>