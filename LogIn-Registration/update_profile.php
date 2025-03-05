<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

include('database.php');

$user_id = $_SESSION["user"];
$firstname = $_POST['firstname'];
$lastname = $_POST['lastname'];
$emailadd = $_POST['emailadd'];
$password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

// Update query with or without password
if ($password) {
    $query = "UPDATE users SET firstname = ?, lastname = ?, emailadd = ?, password = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ssssi", $firstname, $lastname, $emailadd, $password, $user_id);
} else {
    $query = "UPDATE users SET firstname = ?, lastname = ?, emailadd = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "sssi", $firstname, $lastname, $emailadd, $user_id);
}

// Execute the query
if (mysqli_stmt_execute($stmt)) {
    $_SESSION['message'] = "Profile updated successfully!";
    header("Location: index.php");
    exit();
} else {
    error_log("Error updating profile: " . mysqli_error($conn));
    $_SESSION['error'] = "Error updating profile. Please try again.";
    header("Location: index.php");
    exit();
}
?>