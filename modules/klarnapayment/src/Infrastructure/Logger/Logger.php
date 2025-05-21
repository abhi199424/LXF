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

namespace KlarnaPayment\Module\Infrastructure\Logger;

use KlarnaPayment\Module\Core\Config\Config;
use KlarnaPayment\Module\Infrastructure\Adapter\Configuration;
use KlarnaPayment\Module\Infrastructure\Adapter\Context;
use KlarnaPayment\Module\Infrastructure\Context\GlobalShopContextInterface;
use KlarnaPayment\Module\Infrastructure\EntityManager\EntityManagerInterface;
use KlarnaPayment\Module\Infrastructure\EntityManager\ObjectModelUnitOfWork;
use KlarnaPayment\Module\Infrastructure\Logger\Formatter\LogFormatterInterface;
use KlarnaPayment\Module\Infrastructure\Logger\Repository\PrestashopLoggerRepositoryInterface;
use KlarnaPayment\Module\Infrastructure\Provider\NumberIdempotencyProvider;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Logger implements LoggerInterface
{
    const FILE_NAME = 'Logger';

    const LOG_OBJECT_TYPE = 'klarnapaymentLog';

    const SEVERITY_INFO = 1;
    const SEVERITY_WARNING = 2;
    const SEVERITY_ERROR = 3;

    private $logFormatter;
    private $globalShopContext;
    private $configuration;
    private $context;
    private $entityManager;
    private $idempotencyProvider;
    private $prestashopLoggerRepository;

    public function __construct(
        LogFormatterInterface $logFormatter,
        GlobalShopContextInterface $globalShopContext,
        Configuration $configuration,
        Context $context,
        EntityManagerInterface $entityManager,
        NumberIdempotencyProvider $idempotencyProvider,
        PrestashopLoggerRepositoryInterface $prestashopLoggerRepository
    ) {
        $this->logFormatter = $logFormatter;
        $this->globalShopContext = $globalShopContext;
        $this->configuration = $configuration;
        $this->context = $context;
        $this->entityManager = $entityManager;
        $this->idempotencyProvider = $idempotencyProvider;
        $this->prestashopLoggerRepository = $prestashopLoggerRepository;
    }

    public function emergency($message, array $context = []): void
    {
        $this->log(
            $this->configuration->getAsInteger(
                'PS_LOGS_BY_EMAIL',
                $this->globalShopContext->getShopId()
            ),
            $message,
            $context
        );
    }

    public function alert($message, array $context = []): void
    {
        $this->log(self::SEVERITY_WARNING, $message, $context);
    }

    public function critical($message, array $context = []): void
    {
        $this->log(
            $this->configuration->getAsInteger(
                'PS_LOGS_BY_EMAIL',
                $this->globalShopContext->getShopId()
            ),
            $message,
            $context
        );
    }

    public function error($message, array $context = []): void
    {
        $this->log(self::SEVERITY_ERROR, $message, $context);
    }

    public function warning($message, array $context = []): void
    {
        $this->log(self::SEVERITY_WARNING, $message, $context);
    }

    public function notice($message, array $context = []): void
    {
        $this->log(self::SEVERITY_INFO, $message, $context);
    }

    public function info($message, array $context = []): void
    {
        $this->log(self::SEVERITY_INFO, $message, $context);
    }

    public function debug($message, array $context = []): void
    {
        if (!$this->configuration->getByEnvironment(Config::KLARNA_PAYMENT_DEBUG_MODE)) {
            return;
        }

        $this->log(self::SEVERITY_INFO, $message, $context);
    }

    public function log($level, $message, array $context = []): void
    {
        $idempotencyKey = $this->idempotencyProvider->getIdempotencyKey();

        \PrestaShopLogger::addLog(
            $this->logFormatter->getMessage($message),
            $level,
            null,
            self::LOG_OBJECT_TYPE,
            $idempotencyKey
        );

        $logId = $this->prestashopLoggerRepository->getLogIdByObjectId(
            $idempotencyKey,
            $this->globalShopContext->getShopId()
        );

        if (!$logId) {
            return;
        }

        $this->logContext($logId, $context);
    }

    private function logContext($logId, array $context): void
    {
        $request = '';
        $response = '';
        $correlationId = '';

        if (isset($context['request'])) {
            $request = $context['request'];
            unset($context['request']);
        }

        if (isset($context['response'])) {
            $response = $context['response'];
            unset($context['response']);
        }

        if (isset($context['correlation-id'])) {
            $correlationId = $context['correlation-id'];
            unset($context['correlation-id']);
        }

        $log = new \KlarnaPaymentLog();
        $log->id_log = $logId;
        $log->id_shop = $this->globalShopContext->getShopId();
        $log->correlation_id = $correlationId;
        $log->context = json_encode($this->getFilledContextWithShopData($context));
        $log->request = json_encode($request);
        $log->response = json_encode($response);

        $this->entityManager->persist($log, ObjectModelUnitOfWork::UNIT_OF_WORK_SAVE);
        $this->entityManager->flush();
    }

    private function getFilledContextWithShopData(array $context = []): array
    {
        $context['context_id_customer'] = $this->context->getCustomerId();
        $context['id_shop'] = $this->globalShopContext->getShopId();
        $context['currency'] = $this->globalShopContext->getCurrencyIso();
        $context['id_language'] = $this->globalShopContext->getLanguageId();

        return $context;
    }
}
