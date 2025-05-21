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

namespace KlarnaPayment\Module\Presentation\Controller\Front;

use KlarnaPayment\Module\Core\Order\Exception\CouldNotAutoCapture;
use KlarnaPayment\Module\Core\Order\Repository\KlarnaPaymentOrderRepositoryInterface;
use KlarnaPayment\Module\Core\Payment\DTO\CheckoutData;
use KlarnaPayment\Module\Core\Payment\Processor\CheckoutProcessor;
use KlarnaPayment\Module\Core\Shared\Repository\OrderRepositoryInterface;
use KlarnaPayment\Module\Infrastructure\Adapter\ModuleFactory;
use KlarnaPayment\Module\Infrastructure\Exception\KlarnaPaymentException;
use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;
use KlarnaPayment\Module\Infrastructure\Request\Request;
use KlarnaPayment\Module\Infrastructure\Response\Response;
use KlarnaPayment\Module\Infrastructure\Utility\ExceptionUtility;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CheckoutController
{
    const FILE_NAME = 'CheckoutController';

    private $logger;
    private $module;
    private $checkoutProcessor;
    private $orderRepository;
    private $klarnaPaymentOrderRepository;

    public function __construct(
        LoggerInterface $logger,
        ModuleFactory $moduleFactory,
        CheckoutProcessor $checkoutProcessor,
        OrderRepositoryInterface $orderRepository,
        KlarnaPaymentOrderRepositoryInterface $klarnaPaymentOrderRepository
    ) {
        $this->logger = $logger;
        $this->module = $moduleFactory->getModule();
        $this->checkoutProcessor = $checkoutProcessor;
        $this->orderRepository = $orderRepository;
        $this->klarnaPaymentOrderRepository = $klarnaPaymentOrderRepository;
    }

    public function execute(Request $request): Response
    {
        $data = CheckoutData::fromRequest($request);

        /** @var ?\KlarnaPaymentOrder $externalOrder */
        $externalOrder = $this->klarnaPaymentOrderRepository->findOneBy([
            'authorization_token' => $data->getAuthorizationToken(),
        ]);

        if ($externalOrder) {
            $this->logger->error('Authorization token has already been used.', [
                'context' => [
                    'cart_id' => $data->getCartId(),
                    'authorization_token' => $data->getAuthorizationToken(),
                ],
            ]);

            return Response::respond(
                $this->module->l('Unauthorized', self::FILE_NAME),
                Response::HTTP_UNAUTHORIZED
            );
        }

        /** @var \Order|null $internalOrder */
        $internalOrder = $this->orderRepository->findOneBy([
            'id_cart' => $data->getCartId(),
        ]);

        if ($internalOrder) {
            $this->logger->debug('Order already exists.', [
                'context' => [
                    'cart_id' => $data->getCartId(),
                    'internal_order_id' => $internalOrder->id,
                ],
            ]);

            return Response::respond(
                $this->module->l('Order already exists.', self::FILE_NAME),
                Response::HTTP_OK
            );
        }

        try {
            $this->action($data);
        } catch (\Throwable $exception) {
            $this->logger->error('Failed to process checkout.', [
                'context' => [
                    'cart_id' => $data->getCartId(),
                    'authorization_token' => $data->getAuthorizationToken(),
                ],
                'exceptions' => ExceptionUtility::getExceptions($exception),
            ]);

            return Response::respond($this->module->l('Failed to process checkout', self::FILE_NAME), Response::HTTP_BAD_REQUEST);
        }

        return Response::respond('', Response::HTTP_OK);
    }

    /**
     * @throws CouldNotAutoCapture
     * @throws KlarnaPaymentException
     */
    private function action(CheckoutData $data): void
    {
        $this->checkoutProcessor->run(
            $data->getCartId(),
            $data->getAuthorizationToken()
        );
    }
}
