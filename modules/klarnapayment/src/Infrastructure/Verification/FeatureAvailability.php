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

namespace KlarnaPayment\Module\Infrastructure\Verification;

use KlarnaPayment\Module\Infrastructure\Adapter\Configuration;

if (!defined('_PS_VERSION_')) {
    exit;
}

class FeatureAvailability
{
    /** @var Configuration */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @param string ...$featuresKeys
     *
     * @return bool
     */
    public function verify(string ...$featuresKeys): bool
    {
        foreach ($featuresKeys as $featureKey) {
            if ($this->configuration->getAsBoolean($featureKey)) {
                return true;
            }
        }

        return false;
    }
}
