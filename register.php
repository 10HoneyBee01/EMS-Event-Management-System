<?php
require 'database/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $password);
    if ($stmt->execute()) {
        $success_message = "Registration successful! You can now <a href='login.php'>log in</a>.";
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
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background: url('uploads/images/nature.jpg') no-repeat center center fixed;
        background-size: cover;
        font-family: 'Roboto', sans-serif;
        color: #fff;
        height: 100vh;
        margin: 0;
        position: relative;
    }

    .overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1;
    }

    .register-container {
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        position: relative;
        z-index: 2;
    }

    .register-card {
        background: rgba(0, 0, 0, 0.7);
        border-radius: 20px;
        padding: 50px 60px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        max-width: 500px;
        width: 100%;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .register-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    }

    .register-card h2 {
        font-size: 2.5rem;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 30px;
        color: #ffcc00;
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
        .register-card {
            padding: 30px 40px;
        }

        .register-card h2 {
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
    <div class="register-container">
        <div class="card register-card p-4">
            <h2 class="text-center mb-4">Register</h2>
            <?php if (!empty($success_message)) : ?>
            <div class="alert alert-success text-center">
                <?= $success_message ?>
            </div>
            <?php endif; ?>
            <?php if (!empty($error_message)) : ?>
            <div class="alert alert-danger text-center">
                <?= htmlspecialchars($error_message) ?>
            </div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label for="name" class="form-label text-light">Full Name</label>
                    <input type="text" class="form-control form-control-lg" id="name" name="name"
                        placeholder="Enter your full name" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label text-light">Email Address</label>
                    <input type="email" class="form-control form-control-lg" id="email" name="email"
                        placeholder="Enter your email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label text-light">Password</label>
                    <input type="password" class="form-control form-control-lg" id="password" name="password"
                        placeholder="Enter your password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2">Register</button>
            </form>
            <div class="text-center mt-3">
                <p class="text-light">Already have an account? <a href="login.php" class="text-info">Sign in here</a>.
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