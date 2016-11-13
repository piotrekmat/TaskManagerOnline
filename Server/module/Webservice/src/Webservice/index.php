<?php

include_once 'jira.php';
include_once 'input.php';
include_once 'output.php';
include_once 'PHPExcel/Classes/PHPExcel.php';

/**
 * Created by PhpStorm.
 * User: marcin
 * Date: 13.06.2016
 * Time: 23:09
 */
class index
{
    protected $params;

    public function __construct($params)
    {
        $this->params = $params;
        $this->run();
    }

    public function run()
    {

    }
}


//$index = new index($argv);
$inputFileName = 'exportData.xls';

$data = file_get_contents($inputFileName);
//$data = strip_tags($data);
//$data = array_map("trim", explode("\n", $data));
$data = array_map("trim", explode("<tr>", $data));
foreach ($data as $row) {
    $explode[] = array_map('strip_tags', explode("<td>", $row));
}
foreach ($explode as $row) {

    $key = trim($row[3]);
    $programmer = trim($row[9]);
    $timeSpent = (double)trim($row[10]);

    $result[$key]['summary'] = $result[$row[3]]['summary'] + $timeSpent;
    $result[$key]['HELP'] = $result[$row[3]]['VAL'] = $result[$row[3]]['DEV'] = 0;

    if (strpos($row[11], "HEL") !== false) {
        $result[$key]['HELP'] = $result[$row[3]]['HELP'] + $timeSpent;
    } elseif (strpos($row[11], "VAL") !== false) {
        $result[$key]['VAL'] = $result[$row[3]]['VAL'] + $timeSpent;
    } else {
        $result[$row[3]]['DEV'] = $result[$row[3]]['DEV'] + $timeSpent;
    }
    $result[$key][$programmer] = $timeSpent;
//    $result[$key]['data'][] = $row;
}

var_dump($result);


