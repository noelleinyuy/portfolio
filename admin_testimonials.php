<?php

require_once "admin_helpers.php";
require_once "db.php";
require_admin_login();
ensure_testimonial_rating_column($conn);

$adminName = $_SESSION["admin_name"] ?? "Admin";
$hasCreatedAt = column_exists($conn, "portfolio_testimonials", "CreatedAt");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $action = $_POST["action"] ?? "";
    $id = (int) ($_POST["id"] ?? 0);

    if ($action === "toggle" && $id) {

        $conn->query("UPDATE portfolio_testimonials SET Status = IF(Status = 'Visible', 'Hidden', 'Visible') WHERE TestimonialID = $id");
    } elseif ($action === "delete" && $id) {

        $conn->query("DELETE FROM portfolio_testimonials WHERE TestimonialID = $id");
        flash("success", "Testimonial deleted.");
    } elseif ($action === "add" || $action === "update") {

        $name = trim($_POST["name"] ?? "");
        $email = trim($_POST["email"] ?? "");
        $role = trim($_POST["role"] ?? "");
        $image = trim($_POST["image_url"] ?? "");
        $image = $image === "" ? trim($_POST["existing_image"] ?? "") : $image;
        $message = trim($_POST["message"] ?? "");
        $rating = max(1, min(5, (int) ($_POST["rating"] ?? 5)));

        if (isset($_FILES["image"]) && $_FILES["image"]["error"] !== UPLOAD_ERR_NO_FILE) {
            $uploadedImage = handle_image_upload("image", "images/testimonials");

            if ($uploadedImage === false) {
                flash("error", "Only JPG, PNG, GIF, and WEBP images are allowed.");
                header("Location: admin_testimonials.php");
                exit;
            }

            if ($uploadedImage !== null) {
                $image = $uploadedImage;
            } elseif (trim($_POST["existing_image"] ?? "") !== "") {
                $image = trim($_POST["existing_image"] ?? "");
            } else {
                flash("error", "Image upload failed. Please try again.");
                header("Location: admin_testimonials.php");
                exit;
            }
        }

        if ($name === "" || $message === "") {

            flash("error", "Name and message are required.");
        } elseif ($action === "add") {

            $stmt = $conn->prepare("
                INSERT INTO portfolio_testimonials (Name, Email, Role, Image, Message, Rating, Status)
                VALUES (?, ?, ?, ?, ?, ?, 'Visible')
            ");
            $stmt->bind_param("sssssi", $name, $email, $role, $image, $message, $rating);
            $stmt->execute();
            $stmt->close();
            flash("success", "Testimonial added.");
        } else {

            $stmt = $conn->prepare("
                UPDATE portfolio_testimonials
                SET Name = ?, Email = ?, Role = ?, Image = ?, Message = ?, Rating = ?
                WHERE TestimonialID = ?
            ");
            $stmt->bind_param("sssssii", $name, $email, $role, $image, $message, $rating, $id);
            $stmt->execute();
            $stmt->close();
            flash("success", "Testimonial updated.");
        }
    }

    header("Location: admin_testimonials.php");
    exit;
}

$editId = (int) ($_GET["edit"] ?? 0);
$editing = null;

if ($editId) {
    $stmt = $conn->prepare("SELECT * FROM portfolio_testimonials WHERE TestimonialID = ?");
    $stmt->bind_param("i", $editId);
    $stmt->execute();
    $editing = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$testimonials = $conn->query("SELECT * FROM portfolio_testimonials ORDER BY TestimonialID DESC");

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Testimonials — Admin</title>

    <link rel="stylesheet" href="css/boxicons.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/admin.css">

</head>

<body>

    <div class="admin-shell">

        <aside class="admin-sidebar" id="admin-sidebar">

            <div class="admin-logo">Noel<span>.admin</span></div>

            <nav class="admin-nav">
                <a href="admin_dashboard.php"><i class="bx bx-grid-alt"></i> Dashboard</a>
                <a href="admin_analytics.php"><i class="bx bx-line-chart"></i> Analytics</a>
                <a href="admin_testimonials.php" class="active"><i class="bx bx-chat"></i> Testimonials</a>
                <a href="admin_messages.php"><i class="bx bx-envelope"></i> Messages</a>
                <a href="admin_services.php"><i class="bx bx-briefcase"></i> Services</a>
                <a href="admin_projects.php"><i class="bx bx-code-alt"></i> Projects</a>
                <a href="admin_readme.php"><i class="bx bx-file"></i> Read Me</a>
                <a href="admin_settings.php"><i class="bx bx-cog"></i> Settings</a>
            </nav>

            <a href="admin_logout.php" class="admin-logout"><i class="bx bx-log-out"></i> Logout</a>

        </aside>

        <div class="admin-main">

            <header class="admin-topbar">
                <button id="sidebar-toggle" class="sidebar-toggle" aria-label="Toggle menu">
                    <i class="bx bx-menu"></i>
                </button>
                <h1>Testimonials</h1>
                <div class="admin-user">
                    <i class="bx bx-user-circle"></i>
                    <?php echo e($adminName); ?>
                </div>
            </header>

            <main class="admin-content">

                <?php $f = get_flash();
                if ($f): ?>
                    <div class="admin-alert admin-alert-<?php echo e($f["type"]); ?>"><?php echo e($f["message"]); ?></div>
                <?php endif; ?>

                <div class="admin-panel">
                    <h2><?php echo $editing ? "Edit testimonial" : "Add a testimonial"; ?></h2>

                    <?php if (!$editing): ?>
                        <p class="muted" style="margin-bottom: 1.2rem;">
                            Testimonials submitted through the public "Write a Testimonial" form arrive
                            here automatically with a Hidden status — approve them below by clicking Show.
                        </p>
                    <?php endif; ?>

                    <form method="POST" class="admin-form" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="<?php echo $editing ? "update" : "add"; ?>">
                        <?php if ($editing): ?>
                            <input type="hidden" name="id" value="<?php echo (int) $editing["TestimonialID"]; ?>">
                            <input type="hidden" name="existing_image" value="<?php echo e($editing["Image"] ?? ""); ?>">
                        <?php endif; ?>

                        <div class="form-row">
                            <div>
                                <label>Name</label>
                                <input type="text" name="name" required value="<?php echo e($editing["Name"] ?? ""); ?>">
                            </div>
                            <div>
                                <label>Email</label>
                                <input type="email" name="email" value="<?php echo e($editing["Email"] ?? ""); ?>">
                            </div>
                        </div>

                        <div class="form-row">
                            <div>
                                <label>Role</label>
                                <input type="text" name="role" value="<?php echo e($editing["Role"] ?? ""); ?>">
                            </div>
                            <div>
                                <label>Rating</label>
                                <select name="rating" required>
                                    <?php for ($i = 5; $i >= 1; $i--): ?>
                                        <option value="<?php echo $i; ?>" <?php echo ((int) ($editing["Rating"] ?? 5)) === $i ? "selected" : ""; ?>>
                                            <?php echo $i; ?> <?php echo $i === 1 ? "star" : "stars"; ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div style="width: 100%;">
                                <label>Testimonial image</label>
                                <input type="file" name="image" accept="image/*">
                                <input type="text" name="image_url" placeholder="images/testimonial1.jpg" value="<?php echo e($editing["Image"] ?? ""); ?>" style="margin-top: 0.6rem;">
                                <?php if (!empty($editing["Image"] ?? "")): ?>
                                    <div style="margin-top: 0.8rem;">
                                        <img src="<?php echo e($editing["Image"]); ?>" alt="Current testimonial image" style="max-width: 140px; border-radius: 10px; display: block;">
                                        <small class="muted">Upload a new file to replace it or keep the current image.</small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <label>Message</label>
                        <textarea name="message" rows="3" required><?php echo e($editing["Message"] ?? ""); ?></textarea>

                        <div class="row-actions">
                            <button type="submit" class="btn-primary"><?php echo $editing ? "Save changes" : "Add testimonial"; ?></button>
                            <?php if ($editing): ?>
                                <a href="admin_testimonials.php" class="btn-small">Cancel</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <div class="admin-panel">
                    <h2>All testimonials</h2>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Rating</th>
                                <th>Message</th>
                                <th>Status</th>
                                <?php if ($hasCreatedAt): ?><th>Submitted</th><?php endif; ?>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($testimonials && $testimonials->num_rows > 0): ?>
                                <?php while ($t = $testimonials->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo e($t["Name"]); ?></td>
                                        <td class="muted"><?php echo e($t["Email"]); ?></td>
                                        <td><?php echo e($t["Role"]); ?></td>
                                        <td><?php echo (int) ($t["Rating"] ?? 5); ?> / 5</td>
                                        <td class="truncate"><?php echo e($t["Message"]); ?></td>
                                        <td>
                                            <span class="badge <?php echo $t["Status"] === "Visible" ? "badge-green" : "badge-gray"; ?>">
                                                <?php echo e($t["Status"]); ?>
                                            </span>
                                        </td>
                                        <?php if ($hasCreatedAt): ?>
                                            <td class="muted"><?php echo e(date("M j, Y", strtotime($t["CreatedAt"]))); ?></td>
                                        <?php endif; ?>
                                        <td class="row-actions">
                                            <a href="admin_testimonials.php?edit=<?php echo (int) $t["TestimonialID"]; ?>" class="btn-small">Edit</a>

                                            <form method="POST">
                                                <input type="hidden" name="action" value="toggle">
                                                <input type="hidden" name="id" value="<?php echo (int) $t["TestimonialID"]; ?>">
                                                <button type="submit" class="btn-small"><?php echo $t["Status"] === "Visible" ? "Hide" : "Show"; ?></button>
                                            </form>

                                            <form method="POST" onsubmit="return confirm('Delete this testimonial?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo (int) $t["TestimonialID"]; ?>">
                                                <button type="submit" class="btn-small btn-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7">No testimonials yet.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </main>

        </div>

    </div>

    <script src="js/admin.js"></script>

</body>

</html>