<?php
/**
 * Created by PhpStorm.
 * User: kudlaty01
 * Date: 09.02.16
 * Time: 14:04
 */

namespace Webservice\Model;


/**
 * Class for model representation of an order product
 * @package Webservice\Model
 * @property int $Quantity
 * @property float $PriceFix
 * @property string $SupplierPid
 * @property string $InternationalPid
 * @property string $DescriptionShort
 */
class ActionEuropeOrderItemModel
{
	/**
	 * ActionEuropeOrderItemModel constructor.
	 * @param \Order\Entity\OrderProduct $orderProduct
	 * @param int $productCodeType
	 * @param int $warehouseId
	 */
	public function __construct(\Order\Entity\OrderProduct $orderProduct, $productCodeType = 11, $warehouseId = 47)
	{
		$this->Quantity = $orderProduct->quantity;
		$this->setPriceFix($orderProduct, $warehouseId);

		$this->setSupplierPid($orderProduct, $productCodeType);
		$this->setInternationalPid($orderProduct);

		$this->DescriptionShort = htmlspecialchars($orderProduct->productName, ENT_NOQUOTES);
	}

	/**
	 * @param \Order\Entity\OrderProduct $orderProduct
	 * @param $warehouseId
	 * @return float
	 */
	public function setPriceFix(\Order\Entity\OrderProduct $orderProduct, $warehouseId)
	{
		$stocks = $orderProduct->idProductVersion->stocks;
		if ($stocks->count() > 1) {
			$stocks = $stocks->filter(function ($stock) use ($warehouseId) {
				return $stock->magazine->id == $warehouseId;
			});
		}
		/** @var \Product\Entity\MagazineDefinition $stock */
		$stock = $stocks->first();
		$this->PriceFix = $stock->averagePrice;
		return $this->PriceFix;
	}

	/**
	 * @param \Order\Entity\OrderProduct $orderProduct
	 * @param $productCodeType
	 * @return string
	 */
	private function setSupplierPid(\Order\Entity\OrderProduct $orderProduct, $productCodeType)
	{
		$productCodes = $orderProduct->idProductVersion->codes;
		if ($productCodes->count() > 1) {
		$productCodes = $orderProduct->idProductVersion->codes->filter(
				function ($code) use ($productCodeType) {
					return $code->type->id === $productCodeType;
			});
		}
		if ($productCodes->count() > 0) {
			/** @var \Product\Entity\ProductCode $productCode */
			$productCode = $productCodes->first();
			$this->SupplierPid = $productCode->code;
		} else {
			$this->SupplierPid = $orderProduct->externalId;
		}
		return $this->SupplierPid;
	}

	/**
	 * @param \Order\Entity\OrderProduct $orderProduct
	 */
	public function setInternationalPid(\Order\Entity\OrderProduct $orderProduct)
	{
		/** @var \Product\Entity\ProductEan[] $eans */
		$eans = $orderProduct->idProductVersion->eans;
		if ($eans->count() > 0) {
			$ean = $eans->first();
			$this->InternationalPid = $ean->ean;
		} else {
			$this->InternationalPid = 'not an ean';
		}
	}

}