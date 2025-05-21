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

namespace KlarnaPayment\Module\Core\Order\Action;

use KlarnaPayment\Module\Api\Exception\ApiException;
use KlarnaPayment\Module\Core\Order\Api\Repository\OrderApiRepositoryInterface;
use KlarnaPayment\Module\Core\Order\DTO\UpdateMerchantReferencesRequestData;
use KlarnaPayment\Module\Core\Order\Exception\CouldNotUpdateMerchantReferencesOrder;
use KlarnaPayment\Module\Core\Order\Provider\UpdateMerchantReferencesRequestProvider;
use KlarnaPayment\Module\Infrastructure\Exception\KlarnaPaymentException;
use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class UpdateMerchantReferencesAction
{
    private $logger;
    private $orderApiRepository;
    private $updateMerchantReferencesRequestProvider;

    public function __construct(
        LoggerInterface $logger,
        OrderApiRepositoryInterface $orderApiRepository,
        UpdateMerchantReferencesRequestProvider $updateMerchantReferencesRequestProvider
    ) {
        $this->logger = $logger;
        $this->orderApiRepository = $orderApiRepository;
        $this->updateMerchantReferencesRequestProvider = $updateMerchantReferencesRequestProvider;
    }

    /**
     * @throws KlarnaPaymentException
     */
    public function run(UpdateMerchantReferencesRequestData $data): void
    {
        $this->logger->debug(sprintf('%s - Function called', __METHOD__));

        try {
            $request = $this->updateMerchantReferencesRequestProvider->get($data);

            $this->orderApiRepository->updateMerchantReferences($request);
        } catch (ApiException $exception) {
            throw CouldNotUpdateMerchantReferencesOrder::failedToGetSuccessfulApiResponse($exception);
        } catch (\Exception $exception) {
            throw CouldNotUpdateMerchantReferencesOrder::unknownError($exception);
        } catch (\Throwable $exception) {
            throw CouldNotUpdateMerchantReferencesOrder::failedToCreateApiRequest($exception);
        }

        $this->logger->debug(sprintf('%s - Function ended', __METHOD__));
    }
}
