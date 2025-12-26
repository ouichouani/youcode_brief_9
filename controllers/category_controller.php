<?

if (basename($_SERVER['PHP_SELF']) != 'index.php') {
    session_start();
    include '../connection/connection.php';
}

include_once 'category.php' ;

use Category\Category ;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (empty($_SESSION['AuthUser'])) {
        header('location: ../index.php?Unauthorized');
        exit;
    }

    if (isset($_POST['create'])) {
        $category = new Category($_POST['name'], $_POST['limits'], $_POST['description']);
        $status = $category->create();
        if ($status) {
            header('location: ../index.php?cat_created');
            exit;
        } else {
            header('location: ../index.php?cat_cration_faild');
            exit;
        }
    } else {
        header('location: ../index.php?no_creation?');
        exit;
    }
} else {
    header('location: ../index.php?invalid_method');
    exit;
}


