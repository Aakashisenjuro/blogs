<?php
// Start the session to access session variables
session_start();

// Include your database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "blogdb";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if images are selected
    if (isset($_FILES['images']) && count($_FILES['images']['name']) > 0) {
        $uploadDir = 'uploads/'; // Define the upload directory
        $uploadedImages = []; // Array to store uploaded image paths

        // Loop through the images and upload them
        foreach ($_FILES['images']['name'] as $key => $name) {
            $imageTmpName = $_FILES['images']['tmp_name'][$key];
            $imagePath = $uploadDir . basename($name);

            // Check if the file is an image
            if (getimagesize($imageTmpName)) {
                if (move_uploaded_file($imageTmpName, $imagePath)) {
                    $uploadedImages[] = $imagePath;
                } else {
                    echo "Error uploading image: $name.<br>";
                }
            } else {
                echo "File $name is not an image.<br>";
            }
        }

        // Check if images were uploaded
        if (count($uploadedImages) > 0) {
            // Check if the post_id exists in the session
            if (isset($_SESSION['post_id'])) {
                $postId = $_SESSION['post_id'];  // Get the post ID from session
                $imagePaths = implode(',', $uploadedImages); // Convert image paths to comma-separated string

                // Insert the image paths into the database
                $sql = "UPDATE blog_post SET images = '$imagePaths' WHERE id = $postId";

                if ($conn->query($sql) === TRUE) {
                    echo "Images uploaded successfully!";
                } else {
                    echo "Error inserting image paths into database: " . $conn->error;
                }
            } else {
                echo "No post ID found in session.";
            }
        } else {
            echo "No valid images uploaded.";
        }
    } else {
        echo "No images selected.";
    }
}

// Close the connection
$conn->close();
?>
