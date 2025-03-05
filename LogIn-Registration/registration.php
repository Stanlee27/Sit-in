<?php
session_start();
if (isset($_SESSION["user"])) {
    header("Location: index.php");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <link rel="icon" type="image/x-icon" href="ccs.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>

<img class="logo1" src="ccs.png" alt="" >
<img class="logo2" src="uc.png" alt="">

<body>

<div class="container">
    <?php
    $errors = array();

    if (isset($_POST["submit"])) {
        $idno = isset($_POST["idno"]) ? $_POST["idno"] : '';
        $lastname = isset($_POST["lastname"]) ? $_POST["lastname"] : '';
        $firstname = isset($_POST["firstname"]) ? $_POST["firstname"] : '';
        $midname = isset($_POST["midname"]) ? $_POST["midname"] : '';
        $course = isset($_POST["course"]) ? $_POST["course"] : '';
        $yearlvl = isset($_POST["yearlvl"]) ? $_POST["yearlvl"] : '';
        $emailadd = isset($_POST["emailadd"]) ? $_POST["emailadd"] : '';
        $username = isset($_POST["username"]) ? $_POST["username"] : '';
        $password = isset($_POST["password"]) ? $_POST["password"] : '';

        // Validation for empty fields
        if (empty($idno) || empty($lastname) || empty($firstname) || empty($midname) || empty($course) || empty($yearlvl) || empty($emailadd) || empty($username) || empty($password)) {
            array_push($errors, "Please fill in all fields");
        }

        // Validate email
        if (!filter_var($emailadd, FILTER_VALIDATE_EMAIL)) {
            array_push($errors, "Email is not valid");
        }

        // Validate password length
        if (strlen($password) < 8) {
            array_push($errors, "Password must be at least 8 characters");
        }

        // Check if the username exists
        require_once "database.php";
        $sql = "SELECT * FROM users WHERE username = '$username'";
        $result = mysqli_query($conn, $sql);
        $rowCount = mysqli_num_rows($result);
        if ($rowCount > 0) {
            array_push($errors, "Username already exists!");
        }

        // Check if the email exists
        $sql_email = "SELECT * FROM users WHERE emailadd = '$emailadd'";
        $result_email = mysqli_query($conn, $sql_email);
        $rowCount_email = mysqli_num_rows($result_email);
        if ($rowCount_email > 0) {
            array_push($errors, "Email already exists!");
        }

        // If there are errors, display them
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                echo "<div class='alert alert-danger'>$error</div>";
            }
        } else {
            // If no errors, proceed with registration
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (idno, lastname, firstname, midname, course, yearlvl, emailadd, username, password ) VALUES (?,?,?,?,?,?,?,?,?)";
            $stmt = mysqli_stmt_init($conn);
            $prepareStmt = mysqli_stmt_prepare($stmt, $sql);
            if ($prepareStmt) {
                mysqli_stmt_bind_param($stmt, "sssssssss", $idno, $lastname, $firstname, $midname, $course, $yearlvl, $emailadd, $username, $passwordHash);
                mysqli_stmt_execute($stmt);
                echo "<div class='alert alert-success'>You are registered successfully. Redirecting to login...</div>";
                header("Location: login.php");
                exit();
            } else {
                die("Something went wrong");
            }
        }
    }
    ?>

    <form action="registration.php" method="post">
        <div class="label">Registration </div>
        <div class="form-group">
            <input type="text" class="form-control" name="idno" id="idno" placeholder="ID NO:" value="<?php echo isset($idno) ? $idno : ''; ?>" oninput="mirrorusername()">
        </div>
        <div class="form-group">
            <input type="text" class="form-control" name="lastname" placeholder="Last Name:" value="<?php echo isset($lastname) ? $lastname : ''; ?>">
        </div>
        <div class="form-group">
            <input type="text" class="form-control" name="firstname" placeholder="First Name:" value="<?php echo isset($firstname) ? $firstname : ''; ?>">
        </div>
        <div class="form-group">
            <input type="text" class="form-control" name="midname" placeholder="Middle Name:" value="<?php echo isset($midname) ? $midname : ''; ?>">
        </div>
        <div class="form-group">
            <label for="course">Course:</label>
            <select class="form-control" name="course" id="course">
                <option value="Select" <?php echo (isset($course) && $course == 'Select') ? 'selected' : ''; ?>>Select Course</option>
                <option value="BSIT" <?php echo (isset($course) && $course == 'BSIT') ? 'selected' : ''; ?>>BSIT</option>
                <option value="BSCS" <?php echo (isset($course) && $course == 'BSCS') ? 'selected' : ''; ?>>BSCS</option>
                <option value="ACT" <?php echo (isset($course) && $course == 'ACT') ? 'selected' : ''; ?>>ACT</option>
            </select>
        </div>
        <div class="form-group">
            <label for="yearlvl">Year Level:</label>
            <select class="form-control" name="yearlvl" id="yearlvl">
                <option value="Select" <?php echo (isset($course) && $course == 'Select') ? 'selected' : ''; ?>>Select Year Level</option>
                <option value="1" <?php echo (isset($yearlvl) && $yearlvl == '1') ? 'selected' : ''; ?>>1</option>
                <option value="2" <?php echo (isset($yearlvl) && $yearlvl == '2') ? 'selected' : ''; ?>>2</option>
                <option value="3" <?php echo (isset($yearlvl) && $yearlvl == '3') ? 'selected' : ''; ?>>3</option>
                <option value="4" <?php echo (isset($yearlvl) && $yearlvl == '4') ? 'selected' : ''; ?>>4</option>
            </select>
        </div>
        <div class="form-group">
            <input type="text" class="form-control" name="emailadd" placeholder="Email Address:" value="<?php echo isset($emailadd) ? $emailadd : ''; ?>">
        </div>
        <div class="form-group">
            <input type="text" class="form-control" name="username" id="username" placeholder="Username:" value="<?php echo isset($username) ? $username : ''; ?>" readonly>
        </div>
        <div class="form-group">
            <input type="password" class="form-control" name="password" placeholder="Password:" value="<?php echo isset($password) ? $password : ''; ?>">
        </div>        
        <div class="form-btn">
            <input type="submit" class="btn btn-primary" value="Register" name="submit">
        </div>   
    </form>
    <div class="text"><p>Already Registered?<a href="login.php">Login</a></p></div>
</div>

<script>
    function mirrorusername() {
        document.getElementById('username').value = document.getElementById('idno').value;
    }
</script>
   
</body>
</html>
