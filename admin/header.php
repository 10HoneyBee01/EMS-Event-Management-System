<?php
require '../database/db.php';


// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get the current user's ID
$user_id = $_SESSION['user_id'];

// Prepare the SQL query to fetch the user details including image path
$stmt = $conn->prepare("SELECT id, name, email, is_admin, image_path FROM users WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($id, $name, $email, $is_admin, $image_path);
$stmt->fetch();
$stmt->close();

// Set the user role (default to 'user' if not set)
$user_role = ($is_admin) ? 'admin' : 'user';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    .header {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 60px;
        background-color: #343a40;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 20px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        z-index: 1000;
    }

    .header .user-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .header .user-info img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }

    .header .user-info span {
        font-size: 16px;
        font-weight: 500;
    }

    .header a.btn {
        font-size: 14px;
    }

    body {
        margin-top: 60px;
    }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <div class="user-info">
            <img src="<?php echo htmlspecialchars($image_path ? $image_path : '../uploads/images/img.png'); ?>"
                alt="User Image">
            <span>Welcome, <strong><?php echo htmlspecialchars($name); ?></strong></span>
        </div>
        <a href="../logout.php" class="btn btn-danger">Logout</a>
    </div>
</body>

</html>

</html>