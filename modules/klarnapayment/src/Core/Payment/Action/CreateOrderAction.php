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

use KlarnaPayment\Module\Api\Exception\ApiException;
use KlarnaPayment\Module\Api\Responses\CreateOrderResponse;
use KlarnaPayment\Module\Core\Order\Api\Repository\OrderApiRepositoryInterface;
use KlarnaPayment\Module\Core\Payment\DTO\CreateOrderRequestData;
use KlarnaPayment\Module\Core\Payment\Exception\CouldNotCreateOrder;
use KlarnaPayment\Module\Core\Payment\Provider\CreateOrderRequestProvider;
use KlarnaPayment\Module\Core\Shared\Repository\KlarnaExpressCheckoutRepositoryInterface;
use KlarnaPayment\Module\Infrastructure\Exception\KlarnaPaymentException;
use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CreateOrderAction
{
    private $logger;
    private $orderApiRepository;
    private $createOrderRequestProvider;
    private $klarnaExpressCheckoutRepository;

    public function __construct(
        LoggerInterface $logger,
        OrderApiRepositoryInterface $orderApiRepository,
        CreateOrderRequestProvider $createOrderRequestProvider,
        KlarnaExpressCheckoutRepositoryInterface $klarnaExpressCheckoutRepository
    ) {
        $this->logger = $logger;
        $this->orderApiRepository = $orderApiRepository;
        $this->createOrderRequestProvider = $createOrderRequestProvider;
        $this->klarnaExpressCheckoutRepository = $klarnaExpressCheckoutRepository;
    }

    /**
     * @throws KlarnaPaymentException
     */
    public function run(CreateOrderRequestData $data): ?CreateOrderResponse
    {
        $this->logger->debug(sprintf('%s - Function called', __METHOD__));

        try {
            $request = $this->createOrderRequestProvider->get($data);

            $isKecOrder = $this->isKecOrder((int) $data->getCart()->id);

            $result = $this->orderApiRepository->createOrder($request, $isKecOrder);
        } catch (ApiException $exception) {
            throw CouldNotCreateOrder::failedToGetSuccessfulApiResponse($exception);
        } catch (\Exception $exception) {
            throw CouldNotCreateOrder::unknownError($exception);
        } catch (\Throwable $exception) {
            throw CouldNotCreateOrder::failedToCreateApiRequest($exception);
        }

        $this->logger->debug(sprintf('%s - Function ended', __METHOD__));

        return $result;
    }

    private function isKecOrder(int $cartId): bool
    {
        /** @var \KlarnaExpressCheckout|null $klarnaExpressCheckout */
        $klarnaExpressCheckout = $this->klarnaExpressCheckoutRepository->findOneBy(['id_cart' => $cartId]);

        return $klarnaExpressCheckout && $klarnaExpressCheckout->is_kec;
    }
}
