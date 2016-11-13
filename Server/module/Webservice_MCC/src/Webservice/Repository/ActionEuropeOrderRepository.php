<?php
/**
 * Created by PhpStorm.
 * User: kudlaty01
 * Date: 16.02.16
 * Time: 12:36
 */

namespace Webservice\Repository;

use Doctrine\ORM\Mapping as ORM;
use Webservice\Entity\ActionEuropeOrder;
use Webservice\Enums\ActionEuropeFeedStatusType;
use Webservice\Enums\ActionEuropeFeedType;
use Webservice\Enums\ActionEuropeOrderStatusType;

/**
 * ActionEuropeOrderRepository
 */
class ActionEuropeOrderRepository extends \Application\Repository\MccEntityRepository
{
	/**
	 * Finds an entity for an order or creates one if it does not exist
	 * @param \Order\Entity\Order $order
	 * @return null|object|ActionEuropeOrder
	 */
	public function findOneByOrderOrCreate($order)
	{
		if (NULL === $actionEuropeOrder = $this->findOneBy(['order' => $order->id])) {
			$actionEuropeOrder = new ActionEuropeOrder();
			$actionEuropeOrder->order = $order;
			$actionEuropeOrder->status = ActionEuropeOrderStatusType::AE_ORDER_STATUS_NONE;
			$order->actionEuropeStatus = $actionEuropeOrder;
		}
		return $actionEuropeOrder;
	}

	/**
	 * Finds orders qualifying to be reserved in ActionEurope
	 * which are orders in status 1.0 or 1.7, without reservation and with at least one product from AE warehouse
	 * @param int $warehouseId the ActionEU warehouse ID taken from config
	 * @param int $codeTypeId the id of the product code type for AEU
	 * @param array $relevantStatuses statuses for orders to be processed for reservation
	 * @return array qualified orders
	 */
	public function findOrdersForReservationSending($warehouseId, $codeTypeId, $relevantStatuses)
	{
		$qb = $this->_em->createQueryBuilder()
			->distinct()
			->select('o', 'op', 'pv', 's', 'c')
			->from('Order\Entity\Order', 'o')
			->join('o.idOrderMainStatus', 'oms')
			->join('o.products', 'op')
			->join('op.idProductVersion', 'pv')
			->join('pv.stocks', 's')
			->join('pv.codes', 'c')
			->leftJoin('o.actionEuropeStatus', 'aes')
			->where('oms.statusNumber IN (:statuses)')
			->andWhere('op.idMagazine = :actionEuropeMagazineId')
			->andWhere('s.magazine = :actionEuropeMagazineId')
			->andWhere('c.type = :actionEuropeCodeTypeId')
			->andWhere('aes.id IS NULL OR aes.status < :reservedStatus OR aes.status = :cancelledStatus')
			->setParameters([
				':statuses'               => $relevantStatuses,
				':actionEuropeMagazineId' => $warehouseId,
				':actionEuropeCodeTypeId' => $codeTypeId,
				':reservedStatus'         => ActionEuropeOrderStatusType::AE_ORDER_STATUS_PENDING_RESERVATION,
				':cancelledStatus'        => ActionEuropeOrderStatusType::AE_ORDER_STATUS_CANCELLED,
			]);
		$query = $qb->getQuery();
		$orders = $query->getResult();
		return $orders;
	}

	/**
	 * Returns orders for confirmation
	 * @param float $awaitingConfirmationOrderStatus
	 * @param int $delayMinutes age of the successfully sent reservation request feed in minutes
	 * @return \Order\Entity\Order[]
	 */
	public function findOrdersForReservationConfirmation($awaitingConfirmationOrderStatus, $delayMinutes)
	{
		$feedSubQuery = $this->_em->createQueryBuilder()
			->select('o2.id')
			->from('Order\Entity\Order', 'o2')
			->join('o2.actionEuropeStatus', 'aeo')
			->join('o2.idOrderMainStatus', 'oms')
			->andWhere('oms.statusNumber = :awaitingConfirmationStatus')
			->andWhere('aeo.status = :pendingReservationStatus');

		$delay = new \DateTime(sprintf('-%d minutes', $delayMinutes));
		$expressionBuilder = $this->_em->getExpressionBuilder();
		$subQuery = $this->_em->createQueryBuilder()
			->select('f.orderId')
			->from('Webservice\Entity\ActionEuropeFeed', 'f')
			->andWhere($expressionBuilder->in('f.orderId', $feedSubQuery->getDQL()))
			->andWhere('f.feedType = :requestType')
			->andWhere('f.feedStatus = :successStatus')
			->groupBy('f.orderId')
			->having('MAX(f.feedDate) < :delay');
		$mainQuery = $this->_em->createQueryBuilder()
			->select('o')
			->from('Order\Entity\Order', 'o')
			->where($expressionBuilder->in('o.id', $subQuery->getDQL()))
			->setParameters([
				':requestType'                => ActionEuropeFeedType::AE_ORDER_REQUEST,
				':successStatus'              => ActionEuropeFeedStatusType::AE_SUCCESS,
				':delay'                      => $delay,
				':pendingReservationStatus'   => ActionEuropeOrderStatusType::AE_ORDER_STATUS_PENDING_RESERVATION,
				':awaitingConfirmationStatus' => $awaitingConfirmationOrderStatus,
			]);

		$result = $mainQuery->getQuery()->getResult();
		return $result;

	}

