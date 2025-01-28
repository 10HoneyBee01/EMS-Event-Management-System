<?php
require '../database/db.php';

// Handle User Deletion
if (isset($_GET['delete_user'])) {
    $user_id = intval($_GET['delete_user']);
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    header("Location: users.php"); // Redirect to refresh user list after deletion
    exit();
}

// Handle Role Change (Promote/Demote)
if (isset($_GET['change_role'])) {
    $user_id = intval($_GET['change_role']);
    $current_role = intval($_GET['current_role']); // Get current role from URL

    // Toggle the role
    $new_role = ($current_role == 1) ? 0 : 1; // If admin, set to regular user; if user, set to admin
    $stmt = $conn->prepare("UPDATE users SET is_admin = ? WHERE id = ?");
    $stmt->bind_param("ii", $new_role, $user_id);
    $stmt->execute();
    header("Location: users.php"); // Redirect after updating the role
    exit();
}

// Fetch user details
$users = $conn->query("SELECT * FROM users");

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">User Management</h1>

        <!-- User List Table -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $users->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo $user['name']; ?></td>
                    <td><?php echo $user['email']; ?></td>
                    <td>
                        <?php 
                            // Display "Admin" or "User" based on the is_admin column value
                            echo $user['is_admin'] == 1 ? 'Admin' : 'User';
                        ?>
                    </td>
                    <td>
                        <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="users.php?delete_user=<?php echo $user['id']; ?>" class="btn btn-danger btn-sm"
                            onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                        <a href="users.php?change_role=<?php echo $user['id']; ?>&current_role=<?php echo $user['is_admin']; ?>"
                            class="btn btn-info btn-sm">
                            <?php echo $user['is_admin'] == 1 ? 'Demote' : 'Promote'; ?>
                        </a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <footer class="bg-light text-center py-3 mt-5">
        <p>&copy; <?php echo date("Y"); ?> Event Management System. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>