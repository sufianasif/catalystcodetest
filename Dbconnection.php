<?php

function createConnection()
{
    $data = file_get_contents("env.json");
    $json = json_decode($data, true);
    $user = $json["DB"]["User"];
    $password = $json["DB"]["Password"];
    $db = $json["DB"]["Database"];
    $host = $json["DB"]["Host"];
    $port = $json["DB"]["Port"];

    try {
        $conn = new PDO("mysql:host=$host;port=$port;dbname=$db", $user, $password);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        echo "\e[32m Database Connected successfully \n";
        return $conn;

    } catch (PDOException $e) {
        echo "\e[31m Connection failed: " . $e->getMessage();
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
    email VARCHAR(50),
    UNIQUE KEY unique_email (email)
    )";
            $conn->exec($sql);
            echo "\e[32m  Created users table successfully";
        } catch (PDOexception $e) {
            echo "\e[31m Something went wrong.Table could not be created" . $e->getMessage();
        }
    }
}
