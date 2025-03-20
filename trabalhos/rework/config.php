<?php

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'barbearia');

// Function to get database connection
function get_db_connection() {
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) {
            // More specific error logging
            error_log("Database connection error: ". $conn->connect_error. " - Error Code: ". $conn->connect_errno);
            throw new Exception("Falha na conexão com o banco de dados."); // User-friendly message
        }
        // Set character set (important for handling special characters)
        $conn->set_charset("utf8mb4"); // Or utf8, depending on your needs. utf8mb4 supports a wider range of characters.
        return $conn;
    } catch (Exception $e) {
        // Log the detailed error for debugging
        error_log($e->getMessage());
        // Display a generic user-friendly message (don't reveal sensitive info)
        die("Ocorreu um erro ao conectar ao banco de dados. Tente novamente mais tarde.");
    }
}?>