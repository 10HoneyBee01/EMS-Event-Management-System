<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Management System</title>
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

    .container {
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        position: relative;
        z-index: 2;
    }

    .card {
        background: rgba(0, 0, 0, 0.7);
        border-radius: 20px;
        padding: 50px 60px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        max-width: 500px;
        width: 100%;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    }

    .card h1 {
        font-size: 3rem;
        font-weight: 700;
        margin-bottom: 25px;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        color: #f39c12;
    }

    .card p {
        font-size: 1.2rem;
        margin-bottom: 30px;
        color: #bdc3c7;
    }

    .btn {
        padding: 15px 35px;
        font-size: 1.1rem;
        border-radius: 50px;
        transition: all 0.3s ease;
        text-transform: uppercase;
    }

    .btn-primary {
        background: #f39c12;
        color: #fff;
        border: none;
    }

    .btn-primary:hover {
        background: #e67e22;
        transform: scale(1.05);
    }

    .btn-secondary {
        background: #2980b9;
        color: #fff;
        border: none;
    }

    .btn-secondary:hover {
        background: #3498db;
        transform: scale(1.05);
    }

    footer {
        position: absolute;
        bottom: 0;
        width: 100%;
        background: #1a252f;
        color: #bdc3c7;
        padding: 10px 0;
        text-align: center;
    }

    footer p {
        margin: 0;
        font-size: 1rem;
    }

    @media (max-width: 768px) {
        .card {
            padding: 40px 30px;
        }

        .card h1 {
            font-size: 2.5rem;
        }

        .btn {
            font-size: 1rem;
            padding: 12px 28px;
        }
    }
    </style>
</head>

<body>
    <div class="overlay"></div>
    <div class="container">
        <div class="card text-center">
            <h1>Welcome to the Event Management System</h1>
            <p>Discover and manage events effortlessly. Sign in to your account or register to get started!</p>
            <div class="d-grid gap-3">
                <a href="login.php" class="btn btn-primary btn-lg">Sign In</a>
                <a href="register.php" class="btn btn-secondary btn-lg">Register</a>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Event Management System. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>