	/**
	 * Finds all orders of configured status and without mail sent to configured address
	 * @param float $status the status of the orders for mail notification
	 * @return \Order\Entity\Order[] array of orders for the mail to be sent
	 */
	public function findOrdersForReservationNotification($status)
	{
		$qb = $this->_em->createQueryBuilder()
			->distinct()
			->select('o')
			->from('Order\Entity\Order', 'o')
			->join('o.idOrderMainStatus', 'oms')
			->join('o.actionEuropeStatus', 'aes')
			->where('oms.statusNumber = :status')
			->andWhere('aes.status >= :confirmedStatus')
			->andWhere('aes.status < :sentStatus')
			->setParameters([
				':status'          => $status,
				':confirmedStatus' => ActionEuropeOrderStatusType::AE_ORDER_STATUS_RESERVATION_SUCCESSFUL,
				':sentStatus'      => ActionEuropeOrderStatusType::AE_ORDER_STATUS_FULFILLMENT_REQUEST_SENT,
			]);
		$query = $qb->getQuery();
		$orders = $query->getResult();
		return $orders;
	}

	/**
	 * Finds cancelled orders for mail notification
	 * @param array $cancelledStatuses an array with cancellation statuses
	 * @return \Order\Entity\Order[]
	 */
	public function findOrderForCancellationNotification($cancelledStatuses)
	{
		$qb = $this->_em->createQueryBuilder()
			->distinct()
			->select('o')
			->from('Order\Entity\Order', 'o')
			->join('o.idOrderMainStatus', 'oms')
			->join('o.actionEuropeStatus', 'aes')
			->where('oms.statusNumber IN (:statuses)')
			->andWhere('aes.status >= :confirmedStatus')
			->andWhere('aes.status <> :cancelledStatus')
			->setParameters([
				':statuses'        => $cancelledStatuses,
				':confirmedStatus' => ActionEuropeOrderStatusType::AE_ORDER_STATUS_RESERVATION_SUCCESSFUL,
				':cancelledStatus' => ActionEuropeOrderStatusType::AE_ORDER_STATUS_CANCELLED,
			]);
		$query = $qb->getQuery();
		$orders = $query->getResult();
		return $orders;
	}

	/**
	 * @param \Order\Entity\Order $order
	 * @param int $warehouseId
	 * @return \Order\Entity\OrderProduct[]
	 */
	public function getOrderProductsForOrderModelGeneration($order, $warehouseId = 47)
	{
		$qb = $this->_em->createQueryBuilder()
			->distinct()
			->select('op', 's')
			->from('Order\Entity\OrderProduct', 'op')
			->join('op.idProductVersion', 'pv')
			->join('pv.stocks', 's')
			->where('op.idOrder = :orderId')
			->andWhere('s.magazine = :warehouseId')
			->setParameters([
				':orderId'     => $order->id,
				':warehouseId' => $warehouseId,
			]);
		$query = $qb->getQuery();
		$orders = $query->getResult();
		return $orders;

	}


	/**
	 * @param int $orderId the id of the order
	 * @param int $warehouseId
	 * @param int $codeTypeId
	 * @param array $productCodes Action product codes
	 * @return array [code, erpId]
	 */
	public function getProductCodes($orderId, $warehouseId, $codeTypeId, $productCodes)
	{
		$qb = $this->_em->createQueryBuilder()
			->distinct()
			->select('c.code', 'pv.erpId')
			->from('Order\Entity\Order', 'o')
			->join('o.products', 'op')
			->join('op.idProductVersion', 'pv')
			->join('pv.codes', 'c')
			->where('o.id = :orderId')
			->andWhere('op.idMagazine = :actionEuropeMagazineId')
			->andWhere('c.type = :actionEuropeCodeTypeId')
			->andWhere('c.code IN (:productCodes)')
			->setParameters([
				':orderId'                => $orderId,
				':actionEuropeMagazineId' => $warehouseId,
				':actionEuropeCodeTypeId' => $codeTypeId,
				':productCodes'           => $productCodes,
			]);
		$query = $qb->getQuery();
		$codes = $query->getArrayResult();
		return $codes;
	}

}