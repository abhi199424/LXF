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

namespace KlarnaPayment\Module\Infrastructure\Exception;

if (!defined('_PS_VERSION_')) {
    exit;
}

// NOTE class to define most used exception codes for our development.
class ExceptionCode
{
    // API related codes should start with 1***
    public const API_FAILED_TO_GET_SUCCESSFUL_RESPONSE = 1001;
    public const API_FAILED_TO_CREATE_REQUEST = 1002;

    // Configuration related codes should start with 2***
    public const CONFIGURATION_UNSUPPORTED_CURRENCY = 2003;
    public const CONFIGURATION_MERCHANT_IS_NOT_LOGGED_IN = 2004;
    public const CONFIGURATION_MERCHANT_LOGIN_FAILED = 2005;

    // Account related codes should start with 3***
    public const ACCOUNT_FAILED_TO_CREATE_CART_INSTANCE = 3001;
    public const ACCOUNT_FAILED_TO_ADD_PRODUCT_TO_CART = 3002;
    public const ACCOUNT_FAILED_TO_UPDATE_CONTEXT = 3003;
    public const ACCOUNT_FAILED_TO_FIND_COUNTRY = 3004;
    public const ACCOUNT_FAILED_TO_FIND_STATE = 3005;

    public const ACCOUNT_TOKEN_EXPIRED = 3008;
    public const ACCOUNT_INVALID_ISSUER = 3009;
    public const ACCOUNT_INVALID_SCOPE = 3010;
    public const ACCOUNT_FAILED_TO_RETRIEVE_JWKS = 3011;
    public const ACCOUNT_FAILED_TO_PARSE_JWT = 3012;

    public const ACCOUNT_INVALID_KID = 3013;
    public const ACCOUNT_INACTIVE_COUNTRY = 3014;
    public const ACCOUNT_INACTIVE_ADDRESS_STATE = 3015;
    public const ACCOUNT_COULD_NOT_FIND_CUSTOMER = 3016;
    public const ACCOUNT_COULD_NOT_FIND_ADDRESS = 3017;

    // Payment related codes starts from 5***

    public const PAYMENT_FAILED_TO_FIND_CUSTOMER = 5001;
    public const PAYMENT_FAILED_TO_FIND_KLARNA_ORDER = 5002;
    public const PAYMENT_FAILED_TO_INSERT_ORDER_PAYMENT = 5004;
    public const PAYMENT_MISSING_PAYMENT_AMOUNT = 5005;
    public const PAYMENT_FAILED_TO_FIND_ORDER = 5006;
    public const PAYMENT_FAILED_TO_FIND_CART = 5007;
    public const PAYMENT_FAILED_TO_LOCK_CART = 5008;
    public const PAYMENT_FAILED_TO_CREATE_EXTERNAL_ORDER = 5009;
    public const PAYMENT_FAILED_TO_RETRIEVE_EXTERNAL_ORDER = 5010;
    public const PAYMENT_FAILED_TO_VALIDATE_ORDER = 5011;
    public const PAYMENT_FAILED_TO_UPDATE_MERCHANT_REFERENCES = 5012;
    public const PAYMENT_FAILED_TO_ADD_ORDER_MAPPING = 5013;
    public const PAYMENT_FAILED_TO_UNLOCK_CART = 5014;
    public const PAYMENT_FAILED_TO_AUTO_CAPTURE = 5015;
    public const PAYMENT_FAILED_TO_RETRIEVE_SESSION = 5016;
    public const PAYMENT_FAILED_TO_VALIDATE_STATUS = 5017;
    public const PAYMENT_FAILED_TO_VALIDATE_TOKEN = 5018;
    public const PAYMENT_FAILED_COULD_NOT_VALIDATE_SECURITY_TOKEN = 5019;

    // Infrastructure related code should start with 6***
    public const INFRASTRUCTURE_FAILED_TO_INSTALL_DATABASE_TABLE = 6001;
    public const INFRASTRUCTURE_FAILED_TO_UNINSTALL_DATABASE_TABLE = 6003;
    public const INFRASTRUCTURE_FAILED_TO_INSTALL_MODULE_TAB = 6005;
    public const INFRASTRUCTURE_FAILED_TO_UNINSTALL_MODULE_TAB = 6006;
    public const INFRASTRUCTURE_FAILED_TO_INSTALL_ORDER_STATE = 6007;
    public const INFRASTRUCTURE_LOCK_EXISTS = 6008;
    public const INFRASTRUCTURE_LOCK_ON_ACQUIRE_IS_MISSING = 6009;
    public const INFRASTRUCTURE_LOCK_ON_RELEASE_IS_MISSING = 6010;

    // Order related codes starts from 7***
    public const ORDER_FAILED_TO_FIND_ORDER = 7001;
    public const ORDER_UNHANDLED_ORDER_STATUS = 7002;
    public const ORDER_VERIFICATION_FAILED = 7003;
    public const ORDER_FAILED_TO_CHANGE_ORDER_STATUS = 7004;
    public const ORDER_STATUS_IS_INVALID = 7005;
    public const ORDER_HAS_ALREADY_BEEN_FULLY_REFUNDED = 7006;
    public const ORDER_FAILED_TO_AUTO_CAPTURE = 7007;
    public const ORDER_AUTO_CAPTURE_DISABLED = 7008;
    public const ORDER_STATUS_IS_NOT_IN_AUTO_CAPTURE_LIST = 7009;
    public const ORDER_CAPTURE_AMOUNT_IS_INVALID = 7010;
    public const ORDER_REFUND_AMOUNT_IS_INVALID = 7011;
    public const ORDER_FAILED_TO_RETRIEVE_ORDER = 7012;
    public const ORDER_FAILED_TO_FIND_KLARNA_ORDER = 7001;

    // Tools related codes starts from 8***
    public const TOOLS_FAILED_TO_PRUNE_RECORDS = 8001;

    // Any other unhandled codes should start with 9***
    public const UNKNOWN_ERROR = 9001;

    public const KEC_FLOW_NOT_AVAILABLE = 9002;
}
