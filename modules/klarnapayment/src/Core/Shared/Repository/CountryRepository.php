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

namespace KlarnaPayment\Module\Core\Shared\Repository;

use KlarnaPayment\Module\Infrastructure\Repository\CollectionRepository;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CountryRepository extends CollectionRepository implements CountryRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(\Country::class);
    }

    public function getCountriesByIsoCode(array $isoCodes, int $langId): array
    {
        $query = new \DbQuery();

        $query
            ->select('c.iso_code, cl.name')
            ->from('country', 'c')
            ->leftJoin(
                'country_lang', 'cl',
                'c.id_country = cl.id_country AND cl.id_lang = ' . $langId
            )
            ->where('c.iso_code IN (\'' . implode("','", $isoCodes) . '\')');

        $result = \Db::getInstance()->executeS($query);

        return !empty($result) ? $result : [];
    }

    /**
     * Get not restricted countries for a payment module
     *
     * @return array
     */
    public function getPaymentCountries($idModule, $idShop): array
    {
        $query = new \DbQuery();
        $query->select('c.iso_code')
            ->from('country', 'c')
            ->leftJoin('module_country', 'mc', 'c.id_country = mc.id_country')
            ->where('c.active = 1')
            ->where('mc.id_module = ' . (int) $idModule)
            ->where('mc.id_shop = ' . (int) $idShop)
            ->orderBy('c.iso_code ASC');

        return \Db::getInstance()->executeS($query) ?? [];
    }
}
