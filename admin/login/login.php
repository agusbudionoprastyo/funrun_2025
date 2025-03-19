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

// Close the database connection when done
closeConnection();
?>