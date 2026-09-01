<?php

require_once "db.php";
require_once "visitor_tracker.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists("ensure_testimonial_rating_column")) {
    require_once __DIR__ . "/admin_helpers.php";
}

ensure_testimonial_rating_column($conn);

// Don't pollute visitor stats with your own visits while logged into admin.
if (!isset($_SESSION["admin_id"])) {
    track_visit($conn, "Home");
}

$testimonialSubmitted = isset($_GET["testimonial"]) && $_GET["testimonial"] === "success";
$messageSent = false;
$messageError = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $number = trim($_POST["number"] ?? "");
    $subject = trim($_POST["subject"] ?? "");
    $message = trim($_POST["message"] ?? "");

    if ($name !== "" && $email !== "" && $message !== "") {

        $stmt = $conn->prepare("
            INSERT INTO portfolio_messages
            (FullName, Email, Phone, Subject, Message)
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "sssss",
            $name,
            $email,
            $number,
            $subject,
            $message
        );

        if ($stmt->execute()) {
            $messageSent = true;
            track_action($conn, "contact_form_submit", $subject ?: "Contact form submitted");
        } else {
            $messageError = "Unable to send your message.";
        }

        $stmt->close();
    } else {

        $messageError = "Please fill in all required fields.";
    }
}

function get_site_setting(mysqli $conn, string $key, string $default = ''): string
{
    try {
        $stmt = $conn->prepare('SELECT SettingValue FROM portfolio_settings WHERE SettingName = ?');
        if (!$stmt) {
            return $default;
        }
        $stmt->bind_param('s', $key);
        $stmt->execute();
        $result = $stmt->get_result();
        $value = $default;
        if ($row = $result->fetch_assoc()) {
            $value = $row['SettingValue'];
        }
        $stmt->close();
        return $value;
    } catch (\mysqli_sql_exception $e) {
        return $default;
    }
}

$siteName = get_site_setting($conn, 'SiteName', 'Noel');
$homeIntro = get_site_setting($conn, 'HomeIntro', 'Software Engineering student at CATUC Bamenda with a passion for Web Development, Artificial Intelligence, Mobile App Development, and Cybersecurity.');
$typedRoles = get_site_setting($conn, 'TypedRoles', 'Frontend Developer,Backend Developer,Blockchain Developer,Web Designer,Youtuber');
$aboutHeading = get_site_setting($conn, 'AboutHeading', 'Software Engineering Student');
$aboutText = get_site_setting($conn, 'AboutText', 'at the Catholic University of Cameroon (CATUC), Bamenda.');
$profileImage = get_site_setting($conn, 'ProfileImage', 'NOEL.jpg');
$aboutImage = get_site_setting($conn, 'AboutImage', 'images/about.jpeg');
$cvLink = get_site_setting($conn, 'CVLink', 'files/Noel_CV.pdf');
$socialLinkedIn = get_site_setting($conn, 'SocialLinkedIn', '#');
$socialGithub = get_site_setting($conn, 'SocialGithub', '#');
$socialFacebook = get_site_setting($conn, 'SocialFacebook', '#');
$socialInstagram = get_site_setting($conn, 'SocialInstagram', '#');

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <script>
        (function () {
            const urlTheme = new URLSearchParams(window.location.search).get("theme");
            const savedTheme = urlTheme || localStorage.getItem("portfolioTheme");
            const isLight = savedTheme === "light";

            if (urlTheme) {
                localStorage.setItem("portfolioTheme", urlTheme);
            }

            document.documentElement.setAttribute("data-theme", isLight ? "light" : "dark");

            if (isLight) {
                document.documentElement.classList.add("theme-light-loading");
            }
        })();
    </script>
    <link href="css/boxicons.min.css" rel="stylesheet" />
    <link href="css/style.css" rel="stylesheet" />
    <title><?php echo htmlspecialchars($siteName); ?> Software Engineering Student</title>
</head>

