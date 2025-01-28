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

// Assuming the admin is logged in and their user ID is stored in the session
$user_id = $_SESSION['user_id']; // Admin is logged in

// Pagination logic
$limit = 5; // Number of events per page
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Handle event deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_event_id'])) {
    $event_id = $_POST['delete_event_id'];
    // Prepare statement to delete the event
    $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
    $stmt->bind_param("i", $event_id); // Admin can delete any event

    // Execute the deletion and display a success or error message
    if ($stmt->execute()) {
        $success_message = "Event deleted successfully!";
    } else {
        $error_message = "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Handle attendees list download
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['download_event_id'])) {
    $event_id = $_GET['download_event_id'];

    // Fetch event name
    $event_stmt = $conn->prepare("SELECT name FROM events WHERE id = ?");
    $event_stmt->bind_param("i", $event_id);
    $event_stmt->execute();
    $event_result = $event_stmt->get_result();
    $event = $event_result->fetch_assoc();
    $event_name = $event['name'];
    $event_stmt->close();

    // Fetch attendees for the event
    $attendee_stmt = $conn->prepare("SELECT name, email FROM attendees WHERE event_id = ?");
    $attendee_stmt->bind_param("i", $event_id);
    $attendee_stmt->execute();
    $attendee_result = $attendee_stmt->get_result();

    // Set headers to initiate a file download
    header("Content-Type: text/csv");
    header("Content-Disposition: attachment; filename=attendees_event_{$event_id}.csv");
    header("Pragma: no-cache");
    header("Expires: 0");

    // Clean any output buffer to avoid unintended content
    ob_clean();
    flush();

    // Open the output stream
    $output = fopen("php://output", "w");

    // Write the event name as the first row
    fputcsv($output, ["Event Name", $event_name]);
    fputcsv($output, []); // Add an empty row for better readability

    // Write the attendee header row
    fputcsv($output, ["Name", "Email"]);

    // Write attendee data to the CSV file
    while ($row = $attendee_result->fetch_assoc()) {
        fputcsv($output, $row);
    }

    // Close the statement and the output stream
    $attendee_stmt->close();
    fclose($output);
    exit();
}

// Search functionality for events
$search_term = isset($_POST['search_term']) ? "%" . htmlspecialchars($_POST['search_term']) . "%" : null;
if ($search_term) {
    $stmt = $conn->prepare("SELECT * FROM events WHERE (name LIKE ? OR description LIKE ?) LIMIT ? OFFSET ?");
    $stmt->bind_param("ssii", $search_term, $search_term, $limit, $offset);
} else {
    $stmt = $conn->prepare("SELECT * FROM events LIMIT ? OFFSET ?");
    $stmt->bind_param("ii", $limit, $offset);
}
$stmt->execute();
$event_result = $stmt->get_result();

// Get total number of events for pagination
$total_stmt = $conn->prepare("SELECT COUNT(*) FROM events");
$total_stmt->execute();
$total_result = $total_stmt->get_result();
$total_row = $total_result->fetch_row();
$total_events = $total_row[0];
$total_pages = ceil($total_events / $limit);

$stmt->close();
$total_stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* General Body Styling */
        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            box-sizing: border-box;
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
        }

        /* Main content container */
        .main-content {
            margin-left: 250px;
            margin-top: 60px;
            padding: 20px;
        }

        /* Card Styling */
        .card {
            border: none;
            border-radius: 15px;
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .card h5 {
            font-size: 1.25rem;
            margin-bottom: 15px;
        }

        .list-unstyled li {
            font-size: 0.9rem;
            color: #fff;
            margin-bottom: 8px;
        }

        footer {
            text-align: center;
            margin-top: 50px;
            padding: 10px 20px;
            font-size: 0.875rem;
            color: #6c757d;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <header>
        <?php include '../admin/header.php'; ?>
    </header>

    <!-- Sidebar -->
    <div class="sidebar">
        <?php include '../admin/sidebar.php'; ?>
    </div>

    <div class="main-content">
        <h1 class="mb-4 text-center">All Events</h1>
        <!-- Search Form -->
        <form method="POST" action="events.php" class="mb-4">
            <div class="input-group">
                <input type="text" class="form-control" name="search_term" placeholder="Search by Name or Description">
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
        </form>
        <!-- Display success or error messages -->
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

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Registreation ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Max Capacity</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch events associated with the logged-in user
                while ($row = $event_result->fetch_assoc()) {
                    $event_id = $row['id'];

                    // Fetch attendee count for the event
                    $attendee_count_stmt = $conn->prepare("SELECT COUNT(*) as attendee_count FROM attendees WHERE event_id = ?");
                    $attendee_count_stmt->bind_param("i", $event_id);
                    $attendee_count_stmt->execute();
                    $attendee_count_result = $attendee_count_stmt->get_result();
                    $attendee_data = $attendee_count_result->fetch_assoc();
                    $attendee_count = $attendee_data['attendee_count'];
                    $attendee_count_stmt->close();

                    // Calculate the percentage of attendees relative to max capacity
                    $max_capacity = $row['max_capacity'];
                    $progress_percentage = ($max_capacity > 0) ? min(($attendee_count / $max_capacity) * 100, 100) : 0;

                    // Determine progress bar color
                    if ($progress_percentage == 100) {
                        $progress_color = 'bg-danger'; // Red for full
                    } elseif ($progress_percentage >= 51) {
                        $progress_color = 'bg-warning'; // Orange for almost full
                    } else {
                        $progress_color = 'bg-success'; // Green for less than 50% full
                    }

                    echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['name']}</td>
                <td>{$row['description']}</td>
                <td>{$row['max_capacity']}</td>
                <td>
                    <div class='d-flex align-items-center'>
                        <div class='progress' style='width: 80%; margin-right: 10px;'>
                            <div class='progress-bar $progress_color' role='progressbar' style='width: {$progress_percentage}%;' aria-valuenow='{$progress_percentage}' aria-valuemin='0' aria-valuemax='100'>
                                {$attendee_count}/{$max_capacity}
                            </div>
                        </div>
                        <span>{$progress_percentage}%</span>
                    </div>
                </td>
                <td>
                    <!-- Delete Button -->
                    <form method='POST' style='display:inline-block;'>
                        <input type='hidden' name='delete_event_id' value='{$row['id']}'>
                        <button type='submit' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this event?\");'>Delete</button>
                    </form>
                    <!-- Download Button -->
                    <form method='GET' action='' style='display:inline-block;'>
                        <input type='hidden' name='download_event_id' value='{$row['id']}'>
                        <button type='submit' class='btn btn-primary btn-sm'>Download Attendees</button>
                    </form>
                </td>
            </tr>";
                }
                ?>
            </tbody>

        </table>
        <nav aria-label="Page navigation example">
            <ul class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                    <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                        <a class="page-link" href="events.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>