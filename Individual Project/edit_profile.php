<?php
session_start();
include_once 'config.php';

// Check if the user is logged in
if (isset($_SESSION["SID"])) {
    $studentID = $_SESSION["SID"];
    
    // Fetch user-specific information from the database
    $query = "SELECT * FROM user WHERE studentID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $studentID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $userName = $row["userName"];
        $userEmail = $row["userEmail"];
        $phoneNo = $row["phoneNo"];
        $program = $row["program"];
        $intakeBatch = $row["intakeBatch"];
        $mentorName = $row["mentorName"];
        $address = $row["address"];
        $state = $row["state"];
        $studyMotto = $row["studyMotto"];
        $imgpath = $row["imgpath"]; // Make sure imgpath column exists in your database
    } else {
        // Handle the case where the user is not found in the database
        echo "User not found in the database.";
        exit();
    }
} else {
    // Redirect to the login page if the user is not logged in
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script>
        function resetForm() {
            document.getElementById("myForm").reset();
        }

        function clearForm() {
            var elements = document.getElementById("myForm").elements;
            for (var i = 0; i < elements.length; i++) {
                if (elements[i].type !== "button" && elements[i].type !== "reset") {
                    elements[i].value = "";
                }
            }
        }
    </script>
    <style>
        /* Add your custom styles here */
    </style>
</head>
<body>
    <div class="header"></div>

    <?php include_once 'menu.php'; ?>

    <div class="userAuth">
        <?php 
        // Login & logout section
        if (isset($_SESSION["SID"])) {
            echo '<b>'. htmlspecialchars($_SESSION["userName"], ENT_QUOTES, 'UTF-8') . '</b> <a href="logout.php">| Logout |</a> &nbsp;&nbsp;&nbsp; <br>';
        } else {
            echo '<a href="index.php?login=1">| Login |</a> &nbsp;&nbsp;&nbsp; <br>';
        }
        ?>
    </div>

    <div style="padding:0 10px;" id="challengeDiv">
        <h1>Edit Profile</h1>

        <form method="POST" action="edit_profile_action.php" id="myForm" enctype="multipart/form-data">
            <table border="1" id="myTable">
                <thead>
                    <tr>
                        <th>Field</th>
                        <th>:</th>
                        <th>Input</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Name*</td>
                        <td>:</td>
                        <td>
                            <input type="text" name="userName" size="15" value="<?= htmlspecialchars($userName, ENT_QUOTES, 'UTF-8') ?>" required>
                        </td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td>:</td>
                        <td>
                            <input type="email" name="userEmail" size="30" value="<?= htmlspecialchars($userEmail, ENT_QUOTES, 'UTF-8') ?>" required>
                        </td>
                    </tr>
                    <tr>
                        <td>Phone No</td>
                        <td>:</td>
                        <td>
                            <input type="text" name="phoneNo" size="30" value="<?= htmlspecialchars($phoneNo, ENT_QUOTES, 'UTF-8') ?>" required>
                        </td>
                    </tr>
                    <tr>
                        <td>Program*</td>
                        <td>:</td>
                        <td>
                            <input type="text" name="program" size="30" value="<?= htmlspecialchars($program, ENT_QUOTES, 'UTF-8') ?>" required>
                        </td>
                    </tr>
                    <tr>
                        <td>Intake Batch*</td>
                        <td>:</td>
                        <td>
                            <input type="text" name="intakeBatch" size="30" value="<?= htmlspecialchars($intakeBatch, ENT_QUOTES, 'UTF-8') ?>" required placeholder="YYYY/YYYY">
                        </td>
                    </tr>
                    <tr>
                        <td>Mentor Name</td>
                        <td>:</td>
                        <td>
                            <input type="text" name="mentorName" size="30" value="<?= htmlspecialchars($mentorName, ENT_QUOTES, 'UTF-8') ?>">
                        </td>
                    </tr>
                    <tr>
                        <td>Address</td>
                        <td>:</td>
                        <td>
                            <textarea rows="4" name="address" cols="30"><?= htmlspecialchars($address, ENT_QUOTES, 'UTF-8') ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>State</td>
                        <td>:</td>
                        <td>
                            <input type="text" name="state" size="30" value="<?= htmlspecialchars($state, ENT_QUOTES, 'UTF-8') ?>">
                        </td>
                    </tr>
                    <tr>
                        <td>Study Motto</td>
                        <td>:</td>
                        <td>
                            <textarea rows="4" name="studyMotto" cols="30"><?= htmlspecialchars($studyMotto, ENT_QUOTES, 'UTF-8') ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>Current Photo</td>
                        <td>:</td>
                        <td>
                            <?php if (!empty($imgpath)): ?>
                                <img src="<?= htmlspecialchars($imgpath, ENT_QUOTES, 'UTF-8') ?>" alt="Current Photo">
                            <?php else: ?>
                                <span>No photo available</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Upload New Photo</td>
                        <td>:</td>
                        <td>
                            Max size: 488.28KB<br>
                            <input type="file" name="fileToUpload" id="fileToUpload" accept=".jpg, .jpeg, .png">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" align="right">
                            <input type="submit" value="Submit" name="B1">
                            <input type="reset" value="Reset" name="B2" onclick="resetForm()">
                            <input type="button" value="Clear" name="B3" onclick="clearForm()">
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
    </div>

    <footer>
        <small><i>Copyright &copy; 2023 - Leong Jie Wen</i></small>
    </footer>
</body>
</html>
