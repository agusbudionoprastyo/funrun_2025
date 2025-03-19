<?php
// Include the db.php file to establish a database connection
include('../../helper/db.php');

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    // Get user inputs
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if email exists in the database
    $stmt = $conn->prepare("SELECT user_id, user_name, password FROM auth WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $user_name, $stored_password);
        $stmt->fetch();

        // Verify the password
        if (password_verify($password, $stored_password)) {
            echo "Login successful!";
            // You can start a session here and redirect the user to a protected page
        } else {
            echo "Invalid credentials!";
        }
    } else {
        echo "Email not found!";
    }

    $stmt->close();
}

// Handle sign-up form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['signup'])) {
    // Get user inputs
    $user_name = $_POST['user_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Hash the password before storing it
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if the email already exists
    $stmt = $conn->prepare("SELECT user_id FROM auth WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "Email already taken!";
    } else {
        // Insert new user into the database
        $stmt = $conn->prepare("INSERT INTO auth (user_name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $user_name, $email, $hashed_password);

        if ($stmt->execute()) {
            echo "Sign-up successful!";
        } else {
            echo "Error: " . $stmt->error;
        }
    }

    $stmt->close();
}

// Close the database connection when done
closeConnection();
?>