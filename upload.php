<?php
// Handle the image upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['images'])) {
    $upload_dir = 'Suploads/';  // Directory to store uploaded images
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "blogdb"; // Replace with your database name

    // Create database connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Loop through the uploaded files
    foreach ($_FILES['images']['name'] as $key => $name) {
        $file_tmp = $_FILES['images']['tmp_name'][$key];
        $file_name = basename($name);
        $file_path = $upload_dir . $file_name;

        // Move the uploaded file to the "uploads" folder
        if (move_uploaded_file($file_tmp, $file_path)) {
            // Insert the image path into the database
            $sql = "INSERT INTO images (image_path) VALUES ('$file_path')";
            if (mysqli_query($conn, $sql)) {
                echo "Image uploaded successfully: $file_name<br>";
            } else {
                echo "Error uploading image: $file_name<br>";
            }
        }
    }

    // Close database connection
    mysqli_close($conn);

    // Redirect back to the admin page or gallery page after upload
    header("Location: index.php");  // Replace with your index or gallery page
    exit;
}
?>
