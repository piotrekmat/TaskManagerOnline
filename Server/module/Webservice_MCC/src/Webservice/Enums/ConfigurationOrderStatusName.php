<?php
/**
 * Created by PhpStorm.
 * User: kudlaty01
 * Date: 04.03.16
 * Time: 14:16
 */

namespace Webservice\Enums;


/**
 * Enum class for order statuses taken from config integrity
 * @package Webservice\Enums
 */
class ConfigurationOrderStatusName
{
	const PENDING_RESERVATION = 'pendingReservation';
	const RESERVATION_FAILED = 'reservationFailed';
	const AWAITING_CONFIRMATION = 'awaitingConfirmation';
	const CONFIRMED = 'confirmed';
	const RESERVED = 'reserved';
	const PENDING_FULFILLMENT = 'pendingFulfillment';
	const PACKING_FAILED = 'packingFailed';
	const PACKED = 'packed';
	const INVOICE_CREATED = 'invoiceCreated';
	const INVOICE_FAILED = 'invoiceFailed';
	const CANCELLED = 'cancelled';

}