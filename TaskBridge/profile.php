<?php
session_start();
include 'includes/db.php'; // Include database connection

if (!isset($_SESSION['worker_id'])) {
    header("Location: login.php");
    exit();
}

$worker_id = $_SESSION['worker_id'];
$stmt = $conn->prepare("SELECT * FROM workers WHERE id = ?");
$stmt->bind_param("i", $worker_id);
$stmt->execute();
$result = $stmt->get_result();
$worker = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle form submission for profile update
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $constituency = $_POST['constituency'];
    $ward = $_POST['ward'];
    $county = $_POST['county'];
    $password = $_POST['password']; // New password field

    // Handle file upload if a new photo is uploaded
    $photoPath = $worker['photo']; // Default to current photo
    if (!empty($_FILES['photo']['name'])) {
        $photo = $_FILES['photo'];
        $photoPath = 'images/profile-photos/' . basename($photo['name']);
        
        // Check if the file is an image
        $check = getimagesize($photo['tmp_name']);
        if ($check === false) {
            $_SESSION['error'] = "File is not an image.";
        } elseif (move_uploaded_file($photo['tmp_name'], $photoPath)) {
            // Photo uploaded successfully
        } else {
            $_SESSION['error'] = "Error uploading file.";
        }
    }

    // Update the database
    $stmt = $conn->prepare("UPDATE workers SET name = ?, email = ?, phone = ?, constituency = ?, ward = ?, county = ?, photo = ? WHERE id = ?");
    $stmt->bind_param("sssssssi", $name, $email, $phone, $constituency, $ward, $county, $photoPath, $worker_id);

    if ($stmt->execute()) {
        // Update password if provided
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE workers SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $hashedPassword, $worker_id);
            $stmt->execute();
        }
    } else {
        $_SESSION['error'] = "Update failed: " . $stmt->error;
    }
}

// Handle account deletion
if (isset($_POST['delete_account'])) {
    $stmt = $conn->prepare("DELETE FROM workers WHERE id = ?");
    $stmt->bind_param("i", $worker_id);
    if ($stmt->execute()) {
        session_destroy(); // Destroy the session
        header("Location: login.php"); // Redirect to login page
        exit();
    } else {
        $_SESSION['error'] = "Account deletion failed: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskBridge - Profile</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif; /* Set a default font */
            background-color: #f9f9f9; /* Light background color */
            margin: 0; /* Remove default margin */
            padding: 20px; /* Add padding around the body */
        }

        .container {
            max-width: 600px; /* Limit the width of the container */
            margin: auto; /* Center the container */
            background: white; /* White background for the container */
            padding: 20px; /* Padding inside the container */
            border-radius: 8px; /* Rounded corners */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow */
        }

        .profile-photo {
            width: 100px; /* Set the width of the profile photo */
            height: auto; /* Maintain aspect ratio */
            border-radius: 50%; /* Make it circular */
            margin-bottom: 20px; /* Space below the photo */
        }

        h1, h2 {
            color: #333; /* Darker color for headings */
        }

        label {
            display: block; /* Make labels block elements */
            margin: 10px 0 5px; /* Space around labels */
            color: #555; /* Slightly lighter color for labels */
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="file"],
        select {
            width: 100%; /* Full width for inputs */
            padding: 10px; /* Padding inside inputs */
            margin-bottom: 15px; /* Space below inputs */
            border: 1px solid #ccc; /* Light border */
            border-radius: 4px; /* Rounded corners */
            box-sizing: border-box; /* Include padding in width */
        }

        button {
            background-color: #28a745; /* Green background for buttons */
            color: white; /* White text */
            padding: 10px; /* Padding inside buttons */
            border: none; /* No border */
            border-radius: 4px; /* Rounded corners */
            cursor: pointer; /* Pointer cursor on hover */
            width: 100%; /* Full width for buttons */
        }

        button:hover {
            background-color: #218838; /* Darker green on hover */
        }

        .error {
            color: red; /* Red color for error messages */
        }

        .success {
            color: green; /* Green color for success messages */
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <h1>Your Profile</h1>
        <img src="<?php echo htmlspecialchars($worker['photo']); ?>" alt="Profile Photo" class="profile-photo">
        <h2><?php echo htmlspecialchars($worker['name']); ?></h2>
        <p>Email: <?php echo htmlspecialchars($worker['email']); ?></p>
        <p>Phone: <?php echo htmlspecialchars($worker['phone']); ?></p>
        <p>Constituency: <?php echo htmlspecialchars($worker['constituency']); ?></p>
        <p>Ward: <?php echo htmlspecialchars($worker['ward']); ?></p>
        <p>County: <?php echo htmlspecialchars($worker['county']); ?></p>
        
        <h2>Edit Profile</h2>
        <?php if (isset($_SESSION['error'])): ?>
            <p class="error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <p class="success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></p>
        <?php endif; ?>
        <form action="" method="POST" enctype="multipart/form-data">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($worker['name']); ?>" required>

            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($worker['email']); ?>" required>

            <label for="phone">Phone</label>
            <input type="text" name="phone" id="phone" value="<?php echo htmlspecialchars($worker['phone']); ?>" required>

            <label for="constituency">Constituency</label>
            <input type="text" name="constituency" id="constituency" value="<?php echo htmlspecialchars($worker['constituency']); ?>" required>

            <label for="ward">Ward</label>
            <input type="text" name="ward" id="ward" value="<?php echo htmlspecialchars($worker['ward']); ?>" required>

            <label for="county">County</label>
            <input type="text" name="county" id="county" value="<?php echo htmlspecialchars($worker['county']); ?>" required>

            <label for="password">New Password (leave blank to keep current)</label>
            <input type="password" name="password" id="password" autocomplete="new-password">

            <label for="photo">Profile Photo</label>
            <input type="file" name="photo" id="photo">

            <button type="submit">Update Profile</button>
        </form>

        <h2>Delete Account</h2>
        <form action="" method="POST">
            <p>Are you sure you want to delete your account? This action cannot be undone.</p>
            <button type="submit" name="delete_account">Delete Account</button>
        </form>
    </div>
</body>
</html>       $_SESSION['success'] = "Profile updated successfully!";
        header("Location: profile.php");
        exit();
 