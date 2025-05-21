<?php
/**
 * All-in-one Rewards Module
 *
 * @author    Yann BONNAILLIE - ByWEB
 * @copyright 2012-2025 Yann BONNAILLIE - ByWEB
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

if (!defined('_PS_VERSION_')) { exit; }

use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchProviderInterface;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchResult;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrderFactory;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;
use Symfony\Component\Translation\TranslatorInterface;

class GiftsProductsProductSearchProvider implements ProductSearchProviderInterface
{
    private $translator;
    private $sortOrderFactory;

    public function __construct(
        TranslatorInterface $translator
    ) {
        $this->translator = $translator;
        $this->sortOrderFactory = new SortOrderFactory($this->translator);
    }

    private function getProductsOrCount(
        ProductSearchContext $context,
        ProductSearchQuery $query,
        $type = 'products'
    ) {
        return RewardsGiftProductModel::getGiftsProducts(
            $context->getIdLang(),
            $query->getPage(),
            $query->getResultsPerPage(),
            $query->getSortOrder()->toLegacyOrderBy(),
            $query->getSortOrder()->toLegacyOrderWay(),
            $type !== 'products'
        );
    }

    public function runQuery(
        ProductSearchContext $context,
        ProductSearchQuery $query
    ) {
        if (!$products = $this->getProductsOrCount($context, $query, 'products')) {
            $products = array();
        }
        $count = $this->getProductsOrCount($context, $query, 'count');

        $result = new ProductSearchResult();
        $result
            ->setProducts($products)
            ->setTotalProductsCount($count)
        ;

        $result->setAvailableSortOrders(
            array(
                (new SortOrder('product', 'date_add', 'desc'))->setLabel(
                    $this->translator->trans('Date added, newest to oldest', array(), 'Shop.Theme.Catalog')
                ),
                (new SortOrder('product', 'date_add', 'asc'))->setLabel(
                    $this->translator->trans('Date added, oldest to newest', array(), 'Shop.Theme.Catalog')
                ),
                (new SortOrder('product', 'name', 'asc'))->setLabel(
                    $this->translator->trans('Name, A to Z', array(), 'Shop.Theme.Catalog')
                ),
                (new SortOrder('product', 'name', 'desc'))->setLabel(
                    $this->translator->trans('Name, Z to A', array(), 'Shop.Theme.Catalog')
                ),
                (new SortOrder('product', 'price', 'asc'))->setLabel(
                    $this->translator->trans('Price, low to high', array(), 'Shop.Theme.Catalog')
                ),
                (new SortOrder('product', 'price', 'desc'))->setLabel(
                    $this->translator->trans('Price, high to low', array(), 'Shop.Theme.Catalog')
                ),
            )
        );

        return $result;
    }
}
