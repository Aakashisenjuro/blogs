<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog with Image Gallery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }
        .header {
            text-align: center;
            padding: 32px;
        }
        .row {
            display: flex;
            flex-wrap: wrap;
        }
        .column {
            flex: 25%;
            max-width: 25%;
            padding: 0 4px;
        }
        .column img {
            margin-top: 8px;
            vertical-align: middle;
            width: 100%;
        }
        @media screen and (max-width: 800px) {
            .column {
                flex: 50%;
                max-width: 50%;
            }
        }
        @media screen and (max-width: 600px) {
            .column {
                flex: 100%;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

<!-- Blog Content Section -->
<div class="blog-content">
    <h2>My Blog Post</h2>
    <p>This is a sample blog post. You can add your content here.</p>
</div>

<!-- Image Gallery Section -->
<div class="header">
    <h1>Responsive Image Gallery</h1>
    <p>Resize the browser window to see the responsive effect.</p>
</div>

<div class="row">
    <?php
    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "blogdb"; // Replace with your database name
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Fetch images from database
    $sql = "SELECT * FROM images ORDER BY created_at DESC";
    $result = mysqli_query($conn, $sql);

    // Display images dynamically
    while ($row = mysqli_fetch_assoc($result)) {
    ?>
        <div class="column">
            <img src="<?php echo $row['image_path']; ?>" alt="Image">
        </div>
    <?php } ?>
</div>

<!-- Admin link for uploading images -->
<div class="text-center">
    <a href="adminimg.php" class="btn btn-primary">Go to Admin Panel</a>
</div>

</body>
</html>