<body>
    <!-- HEADER SECTION -->
    <header class="header">
        <a class="logo" href="#home"><?php echo htmlspecialchars($siteName); ?></a>

        <nav class="navbar">
            <a href="#home">Home</a>
            <a href="#about">About</a>
            <a href="#services">Services</a>
            <a href="#projects">Projects</a>
            <a href="#testimonials">Testimonial</a>
            <a href="#contact">Contact</a>
        </nav>

        <div class="header-controls">
            <i class="bx bx-menu" id="menu-icon"></i>

            <button class="theme-toggle" id="theme-toggle" aria-label="Toggle theme">
                <i class="bx bx-moon"></i>
            </button>
        </div>
    </header>
    <!-- Home Section -->
    <section class="home" id="home">
        <div class="home-img">
            <img alt="Profile Image" src="<?php echo htmlspecialchars($profileImage); ?>" />
        </div>
        <div class="home-content">
            <h3>Hello, I'm</h3>
            <h1><?php echo htmlspecialchars($siteName); ?></h1>
            <h3>And I'm a <span class="multiple-text" data-roles="<?php echo htmlspecialchars($typedRoles); ?>"></span></h3>
            <p><?php echo htmlspecialchars($homeIntro); ?></p>
            <div class="social-media">
                <a href="<?php echo htmlspecialchars($socialLinkedIn); ?>"><i class="bx bxl-linkedin"></i></a>
                <a href="<?php echo htmlspecialchars($socialGithub); ?>"><i class="bx bxl-github"></i></a>
                <a href="<?php echo htmlspecialchars($socialFacebook); ?>"><i class="bx bxl-facebook"></i></a>
                <a href="<?php echo htmlspecialchars($socialInstagram); ?>"><i class="bx bxl-instagram"></i></a>
            </div>
            <a class="btn" href="<?php echo htmlspecialchars($cvLink); ?>" download>Download CV</a>
        </div>
    </section>
    <!-- About Section -->
    <section class="about" id="about">
        <div class="about-content">
            <h2 class="heading">About <span>Me</span> </h2>
            <h3>I'm a <span><?php echo htmlspecialchars($aboutHeading); ?></span></h3>
            <p><?php echo htmlspecialchars($aboutText); ?></p>
            <a class="btn" href="readme.html">Read more</a>
        </div>
        <div class="about-img">
            <img alt="" src="<?php echo htmlspecialchars($aboutImage); ?>" />
        </div>
    </section>
    <!-- Services Section -->
    <section class="services" id="services">
        <h2 class="heading">My <span>Services</span> </h2>
        <div class="services-container">
            <?php
            $services = [];
            try {
                $result = $conn->query("SELECT * FROM portfolio_services WHERE Status='Visible' ORDER BY ServiceID ASC");
                if ($result) {
                    $services = $result->fetch_all(MYSQLI_ASSOC);
                }
            } catch (\mysqli_sql_exception $e) {
                $services = [];
            }
            ?>
            <?php if (!empty($services)): ?>
                <?php foreach ($services as $svc): ?>
                    <div class="services-box">
                        <i class="<?php echo htmlspecialchars($svc['Icon']); ?>"></i>
                        <h3><?php echo htmlspecialchars($svc['Title']); ?></h3>
                        <p><?php echo nl2br(htmlspecialchars($svc['Description'])); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="services-box">
                    <i class="bx bx-info-circle"></i>
                    <h3>Coming soon</h3>
                    <p>Services will be listed here shortly.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
    <!-- Projects Section -->
    <section class="projects" id="projects">
        <h2 class="heading">My <span>Projects</span> </h2>
        <div class="projects-container">
            <?php
            $projects = [];
            try {
                $result = $conn->query("SELECT * FROM portfolio_projects WHERE Status='Visible' ORDER BY ProjectID ASC");
                if ($result) {
                    $projects = $result->fetch_all(MYSQLI_ASSOC);
                }
            } catch (\mysqli_sql_exception $e) {
                $projects = [];
            }
            ?>
            <?php if (!empty($projects)): ?>
                <?php foreach ($projects as $proj): ?>
                    <div class="project-card">
                        <?php if (!empty($proj['Image'])): ?>
                            <img src="<?php echo htmlspecialchars($proj['Image']); ?>" alt="<?php echo htmlspecialchars($proj['Title']); ?>" />
                        <?php endif; ?>
                        <h3><?php echo htmlspecialchars($proj['Title']); ?></h3>
                        <p><?php echo nl2br(htmlspecialchars($proj['Description'])); ?></p>
                        <?php if (!empty($proj['ProjectLink'])): ?>
                            <a class="btn" href="<?php echo htmlspecialchars($proj['ProjectLink']); ?>">View Project</a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="testimonial-intro">Projects will be added soon.</p>
            <?php endif; ?>
        </div>
    </section>
    <!-- Testimonials Section -->
    <section class="testimonials" id="testimonials">

        <div class="testimonials-box">

            <h2 class="heading">
                Testimonials
            </h2>

            <p class="testimonial-intro">
                See what people have to say about working with me.
                If you have worked with me, you can also share your experience.
            </p>

            <?php if ($testimonialSubmitted): ?>
                <div class="success-message" style="margin-bottom: 1.5rem;">
                    Thank you! Your testimonial has been submitted successfully and will appear after admin approval.
                </div>
            <?php endif; ?>

            <div class="wrapper">

                <?php

                $testimonials = $conn->query("
                SELECT
                    TestimonialID,
                    Name,
                    Role,
                    Image,
                    Message,
                    Rating
                FROM portfolio_testimonials
                WHERE Status = 'Visible'
                ORDER BY TestimonialID DESC
            ");

                ?>

                <?php if ($testimonials && $testimonials->num_rows > 0): ?>

                    <?php while ($testimonial = $testimonials->fetch_assoc()): ?>

                        <div class="testimonial-item">

                            <?php if (!empty($testimonial["Image"])): ?>

                                <img
                                    src="<?php echo htmlspecialchars($testimonial["Image"]); ?>"
                                    alt="<?php echo htmlspecialchars($testimonial["Name"]); ?>">

                            <?php endif; ?>

                            <h2>
                                <?php
                                echo htmlspecialchars($testimonial["Name"]);
                                ?>
                            </h2>

                            <?php if (!empty($testimonial["Role"])): ?>

                                <h4>
                                    <?php
                                    echo htmlspecialchars($testimonial["Role"]);
                                    ?>
                                </h4>

                            <?php endif; ?>

                            <div class="rating" aria-label="<?php echo (int) ($testimonial["Rating"] ?? 5); ?> out of 5 stars">
                                <?php for ($star = 1; $star <= 5; $star++): ?>
                                    <i class="bx <?php echo $star <= (int) ($testimonial["Rating"] ?? 5) ? "bxs-star" : "bx-star"; ?>"></i>
                                <?php endfor; ?>
                            </div>

                            <p>
                                <?php
                                echo nl2br(
                                    htmlspecialchars($testimonial["Message"])
                                );
                                ?>
                            </p>

                        </div>

                    <?php endwhile; ?>

                <?php else: ?>

                    <div class="testimonial-item">

                        <h2>No testimonials yet</h2>

                        <p>
                            Be the first person to share your experience.
                        </p>

                    </div>

                <?php endif; ?>

            </div>


            <!-- Write Testimonial Button -->

            <div class="testimonial-action">

                <a
                    href="testimonial.php"
                    class="btn">
                    Write a Testimonial
                </a>

            </div>

        </div>

    </section>
    <!-- Contact Section -->
    <?php if ($messageSent): ?>

        <p class="success-message">
            Your message has been sent successfully.
        </p>

    <?php endif; ?>

    <?php if ($messageError !== ""): ?>

        <p class="error-message">
            <?php echo htmlspecialchars($messageError); ?>
        </p>

    <?php endif; ?>
    <section class="contact" id="contact">
        <h2 class="heading">Contact <span>Me</span></h2>
        <form action="index.php" method="POST">
            <div class="input-box">
                <input name="name" placeholder="Full Name" type="text" required />
                <input name="email" placeholder="Email Address" type="email" required />
            </div>
            <div class="input-box">
                <input name="number" placeholder="Phone Number" type="tel" />
                <input name="subject" placeholder="Email Subject" type="text" />
            </div>
            <textarea cols="30" name="message" placeholder="Your Message" rows="10" required></textarea>
            <input class="btn" type="submit" value="Send Message" />
        </form>
    </section>
    <!-- Footer Section -->
    <footer class="footer">
        <div class="social">
            <a href="<?php echo htmlspecialchars($socialLinkedIn); ?>"><i class="bx bxl-linkedin"></i></a>
            <a href="<?php echo htmlspecialchars($socialGithub); ?>"><i class="bx bxl-github"></i></a>
            <a href="<?php echo htmlspecialchars($socialFacebook); ?>"><i class="bx bxl-facebook"></i></a>
            <a href="<?php echo htmlspecialchars($socialInstagram); ?>"><i class="bx bxl-instagram"></i></a>
        </div>
        <p class="copyright">
            © 2026 <?php echo htmlspecialchars($siteName); ?> | All Rights Reserved
            <a href="admin_login.php" class="admin-link">Admin</a>
        </p>
    </footer>
    <script src="js/typed.umd.js"></script>
    <script src="js/script.js"></script>
</body>

</html>