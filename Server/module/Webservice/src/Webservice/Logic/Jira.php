<?php
/**
 * Created by PhpStorm.
 * User: marcin
 * Date: 25.06.2016
 * Time: 21:11
 */

namespace Report\Logic;

use \chobie\Jira\Api;
use \chobie\Jira\Api\Authentication\Basic;
use chobie\Jira\Issue;
use \chobie\Jira\Issues\Walker;

define("JIRAHOST", "https://mccomprojects.atlassian.net");
define("JIRAUSER", "mccom_mzw@mccom.pl");
define("JIRAPASSWORD", "zaq1@wsx1@");

class Jira
{
    /**
     * @var Api
     */
    protected $api;

    /**
     * @var Walker
     */
    protected $walker;

    /**
     * @var Issue[]
     */
    protected $data = [];

    public function __construct()
    {
        $this->api = new Api(JIRAHOST, new Basic(JIRAUSER, JIRAPASSWORD));
        $this->walker = new Walker($this->api);
    }

    public function searchIssue($data)
    {
        $jql = sprintf("issuekey in (%s)", implode(',', $data));
        $this->setJql($jql);
        return $this;
    }

    public function searchByDate($start, $end)
    {
        $jql = [];
        if ($start) {
            $jql[] = sprintf("worklogDate >= %s", $start);
        }

        if ($end) {
            $jql[] = sprintf("worklogDate <= %s", $end);
        }

        $jql = implode(" and ", $jql);
        $this->setJql($jql);
        return $this;
    }


    public function getWorklog($key)
    {
        return $this->api->getWorklogs($key, [], true);
    }

    public function setJql($string)
    {
        $this->walker->push($string);
        $this->setData();
        return $this;
    }

    protected function setData()
    {
        foreach ($this->walker as $item) {
            $this->data[] = $item;
        }
        return $this;
    }

    /**
     * @return Issue[]
     */
    public function getData()
    {
        return $this->data;
    }

}