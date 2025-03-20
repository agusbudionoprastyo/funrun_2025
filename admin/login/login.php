<?php
// Include the db.php file to establish a database connection
include('../../helper/db.php');

// Start the session at the beginning of the script
session_start();

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    // Get user inputs
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Sanitize the email input to prevent XSS attacks
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);

    // Check if email exists in the database
    $stmt = $conn->prepare("SELECT user_id, user_name, password FROM auth WHERE email = ? AND active = 'true'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $user_name, $stored_password);
        $stmt->fetch();

        // Verify the password
        if (password_verify($password, $stored_password)) {
            // Login successful, store user info in session
            $_SESSION['user_id'] = $id;
            $_SESSION['user_name'] = $user_name;
            
            // Redirect to the admin page
            header("Location: /admin");
            exit(); // Make sure to call exit() to stop further script execution
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