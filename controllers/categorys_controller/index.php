<?php
// session_start();
// include '../../connection/connection.php';
include 'connection/connection.php';
unset($_SESSION['error']);
unset($_SESSION['success']);

$CATEGORIES = [];
$ERRORS = [];

if (isset($_SESSION['AuthUser'])) {

    $statement = $connection->query('SELECT * FROM categories WHERE user_id = ' . $_SESSION['AuthUser']['id']);

    if (!$statement) {
        $_SESSION['error'] = 'Database error: ' . $connection->error;
        $ERRORS[] = 'Query execution failed';
        $connection->close();
        header('Location: ../../index.php?error_category_fetched');
        exit;
    }

    if ($statement->num_rows > 0) {
        while ($row = $statement->fetch_assoc()) {
            $CATEGORIES[] = $row;
        }
        $_SESSION['CATEGORIES'] = $CATEGORIES;
        $_SESSION['success'] = 'Data fetched successfully';
    } else {
        $_SESSION['CATEGORIES'] = [];
    }

    $statement->close();
} else {
    $_SESSION['error'] = ['message' => 'user must be authenticated first'];
    $_SESSION['CATEGORIES'] = [];
}
$connection->close();

// header('Location: ../../index.php?category_fetched_successfully');
// exit;