<?php

/**
 * Created by PhpStorm.
 * User: marcin
 * Date: 25.06.2016
 * Time: 20:18
 */

namespace Report\Logic;

use ZendDeveloperTools\Report;
use \Report\Model\TimeReport;


class File extends \Application\Logic
{
    protected static $fileInput = "/tmp/file.xls";

    protected static $fileOutput = "/tmp/file.xls";

    protected $_oForm = '\Report\Form\File';

    protected $dataStart = null;

    protected $dataEnd = null;

    protected $data;

    static function file($output = false)
    {
        if ($output) {
            return dirname(__DIR__) . self::$fileOutput;
        } else {
            return dirname(__DIR__) . self::$fileInput;
        }
    }


    public function init()
    {

        parent::init();

        $this->removeFile();

        $post = array_merge_recursive(
            $this->params()->fromPost(), $this->params()->fromFiles()
        );

        $this->form()->setData($post);
        if ($this->form()->isValid()) {
            $this->form()->getData();
        }
    }

    public function removeFile()
    {
        try {
            $file = self::file();
            if (file_exists($file))
                unlink($file);
        } catch (Exception $ex) {
            $this->flashMessanger()->addErrorMessage("Nie można usunąć starego pliku, przed wygenerowaniem nowego. Należy sprawdzić uprawnienia: $file");
        }
    }

    public function decodeFide()
    {
        $result = [];
        $timeReport = new TimeReport(self::file());

        $this->dataEnd = $timeReport->getDataEnd();
        $this->dataStart = $timeReport->getDataStart();

        $data = $timeReport->getData();
        foreach ($data as $row) {
            $key = $row[TimeReport::key];
            $timeSpent = $row[TimeReport::timeSpent];
            $comment = $row[TimeReport::worklogDescription];

            if (empty($key) or sizeof($row) != TimeReport::sizeof) {
                continue;
            }

            //zerowanie
            if (!isset($result[$key])) {

                $result[$key]['HelpDeskPeriodTime']
                    = $result[$key]['ValuePeriodTime']
                    = $result[$key]['DeveloperPeriodTime']
                    = $result[$key]['TimePeriodSpent']
                    = 0;

                $sResult[$key]['HelpDeskPeriodTime']
                    = $sResult[$key]['ValuePeriodTime']
                    = $sResult[$key]['DeveloperPeriodTime']
                    = $sResult[$key]['TimePeriodSpent']
                    = 0;
            }

            $result[$key]['FULLTIME'] += $timeSpent;
            $sResult[$key]['TimePeriodSpent'] = number_format($result[$key]['FULLTIME'], 2, ',', '');

            if (strpos($comment, "HEL") !== false or strpos($comment, "ORG") !== false) {
                $result[$key]['HELP'] += $timeSpent;
                $sResult[$key]['HelpDeskPeriodTime'] = number_format($result[$key]['HELP'], 2, ',', '');
            } elseif (strpos($comment, "VAL") !== false) {
                $result[$key]['VAL'] += $timeSpent;
                $sResult[$key]['ValuePeriodTime'] = number_format($result[$key]['VAL'], 2, ',', '');
            } else {
                $result[$key]['DEV'] += $timeSpent;
                $sResult[$key]['DeveloperPeriodTime'] = number_format($result[$key]['DEV'], 2, ',', '');
            }
        }

        $this->data = $sResult;
    }


    public function moreInformation()
    {
        $keys = array_keys($this->data);
        $jira = new Jira();
        $jira->searchIssue($keys);
        $data = $jira->getData();

        foreach ($data as $item) {
            $key = $item->getKey();
            if (isset($this->data[$key])) {

                $dateCreated = substr($item->getCreated(), 0, 10);

                $data = [
                    'Project' => $item->getProject()['name'],
                    'Key' => $item->getKey(),
                    'Summary' => $item->getSummary(),
                    'Status' => $item->getStatus()['name'],
                    'Type' => $item->getIssueType()['name'],
                    'Created' => $dateCreated,
                    'Resolution' => $item->getResolution()['name'],
                    'ResolutioDate' => substr($item->getResolutionDate(), 0, 10),
                    'Update' => substr($item->getUpdated(), 0, 10),
                    'Labels' => str_replace('_', ' ', implode(",", $item->getLabels())),
                    'Reporter' => $item->getReporter()['displayName'],
                    'Assignee' => $item->getAssignee()['displayName'],
                    'BusinessValue' => number_format($item->get("Business Value"), 2, ',', ''),
                ];

                // jeśli task tak jest poza okresem TimeSheet
                // fest prowizorka
                if ($this->dataStart >= $dateCreated and $this->dataEnd <= $dateCreated) {
                    $worklogs = $jira->getWorklog($key);
                    $result = [];

                    foreach ($worklogs['worklogs'] as $worklog) {

                        $comment = $worklog['comment'];
                        $timeSpent = $worklog['timeSpentSeconds'];

                        $result['FULL'] += $timeSpent;


                        if (strpos($comment, "HEL") !== false or strpos($comment, "ORG") !== false) {
                            $result['HELP'] += $timeSpent;

                        } elseif (strpos($comment, "VAL") !== false) {
                            $result['VAL'] += $timeSpent;

                        } else {
                            $result['DEV'] += $timeSpent;
                        }
                    }

                    $data['TimeSpent'] = number_format($result['FULL'] / 60 / 60, 2, ',', '');
                    $data['DeveloperTime'] = number_format($result['DEV'] / 60 / 60, 2, ',', '');
                    $data['ValueTime'] = number_format($result['VAL'] / 60 / 60, 2, ',', '');
                    $data['HelpDeskTime'] = number_format($result['HELP'] / 60 / 60, 2, ',', '');

                } else {
                    $data['TimeSpent'] = $this->data[$key]['TimePeriodSpent'];
                    $data['DeveloperTime'] = $this->data[$key]['DeveloperPeriodTime'];
                    $data['ValueTime'] = $this->data[$key]['ValuePeriodTime'];
                    $data['HelpDeskTime'] = $this->data[$key]['HelpDeskPeriodTime'];


                }

                $this->data[$key] = $data + $this->data[$key];

            }
        }
    }

    public function setColumnName()
    {
        // ustawienie nazw kolumn
        $this->data['Key'] = [
            'Project',
            'Key',
            'Summary',
            'Status',
            'Type',
            'Created',
            'Resolution',
            'ResolutioDate',
            'Update',
            'Labels',
            'Reporter',
            'Assignee',
            'BusinessValue',
            'TimeSpent',
            'DeveloperTime',
            'ValueTime',
            'HelpDeskTime',
            'TimePeriodSpent',
            'DeveloperPeriodTime',
            'ValuePeriodTime',
            'HelpDeskPeriodTime',
        ];
    }


    public function getCSV()
    {
        ini_set("error_display", "0");
        error_reporting("0");

        // przygotowanie danych $data
        $this->decodeFide();
        $this->moreInformation();
        $this->setColumnName();


        $oCsv = new \Report\Model\Csv($this->data, ';', '"', false);
        $sCsv = $oCsv->render();
        return $sCsv;


    }
}