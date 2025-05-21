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

class ImageRepository extends CollectionRepository implements ImageRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(\Image::class);
    }

    public function getImageFromProduct(int $productId, int $langId, int $shopId): ?int
    {
        $query = new \DbQuery();

        $query
            ->select('i.id_image')
            ->from('image', 'i')
            ->leftJoin(
                'image_lang', 'il', 'i.id_image = il.id_image AND il.id_lang = ' . $langId
            )
            ->leftJoin(
                'image_shop', 'is', 'i.id_image = is.id_image AND is.id_shop = ' . $shopId
            )
            ->where('i.id_product = ' . $productId)
            ->where('i.cover = 1');

        $result = \Db::getInstance()->getValue($query);

        return (int) $result ?: null;
    }
}
