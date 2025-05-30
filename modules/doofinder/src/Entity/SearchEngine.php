<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 * @author    Doofinder
 * @copyright Doofinder
 * @license   GPLv3
 */

namespace PrestaShop\Module\Doofinder\Src\Entity;

if (!defined('_PS_VERSION_')) {
    exit;
}

class SearchEngine
{
    /**
     * Get the configuration key for the language and currency corresponding to the hashid
     *
     * @param int $idLang
     * @param int $idCurrency
     * @param int|null $shopGroupId
     * @param int|null $shopId
     *
     * @return string
     */
    public static function getHashId($idLang, $idCurrency, $shopGroupId = null, $shopId = null)
    {
        $context = \Context::getContext();
        $currIso = strtoupper(LanguageManager::getIsoCodeById($idCurrency));
        $lang = new \Language($idLang);
        $hashidKey = 'DF_HASHID_' . $currIso . '_' . strtoupper($lang->language_code);
        $shopGroupId = isset($shopGroupId) ? $shopGroupId : $context->shop->id_shop_group;
        $shopId = isset($shopId) ? $shopId : $context->shop->id;
        $hashid = \Configuration::get($hashidKey, $idLang, $shopGroupId, $shopId);

        if (!$hashid) {
            // If not found, try to obtain hashid without context
            $hashid = \Configuration::get($hashidKey, $idLang);
        }

        if (!$hashid) {
            // If not found, try to obtain hashid without idLang
            $hashid = \Configuration::get($hashidKey);
        }

        if (!$hashid) {
            $hashidKey = 'DF_HASHID_' . $currIso . '_' . strtoupper(LanguageManager::getLanguageCode($lang->language_code));
            $hashid = \Configuration::get($hashidKey);
        }

        return $hashid;
    }

    /**
     * Update the hashid of the search engines of the store in the configuration
     *
     * @param int|null $idShopGroup
     * @param int|null $idShop
     *
     * @return true
     */
    public static function setSearchEnginesByConfig($idShopGroup = null, $idShop = null)
    {
        $context = \Context::getContext();
        $idShopGroup = isset($idShopGroup) ? $idShopGroup : $context->shop->id_shop_group;
        $idShop = isset($idShop) ? $idShop : $context->shop->id;
        $installationID = \Configuration::get('DF_INSTALLATION_ID', null, $idShopGroup, $idShop);
        $apiKey = \Configuration::get('DF_API_KEY');
        $region = \Configuration::get('DF_REGION');

        $data = DoofinderLayerApi::getInstallationData($installationID, $apiKey, $region);

        foreach ($data['config']['search_engines'] as $lang => $currencies) {
            foreach ($currencies as $currency => $hashid) {
                $hashidKey = 'DF_HASHID_' . strtoupper($currency) . '_' . strtoupper($lang);
                \Configuration::updateValue($hashidKey, $hashid, false, $idShopGroup, $idShop);
            }
        }

        return true;
    }
}
