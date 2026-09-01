<?php

require_once __DIR__ . "/db.php";
require_once __DIR__ . "/visitor_tracker.php";

if (!function_exists("save_uploaded_image")) {
    require_once __DIR__ . "/admin_helpers.php";
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists("ensure_testimonial_rating_column")) {
    require_once __DIR__ . "/admin_helpers.php";
}

ensure_testimonial_rating_column($conn);

$testimonialSubmitted = isset($_GET["testimonial"]) &&
    $_GET["testimonial"] === "submitted";

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $role = trim($_POST["role"] ?? "");
    $testimonial = trim($_POST["message"] ?? "");
    $rating = max(1, min(5, (int) ($_POST["rating"] ?? 5)));
    $image = "";

    if (isset($_FILES["image"]) && $_FILES["image"]["error"] !== UPLOAD_ERR_NO_FILE) {
        $uploadedImage = save_uploaded_image($_FILES["image"], "images/testimonials");

        if ($uploadedImage === false) {
            $message = "Only JPG, PNG, GIF, and WEBP images are allowed.";
            $messageType = "error";
        } elseif ($uploadedImage !== null) {
            $image = $uploadedImage;
        } else {
            $message = "Unable to upload the image.";
            $messageType = "error";
        }
    }

    if ($message === "") {
        if ($name === "" || $email === "" || $testimonial === "") {
            $message = "Please fill in all required fields.";
            $messageType = "error";
        } else {
            $stmt = $conn->prepare("
                INSERT INTO portfolio_testimonials
                (Name, Email, Role, Image, Message, Rating, Status)
                VALUES (?, ?, ?, ?, ?, ?, 'Hidden')
            ");

            $stmt->bind_param(
                "sssssi",
                $name,
                $email,
                $role,
                $image,
                $testimonial,
                $rating
            );

            if ($stmt->execute()) {
                track_action($conn, "testimonial_submit", $name);
                header("Location: index.php?testimonial=success#testimonials");
                exit;
            } else {
                $message = "Unable to submit your testimonial.";
                $messageType = "error";
            }

            $stmt->close();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Write a Testimonial</title>

    <link
        rel="stylesheet"
        href="css/style.css">

</head>

<body>
    <?php if ($testimonialSubmitted): ?>

        <div class="success-message">
            Thank you! Your testimonial has been submitted successfully.
            It will appear here once it has been approved by the admin.
        </div>

    <?php endif; ?>

    <section class="contact">

        <h2 class="heading">
            Write a <span>Testimonial</span>
        </h2>

        <?php if ($message !== ""): ?>

            <p class="<?php echo htmlspecialchars($messageType); ?>">
                <?php echo htmlspecialchars($message); ?>
            </p>

        <?php endif; ?>

        <form
            action="testimonial.php"
            method="POST"
            enctype="multipart/form-data">

            <div class="input-box">

                <input
                    type="text"
                    name="name"
                    placeholder="Full Name"
                    required>

                <input
                    type="email"
                    name="email"
                    placeholder="Email Address"
                    required>

            </div>

            <div class="input-box">

                <input
                    type="text"
                    name="role"
                    placeholder="Role / Position">

                <input
                    type="file"
                    name="image"
                    accept="image/*">

            </div>

            <div class="input-box rating-field">
                <label for="rating" class="rating-label">Your rating</label>
                <select name="rating" id="rating" required class="rating-select">
                    <option value="">Choose a rating</option>
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                        <option value="<?php echo $i; ?>"><?php echo $i; ?> <?php echo $i === 1 ? 'star' : 'stars'; ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <textarea
                name="message"
                rows="10"
                placeholder="Write your testimonial..."
                required></textarea>

            <input
                class="btn"
                type="submit"
                value="Submit Testimonial">

        </form>

    </section>

</body>

</html>

<?php

$conn->close();

?>