<?php

// Requires $conn (from db.php) and an active session to already exist
// before these are called.

function track_visit(mysqli $conn, string $pageUrl): void
{
    $sessionId = session_id();
    $ip = $_SERVER["REMOTE_ADDR"] ?? "";
    $userAgent = substr($_SERVER["HTTP_USER_AGENT"] ?? "", 0, 255);
    $referrer = substr($_SERVER["HTTP_REFERER"] ?? "", 0, 255);

    try {
        $stmt = $conn->prepare("
            INSERT INTO portfolio_visits (PageURL, IPAddress, UserAgent, Referrer, SessionID)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("sssss", $pageUrl, $ip, $userAgent, $referrer, $sessionId);
        $stmt->execute();
        $stmt->close();
    } catch (\mysqli_sql_exception $e) {
        // Tracking should never break the site — fail silently.
    }
}

function track_action(mysqli $conn, string $actionType, string $detail = ""): void
{
    $sessionId = session_id();

    try {
        $stmt = $conn->prepare("
            INSERT INTO portfolio_visitor_actions (SessionID, ActionType, ActionDetail)
            VALUES (?, ?, ?)
        ");
        $stmt->bind_param("sss", $sessionId, $actionType, $detail);
        $stmt->execute();
        $stmt->close();
    } catch (\mysqli_sql_exception $e) {
        // Fail silently.
    }
}