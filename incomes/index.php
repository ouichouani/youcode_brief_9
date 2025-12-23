<?php

include 'connection/connection.php';
$incomes = [] ;
$total_incomes = 0 ;

function index_incomes($connection, &$table , &$total_incomes)
{
    $statement = $connection->prepare('SELECT * FROM incomes WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)');
    $statement->execute();

    $results = $statement->get_result();

    while ($row = $results->fetch_assoc()) {
        $table[] = $row;
        $total_incomes += $row['montant'] ;
    }

    $statement->close();
}


index_incomes($connection, $incomes , $total_incomes) ;
// header("Location: ../index.php");
// exit;
