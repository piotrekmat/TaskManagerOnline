<?php
/**
 * Created by PhpStorm.
 * User: kudlaty01
 * Date: 15.02.16
 * Time: 14:49
 */


namespace Webservice\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * ActionEuropeFeed
 *
 * @ORM\Table(name="service.action_europe_feeds",indexes={
 *  @ORM\Index(name="IDX_E52FF8EE8A5E1A6E", columns={"order_id"})
 * })
 * @ORM\Entity(repositoryClass="Webservice\Repository\ActionEuropeFeedRepository")
 */
class ActionEuropeFeed {
	use \Application\Behavior\MagicAccessTrait;

	/**
	 * Also the filename in Data\Xml\ActionEurope\FeedType\$id
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;

	/**
	 * The feed type
	 *
	 * For types see enum ActionEuropeFeedStatusType
	 *
	 * @ORM\Column(type="integer")
	 * @var integer
	 */
	protected $feedType;

	/**
	 * @ORM\Column(type="datetime", nullable=TRUE)
	 * @var datetime
	 */
	protected $feedDate;

	/**
	 * @ORM\Column(name="order_id", type="integer", nullable=TRUE)
	 * @var integer
	 */
	protected $orderId;


	/**
	 * 0    success
	 * 1    error
	 * @ORM\Column(type="smallint", nullable=TRUE)
	 * @var smallint
	 */
	protected $feedStatus;


	/**
	 * setter for xml feed data saving it to a .xml file
	 *
	 * @param string $feedData raw xml data to be logged for feed
	 * @return bool|string path on success of false if none
	 * @throws \Exception if entity is not persisted yet
	 */
	public function setFeedData($feedData)
	{
		if ($this->id === null) {
			throw new \Exception("Cannot save feed data on an unpersisted entity");
		}
		$feedId = $this->id;
		$feedType = $this->feedType;
		$logPath = $this->getDirectory($feedType);
		if (!file_exists($logPath)) {
			mkdir($logPath, 0777, true);
		}
		$logFilePath = "$logPath/$feedId.xml";
		if (FALSE !== file_put_contents($logFilePath, $feedData)) {
			return $logFilePath;
		} else {
			return FALSE;
		}
	}


	/**
	 * getter for the xml feed data
	 * retrieves data from a file in data/Xml/ActionEuropeStock/$feedType
	 * @return string
	 */
	public function getFeedData()
	{
		if ($this->id === null) {
			return '';
		}
		$feedId = $this->id;
		$feedType = $this->feedType;
		$logPath = $this->getDirectory($feedType);
		if (!file_exists($logPath)) {
			mkdir($logPath, 0777, true);
		}
		$content= file_get_contents("$logPath/$feedId.xml");
		return $content;

	}

	/**
	 * @param $feedType
	 * @return string
	 */
	private function getDirectory($feedType)
	{
		return dirname(dirname(dirname(dirname(dirname(__DIR__))))) . "/data/Xml/ActionEuropeStock/$feedType";
	}


}