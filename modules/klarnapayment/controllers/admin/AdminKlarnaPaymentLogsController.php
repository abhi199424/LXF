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

use KlarnaPayment\Module\Core\Config\Config;
use KlarnaPayment\Module\Core\Merchant\Provider\CredentialsConfigurationKeyProvider;
use KlarnaPayment\Module\Infrastructure\Adapter\Configuration;
use KlarnaPayment\Module\Infrastructure\Adapter\Context;
use KlarnaPayment\Module\Infrastructure\Bootstrap\ModuleTabs;
use KlarnaPayment\Module\Infrastructure\Context\GlobalShopContextInterface;
use KlarnaPayment\Module\Infrastructure\Controller\AbstractAdminController as ModuleAdminController;
use KlarnaPayment\Module\Infrastructure\Enum\PermissionType;
use KlarnaPayment\Module\Infrastructure\Logger\Formatter\LogFormatter;
use KlarnaPayment\Module\Infrastructure\Logger\Logger;
use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;
use KlarnaPayment\Module\Infrastructure\Logger\Repository\KlarnaPaymentLogRepositoryInterface;
use KlarnaPayment\Module\Infrastructure\Utility\ExceptionUtility;
use KlarnaPayment\Module\Infrastructure\Utility\VersionUtility;

require_once dirname(__FILE__) . '/../../vendor/autoload.php';

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminKlarnaPaymentLogsController extends ModuleAdminController
{
    const FILE_NAME = 'AdminKlarnaPaymentLogsController';

    const LOG_INFORMATION_TYPE_REQUEST = 'request';
    const LOG_INFORMATION_TYPE_RESPONSE = 'response';
    const LOG_INFORMATION_TYPE_CONTEXT = 'context';

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'log';
        $this->className = 'PrestaShopLogger';
        $this->lang = false;
        $this->noLink = true;
        $this->allow_export = true;

        parent::__construct();

        $this->toolbar_btn = [];
        $this->fields_list = [
            'id_log' => [
                'title' => $this->module->l('ID', self::FILE_NAME),
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
            ],
            'severity' => [
                'title' => $this->module->l('Severity (1-4)', self::FILE_NAME),
                'align' => 'text-center',
                'class' => 'fixed-width-xs',
                'callback' => 'printSeverityLevel',
            ],
            'message' => [
                'title' => $this->module->l('Message', self::FILE_NAME),
            ],
            'correlation_id' => [
                'title' => $this->module->l('Correlation ID', self::FILE_NAME),
                'class' => 'fixed-width-xs',
            ],
            'request' => [
                'title' => $this->module->l('Request', self::FILE_NAME),
                'align' => 'text-center',
                'callback' => 'printRequestButton',
                'orderby' => false,
                'search' => false,
                'remove_onclick' => true,
            ],
            'response' => [
                'title' => $this->module->l('Response', self::FILE_NAME),
                'align' => 'text-center',
                'callback' => 'printResponseButton',
                'orderby' => false,
                'search' => false,
                'remove_onclick' => true,
            ],
            'context' => [
                'title' => $this->module->l('Context', self::FILE_NAME),
                'align' => 'text-center',
                'callback' => 'printContextButton',
                'orderby' => false,
                'search' => false,
                'remove_onclick' => true,
            ],
            'date_add' => [
                'title' => $this->module->l('Date', self::FILE_NAME),
                'align' => 'right',
                'type' => 'datetime',
                'filter_key' => 'a!date_add',
            ],
        ];

        $this->_orderBy = 'id_log';
        $this->_orderWay = 'desc';

        $this->_select .= '
            REPLACE(a.`message`, "' . LogFormatter::KLARNA_PAYMENT_LOG_PREFIX . '", "") as message,
            kpl.request, kpl.response, kpl.context,
            kpl.correlation_id
        ';

        $shopIdCheck = '';

        if (VersionUtility::isPsVersionGreaterOrEqualTo('1.7.8.0')) {
            $shopIdCheck = ' AND kpl.id_shop = a.id_shop';
        }

        $this->_join .= ' JOIN ' . _DB_PREFIX_ . 'klarnapayment_logs kpl ON (kpl.id_log = a.id_log' . $shopIdCheck . ' AND a.object_type = "' . pSQL(Logger::LOG_OBJECT_TYPE) . '")';
        $this->_use_found_rows = false;
        $this->list_no_link = true;
    }

    /**
     * @return false|string
     *
     * @throws SmartyException
     */
    public function displaySeverityInformation()
    {
        return $this->context->smarty->fetch(
            "{$this->module->getLocalPath()}views/templates/admin/logs/severity_levels.tpl"
        );
    }

    /**
     * @throws SmartyException
     */
    public function initContent(): void
    {
        // NOTE: we cannot add new logs here.
        if (isset($this->toolbar_btn['new'])) {
            unset($this->toolbar_btn['new']);
        }

        $this->content .= $this->displaySeverityInformation();

        parent::initContent();
    }

    public function setMedia($isNewTheme = false): void
    {
        parent::setMedia($isNewTheme);

        /** @var Context $context */
        $context = $this->module->getService(Context::class);

        Media::addJsDef([
            'klarnapayment' => [
                'logsUrl' => $context->getAdminLink(ModuleTabs::LOGS_MODULE_TAB_CONTROLLER_NAME),
            ],
        ]);

        $this->addJS($this->module->getPathUri() . 'views/js/admin/logs/log.js', false);
        $this->addCss($this->module->getPathUri() . 'views/css/admin/logs/log.css');
    }

    /**
     * @param string $request
     * @param array $data
     *
     * @return false|string
     *
     * @throws SmartyException
     */
    public function printRequestButton(string $request, array $data)
    {
        return $this->getDisplayButton($data['id_log'], $request, self::LOG_INFORMATION_TYPE_REQUEST);
    }

    /**
     * @param string $response
     * @param array $data
     *
     * @return false|string
     *
     * @throws SmartyException
     */
    public function printResponseButton(string $response, array $data)
    {
        return $this->getDisplayButton($data['id_log'], $response, self::LOG_INFORMATION_TYPE_RESPONSE);
    }

    /**
     * @param string $context
     * @param array $data
     *
     * @return false|string
     *
     * @throws SmartyException
     */
    public function printContextButton(string $context, array $data)
    {
        return $this->getDisplayButton($data['id_log'], $context, self::LOG_INFORMATION_TYPE_CONTEXT);
    }

    /**
     * @param int $level
     *
     * @return false|string
     *
     * @throws SmartyException
     */
    public function printSeverityLevel(int $level)
    {
        $this->context->smarty->assign([
            'log_severity_level' => $level,
            'log_severity_level_informative' => defined('\PrestaShopLogger::LOG_SEVERITY_LEVEL_INFORMATIVE') ?
                PrestaShopLogger::LOG_SEVERITY_LEVEL_INFORMATIVE :
                Config::LOG_SEVERITY_LEVEL_INFORMATIVE,
            'log_severity_level_warning' => defined('\PrestaShopLogger::LOG_SEVERITY_LEVEL_WARNING') ?
                PrestaShopLogger::LOG_SEVERITY_LEVEL_WARNING :
                Config::LOG_SEVERITY_LEVEL_WARNING,
            'log_severity_level_error' => defined('\PrestaShopLogger::LOG_SEVERITY_LEVEL_ERROR') ?
                PrestaShopLogger::LOG_SEVERITY_LEVEL_ERROR :
                Config::LOG_SEVERITY_LEVEL_ERROR,
            'log_severity_level_major' => defined('\PrestaShopLogger::LOG_SEVERITY_LEVEL_MAJOR') ?
                PrestaShopLogger::LOG_SEVERITY_LEVEL_MAJOR :
                Config::LOG_SEVERITY_LEVEL_MAJOR,
        ]);

        return $this->context->smarty->fetch(
            "{$this->module->getLocalPath()}views/templates/admin/logs/severity_level_column.tpl"
        );
    }

    /**
     * @param int $logId
     * @param string $data
     * @param string $logInformationType
     *
     * @return false|string
     *
     * @throws SmartyException
     */
    public function getDisplayButton(int $logId, string $data, string $logInformationType)
    {
        $unserializedData = json_decode($data);

        if (empty($unserializedData)) {
            return '--';
        }

        $this->context->smarty->assign([
            'log_id' => $logId,
            'log_information_type' => $logInformationType,
        ]);

        return $this->context->smarty->fetch(
            "{$this->module->getLocalPath()}views/templates/admin/logs/log_modal.tpl"
        );
    }

    public function displayAjaxGetLog()
    {
        if (!$this->ensureHasPermissions([PermissionType::EDIT, PermissionType::VIEW], true)) {
            return;
        }

        /** @var \KlarnaPayment\Module\Infrastructure\Adapter\Tools $tools */
        $tools = $this->module->getService(\KlarnaPayment\Module\Infrastructure\Adapter\Tools::class);

        /** @var KlarnaPaymentLogRepositoryInterface $logRepository */
        $logRepository = $this->module->getService(KlarnaPaymentLogRepositoryInterface::class);

        /** @var GlobalShopContextInterface $globalShopContext */
        $globalShopContext = $this->module->getService(GlobalShopContextInterface::class);

        $logId = $tools->getValueAsInt('log_id');

        /** @var LoggerInterface $logger */
        $logger = $this->module->getService(LoggerInterface::class);

        try {
            /** @var \KlarnaPaymentLog|null $log */
            $log = $logRepository->findOneBy([
                'id_log' => $logId,
                'id_shop' => $globalShopContext->getShopId(),
            ]);
        } catch (Exception $exception) {
            $logger->error('Failed to find log', [
                'context' => [
                    'id_log' => $logId,
                    'id_shop' => $globalShopContext->getShopId(),
                ],
                'exceptions' => ExceptionUtility::getExceptions($exception),
            ]);

            $this->ajaxResponse(json_encode([
                'error' => true,
                'message' => $this->module->l('Failed to find log.', self::FILE_NAME),
            ]));
        }

        if (!isset($log)) {
            $logger->error('No log information found.', [
                'context' => [
                    'id_log' => $logId,
                    'id_shop' => $globalShopContext->getShopId(),
                ],
                'exceptions' => [],
            ]);

            $this->ajaxResponse(json_encode([
                'error' => true,
                'message' => $this->module->l('No log information found.', self::FILE_NAME),
            ]));
        }

        $this->ajaxResponse(json_encode([
            'error' => false,
            'log' => [
                self::LOG_INFORMATION_TYPE_REQUEST => $log->request,
                self::LOG_INFORMATION_TYPE_RESPONSE => $log->response,
                self::LOG_INFORMATION_TYPE_CONTEXT => $log->context,
            ],
        ]));
    }

    public function processExport($textDelimiter = '"')
    {
        // clean buffer
        if (ob_get_level() && ob_get_length() > 0) {
            ob_clean();
        }

        header('Content-type: text/csv');
        header('Content-Type: application/force-download; charset=UTF-8');
        header('Cache-Control: no-store, no-cache');
        header('Content-disposition: attachment; filename="' . $this->table . '_' . date('Y-m-d_His') . '.csv"');

        $fd = fopen('php://output', 'wb');

        /** @var Configuration $configuration */
        $configuration = $this->module->getService(Configuration::class);

        /** @var Context $context */
        $context = $this->module->getService(Context::class);

        /** @var CredentialsConfigurationKeyProvider $configurationKeyProvider */
        $configurationKeyProvider = $this->module->getService(CredentialsConfigurationKeyProvider::class);

        $storeInfo = [
            'PrestaShop Version' => _PS_VERSION_,
            'PHP Version' => phpversion(),
            'Module Version' => $this->module->version,
            'MySQL Version' => \Db::getInstance()->getVersion(),
            'MID' => $configuration->get($configurationKeyProvider->getApiUsername()),
            'Shop URL' => $context->getShopDomain(),
            'Shop Name' => $context->getShopName(),
        ];

        $moduleConfigurations = [
            'Environment' => $configuration->getAsBoolean(Config::KLARNA_PAYMENT_IS_PRODUCTION_ENVIRONMENT) ? 'Production' : 'Sandbox',
            'Automatic order status change' => $configuration->getByEnvironmentAsBoolean(Config::KLARNA_PAYMENT_ALLOW_ORDER_STATUS_CHANGE) ? 'Yes' : 'No',
            'Allow HPP' => $configuration->getByEnvironmentAsBoolean(Config::KLARNA_PAYMENT_HPP_SERVICE) ? 'Yes' : 'No',
            'Default country' => $configuration->getByEnvironment(Config::KLARNA_PAYMENT_DEFAULT_LOCALE),
            'Capture Klarna order upon fulfillment' => $configuration->getByEnvironment(Config::KLARNA_PAYMENT_AUTO_CAPTURE_ORDER) ? 'Yes' : 'No',
            'Fulfillment order statuses' => $configuration->getByEnvironment(Config::KLARNA_PAYMENT_AUTO_CAPTURE_ORDER_STATUSES),
            'Activate OSM' => $configuration->getByEnvironmentAsBoolean(Config::KLARNA_PAYMENT_ONSITE_MESSAGING_ACTIVE) ? 'Yes' : 'No',
            'Activate KEC' => $configuration->getByEnvironmentAsBoolean(Config::KLARNA_PAYMENT_EXPRESS_CHECKOUT_ACTIVE) ? 'Yes' : 'No',
            'KEC placement' => $configuration->getByEnvironment(Config::KLARNA_PAYMENT_EXPRESS_CHECKOUT_PLACEMENTS),
            'Order minimum' => $configuration->getByEnvironment(Config::KLARNA_PAYMENT_ORDER_MIN),
            'Order maximum' => $configuration->getByEnvironment(Config::KLARNA_PAYMENT_ORDER_MAX),
        ];

        $psSettings = [
            'Default country' => $configuration->get('PS_COUNTRY_DEFAULT'),
            'Default currency' => $configuration->get('PS_CURRENCY_DEFAULT'),
            'Default language' => $configuration->get('PS_LANG_DEFAULT'),
            'Round mode' => $configuration->get('PS_PRICE_ROUND_MODE'),
            'Round type' => $configuration->get('PS_ROUND_TYPE'),
            'Current theme name' => $context->getShopThemeName(),
            'OPC modules' => $context->getActiveOpcModules(),
            'PHP memory limit' => ini_get('memory_limit'),
        ];

        $moduleConfigurationsInfo = "**Module configurations:**\n";
        foreach ($moduleConfigurations as $key => $value) {
            $moduleConfigurationsInfo .= "- $key: $value\n";
        }

        $psSettingsInfo = "**Prestashop settings:**\n";
        foreach ($psSettings as $key => $value) {
            $psSettingsInfo .= "- $key: $value\n";
        }

        fputcsv($fd, array_keys($storeInfo), ';', $textDelimiter);
        fputcsv($fd, $storeInfo, ';', $textDelimiter);
        fputcsv($fd, [], ';', $textDelimiter);

        fputcsv($fd, [$moduleConfigurationsInfo], ';', $textDelimiter);
        fputcsv($fd, [$psSettingsInfo], ';', $textDelimiter);

        $query = new \DbQuery();

        $query
            ->select('kl.id_log, l.severity, l.message, kl.correlation_id, kl.request, kl.response, kl.context, kl.date_add')
            ->from('klarnapayment_logs', 'kl')
            ->leftJoin('log', 'l', 'kl.id_log = l.id_log')
            ->orderBy('kl.id_log DESC')
            ->limit(1000);

        $result = \Db::getInstance()->executeS($query);

        $firstRow = $result[0];
        $headers = [];

        foreach ($firstRow as $key => $value) {
            $headers[] = strtoupper($key);
        }

        $fd = fopen('php://output', 'wb');

        fputcsv($fd, $headers, ';', $textDelimiter);

        $content = !empty($result) ? $result : [];

        foreach ($content as $row) {
            $rowValues = [];
            foreach ($row as $key => $value) {
                $rowValues[] = $value;
            }

            fputcsv($fd, $rowValues, ';', $textDelimiter);
        }

        @fclose($fd);
        exit;
    }
}
