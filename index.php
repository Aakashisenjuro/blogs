<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Blog</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f7f7f7;
        }
        .card {
            margin-top: 50px;
        }
        .container {
            margin-top: 50px;
        }
        .card-title {
            font-size: 2rem;
            font-weight: bold;
        }
        .card-text {
            font-size: 1.2rem;
            color: #555;
        }
        .card-img-top {
            max-width: 100%;
            max-height: 400px;
            object-fit: cover;
        }
        p {
    font-size: 14px;
    line-height: 2rem;
    font-family: 'Akzidenz-Grotesk', sans-serif;
    letter-spacing: 0.02em;
    color: black;
    margin: 1.6rem 0;
}

    </style>
</head>
<body>

    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="#">My Demo Blog</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="create-post.php">Create Post</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Blog Post Section -->
    <div class="container">
        
    <?php
    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "blogdb";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Query to fetch blog posts from the database
    $sql = "SELECT title, content, main_image FROM blog_post ORDER BY id DESC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Output the data for each blog post
        while ($row = $result->fetch_assoc()) {
            echo '<div class="card">';
            echo '<div class="card-body">';
            echo '<h5 class="card-title">' . htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8') . '</h5>';
            echo '<p class="card-text">' . htmlspecialchars($row['content'], ENT_QUOTES, 'UTF-8') . '</p>';
            
            // Display the main image with sanitized URL and dynamic alt text
            echo '<img src="' . htmlspecialchars($row['main_image'], ENT_QUOTES, 'UTF-8') . '" class="card-img-top" alt="Main Image">';
            
            echo '</div>';
            echo '</div>';
        }
    } else {
        echo '<p>No blog posts found.</p>';
    }

    // Close the database connection
    $conn->close();
    ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
