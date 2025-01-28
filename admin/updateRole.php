<?php
require '../database/db.php';

// Ensure the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the raw POST data from the request body
    $data = json_decode(file_get_contents('php://input'), true);

    // Check if userId and action are provided in the request
    if (isset($data['userId']) && isset($data['action'])) {
        $userId = $data['userId'];
        $action = $data['action'];

        // Check if the user exists in the database
        $stmt = $conn->prepare("SELECT is_admin FROM users WHERE id = ?");
        if (!$stmt) {
            echo json_encode(['success' => false, 'error' => 'SQL error: ' . $conn->error]);
            exit;
        }

        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($is_admin);
            $stmt->fetch();
            $stmt->close();

            // Perform the promote/demote action
            if ($action === 'promote' && !$is_admin) {
                // Promote user to admin
                $stmt_update = $conn->prepare("UPDATE users SET is_admin = 1 WHERE id = ?");
                if (!$stmt_update) {
                    echo json_encode(['success' => false, 'error' => 'SQL error: ' . $conn->error]);
                    exit;
                }

                $stmt_update->bind_param("i", $userId);
                $stmt_update->execute();
                $stmt_update->close();
                echo json_encode(['success' => true, 'message' => 'User promoted to Admin']);
            } elseif ($action === 'demote' && $is_admin) {
                // Demote user to regular user
                $stmt_update = $conn->prepare("UPDATE users SET is_admin = 0 WHERE id = ?");
                if (!$stmt_update) {
                    echo json_encode(['success' => false, 'error' => 'SQL error: ' . $conn->error]);
                    exit;
                }

                $stmt_update->bind_param("i", $userId);
                $stmt_update->execute();
                $stmt_update->close();
                echo json_encode(['success' => true, 'message' => 'User demoted to regular user']);
            } else {
                echo json_encode(['success' => false, 'error' => 'Invalid action or no change needed']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'User not found']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid parameters']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}