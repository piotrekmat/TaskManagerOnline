<?php
/**
 * Created by PhpStorm.
 * User: kudlaty01
 * Date: 09.02.16
 * Time: 14:04
 */

namespace Webservice\Model;


/**
 * Model representation of an order for ActionEurope integration
 * @package Webservice\Model
 * @property string $OrderId
 * @property \DateTime $OrderDate
 * @property ActionEuropeOrderItemModel[] $OrderItems
 * @property string $DeliveryName
 * @property string $DeliveryStreet
 * @property string $DeliveryZip
 * @property string $DeliveryCity
 * @property string $DeliveryCountryCoded
 * @property string $DeliveryPhone
 * @property string $DeliveryEmail
 * @property string $CompanyName
 * @property string $CompanyStreet
 * @property string $CompanyZip
 * @property string $CompanyCity
 * @property string $CompanyCountry
 * @property string $CompanyCountryCoded
 * @property string $CompanyPhone
 * @property string $CompanyEmail
 * @property int $TotalItems
 * @property float $OrderTotal
 * @property string LabelOrderId
 * @property \DateTime LabelOrderDate
 * @property string LabelOrderDescription
 *
 */
class ActionEuropeOrderModel
{

	/**
	 * ActionEuropeOrderModel constructor.
	 * @param \Order\Entity\Order $order
	 * @param array $configuration config including all required data
	 * @param int $customOrderId
	 */
	public function __construct(\Order\Entity\Order $order, $configuration, $customOrderId = null)
	{
		$this->OrderItems = [];
		//region Order Header
		if ($customOrderId === null) {
			$this->OrderId = strval($order->id);
		} else {
			$this->OrderId = $customOrderId;
		}

		$this->LabelOrderId = $order->id;
		$this->LabelOrderDate = $order->addDate;
		$labelDescriptionFormat = $configuration['labelDescriptionFormat'];
		$this->LabelOrderDescription = sprintf($labelDescriptionFormat, $order->client->id);

		$this->OrderDate = new \DateTime();
		//endregion

		//region Delivery address
		$this->DeliveryName = htmlspecialchars(join(' ', array_filter([$order->deliveryFirstName, $order->deliveryLastName])), ENT_NOQUOTES);
		$this->DeliveryStreet = htmlspecialchars(trim($order->deliveryStreet . ' ' . $order->deliveryNumber), ENT_NOQUOTES);
		$this->DeliveryZip = $order->deliveryPostCode;
		$this->DeliveryCity = $order->deliveryCity;
		$this->DeliveryCountryCoded = $order->deliveryCountry;
		$this->DeliveryPhone = $order->deliveryPhone;
		$this->DeliveryEmail = $order->deliveryEmail;
		//endregion

		//region Lapado address
		$companyAddress = $configuration['companyAddress'];
		$this->CompanyName = $companyAddress['Name'];
		$this->CompanyStreet = $companyAddress['Street'];
		$this->CompanyZip = $companyAddress['Zip'];
		$this->CompanyCity = $companyAddress['City'];
		$this->CompanyCountry = $companyAddress['Country'];
		$this->CompanyCountryCoded = $companyAddress['CountryCoded'];
		$this->CompanyPhone = $companyAddress['Phone'];
		$this->CompanyEmail = $companyAddress['Email'];
		//endregion

		//region Order Item List
		$this->setOrderItems($order, $configuration);
		//endregion
		$this->TotalItems = count($this->OrderItems);
		$this->OrderTotal = array_sum(array_map(function ($orderItem) {
			return $orderItem->PriceFix * $orderItem->Quantity;
		}, $this->OrderItems));
	}

	/**
	 * @param \Order\Entity\Order $order
	 * @param $configuration
	 * @return array|ActionEuropeOrderItemModel[]
	 */
	public function setOrderItems(\Order\Entity\Order $order, $configuration)
	{
		$nonServiceProducts = $order->products
			->filter(function ($orderProduct) {
				return $orderProduct->idProductVersion !== null;
			});
		$warehouseId = $configuration['warehouseId'];
		$productCodeTypeId = $configuration['codeTypeId'];
		$this->OrderItems = $nonServiceProducts
			->map(function ($orderProduct) use ($productCodeTypeId, $warehouseId) {
				return new ActionEuropeOrderItemModel($orderProduct, $productCodeTypeId, $warehouseId);
			})->getValues();
		return $this->OrderItems;
	}

}