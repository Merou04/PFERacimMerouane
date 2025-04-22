<?php
// Disable warning display
error_reporting(E_ERROR | E_PARSE);

// Database connection parameters
$servername = "localhost";
$username = "root"; // default XAMPP username
$password = "root"; // default XAMPP password
$dbname = "dbpfe";

// Initialize variables
$status = "";
$message = "";

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $user_username = trim($_POST["username"]);
    $user_password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    $account_type = $_POST["account_type"];
    
    // Validate input
    if (empty($user_username) || empty($user_password) || empty($account_type)) {
        $status = "error";
        $message = "All fields are required";
    } elseif ($user_password !== $confirm_password) {
        $status = "error";
        $message = "Passwords do not match";
    } else {
        try {
            // Create database connection
            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            // Set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Create tables if they don't exist
            createTables($conn);
            
            // Check if username already exists
            $stmt = $conn->prepare("SELECT username FROM accounts WHERE username = :username");
            $stmt->bindParam(':username', $user_username);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $status = "error";
                $message = "Username already exists. Please choose another one.";
            } else {
                // Get account type ID (or create it if it doesn't exist)
                $account_type_id = getAccountTypeId($conn, $account_type);
                
                // Hash the password
                $hashed_password = password_hash($user_password, PASSWORD_DEFAULT);
                
                // Insert new account
                $stmt = $conn->prepare("INSERT INTO accounts (username, password, account_type_id) 
                                       VALUES (:username, :password, :account_type_id)");
                $stmt->bindParam(':username', $user_username);
                $stmt->bindParam(':password', $hashed_password);
                $stmt->bindParam(':account_type_id', $account_type_id);
                $stmt->execute();
                
                $status = "success";
                $message = "Account created successfully!";
            }
        } catch(PDOException $e) {
            $status = "error";
            $message = "Database error: " . $e->getMessage();
        }
        
        $conn = null; // Close connection
    }
}

// Helper function to create necessary tables if they don't exist
function createTables($conn) {
    // Create account types table
    $conn->exec("CREATE TABLE IF NOT EXISTS account_types (
        type_id INT AUTO_INCREMENT PRIMARY KEY,
        type_name VARCHAR(10) NOT NULL UNIQUE
    )");
    
    // Create accounts table
    $conn->exec("CREATE TABLE IF NOT EXISTS accounts (
        account_id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        account_type_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (account_type_id) REFERENCES account_types(type_id)
    )");
    
    // Insert default account types if table is empty
    $stmt = $conn->query("SELECT COUNT(*) FROM account_types");
    if ($stmt->fetchColumn() == 0) {
        $conn->exec("INSERT INTO account_types (type_name) VALUES 
            ('admin'), ('tier1'), ('tier2'), ('tier3')");
    }
}

// Helper function to get account type ID
function getAccountTypeId($conn, $account_type) {
    $stmt = $conn->prepare("SELECT type_id FROM account_types WHERE type_name = :type_name");
    $stmt->bindParam(':type_name', $account_type);
    $stmt->execute();
    
    if ($stmt->rowCount() == 0) {
        // Insert the account type if it doesn't exist
        $stmt = $conn->prepare("INSERT INTO account_types (type_name) VALUES (:type_name)");
        $stmt->bindParam(':type_name', $account_type);
        $stmt->execute();
        return $conn->lastInsertId();
    } else {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['type_id'];
    }
}

// Redirect back to the form with message parameters
header("Location: createacc.html?status=" . urlencode($status) . "&message=" . urlencode($message));
exit();
?>