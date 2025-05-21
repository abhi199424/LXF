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

namespace KlarnaPayment\Module\Core\Payment\Action;

use KlarnaPayment\Module\Core\Payment\Exception\CouldNotLockCart;
use KlarnaPayment\Module\Infrastructure\Context\GlobalShopContextInterface;
use KlarnaPayment\Module\Infrastructure\EntityManager\EntityManagerInterface;
use KlarnaPayment\Module\Infrastructure\EntityManager\ObjectModelUnitOfWork;
use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class LockCartAction
{
    private $logger;
    private $globalShopContext;
    private $entityManager;

    public function __construct(
        LoggerInterface $logger,
        GlobalShopContextInterface $globalShopContext,
        EntityManagerInterface $entityManager
    ) {
        $this->logger = $logger;
        $this->globalShopContext = $globalShopContext;
        $this->entityManager = $entityManager;
    }

    /**
     * @throws CouldNotLockCart
     */
    public function run(int $cartId)
    {
        $this->logger->debug(sprintf('%s - Function called', __METHOD__));

        try {
            $cart = new \KlarnaPaymentCart();
            $cart->id_cart = $cartId;
            $cart->id_shop = $this->globalShopContext->getShopId();

            $this->entityManager->persist($cart, ObjectModelUnitOfWork::UNIT_OF_WORK_SAVE);
            $this->entityManager->flush();
        } catch (\Exception $exception) {
            throw CouldNotLockCart::unknownError($exception);
        }

        $this->logger->debug(sprintf('%s - Function ended', __METHOD__));
    }
}
