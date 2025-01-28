<!-- sidebar.php -->
<div class="sidebar">
    <div class="logo text-center py-4">
        <img src="../uploads/images/eventsbg4.png" alt="Event Logo" class="img-fluid"
            style="max-width: 80%; height: auto; border-radius: 50%;">
        <h5 class="mt-2 text-white">Event Management System</h5>
    </div>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link d-flex align-items-center" href="../admin/admin_dashboard.php">
                <i class="fas fa-home me-2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link d-flex align-items-center" href="../admin/user.php">
                <i class="fas fa-users me-2"></i> User Information
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link d-flex align-items-center" href="../admin/attendees.php">
                <i class="fas fa-user-check me-2"></i> Attendee Information
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link d-flex align-items-center" href="../admin/register_attendee.php">
                <i class=" fas fa-user-plus me-2"></i> Add Attendee
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link d-flex align-items-center" href="../admin/events.php">
                <i class="fas fa-calendar-alt me-2"></i> Manage Events
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link d-flex align-items-center" href="../admin/add_event.php">
                <i class="fas fa-plus-circle me-2"></i> Add Event
            </a>
        </li>
    </ul>
    <footer class="text-center py-3">
        <p>&copy; <?php echo date("Y"); ?> Event Management System. All rights reserved.</p>
    </footer>
</div>

<style>
/* Sidebar Container */
.sidebar {
    position: fixed;
    top: 60px;
    /* Matches the height of the header */
    left: 0;
    width: 220px;
    height: calc(100vh - 60px);
    /* Full viewport height minus header height */
    background-color: #0a2540;
    /* Deep Blue */
    color: #fff;
    border-right: 1px solid rgba(0, 0, 0, 0.1);
    overflow-y: auto;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
}

/* Logo Section */
.sidebar .logo {
    background-color: #001a33;
    /* Darker Deep Blue for logo */
    border-bottom: 1px solid rgba(0, 0, 0, 0.2);
    text-align: center;
    padding: 15px;
    font-weight: bold;
    font-size: 1.2rem;
    color: #fff;
}

/* Sidebar Menu */
.sidebar ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.sidebar ul li {
    margin: 0;
    padding: 0;
}

/* Sidebar Links */
.sidebar ul li a {
    display: flex;
    align-items: center;
    padding: 12px 20px;
    color: #fff;
    text-decoration: none;
    font-size: 0.95rem;
    font-weight: 500;
    transition: all 0.3s ease;
    border-radius: 6px;
    margin: 5px;
    background-color: #f57c00;
    /* Orange background for links */
}

/* Icons for Menu Items */
.sidebar ul li a i {
    font-size: 1.2rem;
    margin-right: 10px;
}

/* Hover and Active States */
.sidebar ul li a:hover {
    background-color: #d97500;
    /* Slightly darker orange for hover */
    color: #fff;
    transform: scale(1.02);
}

.sidebar ul li a.active {
    background-color: #001a33;
    /* Darker Deep Blue for active state */
    font-weight: bold;
    color: #fff;
}

/* Scrollbar Customization */
.sidebar::-webkit-scrollbar {
    width: 8px;
}

.sidebar::-webkit-scrollbar-thumb {
    background-color: rgba(255, 255, 255, 0.3);
    border-radius: 10px;
}

.sidebar::-webkit-scrollbar-thumb:hover {
    background-color: rgba(255, 255, 255, 0.5);
}

/* Responsive Design */
@media (max-width: 768px) {
    .sidebar {
        width: 100%;
        height: auto;
        position: relative;
    }

    .sidebar ul li a {
        justify-content: center;
    }

    .sidebar ul li a i {
        margin-right: 0;
    }
}
</style>