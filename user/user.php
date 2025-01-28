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

// Get the current user's ID
$user_id = $_SESSION['user_id'];

// Fetch the user details
$stmt = $conn->prepare("SELECT id, name, email, is_admin, image_path FROM users WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($id, $name, $email, $is_admin, $image_path);
$stmt->fetch();
$stmt->close();

// Determine user role
$user_role = $is_admin ? 'Admin' : 'User';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            /* Adjust to prevent overlap with the header */
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

        /* Main content container */
        .main-content {
            margin-left: 250px;
            /* Matches the width of the sidebar */
            margin-top: 60px;
            /* Matches the height of the header */
            padding: 20px;
            z-index: 2;
            position: relative;
        }

        /* Profile table and form styling */
        .table-container,
        .update-form {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-top: 20px;
        }

        /* Table styling */
        .table img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
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

        /* Responsive Design */
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
        <!-- User Table -->
        <div class="table-container">
            <h3 class="text-center">User Profile</h3>
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Profile Image</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo htmlspecialchars($id); ?></td>
                        <td>
                            <img src="<?php echo htmlspecialchars($image_path ? $image_path : 'uploads/images/default_user_image.jpg'); ?>"
                                alt="User Image">
                        </td>
                        <td><?php echo htmlspecialchars($name); ?></td>
                        <td><?php echo htmlspecialchars($email); ?></td>
                        <td><?php echo htmlspecialchars($user_role); ?></td>
                        <td>
                            <button class="btn btn-primary btn-sm" onclick="showUpdateForm()">Update</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Update Form -->
        <div class="update-form" id="updateForm" style="display: none;">
            <h4>Update Profile</h4>
            <form id="updateProfileForm" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name"
                        value="<?php echo htmlspecialchars($name); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email"
                        value="<?php echo htmlspecialchars($email); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="image" class="form-label">Profile Image</label>
                    <input type="file" class="form-control" id="image" name="image">
                </div>
                <button type="submit" class="btn btn-success">Save Changes</button>
                <button type="button" class="btn btn-secondary" onclick="hideUpdateForm()">Cancel</button>
            </form>
        </div>
    </div>

    <script>
        function showUpdateForm() {
            document.getElementById("updateForm").style.display = "block";
        }

        // Handle form submission with AJAX
        document.getElementById('updateProfileForm').addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent default form submission
            const formData = new FormData(this);

            fetch('../user/updateuserinfo.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update table dynamically
                        const tableRow = document.querySelector('tbody tr');
                        tableRow.children[1].querySelector('img').src = data.image_path;
                        tableRow.children[2].innerText = data.name;
                        tableRow.children[3].innerText = data.email;

                        // Hide the form and show success message
                        document.getElementById("updateForm").style.display = "none";
                        alert(data.message);
                    } else {
                        alert(data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('There was an issue with the request.');
                });
        });

        function hideUpdateForm() {
            const updateForm = document.getElementById("updateForm");
            if (updateForm) {
                updateForm.style.display = "none";
            }
        }
    </script>
</body>

</html>