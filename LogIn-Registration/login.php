<?php
session_start(); // Start session

include('database.php'); // Connect to database

if (isset($_SESSION["user"])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate credentials
    $query = "SELECT id, password, role FROM users WHERE username = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION["user"] = $user['id'];

        // Check if the user is an admin
        if ($user['role'] === 'admin') {
            $_SESSION["admin"] = true; // Set admin session
            header("Location: admindashboard.php"); // Redirect admin
        } else {
            header("Location: index.php"); // Redirect normal user
        }
        exit();
    } else {
        echo "Invalid username or password.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link rel="icon" type="image/x-icon" href="ccs.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<img class="logo1" src="ccs.png" alt="">
<img class="logo2" src="uc.png" alt="">

<div class="container">
    <div class="logo3"></div>
    <form action="login.php" method="post">
        <div class="label">Log in</div>
        <div class="form-group">
            <input type="text" placeholder="Enter Username: " name="username" class="form-control" required>
        </div>
        <div class="form-group">
            <input type="password" placeholder="Enter Password: " name="password" class="form-control" required>
        </div>
        <div class="form-btn">
            <input type="submit" value="Login" name="login" class="btn btn-primary">
        </div>
    </form>

    <div class="text">
        <p>Already have an account? <a href="registration.php">Register</a></p>
    </div>
</div>

</body>
</html>
