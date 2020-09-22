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
    foreach ($json["DB"] as $input => $input_value) {
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
} else if ($argv[1] == "--run" && empty($argv[2])) {
    $runbool = 1;
    readCsv($runbool);
} else if ($argv[1] == "-file") {
    getFilename($argv[2]);
} else if ($argv[1] == "--dry_run" && empty($argv[2])) {
    $runbool = 0;
    readCsv($runbool);
} else {
    echo "Not a valid Command \n";
}

// function reads data from CSV file and inserts into the database
function readCsv($runstate)
{
    $data = file_get_contents("env.json");
    $json = json_decode($data, true);
    $file = $json["File"]["Filename"];

    if (empty($file)) {
        echo "Please run --file [filename] first and enter the file name";
    } else {

        $row = 1;
        $flag = true;
        $check_file = 0;
        if (($handle = fopen($file, "r")) !== false) {
            $connect = createConnection();
            createDbTable($connect);
            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                $check_file = 1;
                if ($flag) {$flag = false; // disregards the first line in the .csv file
                    continue;}
                $num = count($data);
                $row++;
                for ($c = 0; $c < $num; $c++) {
                    echo $data[$c] . "\n";
                }
                //check for valid email!
                if (filter_var($data[2], FILTER_VALIDATE_EMAIL)) {
                    //check to run or to dry run
                    if ($runstate == 1) {
                        try {

                            $sql1 = $connect->prepare("INSERT INTO `Users` (firstname, surname, email)
    VALUES (:param1,:param2,:param3)");
                            $sql1->bindParam(':param1', ucwords($data[0]), PDO::PARAM_STR);
                            $sql1->bindParam(':param2', ucwords($data[1]), PDO::PARAM_STR);
                            $sql1->bindParam(':param3', $data[2], PDO::PARAM_STR);
                            $sql1->execute();
                        } catch (PDOexception $e) {
                            echo "Something went wrong.Data could not be inserted" . $e->getMessage();
                        }
                    }
                } else {
                    fwrite(STDOUT, "invalid email \n");
                }

            }
            $check_file == 0 ? fwrite(STDOUT, "Empty File") : null;
        }
        fclose($handle);
    }
}

//function takes the Mysql env data from command line input and writes in the env.json file
function writeJson($datakey, $datavalue)
{
    $data = file_get_contents("env.json");
    $json = json_decode($data, true);

    $json["DB"][$datakey] = $datavalue;
    $fh = fopen("env.json", 'w')
    or die("" . $datakey . "not set.Error opening output file");
    fwrite($fh, json_encode($json, JSON_UNESCAPED_UNICODE));
    echo ("" . $datakey . " is set.");
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
        "-u [MySQL username]",
        "-p [MySQL password]",
        "-h [MySQL host]",
        "-port [MySQL port]",
        "-db [MySQL Database]",
        "--run - this will run all the functions and add the data to the database table",
    ];
    foreach ($arrayofdirectives as $result) {
        echo $result . "\n";
    }
}

function getFilename($file)
{
    $data = file_get_contents("env.json");
    $json = json_decode($data, true);

    $json["File"]["Filename"] = $file;
    $fh = fopen("env.json", 'w')
    or die("Error opening output file");
    fwrite($fh, json_encode($json, JSON_UNESCAPED_UNICODE));
    fclose($fh);
    echo "file name saved succeessfully. Now you can either choose to --run or --dry_run the script \n";
}
