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

namespace KlarnaPayment\Module\Core\Payment\Provider;

use KlarnaPayment\Module\Api\Models\AssetUrl;
use KlarnaPayment\Module\Api\Models\PaymentMethodCategory;
use KlarnaPayment\Module\Infrastructure\Adapter\Media;
use KlarnaPayment\Module\Infrastructure\Adapter\ModuleFactory;

if (!defined('_PS_VERSION_')) {
    exit;
}

class KecPaymentMethodCategoriesProvider
{
    const TRANSLATION_ID = 'KecPaymentMethodCategoriesProvider';

    private $media;

    /** @var string */
    private $klarnaLogoPath;

    private $module;

    public function __construct(Media $media, ModuleFactory $moduleFactory)
    {
        $this->media = $media;
        $this->module = $moduleFactory->getModule();
        $this->klarnaLogoPath = $this->module->getPathUri() . 'views/img/klarna.svg';
    }

    /**
     * Get KEC payment method categories.
     *
     * @return PaymentMethodCategory[]
     */
    public function get(): array
    {
        $paymentMethodCategory = new PaymentMethodCategory();
        $paymentMethodCategory->setName($this->module->l('Klarna', self::TRANSLATION_ID));
        $paymentMethodCategory->setIdentifier('klarna');

        $assetUrls = new AssetUrl();
        $assetUrls->setStandard($this->media->getMediaPath($this->klarnaLogoPath));
        $paymentMethodCategory->setAssetUrls($assetUrls);

        return [$paymentMethodCategory];
    }
}
