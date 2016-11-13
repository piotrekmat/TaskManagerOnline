<?php
/**
 * Created by PhpStorm.
 * User: kudlaty01
 * Date: 16.02.16
 * Time: 17:47
 */


namespace Webservice\Repository;


use Doctrine\ORM\Mapping as ORM;
use Webservice\Enums\ActionEuropeFeedType;

/**
 * ActionEuropeFeedRepository
 *
 * Repository for handling feeds transacted with Action Europe
 */
class ActionEuropeFeedRepository extends \Application\Repository\MccEntityRepository
{
	/**
	 * Returns orders feed number (for resending)
	 * @param int $orderId
	 * @return int
	 */
	public function getFeedCountByOrderId($orderId)
	{
		$feedCount = $this->_em->createQueryBuilder()
			->select('COUNT(f)')
			->from('Webservice\Entity\ActionEuropeFeed', 'f')
			->where('f.orderId=:orderId')
			->andWhere('f.feedType = :orderRequestFeedType')
			->setParameters([
				':orderId'              => $orderId,
				':orderRequestFeedType' => ActionEuropeFeedType::AE_ORDER_REQUEST,
			])
			->getQuery()
			->getSingleScalarResult();
		return $feedCount;
	}

	/**
	 * Converts OrderId to a unique format by the feedNumber
	 * @param int $orderId
	 * @param bool $subtract
	 * @return string
	 */
	public function getOrderIdWithFeedCount($orderId, $subtract = true)
	{
		$feedCount = $this->getFeedCountByOrderId($orderId);
		$feedCount =  ($subtract) ? $feedCount - 1 : $feedCount;
		if ($feedCount > 0) {
			$orderId .= sprintf('-%d', $feedCount);
		}
		return $orderId;
	}
}