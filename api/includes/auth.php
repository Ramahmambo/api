<?php
session_start();

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if user is an admin
function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === 1;
}

// Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ../pages/login.php');
        exit;
    }
}

// Redirect if not admin (allow if no admin exists yet)
function requireAdmin() {
    global $conn;

    // Allow access if no admin exists in DB (bootstrap logic)
    try {
        $stmt = $conn->query("SELECT COUNT(*) FROM users WHERE is_admin = 1");
        $adminCount = $stmt->fetchColumn();

        if ($adminCount == 0) {
            return; // Allow admin setup
        }
    } catch (PDOException $e) {
        // Fail-safe redirect if DB fails
        header('Location: ../pages/login.php');
        exit;
    }

    // If not admin, redirect to index
    if (!isAdmin()) {
        header('Location: ../pages/index.php');
        exit;
    }
}

// Get current user data
function getCurrentUser() {
    global $conn;

    if (!isLoggedIn()) {
        return null;
    }

    try {
        $stmt = $conn->prepare("SELECT id, username, email, balance, is_admin FROM users WHERE id = :id");
        $stmt->bindParam(':id', $_SESSION['user_id']);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        return null;
    } catch (PDOException $e) {
        return null;
    }
}