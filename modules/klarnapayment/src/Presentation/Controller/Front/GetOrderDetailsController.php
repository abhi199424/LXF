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

use KlarnaPayment\Module\Api\Models\Session;
use KlarnaPayment\Module\Core\Order\DTO\GetOrderDetailsData;
use KlarnaPayment\Module\Core\Payment\Provider\PaymentSessionProvider;
use KlarnaPayment\Module\Core\Shared\Repository\OrderRepositoryInterface;
use KlarnaPayment\Module\Infrastructure\Adapter\ModuleFactory;
use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;
use KlarnaPayment\Module\Infrastructure\Request\Request;
use KlarnaPayment\Module\Infrastructure\Response\JsonResponse;
use KlarnaPayment\Module\Infrastructure\Utility\ExceptionUtility;

if (!defined('_PS_VERSION_')) {
    exit;
}

class GetOrderDetailsController
{
    const FILE_NAME = 'GetOrderDetailsController';

    private $logger;
    /** @var \KlarnaPayment */
    private $module;
    private $paymentSessionProvider;
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    public function __construct(
        LoggerInterface $logger,
        ModuleFactory $moduleFactory,
        PaymentSessionProvider $paymentSessionProvider,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->logger = $logger;
        $this->module = $moduleFactory->getModule();
        $this->paymentSessionProvider = $paymentSessionProvider;
        $this->orderRepository = $orderRepository;
    }

    public function execute(Request $request): JsonResponse
    {
        $data = GetOrderDetailsData::fromRequest($request);

        /** @var \Order|null $internalOrder */
        $internalOrder = $this->orderRepository->findOneBy([
            'id_cart' => $data->getCartId(),
        ]);

        if ($internalOrder) {
            $this->logger->error('Order already exists.', [
                'context' => [
                    'cart_id' => $data->getCartId(),
                    'internal_order_id' => $internalOrder->id,
                ],
            ]);

            return JsonResponse::error($this->module->l('Order already exists.', self::FILE_NAME), JsonResponse::HTTP_BAD_REQUEST);
        }

        try {
            $request = $this->action($data);
        } catch (\Exception $exception) {
            $this->logger->error('Failed to get order details by cart id.', [
                'context' => [
                    'cart_id' => $data->getCartId(),
                ],
                'exceptions' => ExceptionUtility::getExceptions($exception),
            ]);

            return JsonResponse::error($this->module->l('Failed to get order details by cart id', self::FILE_NAME), JsonResponse::HTTP_BAD_REQUEST);
        }

        return JsonResponse::success([
            'order_details' => $request,
        ], JsonResponse::HTTP_OK);
    }

    public function action(GetOrderDetailsData $data): Session
    {
        $cart = new \Cart($data->getCartId());

        return $this->paymentSessionProvider->get($cart, true);
    }
}
