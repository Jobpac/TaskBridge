<?php
session_start();
include 'includes/db.php'; // Include database connection

// Check if worker ID is provided
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$worker_id = intval($_GET['id']);

// Fetch worker details
$stmt = $conn->prepare("SELECT * FROM workers WHERE id = ?");
$stmt->bind_param("i", $worker_id);
$stmt->execute();
$worker = $stmt->get_result()->fetch_assoc();

// Fetch comments for the worker
$comments = $conn->query("SELECT * FROM comments WHERE worker_id = $worker_id")->fetch_all(MYSQLI_ASSOC);

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment'])) {
    $comment = $_POST['comment'];
    $stmt = $conn->prepare("INSERT INTO comments (worker_id, comment) VALUES (?, ?)");
    $stmt->bind_param("is", $worker_id, $comment);
    if ($stmt->execute()) {
        // Append the new comment to the comments array
        $comments[] = ['comment' => $comment]; // Add the new comment to the array
    }
}

// Handle rating submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['rating'])) {
    $rating = intval($_POST['rating']);
    $stmt = $conn->prepare("INSERT INTO ratings (worker_id, rating) VALUES (?, ?)");
    $stmt->bind_param("ii", $worker_id, $rating);
    $stmt->execute();
}

// Fetch average rating
$avg_rating_result = $conn->query("SELECT AVG(rating) as avg_rating FROM ratings WHERE worker_id = $worker_id");
$avg_rating = $avg_rating_result->fetch_assoc()['avg_rating'] ?? 0;

// Capitalize the worker's full name
$full_name = ucwords(strtolower($worker['name']));

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Worker Profile - <?= htmlspecialchars($full_name) ?></title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif; /* Set a default font */
            background-color: #f9f9f9; /* Light background color */
            margin: 0; /* Remove default margin */
            padding: 20px; /* Add padding around the body */
        }

        .container {
            max-width: 800px; /* Limit the width of the container */
            margin: auto; /* Center the container */
            background: white; /* White background for the container */
            padding: 20px; /* Padding inside the container */
            border-radius: 8px; /* Rounded corners */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow */
        }

        .profile-container {
            display: flex;
            align-items: center; /* Center items vertically */
            margin-bottom: 20px; /* Space below the profile container */
        }

        .worker-photo {
            width: 120px; /* Fixed width for the photo */
            height: 120px; /* Fixed height for the photo */
            border-radius: 50%; /* Make the photo round */
            margin-right: 20px; /* Space between photo and details */
            border: 3px solid #38b673; /* Green border around the photo */
        }

        .worker-details {
            flex: 1; /* Allow details to take available space */
        }

        .call-button {
            background-color: #38b673; /* Button color */
            color: white; /* Text color */
            padding: 10px 20px; /* Padding for the button */
            border: none; /* Remove border */
            border-radius: 5px; /* Round corners */
            cursor: pointer; /* Pointer cursor on hover */
            text-align: center; /* Center text */
            font-size: 16px; /* Font size */
            margin-bottom: 20px; /* Space below the button */
        }

        .call-button:hover {
            background-color: #32a367; /* Darker color on hover */
        }

        .avg-rating {
            color: gold; /* Gold color for average rating */
            font-size: 24px; /* Font            /* Font size for average rating */
            margin-bottom: 20px; /* Space below the rating */
        }

        .star-rating {
            display: flex;
            direction: row-reverse;
            justify-content: center; /* Center the stars */
            margin: 10px 0; /* Space above the stars */
        }

        .star-rating input {
            display: none;
        }

        .star-rating label {
            font-size: 30px; /* Increased font size for stars */
            color: #ccc;
            cursor: pointer;
            transition: color 0.2s; /* Smooth color transition */
        }

        .star-rating input:checked ~ label {
            color: gold;
        }

        .star-rating label:hover,
        .star-rating label:hover ~ label {
            color: gold;
        }

        .submit-rating-button {
            background-color: #38b673; /* Button color */
            color: white; /* Text color */
            padding: 10px 20px; /* Padding for the button */
            border: none; /* Remove border */
            border-radius: 5px; /* Round corners */
            cursor: pointer; /* Pointer cursor on hover */
            font-size: 16px; /* Font size */
            margin-top: 10px; /* Space above the button */
        }

        .submit-rating-button:hover {
            background-color: #32a367; /* Darker color on hover */
        }

        h1, h2 {
            color: #333; /* Darker color for headings */
        }

        .comments {
            margin-top: 20px; /* Space above comments section */
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <h1><?= htmlspecialchars($full_name) ?>'s Profile</h1>
        <div class="profile-container">
            <img src="<?= htmlspecialchars($worker['photo']) ?>" alt="Profile Photo" class="worker-photo">
            <div class="worker-details">
                <p>Phone: <?= htmlspecialchars($worker['phone']) ?></p>
                <p>County: <?= htmlspecialchars($worker['county']) ?></p>
                <p>Constituency: <?= htmlspecialchars($worker['constituency']) ?></p>
                <p>Ward: <?= htmlspecialchars($worker['ward']) ?></p>
                <p>Category: <?= htmlspecialchars($worker['category']) ?></p> <!-- Displaying the worker's category -->
                
                <button class="call-button" onclick="window.location.href='tel:<?= htmlspecialchars($worker['phone']) ?>'">Call Worker</button>
                
                <h2 class="avg-rating">Average Rating: <?= round($avg_rating, 1) ?> ★</h2>
                
                <h2>Leave a Comment</h2>
                <form action="" method="POST">
                    <textarea name="comment" rows="4" cols="50" placeholder="Leave your comment here..." required></textarea>
                    <button type="submit">Submit Comment</button>
                </form>

                <h2>Rate this Worker</h2>
                <form action="" method="POST">
                    <div class="star-rating">
                        <input type="radio" id="star5" name="rating" value="5" />
                        <label for="star5">★</label>
                        <input type="radio" id="star4" name="rating" value="4" />
                        <label for="star4">★</label>
                        <input type="radio" id="star3" name="rating" value="3" />
                        <label for="star3">★</label>
                        <input type="radio" id="star2" name="rating" value="2" />
                        <label for="star2">★</label>
                        <input type="radio" id="star1" name="rating" value="1" />
                        <label for="star1">★</label>
                    </div>
                    <button type="submit" class="submit-rating-button">Submit Rating</button>
                </form>

                <h2>Comments</h2>
                <div class="comments">
                    <?php if (count($comments) > 0): ?>
                        <?php foreach ($comments as $comment): ?>
                            <p><?= htmlspecialchars($comment['comment']) ?></p>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No comments yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>