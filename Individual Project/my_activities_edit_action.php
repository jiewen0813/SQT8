<?php
include_once 'config.php';

// Initialize variables
$id = isset($_POST["ac_id"]) ? $_POST["ac_id"] : "";
$sem = "";
$year = "";
$activities = "";
$position = "";
$target_dir = "uploads/activities/";
$target_file = "";
$uploadOk = 0;
$imageFileType = "";
$uploadfileName = "";

// Define the back link
$backLink = '<a href="my_activities.php">Back</a>';

// This block is called when the Submit button is clicked
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Values for add or edit
    $id = isset($_POST["ac_id"]) ? $_POST["ac_id"] : "";
    $sem = isset($_POST["sem"]) ? $_POST["sem"] : "";
    $year = isset($_POST["year"]) ? $_POST["year"] : "";
    $activities = isset($_POST["activities"]) ? trim($_POST["activities"]) : "";
    $position = isset($_POST["position"]) ? trim($_POST["position"]) : "";
    $filetmp = isset($_FILES["fileToUpload"]) ? $_FILES["fileToUpload"] : "";
    $uploadfileName = isset($filetmp["name"]) ? $filetmp["name"] : "";

    // Check if there is an image to be uploaded
    if (isset($_FILES["fileToUpload"]) && !empty($_FILES["fileToUpload"]["name"])) {
        $uploadOk = 1;
        $filetmp = $_FILES["fileToUpload"];
        $uploadfileName = $filetmp["name"];
        $target_file = $target_dir . basename($filetmp["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if file already exists
        if (file_exists($target_file)) {
            echo "ERROR: Sorry, image file " . htmlspecialchars($uploadfileName, ENT_QUOTES, 'UTF-8') . " already exists.<br>";
            $uploadOk = 0;
        }

        // Check file size <= 488.28KB or 500000 bytes
        if ($filetmp["size"] > 500000) {
            echo "ERROR: Sorry, your file is too large. Try resizing your image.<br>";
            $uploadOk = 0;
        }

        // Allow only these file formats
        if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
            echo "ERROR: Sorry, only JPG, JPEG, PNG & GIF files are allowed.<br>";
            $uploadOk = 0;
        }
    } elseif (isset($_FILES["fileToUpload"]) && $_FILES["fileToUpload"]["error"] == UPLOAD_ERR_NO_FILE) {
        // No file uploaded, this is not an error
    } else {
        // Handle other upload errors if needed
        echo "ERROR: File upload error. Please try again.<br>";
        $uploadOk = 0;
    }

    // If uploadOk, then try to add to the database first
    // uploadOK=1 if there is an image to be uploaded, filename does not exist, file size is ok, and format is ok
    if ($uploadOk) {
        // Retrieve the current image path
        $getCurrentImagePathQuery = "SELECT img_path FROM activities WHERE ac_id=?";
        $stmt = $conn->prepare($getCurrentImagePathQuery);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $row = $result->fetch_assoc()) {
            $currentImagePath = $row["img_path"];

            // Update the database with the new form data
            $updateQuery = "UPDATE activities SET sem=?, year=?, activities=?, position=?, img_path=? WHERE ac_id=?";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bind_param("sssssi", $sem, $year, $activities, $position, $uploadfileName, $id);
            $status = $stmt->execute();

            if ($status) {
                // Delete the previous image
                if (!empty($currentImagePath) && file_exists($target_dir . $currentImagePath)) {
                    unlink($target_dir . $currentImagePath);
                    echo "Previous image deleted successfully.<br>";
                }

                // Move the new image to the 'uploads' folder
                if (move_uploaded_file($filetmp["tmp_name"], $target_file)) {
                    echo "Form data updated successfully!<br>";
                    echo $backLink;
                } else {
                    echo "Sorry, there was an error uploading your file.<br>";
                    echo $backLink;
                }
            } else {
                echo "Error updating data: " . mysqli_error($conn) . "<br>";
                echo $backLink;
            }
        } else {
            echo "Error retrieving current image path: " . mysqli_error($conn) . "<br>";
        }
    } else {
        if (!empty($id)) {
            // Retrieve the current image path
            $getCurrentImagePathQuery = "SELECT img_path FROM activities WHERE ac_id=?";
            $stmt = $conn->prepare($getCurrentImagePathQuery);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $row = $result->fetch_assoc()) {
                $currentImagePath = $row["img_path"];

                // Delete the previous image from the folder
                if (!empty($currentImagePath) && file_exists($target_dir . $currentImagePath)) {
                    unlink($target_dir . $currentImagePath);
                    echo "Previous image deleted from the folder successfully.<br>";
                }

                // Update the database with the new form data, setting img_path to an empty string
                $updateQuery = "UPDATE activities SET sem=?, year=?, activities=?, position=?, img_path='' WHERE ac_id=?";
                $stmt = $conn->prepare($updateQuery);
                $stmt->bind_param("ssssi", $sem, $year, $activities, $position, $id);
                $status = $stmt->execute();

                if ($status) {
                    echo "Form data updated successfully!<br>";
                    echo $backLink;
                } else {
                    echo "Error updating data: " . mysqli_error($conn) . "<br>";
                    echo $backLink;
                }
            } else {
                echo "No matching record found for ac_id=" . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . ".<br>";
            }
        } else {
            echo "Error: The ID is empty.<br>";
        }
    }
}

// Close DB connection
mysqli_close($conn);
?>
