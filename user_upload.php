<?php
include 'Dbconnection.php';
if ($argv[0] == "user_upload.php" && $argv[1] == null) {
    echo " Please run --help for Directives List";
} else if ($argv[1] == "--help") {
    directivesDisplay();
} else if($argv[1] == "--create_table") {
    $connect=createConnection();
    createDbTable($connect);
    
}else{
    echo "Not a valid Command";
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
