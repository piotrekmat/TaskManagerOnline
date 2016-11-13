<?php
/**
 * Created by PhpStorm.
 * User: marcin
 * Date: 29.07.2016
 * Time: 19:46
 */

namespace Report\Model;


use \Exception;

class TimeReport
{

    //opisuje kolumny pliku
    const project = 1;
    const type = 2;
    const key = 3;
    const summary = 4;
    const prioryty = 5;
    const dateStarted = 6;
    const username = 7;
    const displayName = 8;
    const timeSpent = 9;
    const worklogDescription = 10;

    const sizeof = 11;

    protected $data = [];
    protected $dataStart = null;
    protected $dataEnd = null;


    public function __construct($srcFile)
    {
        if (!file_exists($srcFile)) {
            throw new Exception("Nie można załadować pliku TimeReport $srcFile");
        }
        ini_set("display_error", "off");
        error_reporting(0);

        $data = file_get_contents($srcFile);

        $data = array_map("trim", explode("<tr>", $data));
        foreach ($data as $row) {
            $this->data[] = array_map('strip_tags', array_map('trim', explode("<td>", $row)));
        }

        $this->setDataStart();
        $this->setDataEnd();

    }

    protected function setDataStart()
    {
        $column = array_column($this->data, self::dateStarted);
        sort($column);
        $this->dataStart = substr($column[0], 0, 10);
    }

    protected function setDataEnd()
    {
        $column = array_column($this->data, self::dateStarted);
        sort($column);
        $this->dataEnd = substr($column[sizeof($column)-2], 0, 10);
    }

    public function getData()
    {
        return $this->data;
    }

    public function getDataStart()
    {
        return $this->dataStart;
    }

    public function getDataEnd()
    {
        return $this->dataEnd;
    }

}