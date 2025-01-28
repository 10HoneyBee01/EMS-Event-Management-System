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
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $event_id = intval($_POST['event_id']);

    if (!empty($name) && !empty($email) && !empty($event_id)) {
        // Check if the event has reached its maximum capacity
        $stmt = $conn->prepare("
            SELECT 
                COUNT(a.id) AS total_attendees, 
                e.max_capacity 
            FROM events e 
            LEFT JOIN attendees a ON e.id = a.event_id 
            WHERE e.id = ?
            GROUP BY e.id
        ");
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $event = $result->fetch_assoc();

        if ($event) {
            $total_attendees = $event['total_attendees'];
            $max_capacity = $event['max_capacity'];

            if ($total_attendees >= $max_capacity) {
                $message = "<div class='alert alert-danger'>This event has reached its maximum capacity. Registration is not allowed.</div>";
            } else {
                // Insert the new attendee
                $stmt = $conn->prepare("INSERT INTO attendees (name, email, event_id) VALUES (?, ?, ?)");
                $stmt->bind_param("ssi", $name, $email, $event_id);

                if ($stmt->execute()) {
                    $message = "<div class='alert alert-success'>Registration successful!</div>";
                } else {
                    $message = "<div class='alert alert-danger'>Error: " . htmlspecialchars($stmt->error) . "</div>";
                }
                $stmt->close();
            }
        } else {
            $message = "<div class='alert alert-danger'>Invalid event selected.</div>";
        }
    } else {
        $message = "<div class='alert alert-warning'>All fields are required!</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendee Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
    /* General body styling */
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
        background: rgba(0, 0, 0, 0.4);
        /* Dark overlay to enhance readability */
        z-index: 1;
    }

    /* Header styling */
    header {
        position: sticky;
        top: 0;
        width: 100%;
        background-color: #212529;
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
        /* Adjust to prevent overlap with the header */
        left: 0;
        padding-top: 20px;
        overflow-y: auto;
        z-index: 1000;
        /* Ensure it stays above the content */
        background-color: #343a40;
    }

    .sidebar a {
        color: white;
        text-decoration: none;
        padding: 10px;
        display: block;
    }

    .sidebar a:hover {
        background-color: #ff7f50;
        /* Coral for hover effect */
    }

    /* Main content container */
    .main-content {
        margin-left: 270px;
        /* Matches the width of the sidebar */
        margin-top: 70px;
        /* Matches the height of the header */
        padding: 20px;
        z-index: 2;
        position: relative;
    }

    /* Card Styling */
    .card {
        background: #ffffff;
        border: none;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        border-radius: 15px;
    }

    /* Card Header Styling */
    .card-header {
        border-top-left-radius: 15px;
        border-top-right-radius: 15px;
        font-weight: 600;
        background: #4caf50;
        color: #fff;
    }

    /* Input and button styles */
    .form-control {
        border-radius: 10px;
        border: 1px solid #ced4da;
        background: #f8f9fa;
    }

    .form-select {
        border-radius: 10px;
        background: #f8f9fa;
    }

    .btn-primary {
        background-color: #00bcd4;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        color: #fff;
        transition: background-color 0.3s ease;
    }

    .btn-primary:hover {
        background-color: #0097a7;
    }

    /* Footer styling */
    footer {
        text-align: center;
        margin-top: 50px;
        padding: 10px 20px;
        font-size: 0.875rem;
        color: #6c757d;
        z-index: 2;
        position: relative;
    }

    /* Responsive styling */
    @media (max-width: 768px) {
        .sidebar {
            width: 100%;
            height: auto;
            position: relative;
        }

        .main-content {
            margin-left: 0;
            margin-top: 100px;
        }
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

    <!-- Main Content -->
    <div class="main-content">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h4>Attendee Registration</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($message) echo $message; ?>
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name"
                                    placeholder="Enter your name" required aria-label="Name">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    placeholder="Enter your email" required aria-label="Email">
                            </div>
                            <div class="mb-3">
                                <label for="event_id" class="form-label">Select Event</label>
                                <select class="form-select" id="event_id" name="event_id" required
                                    aria-label="Event Selection">
                                    <option value="">-- Select an Event --</option>
                                    <?php
                                    // Fetch events created by the logged-in user
                                    $user_id = $_SESSION['user_id']; // Get the logged-in user ID
                                    $stmt = $conn->prepare("SELECT id, name FROM events WHERE created_by = ?");
                                    $stmt->bind_param("i", $user_id); // Bind the user ID to the query
                                    $stmt->execute();
                                    $result = $stmt->get_result();

                                    // Display events created by the user
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['name']) . "</option>";
                                    }
                                    $stmt->close();
                                    ?>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Register</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>