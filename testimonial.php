<?php

require_once "db.php";

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $role = trim($_POST["role"] ?? "");
    $testimonial = trim($_POST["message"] ?? "");
$image = "";

if (isset($_FILES["image"]) && $_FILES["image"]["error"] === 0) {

    $imageName = basename($_FILES["image"]["name"]);

    $uploadDirectory = __DIR__ . "/images/";

    if (!is_dir($uploadDirectory)) {
        mkdir($uploadDirectory, 0777, true);
    }

    $uploadPath = $uploadDirectory . $imageName;

    if (move_uploaded_file(
        $_FILES["image"]["tmp_name"],
        $uploadPath
    )) {
        $image = "images/" . $imageName;
    }
}

    if ($name === "" || $email === "" || $testimonial === "") {

        $message = "Please fill in all required fields.";
        $messageType = "error";

    } else {

        $stmt = $conn->prepare("
            INSERT INTO portfolio_testimonials
            (Name, Email, Role, Image, Message, Status)
            VALUES (?, ?, ?, ?, ?, 'Hidden')
        ");

        $stmt->bind_param(
            "sssss",
            $name,
            $email,
            $role,
            $image,
            $testimonial
        );

        if ($stmt->execute()) {

            $message = "Thank you! Your testimonial has been submitted and is waiting for approval.";
            $messageType = "success";

        } else {

            $message = "Unable to submit your testimonial.";
            $messageType = "error";
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

    <title>Write a Testimonial</title>

    <link
        rel="stylesheet"
        href="css/style.css"
    >

</head>

<body>

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
        enctype="multipart/form-data"
    >

        <div class="input-box">

            <input
                type="text"
                name="name"
                placeholder="Full Name"
                required
            >

            <input
                type="email"
                name="email"
                placeholder="Email Address"
                required
            >

        </div>

        <div class="input-box">

            <input
                type="text"
                name="role"
                placeholder="Role / Position"
            >

            <input
                type="file"
                name="image"
                accept="image/*"
            >

        </div>

        <textarea
            name="message"
            rows="10"
            placeholder="Write your testimonial..."
            required
        ></textarea>

        <input
            class="btn"
            type="submit"
            value="Submit Testimonial"
        >

    </form>

</section>

</body>

</html>

<?php

$conn->close();

?>