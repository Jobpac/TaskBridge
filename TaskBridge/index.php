<?php
session_start();
include 'includes/db.php'; // Include database connection

// Define the categories explicitly
$categories = [
    'Mechanic',
    'Electrician',
    'Plumber',
    'Carpenter',
    'Painter',
    'Mama Fua',
    'Cleaner'
];

// Function to calculate average rating for a worker
function getAverageRating($workerId, $conn) {
    $result = $conn->query("SELECT AVG(rating) as average_rating FROM ratings WHERE worker_id = $workerId");
    $row = $result->fetch_assoc();
    return $row['average_rating'] ? round($row['average_rating'], 1) : 'No ratings yet';
}

// Fetch workers from the database
$workers = $conn->query("SELECT * FROM workers")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskBridge - Home</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .category-box {
            display: inline-block;
            padding: 10px 20px;
            margin: 10px;
            background-color: #38b673;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
        }

        .category-box:hover {
            background-color: #32a367;
        }

        .workers {
            display: flex;
            flex-wrap: wrap; /* Allow wrapping of cards */
            justify-content: center; /* Center the cards */
        }

        .worker-card {
            border: 1px solid #ccc;
            padding: 15px;
            margin: 10px;
            border-radius: 5px;
            display: inline-block;
            width: 250px; /* Fixed width */
            height: auto; /* Allow height to adjust based on content */
            vertical-align: top;
            text-align: center; /* Center text */
            overflow: hidden; /* Prevent overflow */
        }

        .worker-card img {
            width: 100%; /* Make the image responsive */
            height: 150px; /* Set a fixed height for the image */
            object-fit: cover; /* Ensure the image covers the area without distortion */
            border-radius: 5px; /* Optional: round the corners of the image */
        }

        .worker-card h3 {
            margin: 10px 0; /* Add some margin for spacing */
        }

        .worker-card p {
            margin: 5px 0; /* Add some margin for spacing */
        }

        .average-rating {
            font-size: 18px;
            color: #38b673; /* Color for average rating */
            margin: 10px 0;
        }

        .view-button {
            background-color: #38b673; /* Button color */
            color: white; /* Text color */
            padding: 8px 15px; /* Padding for the button */
            border: none; /* Remove border */
            border-radius: 5px; /* Round corners */
            cursor: pointer; /* Pointer cursor on hover */
            text-align: center; /* Center text */
            margin-top: 10px; /* Space above the button */
            display: inline-block; /* Allow margin and padding */
        }

        .view-button:hover {
            background-color: #32a367; /* Darker color on hover */
        }

        /* Responsive Design */
        @media (max-width: 600px) {
            .worker-card {
                width: 100%; /* Full width for cards on small screens */
                margin: 5px 0; /* Reduce margin on small screens */
            }

            .category-box {
                padding: 8px 15px; /* Adjust padding for smaller screens */
                margin: 5px; /* Adjust margin for smaller screens */
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <h1>Welcome to TaskBridge!!!</h1>
        <h4>Imagine this: You’re in a new area, and suddenly, you need urgent help. Your car breaks down, you need a plumber, or maybe you’re just looking for someone to assist with a quick household task. You don’t know anyone nearby, but you need a skilled professional fast. That’s where TaskBridge comes in.

TaskBridge is your ultimate solution for finding trusted service providers quickly and efficiently, no matter where you are. Whether it’s a mechanic, an electrician, a carpenter, or even a house cleaner, TaskBridge connects you with skilled workers in your current location. Simply search for the service you need, filter by location, and get connected to local professionals in seconds.</h4>
        <div class="buttons">
            <a href="signup.php" class="btn">Sign Up</a>
            <a href="login.php" class="btn">Login</a>
        </div>

        <h2>Categories</h2>
        <div class="categories">
            <?php foreach ($categories as $category): ?>
                <a href="workers.php?category=<?= htmlspecialchars($category) ?>" class="category-box"><?= htmlspecialchars($category) ?></a>
            <?php endforeach; ?>
        </div>

        <h2>Available Workers</h2>
        <div class="workers">
            <?php if (count($workers) > 0): ?>
                <?php foreach ($workers as $worker): ?>
                    <div class="worker-card">
                        <img src="<?= htmlspecialchars($worker['photo']) ?: 'path/to/default/image.jpg' ?>" alt="<?= htmlspecialchars($worker['name']) ?>">
                        <h3><?= htmlspecialchars($worker['name']) ?></h3>
                        <p>Category: <?= htmlspecialchars($worker['category']) ?></p>
                        <p>County: <?= htmlspecialchars($worker['county']) ?></p>
                        <p>Constituency: <?= htmlspecialchars($worker['constituency']) ?></p>
                        <p>Ward: <?= htmlspecialchars($worker['ward']) ?></p>
                        <div class="average-rating">Rating: <?= getAverageRating($worker['id'], $conn) ?></div>
                        <a href="view_worker.php?id=<?= $worker['id'] ?>" class="view-button">View Details</a> <!-- Updated link -->
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No workers available at the moment.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>