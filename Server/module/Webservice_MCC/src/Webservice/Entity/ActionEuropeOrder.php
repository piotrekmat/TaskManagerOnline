<?php
/**
 * Entity for handling the status of the order exchanged with Action Europe
 * User: kudlaty01
 * Date: 15.02.16
 * Time: 14:48
 */


namespace Webservice\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * ActionEuropeOrder
 *
 * @ORM\Table(name="service.action_europe_orders")
 * @ORM\Entity(repositoryClass="Webservice\Repository\ActionEuropeOrderRepository")
 */
class ActionEuropeOrder
{
	use \Application\Behavior\MagicAccessTrait;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;

	/**
	 * @ORM\OneToOne(targetEntity="Order\Entity\Order", inversedBy="actionEuropeStatus", cascade={"persist","remove"})
	 * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
	 * @var \Order\Entity\Order
	 */
	protected $order;

	/**
	 * For statuses see enum ActionEuropeOrderStatusType
	 *
	 * @ORM\Column(type="decimal", precision=4, scale=2, options={"default"=0.0})
	 * @var float
	 */
	protected $status;


	/**
	 * @ORM\Column(type="text", nullable=TRUE)
	 * @var text
	 */
	protected $shippingData;

	/**
	 * @ORM\Column(type="text", nullable=TRUE)
	 * @var text
	 */
	protected $invoiceData;

}