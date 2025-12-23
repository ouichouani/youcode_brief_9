<?php
    $servername = 'mysql' ; //container name
    $username = 'root' ;
    $password = 'ouichouani' ;
    $databasename = 'youcode_brief_7' ;

    $connection = new mysqli($servername , $username , $password , $databasename) ;

    if($connection->connect_error){
        die('connection fail : ' . $connection->connect_error);
    }


    
