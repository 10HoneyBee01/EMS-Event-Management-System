<?php
require '../database/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
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
        echo "Event created successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Create Event</title>
</head>

<body>
    <form method="POST">
        <input type="text" name="name" placeholder="Event Name" required>
        <textarea name="description" placeholder="Event Description"></textarea>
        <input type="date" name="date" required>
        <input type="number" name="max_capacity" placeholder="Max Capacity" required>
        <button type="submit">Create Event</button>
    </form>
</body>

</html>
</body>

</html>