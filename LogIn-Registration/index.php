<?php 
session_start();

// Redirect to login if the user is not logged in
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

include('database.php');

// Fetch user data from the database (current user)
$user_id = $_SESSION["user"];
$query = "SELECT id, firstname, lastname, emailadd, idno FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);

if (!$stmt) {
    die("Database query preparation failed: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Check if the user data was fetched successfully
if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
} else {
    // Handle the case where no user data is found
    die("User not found in the database.");
}

// Fetch total sit-in hours from the database
$session_query = "SELECT SUM(hours) AS total_hours FROM sit_in_sessions WHERE user_id = ?";
$session_stmt = mysqli_prepare($conn, $session_query);

if (!$session_stmt) {
    die("Database query preparation failed: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($session_stmt, "i", $user_id);
mysqli_stmt_execute($session_stmt);
$session_result = mysqli_stmt_get_result($session_stmt);

if ($session_result && mysqli_num_rows($session_result) > 0) {
    $session_data = mysqli_fetch_assoc($session_result);
    $total_hours = $session_data['total_hours'] ?? 0; // default to 0 if no sessions found
} else {
    $total_hours = 0; // If no sessions exist
}

// Calculate remaining hours
$max_hours = 30;
$remaining_hours = $max_hours - $total_hours;

// Fetch active announcements from the database
$query = "SELECT * FROM announcements WHERE active = 1 ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

$announcements = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $announcements[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="ccs.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <title>Student Dashboard</title>
</head>
<body class="no-bg">
    <nav class="navbar">
        <div class="navbar-left">
            <img class="logo1" src="ccs.png" alt="CCS Logo">
            <img class="logo2" src="uc.png" alt="UC Logo">
        </div>
        <h1>Student Dashboard</h1>
        <div class="navbar-right">
            <!-- Display user's ID number -->
            <span class="user-idno">
                ID: <?php echo htmlspecialchars($user['idno'] ?? 'N/A'); ?>
            </span>
            <!-- Edit profile button -->
            <a href="#editProfileModal" class="btn btn-prof" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                <img src="pp.png" alt="profile" width="73" height="73">
            </a>
            <a href="#announcementModal" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#announcementModal">
            <img src="an.png" alt="profile" width="60" height="60">
            </a>
            <!-- Logout button -->
            <a href="logout.php" class="btn btn-warning">Logout</a>
        </div>
    </nav>

    <!-- Rules Container -->
    <div class="rules-container">
        <!-- Lab Rules Box -->
        <div class="rules-box">
            <span>LABORATORY RULES AND REGULATIONS</span>
            <p>To avoid embarrassment and maintain camaraderie with your friends and superiors at our laboratories, please observe the following:</p>
            <div class="list">
                <ol>
                    <li>Maintain silence, proper decorum, and discipline inside the laboratory. Mobile phones, walkmans, and other personal pieces of equipment must be switched off.</li>
                    <li>Games are not allowed inside the lab. This includes computer-related games and other games that may disturb the operation of the lab.</li>
                    <li>Surfing the Internet is allowed only with the permission of the instructor. Downloading and installing of software are strictly prohibited.</li>
                    <li>Getting access to other websites not related to the course (especially pornographic and illicit sites) is strictly prohibited.</li>
                    <li>Deleting computer files and changing the set-up of the computer is a major offense.</li>
                    <li>Chewing gum, eating, drinking, smoking, and other forms of vandalism are prohibited inside the lab.</li>
                    <li>Anyone causing a continual disturbance will be asked to leave the lab. Acts or gestures offensive to the members of the community, including public display of physical intimacy, are not tolerated.</li>
                    <li>Persons exhibiting hostile or threatening behavior such as yelling, swearing, or disregarding requests made by lab personnel will be asked to leave the lab.</li>
                    <li>For serious offenses, the lab personnel may call the Civil Security Office (CSU) for assistance.</li>
                    <li>Any technical problem or difficulty must be addressed to the laboratory supervisor, student assistant, or instructor immediately.</li>
                </ol>
            </div>
        </div>

        <!-- Sit-In Rules Box -->
        <div class="rules-box">
            <span>LABORATORY SIT-IN RULES AND REGULATIONS</span>
            <p>To ensure fairness and proper usage of lab resources, please observe the following sit-in rules:</p>
            <div class="list">
                <ol>
                    <li>Sit in will be allowed effectively Midterm period onwards and the only exception is Prelim period.</li>
                    <li>Sit in will only be allowed 15 minutes after the class entered the laboratory and with the permission of the Instructor who will be conducting the class exce for no classes or vacant hours. Once the Instructor is absent, the Laboratory supervisor will decide if they allow the students to sit-in.</li>
                    <li>All bags, knapsacks, and the likes must be deposited at the counter.</li>
                    <li>Approach any of the laboratory in-charge (Working Scholar or Laboratory Supervisor) and not the Instructor who conducted the class before getting in.</li>
                    <li>Students (sit-inners) should submit their ID to any of the laboratory in-charge and tell the in-charge what activities they are going to do.</li>
                    <li>Students (sit-inners) must observe silence while working in their activities to avoid class distraction. Only those who need to sit-in are allowed to stay in tl laboratory.</li>
                    <li>Students (sit-inners) must observe silence while working in their activities to avoid class distraction. Only those who need to sit-in are allowed to stay in tl laboratory.</li>
                </ol>
            </div>
        </div>
    </div>

    <!-- Modal to Edit Profile -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel">User Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Form to edit profile -->
                    <form method="POST" action="update_profile.php">
                        <div class="mb-3">
                            <label for="idno" class="form-label">ID No</label>
                            <input type="text" class="form-control" id="idno" name="idno" value="<?php echo htmlspecialchars($user['idno']); ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="firstname" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="firstname" name="firstname" value="<?php echo htmlspecialchars($user['firstname']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="lastname" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="lastname" name="lastname" value="<?php echo htmlspecialchars($user['lastname']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="emailadd" class="form-label">Email</label>
                            <input type="email" class="form-control" id="emailadd" name="emailadd" value="<?php echo htmlspecialchars($user['emailadd']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password (Optional)</label>
                            <input type="password" class="form-control" id="password" name="password">
                        </div>
                        
                        <!-- Display remaining sit-in hours -->
                        <div class="mb-3">
                            <label for="remaining_hours" class="form-label">Remaining Sit-In Hours</label>
                            <input type="text" class="form-control" id="remaining_hours" name="remaining_hours" value="<?php echo $remaining_hours; ?>" readonly>
                        </div>

                        <button type="submit" class="btn btn-primary">Update Information</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Announcement Modal -->
    <div class="modal fade" id="announcementModal" tabindex="-1" aria-labelledby="announcementModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="announcementModalLabel">Important Announcements</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php if (count($announcements) > 0): ?>
                        <div class="announcement-list">
                            <?php foreach ($announcements as $announcement): ?>
                                <div class="announcement-card">
                                    <h5 class="announcement-title"><?php echo htmlspecialchars($announcement['title']); ?></h5>
                                    <p class="announcement-message"><?php echo nl2br(htmlspecialchars($announcement['message'])); ?></p>
                                    <small class="announcement-date"><?php echo date('F j, Y, g:i a', strtotime($announcement['created_at'])); ?></small>
                                </div>
                                <hr>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p>No announcements at the moment.</p>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
