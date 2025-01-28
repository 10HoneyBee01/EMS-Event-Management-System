<?php
require '../database/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $date = $_POST['date'];
    $max_capacity = $_POST['max_capacity'];
    $created_by = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO events (name, description, date, max_capacity, created_by) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssii", $name, $description, $date, $max_capacity, $created_by);
    if ($stmt->execute()) {
        $success_message = "Event created successfully!";
    } else {
        $error_message = "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
    /* General Body Styling */
    body {
        margin: 0;
        padding: 0;
        font-family: 'Arial', sans-serif;
        background: url('../uploads/images/nature.jpg') no-repeat center center fixed;
        background-size: cover;
        color: #fff;
        box-sizing: border-box;
        height: 100vh;
        /* Make sure body takes up full height */
        position: relative;
        /* Ensure overlay is correctly positioned */
    }

    /* Overlay for background */
    body::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        /* Darker overlay, more transparent */
        z-index: 1;
        /* Ensures overlay stays below content */
    }

    /* Header styling */
    header {
        position: sticky;
        top: 0;
        width: 100%;
        background-color: rgba(33, 37, 41, 0.8);
        /* Transparent dark background */
        color: #fff;
        z-index: 10;
        padding: 10px 20px;
        text-align: center;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }

    /* Sidebar styling */
    .sidebar {
        width: 230px;
        height: 100vh;
        position: fixed;
        top: 50px;
        left: 0;
        padding-top: 20px;
        overflow-y: auto;
        z-index: 1000;
        background-color: rgba(52, 58, 64, 0.9);
        /* Slight transparency for sidebar */
    }

    .sidebar a {
        color: white;
        text-decoration: none;
        padding: 10px;
        display: block;
        transition: background-color 0.3s ease;
    }

    .sidebar a:hover {
        background-color: #ff7f50;
        /* Coral for hover effect */
    }

    /* Main content container */
    .main-content {
        margin-left: 250px;
        margin-top: 60px;
        padding: 20px;
        z-index: 2;
        /* Ensures it stays above the overlay */
        position: relative;
    }

    /* Text color and contrast in main content */
    .main-content h1,
    .main-content p {
        color: #fff;
        /* White text for better contrast */
    }

    /* Optional: Ensure responsiveness */
    @media (max-width: 768px) {
        .sidebar {
            width: 100%;
            height: auto;
            position: relative;
        }

        .main-content {
            margin-left: 0;
        }
    }

    /* Footer styling */
    footer {
        text-align: center;
        margin-top: 50px;
        padding: 10px 20px;
        font-size: 0.875rem;
        color: #6c757d;
        z-index: 2;
        /* Ensure footer stays above the overlay */
    }

    /* Transparent Footer for better integration */
    footer p {
        color: rgba(255, 255, 255, 0.7);
        /* Slightly transparent white text */
        font-size: 0.9rem;
    }
    </style>
</head>

<body>
    <!-- Header -->
    <header>
        <?php include '../user/header.php'; ?>
    </header>

    <!-- Sidebar -->
    <div class="sidebar">
        <?php include '../user/sidebar.php'; ?>
    </div>

    <div class="main-content">
        <h1 class="text-center mb-4">Create New Event</h1>
        <?php if (isset($success_message)) : ?>
        <div class="alert alert-success">
            <?php echo $success_message; ?>
        </div>
        <?php endif; ?>
        <?php if (isset($error_message)) : ?>
        <div class="alert alert-danger">
            <?php echo $error_message; ?>
        </div>
        <?php endif; ?>

        <form method="POST" class="row g-3 shadow p-4 bg-light rounded">
            <div class="col-md-6">
                <label for="name" class="form-label">Event Name</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Enter event name" required>
            </div>
            <div class="col-md-6">
                <label for="date" class="form-label">Event Date</label>
                <input type="date" class="form-control" id="date" name="date" required>
            </div>
            <div class="col-md-12">
                <label for="description" class="form-label">Event Description</label>
                <textarea class="form-control" id="description" name="description" rows="4"
                    placeholder="Enter event description"></textarea>
            </div>
            <div class="col-md-6">
                <label for="max_capacity" class="form-label">Max Capacity</label>
                <input type="number" class="form-control" id="max_capacity" name="max_capacity"
                    placeholder="Enter maximum capacity" required>
            </div>
            <div class="col-md-12 text-center">
                <button type="submit" class="btn btn-primary">Create Event</button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>