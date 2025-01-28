<?php
require '../database/db.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set the correct timezone (adjust as needed)
date_default_timezone_set('Asia/Dhaka');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// Store the login time in the session if not already set
if (!isset($_SESSION['login_time'])) {
    $_SESSION['login_time'] = time();
}

// Format the login time for display
$login_time = date("h:i:s A", $_SESSION['login_time']);

// Get the logged-in user's ID and sanitize it
$user_id = intval($_SESSION['user_id']); // Convert to integer for security

// Fetch events created by the logged-in user using prepared statement
$stmt = $conn->prepare("SELECT name FROM events WHERE created_by = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$events_result = $stmt->get_result();
$events_created_by_user = [];
while ($row = $events_result->fetch_assoc()) {
    $events_created_by_user[] = $row['name'];
}
$stmt->close();

// Fetch total attendees for events created by the logged-in user using prepared statement
$stmt = $conn->prepare("
    SELECT COUNT(*) AS total_attendees
    FROM attendees
    WHERE event_id IN (
        SELECT id
        FROM events
        WHERE created_by = ?
    )
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$attendees_result = $stmt->get_result();
$total_attendees_registered = $attendees_result->fetch_assoc()['total_attendees'] ?? 0;
$stmt->close();

// Handle errors or edge cases
if ($conn->error) {
    echo "Error: " . htmlspecialchars($conn->error);
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
        /* Dark overlay */
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
        left: 0;
        padding-top: 20px;
        overflow-y: auto;
        z-index: 1000;
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

    /* Top Navbar */
    .navbar {
        background-color: #007bff;
        color: white;
    }

    .navbar-brand {
        color: white;
        font-weight: bold;
    }

    .navbar-brand:hover {
        color: #f8f9fa;
    }

    /* Main Content Styling */
    .content {
        margin-left: 250px;
        padding: 30px;
        z-index: 2;
        position: relative;
    }

    .card {
        border-radius: 10px;
        border: 1px solid #ddd;
        background-color: #ffffff;
        z-index: 2;
        position: relative;
    }

    .card-title {
        font-size: 1.2rem;
        font-weight: 600;
        color: #007bff;
        /* Deep blue for titles */
    }

    .card-body {
        color: #212529;
    }

    .card-body h5 {
        color: #ff7f50;
        /* Bright orange for section headings */
    }

    .table thead {
        background-color: #007bff;
        color: white;
    }

    .table tbody tr:hover {
        background-color: #ff7f50;
        /* Light orange on hover for rows */
        color: #fff;
    }

    .table th,
    .table td {
        padding: 12px;
        text-align: left;
    }

    /* Footer styling */
    footer {
        text-align: center;
        margin-top: 50px;
        padding: 10px 20px;
        font-size: 0.875rem;
        color: #6c757d;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .sidebar {
            width: 100%;
            height: auto;
            position: relative;
        }

        .content {
            margin-left: 0;
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
    <div class="content">
        <h1 class="mb-4">Dashboard</h1>

        <!-- Time Details -->
        <div class="row mb-4">
            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Time Details</h5>
                        <p><strong>Current Time:</strong> <span id="currentTime"></span></p>
                        <p><strong>Login Time:</strong> <?php echo $login_time; ?></p>
                        <p><strong>Time Spent:</strong> <span id="timeSpent"></span></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dashboard Statistics -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h5 class="card-title">Total Attendees Registered</h5>
                        <h3 class="text-success fw-bold"><?php echo htmlspecialchars($total_attendees_registered); ?>
                        </h3>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Events Created</h5>
                        <table class="table table-bordered table-hover mt-3">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Event Name</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($events_created_by_user as $index => $event) {
                                    echo '<tr><td>' . ($index + 1) . '</td><td>' . htmlspecialchars($event) . '</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
    // Display current time
    function updateTime() {
        const currentTime = new Date();
        document.getElementById('currentTime').innerText = currentTime.toLocaleTimeString();
    }
    setInterval(updateTime, 1000);

    // Calculate and display time spent
    const loginTime = <?php echo $_SESSION['login_time']; ?> * 1000;

    function updateTimeSpent() {
        const now = new Date().getTime();
        const timeSpent = Math.floor((now - loginTime) / 1000);
        const hours = Math.floor(timeSpent / 3600);
        const minutes = Math.floor((timeSpent % 3600) / 60);
        const seconds = timeSpent % 60;

        document.getElementById('timeSpent').innerText =
            `${hours}h ${minutes}m ${seconds}s`;
    }
    setInterval(updateTimeSpent, 1000);
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>