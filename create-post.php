<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "blogdb";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize title input (escape characters to prevent SQL injection)
    $title = htmlspecialchars($conn->real_escape_string($_POST['title']), ENT_QUOTES, 'UTF-8');
    
    // Get the raw content (HTML content will be preserved)
    $content = $_POST['content'];

    // Set upload directory
    $uploadDir = 'uploads/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Allowed file types and size limit (2MB)
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $maxFileSize = 2 * 1024 * 1024; // 2MB

    // Process main image upload
    $mainImagePath = '';
    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === 0) {
        $mainImage = $_FILES['main_image'];
        
        // Check file type and size
        $mainImageType = mime_content_type($mainImage['tmp_name']);
        if (in_array($mainImageType, $allowedTypes) && $mainImage['size'] <= $maxFileSize) {
            $mainImagePath = $uploadDir . uniqid('main_') . basename($mainImage['name']);
            if (!move_uploaded_file($mainImage['tmp_name'], $mainImagePath)) {
                echo "Error uploading main image.";
                exit;
            }
        } else {
            echo "Invalid file type or file too large for main image.";
            exit;
        }
    } else {
        echo "Main image is required.";
        exit;
    }

    // Insert post data into the database
    $sql = "INSERT INTO blog_post (title, content, main_image) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    
    // Check if prepare() failed and output the error
    if (!$stmt) {
        echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
        exit;
    }

    $stmt->bind_param("sss", $title, $content, $mainImagePath);
    
    if ($stmt->execute()) {
        $post_id = $stmt->insert_id;

        // Process additional images if any
        if (isset($_FILES['additional_images']) && count($_FILES['additional_images']['name']) > 0) {
            $total_images = count($_FILES['additional_images']['name']);
            
            for ($i = 0; $i < $total_images; $i++) {
                $additionalImageType = mime_content_type($_FILES['additional_images']['tmp_name'][$i]);
                if (in_array($additionalImageType, $allowedTypes) && $_FILES['additional_images']['size'][$i] <= $maxFileSize) {
                    $additionalImagePath = $uploadDir . uniqid('img_') . basename($_FILES['additional_images']['name'][$i]);
                    if (move_uploaded_file($_FILES['additional_images']['tmp_name'][$i], $additionalImagePath)) {
                        $sql_image = "INSERT INTO post_images (post_id, image_path) VALUES (?, ?)";
                        $stmt_image = $conn->prepare($sql_image);
                        if (!$stmt_image) {
                            echo "Prepare for image failed: (" . $conn->errno . ") " . $conn->error;
                            exit;
                        }
                        $stmt_image->bind_param("is", $post_id, $additionalImagePath);
                        $stmt_image->execute();
                    } else {
                        echo "Error uploading additional image: " . $_FILES['additional_images']['name'][$i];
                    }
                } else {
                    echo "Invalid file type or file too large for additional image: " . $_FILES['additional_images']['name'][$i];
                }
            }
        }

        echo "New post created successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Create a New Blog Post</h2>
        <form action="create-post.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="title" class="form-label">Post Title</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="mb-3">
                <label for="editor" class="form-label">Post Content</label>
                <div id="editor" contenteditable="true" class="form-control" style="min-height: 200px;"></div>
                <input type="hidden" name="content" id="content">
            </div>
            <div class="mb-3">
                <label for="main_image" class="form-label">Main Post Image</label>
                <input type="file" class="form-control" id="main_image" name="main_image" accept="image/*" required>
            </div>
            <div class="mb-3">
                <label for="additional_images" class="form-label">Upload Additional Images</label>
                <input type="file" class="form-control" id="additional_images" name="additional_images[]" accept="image/*" multiple>
            </div>
            <button type="submit" class="btn btn-primary">Create Post</button>
        </form>
    </div>
    <div class="container">
    <h2 class="mt-5">Upload Images to Gallery</h2>
    <form action="upload.php" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="images" class="form-label">Select Images</label>
            <input type="file" name="images[]" id="images" class="form-control" multiple required>
        </div>
        <button type="submit" class="btn btn-primary">Upload</button>
    </form>
</div>

    <script>
        document.querySelector('form').addEventListener('submit', function() {
            document.getElementById('content').value = document.getElementById('editor').innerHTML;
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

