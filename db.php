<?php // Run this .php code to connect with the database! Don't forget to close the connection afterwards.
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "bowlingdb"; // Change to your own database name.

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: ". $conn->connect_error);
    }
?>
