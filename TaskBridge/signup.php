<?php
session_start();
include 'includes/db.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle form submission
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $constituency = $_POST['constituency'];
    $ward = $_POST['ward'];
    $county = $_POST['county'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $gender = $_POST['gender'];
    $category = $_POST['category'];

    // Handle file upload
    $photo = $_FILES['photo'];
    $photoPath = 'images/profile-photos/' . basename($photo['name']);
    
    // Check if the file is an image
    $check = getimagesize($photo['tmp_name']);
    if ($check === false) {
        $_SESSION['error'] = "File is not an image.";
    } elseif (move_uploaded_file($photo['tmp_name'], $photoPath)) {
        // Insert into the database
        $stmt = $conn->prepare("INSERT INTO workers (name, email, phone, constituency, ward, county, password, gender, category, photo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssss", $name, $email, $phone, $constituency, $ward, $county, $password, $gender, $category, $photoPath);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Registration successful!";
            header("Location: login.php");
            exit();
        } else {
            $_SESSION['error'] = "Registration failed: " . $stmt->error;
        }
    } else {
        $_SESSION['error'] = "Error uploading file.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskBridge - Sign Up</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4; /* Light background color */
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 500px; /* Limit the width of the form */
            margin: 50px auto; /* Center the form */
            padding: 20px;
            background-color: #ffffff; /* White background for the form */
            border-radius: 8px; /* Rounded corners */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow */
        }

        h1 {
            text-align: center; /* Center the heading */
            color: #333; /* Darker color for the heading */
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        select,
        input[type="file"],
        button {
            width: 100%; /* Full width for inputs */
            padding: 10px; /* Padding inside inputs */
            margin: 10px 0; /* Space between inputs */
            border: 1px solid #ccc; /* Light border */
            border-radius: 4px; /* Rounded corners */
            box-sizing: border-box; /* Include padding in width */
        }

        button {
            background-color: #38b673; /* Button color */
            color: white; /* Text color */
            border: none; /* Remove border */
            cursor: pointer; /* Pointer cursor on hover */
        }

        button:hover {
            background-color: #32a367; /* Darker color on hover */
        }

        .error {
            color: red; /* Red color for error messages */
            text-align: center; /* Center error messages */
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <h1>Sign Up</h1>
        <form action="" method="POST" enctype="multipart/form-data">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="phone" placeholder="Phone Number" required>
            <input type="text" name="constituency" placeholder="Constitu ency" required>
            <input type="text" name="ward" placeholder="Ward" required>
            <input type="text" name="county" placeholder="County" required>
            <input type="password" name="password" placeholder="Password" required>
            <select name="gender" required>
                <option value="">Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>
            <select name="category" required>
                <option value="">Select Category</option>
                <option value="Mechanic">Mechanic</option>
                <option value="Electrician">Electrician</option>
                <option value="Plumber">Plumber</option>
                <option value="Carpenter">Carpenter</option>
                <option value="Painter">Painter</option>
                <option value="Mama Fua">Mama Fua</option>
                <option value="Cleaner">Cleaner</option>
            </select>
            <input type="file" name="photo" accept="image/*" required>
            <button type="submit">Sign Up</button>
        </form>
        <?php if (isset($_SESSION['error'])): ?>
            <p class="error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>