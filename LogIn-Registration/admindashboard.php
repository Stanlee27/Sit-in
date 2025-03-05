<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Ensure only admin can access
if (!isset($_SESSION["admin"])) {
    header("Location: login.php");
    exit();
}

include('database.php'); // Database connection

// Debug: Check database connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Fetch statistics
$stats_query = "SELECT COUNT(*) AS total_sessions, COALESCE(SUM(hours), 0) AS total_hours FROM sit_in_sessions";
$stats_result = mysqli_query($conn, $stats_query);
if (!$stats_result) {
    die("Error in statistics query: " . mysqli_error($conn));
}
$stats = mysqli_fetch_assoc($stats_result);

// Handle announcement submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['announcement'])) {
    $announcement = mysqli_real_escape_string($conn, $_POST['announcement']);
    $admin = $_SESSION["admin"]; // Get admin username
    $insert_query = "INSERT INTO announcements (title, created_at, admin) VALUES ('$announcement', NOW(), '$admin')";
    
    if (!mysqli_query($conn, $insert_query)) {
        die("Error inserting announcement: " . mysqli_error($conn));
    }
    header("Location: admin_dashboard.php");
    exit();
}

// Fetch announcements
$announcement_query = "SELECT * FROM announcements ORDER BY created_at DESC";
$announcement_result = mysqli_query($conn, $announcement_query);
if (!$announcement_result) {
    die("Error in announcements query: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .navbar {
            background-color: #0d47a1;
            padding: 10px;
        }
        .navbar a {
            color: white;
            margin-right: 15px;
            text-decoration: none;
        }
        .navbar-right .btn {
            background-color: #ffc107;
            border: none;
            color: black;
            padding: 5px 10px;
            border-radius: 5px;
        }
        .card {
            margin-top: 20px;
        }
        .modal-content {
            padding: 20px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand text-white" href="#">College of Computer Studies Admin</a>
            <div class="navbar-right">
                <a href="index.php" class="btn">Home</a>
                <a href="#" class="btn" data-bs-toggle="modal" data-bs-target="#searchModal">Search Student</a>
                <a href="logout.php" class="btn">Log out</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">Statistics</div>
                    <div class="card-body">
                        <p><strong>Students Registered:</strong> 265</p>
                        <p><strong>Currently Sit-in:</strong> 0</p>
                        <p><strong>Total Sit-in:</strong> <?= htmlspecialchars($stats['total_sessions']) ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">Announcement</div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="announcement" class="form-label">New Announcement</label>
                                <textarea class="form-control" id="announcement" name="announcement" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Post</button>
                        </form>
                        <hr>
                        <ul>
                            <?php while ($row = mysqli_fetch_assoc($announcement_result)): ?>
                                <li><strong><?= htmlspecialchars($row['title']) ?></strong> | <?= htmlspecialchars($row['created_at']) ?></li>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Modal -->
    <div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="searchModalLabel">Search Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="text" class="form-control" placeholder="Search...">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary">Search</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
