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

namespace KlarnaPayment\Module\Infrastructure\Adapter;

use Configuration as PrestaShopConfiguration;
use KlarnaPayment\Module\Core\Config\Config;
use KlarnaPayment\Module\Infrastructure\Context\GlobalShopContextInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Configuration
{
    private $globalShopContext;

    public function __construct(GlobalShopContextInterface $globalShopContext)
    {
        $this->globalShopContext = $globalShopContext;
    }

    public function set(string $id, $value, ?int $shopId = null)
    {
        if (!$shopId) {
            $shopId = $this->globalShopContext->getShopId();
        }

        PrestaShopConfiguration::updateValue($id, $value, false, null, $shopId);
    }

    public function get(string $id, ?int $shopId = null)
    {
        if (!$shopId) {
            $shopId = $this->globalShopContext->getShopId();
        }

        $result = PrestaShopConfiguration::get($id, null, null, $shopId);

        return $result ?: null;
    }

    public function getAsBoolean(string $id, ?int $shopId = null)
    {
        $result = $this->get($id, $shopId);

        if (in_array($result, ['null', 'false', '0', null, false, 0], true)) {
            return false;
        }

        return (bool) $result;
    }

    public function getAsInteger(string $id, ?int $shopId = null)
    {
        $result = $this->get($id, $shopId);

        if (in_array($result, ['null', 'false', '0', null, false, 0], true)) {
            return 0;
        }

        return (int) $result;
    }

    /**
     * Removes by specific shop id
     *
     * @param string $id
     * @param int $shopId
     */
    public function remove(string $id, ?int $shopId = null)
    {
        // making sure to set to null value only for single shop id
        PrestaShopConfiguration::updateValue($id, null, false, null, $shopId);
    }

    /**
     * Drops configuration from all shops.
     *
     * @param string $id
     */
    public function delete(string $id)
    {
        PrestaShopConfiguration::deleteByName($id);
    }

    /**
     * @param array{sandbox: string, production: string} $idByEnvironment
     * @param mixed $value
     * @param int|null $shopId
     */
    public function setByEnvironment(array $idByEnvironment, $value, ?int $shopId = null)
    {
        if (!$shopId) {
            $shopId = $this->globalShopContext->getShopId();
        }

        if ($this->getAsInteger(Config::KLARNA_PAYMENT_IS_PRODUCTION_ENVIRONMENT)) {
            $id = $idByEnvironment['production'];
        } else {
            $id = $idByEnvironment['sandbox'];
        }

        PrestaShopConfiguration::updateValue($id, $value, false, null, $shopId);
    }

    /**
     * @param array{sandbox: string, production: string} $idByEnvironment
     * @param int|null $shopId
     *
     * @return false|string|null
     */
    public function getByEnvironment(array $idByEnvironment, ?int $shopId = null)
    {
        if (!$shopId) {
            $shopId = $this->globalShopContext->getShopId();
        }

        if ($this->getAsInteger(Config::KLARNA_PAYMENT_IS_PRODUCTION_ENVIRONMENT)) {
            $id = $idByEnvironment['production'];
        } else {
            $id = $idByEnvironment['sandbox'];
        }

        $result = PrestaShopConfiguration::get($id, null, null, $shopId);

        return $result ?: null;
    }

    /**
     * @param array{sandbox: string, production: string} $idByEnvironment
     * @param int|null $shopId
     *
     * @return bool
     */
    public function getByEnvironmentAsBoolean(array $idByEnvironment, ?int $shopId = null)
    {
        $result = $this->getByEnvironment($idByEnvironment, $shopId);

        if (in_array($result, ['null', 'false', '0', null, false, 0], true)) {
            return false;
        }

        return (bool) $result;
    }

    /**
     * @param array{sandbox: string, production: string} $idByEnvironment
     * @param int|null $shopId
     *
     * @return int
     */
    public function getByEnvironmentAsInteger(array $idByEnvironment, ?int $shopId = null)
    {
        $result = $this->getByEnvironment($idByEnvironment, $shopId);

        if (in_array($result, ['null', 'false', '0', null, false, 0], true)) {
            return 0;
        }

        return (int) $result;
    }

    public function getMultiShopValuesByEnvironment(array $idByEnvironment)
    {
        if ($this->getAsInteger(Config::KLARNA_PAYMENT_IS_PRODUCTION_ENVIRONMENT)) {
            $id = $idByEnvironment['production'];
        } else {
            $id = $idByEnvironment['sandbox'];
        }

        return PrestaShopConfiguration::getMultiShopValues($id);
    }

    /**
     * @param array{sandbox: string, production: string} $idByEnvironment
     * @param ?int $shopId
     */
    public function removeByEnvironment(array $idByEnvironment, ?int $shopId = null)
    {
        if (!$shopId) {
            $shopId = $this->globalShopContext->getShopId();
        }

        if ($this->getAsInteger(Config::KLARNA_PAYMENT_IS_PRODUCTION_ENVIRONMENT)) {
            $id = $idByEnvironment['production'];
        } else {
            $id = $idByEnvironment['sandbox'];
        }

        // making sure to set to null value only for single shop id
        PrestaShopConfiguration::updateValue($id, null, false, null, $shopId);
    }

    /**
     * Drops configuration from all shops.
     *
     * @param array{sandbox: string, production: string} $idByEnvironment
     */
    public function deleteFromAllEnvironments(array $idByEnvironment)
    {
        PrestaShopConfiguration::deleteByName($idByEnvironment['sandbox']);
        PrestaShopConfiguration::deleteByName($idByEnvironment['production']);
    }
}
