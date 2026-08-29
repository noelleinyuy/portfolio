<?php

require_once "db.php";

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
        } else {
            $messageError = "Unable to send your message.";
        }

        $stmt->close();
    } else {

        $messageError = "Please fill in all required fields.";
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <script>
        if (localStorage.getItem("portfolioTheme") === "light") {
            document.documentElement.classList.add("theme-light-loading");
        }
    </script>
    <link href="css/boxicons.min.css" rel="stylesheet" />
    <link href="css/style.css" rel="stylesheet" />
    <title>Noel Leinyuy Software Engineering Student</title>
</head>

<body>
    <!-- HEADER SECTION -->
    <header class="header">
        <a class="logo" href="#home">Noel</a>

        <nav class="navbar">
            <a href="#home">Home</a>
            <a href="#about">About</a>
            <a href="#services">Services</a>
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
            <img alt="Profile Image" src="NOEL.jpg" />
        </div>
        <div class="home-content">
            <h3>Hello, I'm</h3>
            <h1>Noel</h1>
            <h3>And I'm a <span class="multiple-text"></span></h3>
            <p> Software Engineering student at CATUC Bamenda with a passion for Web Development,
                Artificial Intelligence, Mobile App Development, and Cybersecurity.
            </p>
            <div class="social-media">
                <a href="#"><i class="bx bxl-linkedin"></i></a>
                <a href="#"><i class="bx bxl-github"></i></a>
                <a href="#"><i class="bx bxl-facebook"></i></a>
                <a href="#"><i class="bx bxl-instagram"></i></a>
            </div>
            <a class="btn" href="#">Download CV</a>
        </div>
    </section>
    <!-- About Section -->
    <section class="about" id="about">
        <div class="about-content">
            <h2 class="heading">About <span>Me</span> </h2>
            <h3>I'm a <span>Software Engineering Student</span></h3>
            <p> at the Catholic University of Cameroon (CATUC), Bamenda.
            </p>
            <a class="btn" href="readme.html">Read more</a>
        </div>
        <div class="about-img">
            <img alt="" src="images/about.jpeg" />
        </div>
    </section>
    <!-- Services Section -->
    <section class="services" id="services">
        <h2 class="heading">My <span>Services</span> </h2>
        <div class="services-container">
            <div class="services-box">
                <i class="bx bx-code"></i>
                <h3>Web Development</h3>
                <p></p>
                <a class="btn" href="#">Read More</a>
            </div>
            <div class="services-box">
                <i class="bx bx-palette"></i>
                <h3>UI/UX Design</h3>
                <p></p>
                <a class="btn" href="#">Read More</a>
            </div>
            <div class="services-box">
                <i class="bx bxl-android"></i>
                <h3>App Development</h3>
                <p>
                </p>
                <a class="btn" href="#">Read More</a>
            </div>
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

            <div class="wrapper">

                <?php

                $testimonials = $conn->query("
                SELECT
                    TestimonialID,
                    Name,
                    Role,
                    Image,
                    Message
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

                            <div class="rating">

                                <i class="bx bxs-star"></i>
                                <i class="bx bxs-star"></i>
                                <i class="bx bxs-star"></i>
                                <i class="bx bxs-star"></i>
                                <i class="bx bxs-star"></i>

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
            <a href="#"><i class="bx bxl-linkedin"></i></a>
            <a href="#"><i class="bx bxl-github"></i></a>
            <a href="#"><i class="bx bxl-facebook"></i></a>
            <a href="#"><i class="bx bxl-instagram"></i></a>
        </div>
        <p class="copyright">© 2026 Noel | All Rights Reserved</p>
    </footer>
    <script src="js/typed.umd.js"></script>
    <script src="js/script.js"></script>
</body>

</html>