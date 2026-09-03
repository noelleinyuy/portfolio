<?php

require_once "admin_helpers.php";
require_once "db.php";
require_admin_login();

$adminName = $_SESSION["admin_name"] ?? "Admin";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "";
    $id = (int) ($_POST["id"] ?? 0);

    if ($action === "toggle" && $id) {
        $conn->query("UPDATE portfolio_projects SET Status = IF(Status = 'Visible', 'Hidden', 'Visible') WHERE ProjectID = $id");
    } elseif ($action === "delete" && $id) {
        $conn->query("DELETE FROM portfolio_projects WHERE ProjectID = $id");
        flash("success", "Project deleted.");
    } elseif ($action === "add" || $action === "update") {
        $title = trim($_POST["title"] ?? "");
        $description = trim($_POST["description"] ?? "");
        $projectLink = trim($_POST["project_link"] ?? "");
        $image = trim($_POST["existing_image"] ?? "");

        if (isset($_FILES["image"]) && $_FILES["image"]["error"] === UPLOAD_ERR_OK) {
            $tmpName = $_FILES["image"]["tmp_name"];
            $originalName = basename($_FILES["image"]["name"]);
            $allowedTypes = ["image/jpeg", "image/png", "image/gif", "image/webp"];
            $fileType = mime_content_type($tmpName);

            if (!in_array($fileType, $allowedTypes, true) && !preg_match('/\.(jpe?g|png|gif|webp)$/i', $originalName)) {
                flash("error", "Only JPG, PNG, GIF, and WEBP images are allowed.");
                header("Location: admin_projects.php");
                exit;
            }

            $uploadDirectory = __DIR__ . "/images/projects/";
            if (!is_dir($uploadDirectory)) {
                mkdir($uploadDirectory, 0777, true);
            }

            $safeName = time() . "_" . preg_replace('/[^A-Za-z0-9._-]+/', '_', $originalName);
            $uploadPath = $uploadDirectory . $safeName;

            if (!move_uploaded_file($tmpName, $uploadPath)) {
                flash("error", "Image upload failed. Please try again.");
                header("Location: admin_projects.php");
                exit;
            }

            $image = "images/projects/" . $safeName;
        }

        if ($title === "" || $description === "") {
            flash("error", "Title and description are required.");
        } elseif ($action === "add") {
            $stmt = $conn->prepare("
                INSERT INTO portfolio_projects (Title, Description, Image, ProjectLink, Status)
                VALUES (?, ?, ?, ?, 'Visible')
            ");
            $stmt->bind_param("ssss", $title, $description, $image, $projectLink);
            $stmt->execute();
            $stmt->close();
            flash("success", "Project added.");
        } else {
            $stmt = $conn->prepare("
                UPDATE portfolio_projects
                SET Title = ?, Description = ?, Image = ?, ProjectLink = ?
                WHERE ProjectID = ?
            ");
            $stmt->bind_param("ssssi", $title, $description, $image, $projectLink, $id);
            $stmt->execute();
            $stmt->close();
            flash("success", "Project updated.");
        }
    }

    header("Location: admin_projects.php");
    exit;
}

$editId = (int) ($_GET["edit"] ?? 0);
$editing = null;

if ($editId) {
    $stmt = $conn->prepare("SELECT * FROM portfolio_projects WHERE ProjectID = ?");
    $stmt->bind_param("i", $editId);
    $stmt->execute();
    $editing = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$projects = $conn->query("SELECT * FROM portfolio_projects ORDER BY ProjectID DESC");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projects — Admin</title>
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
            <a href="admin_testimonials.php"><i class="bx bx-chat"></i> Testimonials</a>
            <a href="admin_messages.php"><i class="bx bx-envelope"></i> Messages</a>
            <a href="admin_services.php"><i class="bx bx-briefcase"></i> Services</a>
            <a href="admin_projects.php" class="active"><i class="bx bx-code-alt"></i> Projects</a>
            <a href="admin_visitors.php"><i class="bx bx-line-chart"></i> Visitors</a>
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
            <h1>Projects</h1>
            <div class="admin-user">
                <i class="bx bx-user-circle"></i>
                <?php echo e($adminName); ?>
            </div>
        </header>

        <main class="admin-content">
            <?php $f = get_flash(); if ($f): ?>
                <div class="admin-alert admin-alert-<?php echo e($f["type"]); ?>"><?php echo e($f["message"]); ?></div>
            <?php endif; ?>

            <div class="admin-panel">
                <h2><?php echo $editing ? "Edit project" : "Add a project"; ?></h2>

                <form method="POST" class="admin-form" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="<?php echo $editing ? "update" : "add"; ?>">
                    <?php if ($editing): ?>
                        <input type="hidden" name="id" value="<?php echo (int) $editing["ProjectID"]; ?>">
                        <input type="hidden" name="existing_image" value="<?php echo e($editing["Image"] ?? ""); ?>">
                    <?php endif; ?>

                    <div class="form-row">
                        <div>
                            <label>Title</label>
                            <input type="text" name="title" required value="<?php echo e($editing["Title"] ?? ""); ?>">
                        </div>
                        <div>
                            <label>Project link</label>
                            <input type="url" name="project_link" value="<?php echo e($editing["ProjectLink"] ?? ""); ?>" placeholder="https://example.com">
                        </div>
                    </div>

                    <div class="form-row">
                        <div>
                            <label>Project image</label>
                            <input type="file" name="image" accept="image/*">
                            <?php if (!empty($editing["Image"] ?? "")): ?>
                                <div style="margin-top: 0.8rem;">
                                    <img src="<?php echo e($editing["Image"]); ?>" alt="Current project image" style="max-width: 140px; border-radius: 10px; display: block;">
                                    <small class="muted">Choose a new file to replace the current image.</small>
                                </div>
                            <?php else: ?>
                                <small class="muted">Upload a project image from your computer.</small>
                            <?php endif; ?>
                        </div>
                    </div>

                    <label>Description</label>
                    <textarea name="description" rows="4" required><?php echo e($editing["Description"] ?? ""); ?></textarea>

                    <div class="row-actions">
                        <button type="submit" class="btn-primary"><?php echo $editing ? "Save changes" : "Add project"; ?></button>
                        <?php if ($editing): ?>
                            <a href="admin_projects.php" class="btn-small">Cancel</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <div class="admin-panel">
                <h2>All projects</h2>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($projects && $projects->num_rows > 0): ?>
                            <?php while ($p = $projects->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($p["Image"])): ?>
                                            <img src="<?php echo e($p["Image"]); ?>" alt="<?php echo e($p["Title"]); ?>" style="max-width: 90px; border-radius: 8px;">
                                        <?php else: ?>
                                            <span class="muted">No image</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e($p["Title"]); ?></td>
                                    <td class="truncate"><?php echo e($p["Description"]); ?></td>
                                    <td>
                                        <span class="badge <?php echo $p["Status"] === "Visible" ? "badge-green" : "badge-gray"; ?>"><?php echo e($p["Status"]); ?></span>
                                    </td>
                                    <td class="row-actions">
                                        <a href="admin_projects.php?edit=<?php echo (int) $p["ProjectID"]; ?>" class="btn-small">Edit</a>

                                        <form method="POST">
                                            <input type="hidden" name="action" value="toggle">
                                            <input type="hidden" name="id" value="<?php echo (int) $p["ProjectID"]; ?>">
                                            <button type="submit" class="btn-small"><?php echo $p["Status"] === "Visible" ? "Hide" : "Show"; ?></button>
                                        </form>

                                        <form method="POST" onsubmit="return confirm('Delete this project?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo (int) $p["ProjectID"]; ?>">
                                            <button type="submit" class="btn-small btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5">No projects yet.</td></tr>
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
