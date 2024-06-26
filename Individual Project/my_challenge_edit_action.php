<?php
include_once 'config.php';

// Initialize variables
$id = isset($_POST["ch_id"]) ? htmlspecialchars($_POST["ch_id"], ENT_QUOTES, 'UTF-8') : "";
$sem = isset($_POST["sem"]) ? htmlspecialchars($_POST["sem"], ENT_QUOTES, 'UTF-8') : "";
$year = isset($_POST["year"]) ? htmlspecialchars($_POST["year"], ENT_QUOTES, 'UTF-8') : "";
$challenge = isset($_POST["challenge"]) ? trim(htmlspecialchars($_POST["challenge"], ENT_QUOTES, 'UTF-8')) : "";
$plan = isset($_POST["plan"]) ? trim(htmlspecialchars($_POST["plan"], ENT_QUOTES, 'UTF-8')) : "";
$remark = isset($_POST["remark"]) ? trim(htmlspecialchars($_POST["remark"], ENT_QUOTES, 'UTF-8')) : "";
$target_dir = "uploads/challenge/";
$uploadOk = 0;
$uploadfileName = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if an image file is uploaded
    if (isset($_FILES["fileToUpload"]) && $_FILES["fileToUpload"]["error"] === UPLOAD_ERR_OK) {
        $filetmp = $_FILES["fileToUpload"];
        $uploadfileName = basename($filetmp["name"]);
        $target_file = $target_dir . $uploadfileName;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if file already exists
        if (file_exists($target_file)) {
            echo "ERROR: Sorry, image file already exists.<br>";
        } else {
            // Check file size
            if ($filetmp["size"] > 500000) {
                echo "ERROR: Sorry, your file is too large. Try resizing your image.<br>";
            } else {
                // Allow certain file formats
                if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
                    echo "ERROR: Sorry, only JPG, JPEG, PNG & GIF files are allowed.<br>";
                } else {
                    $uploadOk = 1;
                }
            }
        }
    }

    // If no new image uploaded, update without changing the image
    if ($uploadOk == 0 && empty($_FILES["fileToUpload"]["name"])) {
        $sql = "UPDATE challenge SET sem=?, year=?, challenge=?, plan=?, remark=? WHERE ch_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issssi", $sem, $year, $challenge, $plan, $remark, $id);
    } elseif ($uploadOk == 1) {
        // New image uploaded, update with new image path
        $sql = "UPDATE challenge SET sem=?, year=?, challenge=?, plan=?, remark=?, img_path=? WHERE ch_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isssssi", $sem, $year, $challenge, $plan, $remark, $uploadfileName, $id);

        // Delete previous image if exists
        $getCurrentImagePathQuery = "SELECT img_path FROM challenge WHERE ch_id=?";
        $stmt_getImagePath = $conn->prepare($getCurrentImagePathQuery);
        $stmt_getImagePath->bind_param("i", $id);
        $stmt_getImagePath->execute();
        $result = $stmt_getImagePath->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $currentImagePath = $row["img_path"];

            if (!empty($currentImagePath) && file_exists($target_dir . $currentImagePath)) {
                unlink($target_dir . $currentImagePath);
                echo "Previous image deleted successfully.<br>";
            }
        }
    }

    // Execute update query
    if ($stmt->execute()) {
        if ($uploadOk == 1) {
            // Upload new image file
            if (move_uploaded_file($filetmp["tmp_name"], $target_file)) {
                echo "Form data and image updated successfully!<br>";
            } else {
                echo "Error uploading file.<br>";
            }
        } else {
            echo "Form data updated successfully!<br>";
        }
    } else {
        echo "Error updating data: " . $conn->error . "<br>";
    }

    // Close prepared statements
    $stmt->close();
    if (isset($stmt_getImagePath)) {
        $stmt_getImagePath->close();
    }
}

// Close DB connection
$conn->close();
?>
