<?php
/**
 * Possible ActionEurope Statuses
 * User: kudlaty01
 * Date: 17.02.16
 * Time: 12:58
 */

namespace Webservice\Enums;


class ActionEuropeOrderStatusType
{
	/**
	 * AEu order has been just created
	 * @const float
	 */
	const AE_ORDER_STATUS_NONE = 0.0;
	/**
	 * Order reservation has been successfully sent
	 * @const float
	 */
	const AE_ORDER_STATUS_PENDING_RESERVATION = 1.0;
	/**
	 * No error message from AEU has arrived
	 * @const float
	 */
	const AE_ORDER_STATUS_RESERVATION_SUCCESSFUL = 2.0;
	/**
	 * An email to AEu for completion has been sent
	 * @const float
	 */
	const AE_ORDER_STATUS_FULFILLMENT_REQUEST_SENT = 3.0;
	/**
	 * Confirmation from AEu has arrived for this order
	 * @const float
	 */
	const AE_ORDER_STATUS_RESERVATION_CONFIRMED = 3.5;
	/**
	 * Order has been sent to recipient, a delivery not has arrived
	 * @const float
	 */
	const AE_ORDER_STATUS_FULFILLED = 4.0;
	/**
	 * An invoice from AEu for company has been created, an invoice feed arrived
	 * @const float
	 */
	const AE_ORDER_STATUS_INVOICE_CREATED = 5.0;
	/**
	 * The order has been cancelled
	 */
	const AE_ORDER_STATUS_CANCELLED = 6.0;
}