<?php
ob_start();
// Include database connection
require '../database/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// Handle Attendee Deletion
if (isset($_GET['delete_attendee'])) {
    $attendee_id = intval($_GET['delete_attendee']);
    $stmt = $conn->prepare("DELETE FROM attendees WHERE id = ?");
    $stmt->bind_param("i", $attendee_id);
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Attendee deleted successfully.";
    } else {
        $_SESSION['error_message'] = "Failed to delete attendee.";
    }
    header("Location: attendees.php");
    exit();
}

// Handle Attendee Addition
if (isset($_POST['add_attendee'])) {
    $event_id = intval($_POST['event_id']);
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);

    $stmt = $conn->prepare("INSERT INTO attendees (event_id, name, email) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $event_id, $name, $email);
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Attendee added successfully.";
    } else {
        $_SESSION['error_message'] = "Failed to add attendee.";
    }
    header("Location: attendees.php");
    exit();
}

// Handle Attendee Update
if (isset($_POST['update_attendee'])) {
    $attendee_id = intval($_POST['attendee_id']);
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);

    $stmt = $conn->prepare("UPDATE attendees SET name = ?, email = ? WHERE id = ?");
    $stmt->bind_param("ssi", $name, $email, $attendee_id);
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Attendee updated successfully.";
    } else {
        $_SESSION['error_message'] = "Failed to update attendee.";
    }
    header("Location: attendees.php");
    exit();
}

// Fetch events and attendees
$events = $conn->query("SELECT * FROM events");
$attendees = $conn->query("SELECT * FROM attendees");

