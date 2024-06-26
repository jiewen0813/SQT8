<?php
session_start();
include_once 'config.php';

// Check if the user is logged in
if(isset($_SESSION["SID"])) {
    $studentID = $_SESSION["SID"];
} else {
    // Redirect to the login page if the user is not logged in
    header("location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Study KPI</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">    
    <script>
    // For responsive sandwich menu
    function myFunction() {
        var x = document.getElementById("myTopnav");
        if (x.className === "topnav") {
            x.className += " responsive";
        } else {
            x.className = "topnav";
        }
    }
    </script>
</head>
<body>
<div class="header"></div>

<?php include_once 'menu.php'; ?>

<div class="userAuth">
    <?php 
    // Login & logout section
    if(isset($_SESSION["SID"])){
        echo '<b> '. $_SESSION["userName"] . '</b> <a href="logout.php">| Logout |</a> &nbsp;&nbsp;&nbsp; <br>';        
    } else {
        echo '<a href="login.php">| Login |</a> &nbsp;&nbsp;&nbsp; <br>';
    }
    ?>
</div>

<h1>My Activities</h1>
<h2>Add Activities</h2>
<p align="center">Required fields are marked with *</p>

<div style="padding:0 10px;" id="challengeDiv">
    <form method="POST" action="my_activities_action.php" enctype="multipart/form-data" id="myForm">
        <input type="hidden" name="studentID" value="<?php echo $studentID; ?>">
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
                    <td>Semester*</td>
                    <td>:</td>
                    <td>
                        <select size="1" name="sem" required>
                            <option value="">Select Semester</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Year*</td>
                    <td>:</td>
                    <td>
                        <input type="text" name="year" size="8" required placeholder="YYYY/YYYY">
                    </td>
                </tr>
                <tr>
                    <td>Name of Activities/Club/Association/Competition*</td>
                    <td>:</td>
                    <td>
                        <textarea rows="4" name="activities" cols="30" required></textarea>
                    </td>
                </tr>
                <tr>
                    <td>Position*</td>
                    <td>:</td>
                    <td>
                        <textarea rows="4" name="position" cols="30" required></textarea>
                    </td>
                </tr>
                <tr>
                    <td>Upload photo</td>
                    <td>:</td>
                    <td>
                        Max size: 488.28KB<br>
                        <input type="file" name="fileToUpload" id="fileToUpload" accept=".jpg, .jpeg, .png">
                    </td>
                </tr>
                <tr>
                    <td colspan="3" align="right">
                        <input type="submit" value="Submit" name="B1">
                        <input type="reset" value="Reset" name="B2">
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
