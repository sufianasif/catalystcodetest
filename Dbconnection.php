<?php

function createConnection()
{
    
    $user = "root";
    $password = "root";
    $db = "myDBPDO";
    $host = "127.0.0.1";
    $port = "8889";

    try {
        $conn = new PDO("mysql:host=$host;port=$port;dbname=$db", $user, $password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        echo "Database Connected successfully \n";
        return $conn;

    } catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }

}

function createDbTable($conn)
{
    $tablename = "Users";

    $is_table = $conn->query("SHOW TABLES LIKE '" . $tablename . "'");
    if ($is_table->rowCount() == "1") {
        echo "Table exists! \n";} else {
        try {
            $sql = "CREATE TABLE IF NOT EXISTS `Users` (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(30) NOT NULL,
    surname VARCHAR(30) NOT NULL,
    email VARCHAR(50)
    )";
            $conn->exec($sql);
            echo "Created users table successfully";
        } catch (PDOexception $e) {
            echo "Something went wrong.Table could not be created" . $e->getMessage();
        }
    }
}