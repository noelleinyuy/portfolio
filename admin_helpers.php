<?php

session_start();

function save_uploaded_image(array $file, string $directory = "images"): mixed
{
    if (($file["error"] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return null;
    }

    $tmpName = $file["tmp_name"] ?? "";
    $originalName = basename($file["name"] ?? "");

    if ($tmpName === "" || !is_uploaded_file($tmpName) || $originalName === "") {
        return null;
    }

    $allowedTypes = ["image/jpeg", "image/png", "image/gif", "image/webp"];
    $fileType = mime_content_type($tmpName);

    if (!in_array($fileType, $allowedTypes, true) && !preg_match('/\.(jpe?g|png|gif|webp)$/i', $originalName)) {
        return false;
    }

    $targetDirectory = __DIR__ . "/" . ltrim($directory, "/");
    if (!is_dir($targetDirectory)) {
        mkdir($targetDirectory, 0777, true);
    }

    $safeName = time() . "_" . preg_replace('/[^A-Za-z0-9._-]+/', '_', $originalName);
    $targetPath = $targetDirectory . "/" . $safeName;

    if (!move_uploaded_file($tmpName, $targetPath)) {
        return null;
    }

    return rtrim($directory, "/") . "/" . $safeName;
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

function ensure_testimonial_rating_column(mysqli $conn): void
{
    if (!column_exists($conn, "portfolio_testimonials", "Rating")) {
        $conn->query("ALTER TABLE portfolio_testimonials ADD COLUMN Rating TINYINT UNSIGNED NOT NULL DEFAULT 5 AFTER Message");
    }
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