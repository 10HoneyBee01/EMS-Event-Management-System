<?php
require '../database/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized access.']);
    exit;
}

// Parse the JSON request body
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['userId']) || empty($data['userId'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid request.']);
    exit;
}

$userId = (int)$data['userId'];

// Prevent admins from deleting themselves
if ($userId === $_SESSION['user_id']) {
    echo json_encode(['success' => false, 'error' => 'You cannot delete yourself.']);
    exit;
}

// Check if the user exists
$stmt = $conn->prepare("SELECT id, is_admin FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    echo json_encode(['success' => false, 'error' => 'User not found.']);
    exit;
}

$stmt->bind_result($id, $is_admin);
$stmt->fetch();
$stmt->close();

// Delete the user
$stmt_delete = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt_delete->bind_param("i", $userId);

if ($stmt_delete->execute()) {
    echo json_encode(['success' => true, 'message' => 'User deleted successfully.']);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to delete the user.']);
}

$stmt_delete->close();
$conn->close();