// Group attendees by event
$attendees_by_event = [];
while ($attendee = $attendees->fetch_assoc()) {
    $attendees_by_event[$attendee['event_id']][] = $attendee;
}
ob_end_flush(); // Send the buffered output
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendees</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
    /* General body styling */
    body {
        margin: 0;
        padding: 0;
        font-family: 'Arial', sans-serif;
        background-color: #f8f9fa;
        box-sizing: border-box;
        /* Added to prevent padding and border from affecting width */
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
    }

    /* Main content container */
    .main-content {
        margin-left: 250px;
        /* Matches the width of the sidebar */
        margin-top: 60px;
        /* Matches the height of the header */
        padding: 20px;
    }

    /* Container styling */
    .container {
        margin-left: 230px;
        /* Adjusted to match the sidebar width */
        padding-top: 20px;
        box-sizing: border-box;
        /* Added to prevent padding from affecting layout */
    }

    /* Media Query for mobile devices */
    @media (max-width: 768px) {
        .container {
            margin-left: 0;
        }

        .sidebar {
            width: 100%;
            /* Full width on smaller screens */
        }
    }


    /* Footer styling */
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
        <h1 class="text-center mb-4">Attendees by Event</h1>

        <!-- Search Form -->
        <form method="POST" action="attendees.php" class="mb-4">
            <div class="input-group">
                <input type="text" class="form-control" name="search_term" placeholder="Search by Name or Email">
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
        </form>

        <!-- Success and error messages -->
        <?php if (isset($_SESSION['success_message'])) : ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['success_message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])) : ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $_SESSION['error_message']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <!-- Loop through each event and display attendees -->
        <?php foreach ($events as $event) : ?>
        <div class="card mb-4 shadow-sm">
            <div class="card-header">
                <h5><?php echo htmlspecialchars($event['name']); ?> - Attendees</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Attendee Number</th>
                            <th>Attendee ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $event_id = $event['id'];
                            $limit = 5; // Number of attendees per page
                            $page_param = "page_$event_id"; // Unique pagination parameter for this event
                            $page = isset($_GET[$page_param]) ? intval($_GET[$page_param]) : 1;
                            $offset = ($page - 1) * $limit;

                            // Fetch attendees for this event with pagination
                            $search_term = isset($_POST['search_term']) ? "%" . htmlspecialchars($_POST['search_term']) . "%" : null;
                            if ($search_term) {
                                $stmt = $conn->prepare("SELECT * FROM attendees WHERE (name LIKE ? OR email LIKE ?) AND event_id = ? LIMIT ?, ?");
                                $stmt->bind_param("ssiii", $search_term, $search_term, $event_id, $offset, $limit);
                            } else {
                                $stmt = $conn->prepare("SELECT * FROM attendees WHERE event_id = ? LIMIT ?, ?");
                                $stmt->bind_param("iii", $event_id, $offset, $limit);
                            }
                            $stmt->execute();
                            $attendees_result = $stmt->get_result();

                            // Count total attendees for this event
                            $count_stmt = $conn->prepare("SELECT COUNT(*) FROM attendees WHERE event_id = ?");
                            if ($search_term) {
                                $count_stmt->bind_param("i", $event_id);
                            } else {
                                $count_stmt->bind_param("i", $event_id);
                            }
                            $count_stmt->execute();
                            $count_result = $count_stmt->get_result();
                            $total_row = $count_result->fetch_row();
                            $total_attendees = $total_row[0];
                            $total_pages = ceil($total_attendees / $limit);

                            // Display attendees
                            $attendeeNumber = $offset + 1; // Start attendee numbering from offset
                            while ($attendee = $attendees_result->fetch_assoc()) {
                                echo "<tr>
                                <td>{$attendeeNumber}</td>
                                <td>" . htmlspecialchars($attendee['id']) . "</td>
                                <td>" . htmlspecialchars($attendee['name']) . "</td>
                                <td>" . htmlspecialchars($attendee['email']) . "</td>
                                <td>
                                    <a href='attendees.php?delete_attendee=" . htmlspecialchars($attendee['id']) . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this attendee?\")'>
                                        <i class='fas fa-trash-alt'></i> Delete
                                    </a>
                                    <a href='#' class='btn btn-warning btn-sm' data-bs-toggle='modal' data-bs-target='#updateAttendeeModal' data-id='" . htmlspecialchars($attendee['id']) . "' data-name='" . htmlspecialchars($attendee['name']) . "' data-email='" . htmlspecialchars($attendee['email']) . "'>
                                        <i class='fas fa-edit'></i> Update
                                    </a>
                                </td>
                              </tr>";
                                $attendeeNumber++;
                            }
                            ?>
                    </tbody>
                </table>
            </div>
            <!-- Pagination links -->
            <nav aria-label="Pagination for event <?php echo $event_id; ?>">
                <ul class="pagination">
                    <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                    <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                        <a class="page-link" href="?<?php echo $page_param; ?>=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
        <?php endforeach; ?>

        <!-- Modal for updating attendee information -->
        <div class="modal fade" id="updateAttendeeModal" tabindex="-1" aria-labelledby="updateAttendeeModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateAttendeeModalLabel">Update Attendee</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="attendees.php">
                            <input type="hidden" name="attendee_id" id="attendee_id">
                            <div class="mb-3">
                                <label for="name" class="form-label">Attendee Name</label>
                                <input type="text" name="name" id="name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Attendee Email</label>
                                <input type="email" name="email" id="email" class="form-control" required>
                            </div>
                            <button type="submit" name="update_attendee" class="btn btn-primary">Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
        // JavaScript to handle the population of the modal fields for updating attendee
        var updateModal = document.getElementById('updateAttendeeModal');
        updateModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget; // Button that triggered the modal
            var attendeeId = button.getAttribute('data-id');
            var attendeeName = button.getAttribute('data-name');
            var attendeeEmail = button.getAttribute('data-email');

            var modalAttendeeId = updateModal.querySelector('#attendee_id');
            var modalName = updateModal.querySelector('#name');
            var modalEmail = updateModal.querySelector('#email');

            modalAttendeeId.value = attendeeId;
            modalName.value = attendeeName;
            modalEmail.value = attendeeEmail;
        });
        </script>
</body>

</html>