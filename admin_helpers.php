<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function require_admin_login(): void
{
    if (!isset($_SESSION["admin_id"])) {
        header("Location: admin_login.php");
        exit;
    }
}

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

function flash(string $type, string $message): void
{
    $_SESSION["flash"] = ["type" => $type, "message" => $message];
}

function get_flash(): ?array
{
    if (!empty($_SESSION["flash"])) {
        $flash = $_SESSION["flash"];
        unset($_SESSION["flash"]);
        return $flash;
    }
    return null;
}

function column_exists(mysqli $conn, string $table, string $column): bool
{
    $safeTable = $conn->real_escape_string($table);
    $safeColumn = $conn->real_escape_string($column);
    $result = $conn->query("SHOW COLUMNS FROM `$safeTable` LIKE '$safeColumn'");
    return $result && $result->num_rows > 0;
}

function get_setting(mysqli $conn, string $name, string $default = ""): string
{
    $stmt = $conn->prepare("SELECT SettingValue FROM portfolio_settings WHERE SettingName = ?");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();
    $value = $default;
    if ($row = $result->fetch_assoc()) {
        $value = $row["SettingValue"] ?? $default;
    }
    $stmt->close();
    return $value;
}

function set_setting(mysqli $conn, string $name, string $value): void
{
    $stmt = $conn->prepare("
        INSERT INTO portfolio_settings (SettingName, SettingValue)
        VALUES (?, ?)
        ON DUPLICATE KEY UPDATE SettingValue = VALUES(SettingValue)
    ");
    $stmt->bind_param("ss", $name, $value);
    $stmt->execute();
    $stmt->close();
}

/**
 * Handles an uploaded image from $_FILES[$fieldName]. Validates it's a real
 * image (not just a renamed file), saves it under $uploadDir with a unique
 * name, and returns the relative web path to store in the database.
 * Returns null if no file was chosen or it failed validation — callers
 * should fall back to keeping the existing value in that case.
 */
function handle_image_upload(string $fieldName, string $uploadDir = "images/uploads"): ?string
{
    if (empty($_FILES[$fieldName]) || $_FILES[$fieldName]["error"] !== UPLOAD_ERR_OK) {
        return null;
    }

    $allowedExtensions = ["jpg", "jpeg", "png", "gif", "webp"];
    $originalName = $_FILES[$fieldName]["name"];
    $tmpPath = $_FILES[$fieldName]["tmp_name"];
    $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    if (!in_array($extension, $allowedExtensions, true)) {
        return null;
    }

    // Confirm it's actually a valid image, not just a file with a fake extension.
    if (@getimagesize($tmpPath) === false) {
        return null;
    }

    if ($_FILES[$fieldName]["size"] > 5 * 1024 * 1024) {
        return null;
    }

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $safeName = uniqid("img_", true) . "." . $extension;
    $destination = rtrim($uploadDir, "/") . "/" . $safeName;

    if (move_uploaded_file($tmpPath, $destination)) {
        return $destination;
    }

    return null;
}

/**
 * Adds the Rating column to portfolio_testimonials if it doesn't already
 * exist. Safe to call on every page load — it checks first, so it never
 * re-runs the ALTER once the column is there.
 */
function ensure_testimonial_rating_column(mysqli $conn): void
{
    if (!column_exists($conn, "portfolio_testimonials", "Rating")) {
        $conn->query("ALTER TABLE portfolio_testimonials ADD COLUMN Rating TINYINT UNSIGNED NOT NULL DEFAULT 5");
    }
}