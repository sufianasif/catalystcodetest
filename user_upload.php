<?php
include 'Dbconnection.php';
$bool = 0;
if ($argv[0] == "user_upload.php" && $argv[1] == null) {
    echo " Please run --help for Directives List";
} else if ($argv[1] == "--help") {
    directivesDisplay();
} else if ($argv[1] == "--create_table") {
    $data = file_get_contents("env.json");
    $json = json_decode($data, true);
    foreach ($json as $input => $input_value) {
        if (empty($input_value)) {
            echo "Please enter " . $input . " to create table . For commands run --help\n";
            $bool = 1;
            break;
        }
    }
    if ($bool == 0) {
        $connect = createConnection();
        createDbTable($connect);
    }
} else if ($argv[1] == "-u" && !empty($argv[2])) {
    writeJson("User", $argv[2]);
} else if ($argv[1] == "-p" && !empty($argv[2])) {
    writeJson("Password", $argv[2]);
} else if ($argv[1] == "-h" && !empty($argv[2])) {
    writeJson("Host", $argv[2]);
} else if ($argv[1] == "-db" && !empty($argv[2])) {
    writeJson("Database", $argv[2]);
} else if ($argv[1] == "-port" && !empty($argv[2])) {
    writeJson("Port", $argv[2]);
} else {
    echo "Not a valid Command \n";
}

//fucntion takes the Mysql env data from command line input and writes in the env.json file
function writeJson($datakey, $datavalue)
{
    $data = file_get_contents("env.json");
    $json = json_decode($data, true);

    $json[$datakey] = $datavalue;
    $fh = fopen("env.json", 'w')
    or die("" . $datakey . "not set.Error opening output file");
    fwrite($fh, json_encode($json, JSON_UNESCAPED_UNICODE));
    echo ("" .$datakey . " is set.");
    fclose($fh);
}

//function displays all the valid directives
function directivesDisplay()
{
    $arrayofdirectives = [
        "--file [csv file name] – this is the name of the CSV to be parsed",
        "--create_table – this will cause the MySQL users table to be built (and no further action will be taken)",
        "--dry_run – this will be used with the --file directive in case we want to run the script but not insert into the DB. All other functions will be executed, but the
        database won't be altered",
        "-u - MySQL username",
        "-p - MySQL password",
        "-h - MySQL host",
    ];
    foreach ($arrayofdirectives as $result) {
        echo $result . "\n";
    }
}
