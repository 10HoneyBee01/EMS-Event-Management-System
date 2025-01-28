<?php
require 'database/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password, is_admin FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($id, $hashed_password, $is_admin);
    $stmt->fetch();

    if (password_verify($password, $hashed_password)) {
        $_SESSION['user_id'] = $id;
        $_SESSION['is_admin'] = $is_admin;

        // Redirect based on role
        if ($is_admin == 1) {
            header("Location: admin/admin_dashboard.php"); // Redirect to Admin Dashboard
        } else {
            header("Location: user/user_dashboard.php"); // Redirect to User Dashboard
        }
        exit;
    } else {
        $error_message = "Invalid email or password!";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background: url('uploads/images/nature.jpg') no-repeat center center fixed;
        /* Replace with your background image URL */
        background-size: cover;
        font-family: 'Roboto', sans-serif;
        color: #fff;
        height: 100vh;
        margin: 0;
    }

    .overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        /* Dark overlay for contrast */
        z-index: 1;
    }

    .login-container {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100%;
        z-index: 2;
    }

    .login-card {
        background: rgba(0, 0, 0, 0.7);
        border-radius: 20px;
        padding: 40px 50px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        width: 100%;
        max-width: 500px;
        z-index: 2;
    }

    .login-card h2 {
        font-size: 2.5rem;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 30px;
        color: #f39c12;
    }

    .form-control {
        border-radius: 12px;
        padding: 15px;
        font-size: 1.1rem;
        margin-bottom: 20px;
    }

    .btn-primary {
        border-radius: 50px;
        font-size: 1.1rem;
        padding: 15px;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background: #0066cc;
        transform: scale(1.05);
    }

    .alert {
        border-radius: 10px;
        font-size: 1.1rem;
        margin-bottom: 20px;
    }

    .text-center {
        font-size: 1.1rem;
        margin-top: 20px;
    }

    footer {
        position: absolute;
        bottom: 10px;
        width: 100%;
        background: #1d1d1d;
        color: #aaa;
        padding: 10px;
        text-align: center;
    }

    footer p {
        margin: 0;
        font-size: 1rem;
    }

    @media (max-width: 768px) {
        .login-card {
            padding: 30px 40px;
        }

        .login-card h2 {
            font-size: 2rem;
        }

        .btn-primary {
            font-size: 1rem;
            padding: 12px;
        }
    }
    </style>
</head>

<body>
    <div class="overlay"></div>
    <div class="login-container">
        <div class="card login-card p-4">
            <h2 class="text-center mb-4">Login</h2>
            <?php if (!empty($error_message)) : ?>
            <div class="alert alert-danger text-center">
                <?= htmlspecialchars($error_message) ?>
            </div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-4">
                    <label for="email" class="form-label text-light">Email Address</label>
                    <input type="email" class="form-control form-control-lg" id="email" name="email"
                        placeholder="Enter your email" required>
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label text-light">Password</label>
                    <input type="password" class="form-control form-control-lg" id="password" name="password"
                        placeholder="Enter your password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2">Login</button>
            </form>
            <div class="text-center mt-3">
                <p class="text-light">Don't have an account? <a href="register.php" class="text-info">Register here</a>.
                </p>
            </div>

        </div>
    </div>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Event Management System. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>