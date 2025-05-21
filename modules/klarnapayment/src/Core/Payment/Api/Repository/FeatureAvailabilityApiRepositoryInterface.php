<?php
/**
 * NOTICE OF LICENSE
 *
 * @author    Klarna Bank AB www.klarna.com
 * @copyright Copyright (c) permanent, Klarna Bank AB
 * @license   ISC
 *
 * @see       /LICENSE
 *
 * International Registered Trademark & Property of Klarna Bank AB
 */

namespace KlarnaPayment\Module\Core\Payment\Api\Repository;

use KlarnaPayment\Module\Api\Requests\FeatureAvailabilityRequest;
use KlarnaPayment\Module\Api\Responses\RetrieveFeatureAvailabilityResponse;

if (!defined('_PS_VERSION_')) {
    exit;
}

interface FeatureAvailabilityApiRepositoryInterface
{
    public function retrieveMerchantFeatureAvailability(FeatureAvailabilityRequest $request): ?RetrieveFeatureAvailabilityResponse;
}
