<?php
require '../database/db.php';
session_start();
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get new user data from the form
    $name = $_POST['name'];
    $email = $_POST['email'];
    $image_path = ''; // Default in case no image is uploaded

    // Check if a new image is uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image_name = $_FILES['image']['name'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_extension = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));

        // Define allowed image extensions
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($image_extension, $allowed_extensions)) {
            // Define the upload folder
            $upload_dir = '../uploads/images/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            // Create a unique file name for the image
            $new_image_name = uniqid() . '.' . $image_extension;

            // Move the uploaded file to the server
            if (move_uploaded_file($image_tmp_name, $upload_dir . $new_image_name)) {
                $image_path = $upload_dir . $new_image_name; // Store the image path
            } else {
                echo json_encode(['error' => 'Error uploading the image.']);
                exit;
            }
        } else {
            echo json_encode(['error' => 'Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.']);
            exit;
        }
    }

    // Prepare the SQL update statement
    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, image_path = ? WHERE id = ?");
    $stmt->bind_param("sssi", $name, $email, $image_path, $user_id);

    // Execute the statement and check if the update was successful
    if ($stmt->execute()) {
        // Return success message and updated data as JSON
        echo json_encode([
            'success' => true,
            'message' => 'Profile updated successfully!',
            'name' => $name,
            'email' => $email,
            'image_path' => $image_path ? $image_path : 'uploads/images/default_user_image.jpg'
        ]);
    } else {
        echo json_encode(['error' => 'Error updating the profile.']);
    }

    $stmt->close();
}