<?php
include_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate inputs
    $userName = mysqli_real_escape_string($conn, $_POST['userName']);
    $studentID = mysqli_real_escape_string($conn, $_POST['studentID']);
    $userEmail = mysqli_real_escape_string($conn, $_POST['userEmail']);
    $userPwd = mysqli_real_escape_string($conn, $_POST['userPwd']);
    $confirmPwd = mysqli_real_escape_string($conn, $_POST['confirmPwd']);

    // Validate password and confirm password
    if ($userPwd !== $confirmPwd) {
        die("Password and confirm password do not match.");
    }

    // Check if userEmail already exists
    $sql = "SELECT * FROM user WHERE userEmail=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $userEmail);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<p><b>Error:</b> User exists, please register a new user</p>";
    } else {
        // Hash the password
        $pwdHash = password_hash($userPwd, PASSWORD_DEFAULT);

        // Insert new user record
        $insertSql = "INSERT INTO user (studentID, userName, userEmail, userPwd) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insertSql);
        $stmt->bind_param("ssss", $studentID, $userName, $userEmail, $pwdHash);

        if ($stmt->execute()) {
            echo "<p>New user record created successfully. Welcome <b>" . htmlspecialchars($userName, ENT_QUOTES, 'UTF-8') . "</b></p>";
        } else {
            echo "Error: " . $stmt->error;
        }
    }
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
    <link rel="stylesheet" href="css/style.css">
    <title>User Registration</title>
</head>
<body>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="userName">Username:</label><br>
        <input type="text" id="userName" name="userName" required><br><br>

        <label for="studentID">Student ID:</label><br>
        <input type="text" id="studentID" name="studentID" required><br><br>

        <label for="userEmail">Email:</label><br>
        <input type="email" id="userEmail" name="userEmail" required><br><br>

        <label for="userPwd">Password:</label><br>
        <input type="password" id="userPwd" name="userPwd" required><br><br>

        <label for="confirmPwd">Confirm Password:</label><br>
        <input type="password" id="confirmPwd" name="confirmPwd" required><br><br>

        <input type="submit" value="Register">
    </form>

    <p><a href="login.php"> | Login |</a></p>
</body>
</html>
