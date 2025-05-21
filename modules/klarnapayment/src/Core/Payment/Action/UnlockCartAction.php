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

use KlarnaPayment\Module\Core\Payment\Exception\CouldNotUnlockCart;
use KlarnaPayment\Module\Core\Shared\Repository\KlarnaPaymentCartRepositoryInterface;
use KlarnaPayment\Module\Infrastructure\Context\GlobalShopContextInterface;
use KlarnaPayment\Module\Infrastructure\EntityManager\EntityManagerInterface;
use KlarnaPayment\Module\Infrastructure\EntityManager\ObjectModelUnitOfWork;
use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class UnlockCartAction
{
    private $logger;
    private $globalShopContext;
    private $entityManager;
    private $klarnaPaymentCartRepository;

    public function __construct(
        LoggerInterface $logger,
        GlobalShopContextInterface $globalShopContext,
        EntityManagerInterface $entityManager,
        KlarnaPaymentCartRepositoryInterface $klarnaPaymentCartRepository
    ) {
        $this->logger = $logger;
        $this->globalShopContext = $globalShopContext;
        $this->entityManager = $entityManager;
        $this->klarnaPaymentCartRepository = $klarnaPaymentCartRepository;
    }

    /**
     * @throws CouldNotUnlockCart
     */
    public function run(int $cartId): void
    {
        $this->logger->debug(sprintf('%s - Function called', __METHOD__));

        try {
            $klarnaPaymentCart = $this->klarnaPaymentCartRepository->findOneBy([
                'id_cart' => $cartId,
                'id_shop' => $this->globalShopContext->getShopId(),
            ]);

            if (!$klarnaPaymentCart) {
                $this->logger->debug(sprintf('%s - Function ended', __METHOD__));

                return;
            }

            $this->entityManager->persist($klarnaPaymentCart, ObjectModelUnitOfWork::UNIT_OF_WORK_DELETE);
            $this->entityManager->flush();
        } catch (\Throwable $exception) {
            throw CouldNotUnlockCart::unknownError($exception);
        }

        $this->logger->debug(sprintf('%s - Function ended', __METHOD__));
    }
}
