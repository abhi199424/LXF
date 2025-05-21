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

namespace KlarnaPayment\Module\Core\Config;

use KlarnaPayment\Module\Api\Endpoint;
use KlarnaPayment\Module\Core\Shared\Enum\KlarnaExpressCheckoutButtonShape;
use KlarnaPayment\Module\Core\Shared\Enum\KlarnaExpressCheckoutButtonTheme;
use KlarnaPayment\Module\Core\Shared\Enum\KlarnaSignInWithKlarnaButtonShape;
use KlarnaPayment\Module\Core\Shared\Enum\KlarnaSignInWithKlarnaButtonTheme;
use KlarnaPayment\Module\Core\Shared\Enum\OnsiteMessagingPlacementThemes;
use KlarnaPayment\Module\Core\Shared\Enum\SignInWithKlarnaBadgeAlignment;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Config
{
    public const KLARNA_IMAGE_URL = [
        // Klarna product
        'OSM' => [
            // Theme
            OnsiteMessagingPlacementThemes::LIGHT => [
                // Placement
                'KLARNA_PAYMENT_ONSITE_MESSAGING_FOOTER_DATA_KEY' => [
                    'footer-promotion-auto-size' => 'https://x.klarnacdn.net/plugin-build-sourcing/text_logo_bright/osm_text_logo_bright_footer-6774028fa301dec6e54611cc2b1ff23eddca8cc8.jpg',
                    'footer-promotion-badge' => 'https://x.klarnacdn.net/plugin-build-sourcing/pink_bage_bright/osm_pink_bage_bright_footer-6774028fa301dec6e54611cc2b1ff23eddca8cc8.png',
                ],
                'KLARNA_PAYMENT_ONSITE_MESSAGING_TOP_OF_PAGE_DATA_KEY' => [
                    'top-strip-promotion-auto-size' => 'https://x.klarnacdn.net/plugin-build-sourcing/text_logo_bright/osm_text_logo_bright_sitewide_banner_(topstrip)-6774028fa301dec6e54611cc2b1ff23eddca8cc8.jpg',
                    'top-strip-promotion-badge' => 'https://x.klarnacdn.net/plugin-build-sourcing/pink_bage_bright/osm_pink_bage_bright_sitewide_banner_(topstrip)-6774028fa301dec6e54611cc2b1ff23eddca8cc8.png',
                ],
                'KLARNA_PAYMENT_ONSITE_MESSAGING_PRODUCT_PAGE_DATA_KEY' => [
                    'credit-promotion-badge' => 'https://x.klarnacdn.net/plugin-build-sourcing/pink_bage_bright/osm_pink_bage_bright_product_page-6774028fa301dec6e54611cc2b1ff23eddca8cc8.png',
                    'credit-promotion-auto-size' => 'https://x.klarnacdn.net/plugin-build-sourcing/text_logo_bright/osm_text_logo_bright_product_page-6774028fa301dec6e54611cc2b1ff23eddca8cc8.jpg',
                ],
                'KLARNA_PAYMENT_ONSITE_MESSAGING_CART_PAGE_DATA_KEY' => [
                    'credit-promotion-badge' => 'https://x.klarnacdn.net/plugin-build-sourcing/pink_bage_bright/osm_pink_bage_bright_cart_page-6774028fa301dec6e54611cc2b1ff23eddca8cc8.png',
                    'credit-promotion-auto-size' => 'https://x.klarnacdn.net/plugin-build-sourcing/text_logo_bright/osm_text_logo_bright_cart_page-6774028fa301dec6e54611cc2b1ff23eddca8cc8.jpg',
                ],
            ],
            OnsiteMessagingPlacementThemes::DARK => [
                // Placement
                'KLARNA_PAYMENT_ONSITE_MESSAGING_FOOTER_DATA_KEY' => [
                    'footer-promotion-auto-size' => 'https://x.klarnacdn.net/plugin-build-sourcing/text_logo_dark/osm_text_logo_dark_footer-6774028fa301dec6e54611cc2b1ff23eddca8cc8.jpg',
                    'footer-promotion-badge' => 'https://x.klarnacdn.net/plugin-build-sourcing/pink_badge_dark/osm_pink_badge_dark_footer-6774028fa301dec6e54611cc2b1ff23eddca8cc8.jpg',
                ],
                'KLARNA_PAYMENT_ONSITE_MESSAGING_TOP_OF_PAGE_DATA_KEY' => [
                    'top-strip-promotion-auto-size' => 'https://x.klarnacdn.net/plugin-build-sourcing/text_logo_dark/osm_text_logo_dark_sitewide_banner_(topstrip)-6774028fa301dec6e54611cc2b1ff23eddca8cc8.jpg',
                    'top-strip-promotion-badge' => 'https://x.klarnacdn.net/plugin-build-sourcing/pink_badge_dark/osm_pink_badge_dark_sitewide_banner_(topstrip)-6774028fa301dec6e54611cc2b1ff23eddca8cc8.jpg',
                ],
                'KLARNA_PAYMENT_ONSITE_MESSAGING_PRODUCT_PAGE_DATA_KEY' => [
                    'credit-promotion-badge' => 'https://x.klarnacdn.net/plugin-build-sourcing/pink_badge_dark/osm_pink_badge_dark_product_page-6774028fa301dec6e54611cc2b1ff23eddca8cc8.jpg',
                    'credit-promotion-auto-size' => 'https://x.klarnacdn.net/plugin-build-sourcing/text_logo_dark/osm_text_logo_dark_product_page-6774028fa301dec6e54611cc2b1ff23eddca8cc8.jpg',
                ],
                'KLARNA_PAYMENT_ONSITE_MESSAGING_CART_PAGE_DATA_KEY' => [
                    'credit-promotion-badge' => 'https://x.klarnacdn.net/plugin-build-sourcing/pink_badge_dark/osm_pink_badge_dark_cart_page-6774028fa301dec6e54611cc2b1ff23eddca8cc8.jpg',
                    'credit-promotion-auto-size' => 'https://x.klarnacdn.net/plugin-build-sourcing/text_logo_dark/osm_text_logo_dark_cart_page-6774028fa301dec6e54611cc2b1ff23eddca8cc8.jpg',
                ],
            ],
        ],
        'KEC' => [
            // Theme
            KlarnaExpressCheckoutButtonTheme::DEFAULT => [
                KlarnaExpressCheckoutButtonShape::DEFAULT => 'https://x.klarnacdn.net/plugin-build-sourcing/kec_button/default/kec_button_default_rounded@3x-6774028fa301dec6e54611cc2b1ff23eddca8cc8.png',
                KlarnaExpressCheckoutButtonShape::RECT => 'https://x.klarnacdn.net/plugin-build-sourcing/kec_button/default/kec_button_default_default@3x-6774028fa301dec6e54611cc2b1ff23eddca8cc8.png',
                KlarnaExpressCheckoutButtonShape::PILL => 'https://x.klarnacdn.net/plugin-build-sourcing/kec_button/default/kec_button_default_square@3x-6774028fa301dec6e54611cc2b1ff23eddca8cc8.png',
            ],
            KlarnaExpressCheckoutButtonTheme::LIGHT => [
                KlarnaExpressCheckoutButtonShape::DEFAULT => 'https://x.klarnacdn.net/plugin-build-sourcing/kec_button/light/kec_button_light_round@3x-6774028fa301dec6e54611cc2b1ff23eddca8cc8.png',
                KlarnaExpressCheckoutButtonShape::RECT => 'https://x.klarnacdn.net/plugin-build-sourcing/kec_button/light/kec_button_light_default@3x-6774028fa301dec6e54611cc2b1ff23eddca8cc8.png',
                KlarnaExpressCheckoutButtonShape::PILL => 'https://x.klarnacdn.net/plugin-build-sourcing/kec_button/light/kec_button_light_square@3x-6774028fa301dec6e54611cc2b1ff23eddca8cc8.png',
            ],
            KlarnaExpressCheckoutButtonTheme::OUTLINED => [
                KlarnaExpressCheckoutButtonShape::DEFAULT => 'https://x.klarnacdn.net/plugin-build-sourcing/kec_button/light_outlined/kec_button_light_outlined_rounded@3x-6774028fa301dec6e54611cc2b1ff23eddca8cc8.png',
                KlarnaExpressCheckoutButtonShape::RECT => 'https://x.klarnacdn.net/plugin-build-sourcing/kec_button/light_outlined/kec_button_light_outlined_default@3x-6774028fa301dec6e54611cc2b1ff23eddca8cc8.png',
                KlarnaExpressCheckoutButtonShape::PILL => 'https://x.klarnacdn.net/plugin-build-sourcing/kec_button/light_outlined/kec_button_light_outlined_square@3x-6774028fa301dec6e54611cc2b1ff23eddca8cc8.png',
            ],
        ],
        'SIWK' => [
            // Dark Theme
            KlarnaSignInWithKlarnaButtonTheme::DEFAULT => [
                KlarnaSignInWithKlarnaButtonShape::DEFAULT => [
                    SignInWithKlarnaBadgeAlignment::DEFAULT => 'https://x.klarnacdn.net/plugin-build-sourcing/default_shape/dark_theme_default/siwk_merchant_button-6774028fa301dec6e54611cc2b1ff23eddca8cc8.png',
                    SignInWithKlarnaBadgeAlignment::LEFT => 'https://x.klarnacdn.net/plugin-build-sourcing/default_shape/dark_theme_default/siwk_merchant_button_icon_left-6774028fa301dec6e54611cc2b1ff23eddca8cc8.png',
                    SignInWithKlarnaBadgeAlignment::CENTER => 'https://x.klarnacdn.net/plugin-build-sourcing/default_shape/dark_theme_default/siwk_merchant_button_icon_centered-6774028fa301dec6e54611cc2b1ff23eddca8cc8.png',
                ],
                KlarnaSignInWithKlarnaButtonShape::RECT => [
                    SignInWithKlarnaBadgeAlignment::DEFAULT => 'https://x.klarnacdn.net/plugin-build-sourcing/rectangular_shape/dark_theme_default/siwk_merchant_button-6774028fa301dec6e54611cc2b1ff23eddca8cc8.png',
                    SignInWithKlarnaBadgeAlignment::LEFT => 'https://x.klarnacdn.net/plugin-build-sourcing/rectangular_shape/dark_theme_default/siwk_merchant_button_icon_left-6774028fa301dec6e54611cc2b1ff23eddca8cc8.png',
                    SignInWithKlarnaBadgeAlignment::CENTER => 'https://x.klarnacdn.net/plugin-build-sourcing/rectangular_shape/dark_theme_default/siwk_merchant_button_icon_centered-6774028fa301dec6e54611cc2b1ff23eddca8cc8.png',
                ],
                KlarnaSignInWithKlarnaButtonShape::PILL => [
                    SignInWithKlarnaBadgeAlignment::DEFAULT => 'https://x.klarnacdn.net/plugin-build-sourcing/pill_shape/dark_theme_default/siwk_merchant_button-6774028fa301dec6e54611cc2b1ff23eddca8cc8.png',
                    SignInWithKlarnaBadgeAlignment::LEFT => 'https://x.klarnacdn.net/plugin-build-sourcing/pill_shape/dark_theme_default/siwk_merchant_button_icon_left-6774028fa301dec6e54611cc2b1ff23eddca8cc8.png',
                    SignInWithKlarnaBadgeAlignment::CENTER => 'https://x.klarnacdn.net/plugin-build-sourcing/pill_shape/dark_theme_default/siwk_merchant_button_icon_centered-6774028fa301dec6e54611cc2b1ff23eddca8cc8.png',
                ],
            ],
            // Light Theme
            KlarnaSignInWithKlarnaButtonTheme::LIGHT => [
                KlarnaSignInWithKlarnaButtonShape::DEFAULT => [
                    SignInWithKlarnaBadgeAlignment::DEFAULT => 'https://x.klarnacdn.net/plugin-build-sourcing/default_shape/light_theme/siwk_merchant_button-6774028fa301dec6e54611cc2b1ff23eddca8cc8.png',
                    SignInWithKlarnaBadgeAlignment::LEFT => 'https://x.klarnacdn.net/plugin-build-sourcing/default_shape/light_theme/siwk_merchant_button_icon_left-6774028fa301dec6e54611cc2b1ff23eddca8cc8.png',
                    SignInWithKlarnaBadgeAlignment::CENTER => 'https://x.klarnacdn.net/plugin-build-sourcing/default_shape/light_theme/siwk_merchant_button_icon_centered-6774028fa301dec6e54611cc2b1ff23eddca8cc8.png',
                ],
                KlarnaSignInWithKlarnaButtonShape::RECT => [
                    SignInWithKlarnaBadgeAlignment::DEFAULT => 'https://x.klarnacdn.net/plugin-build-sourcing/rectangular_shape/light_theme/siwk_merchant_button-6774028fa301dec6e54611cc2b1ff23eddca8cc8.png',
                    SignInWithKlarnaBadgeAlignment::LEFT => 'https://x.klarnacdn.net/plugin-build-sourcing/rectangular_shape/light_theme/siwk_merchant_button_icon_left-6774028fa301dec6e54611cc2b1ff23eddca8cc8.png',
                    SignInWithKlarnaBadgeAlignment::CENTER => 'https://x.klarnacdn.net/plugin-build-sourcing/rectangular_shape/light_theme/siwk_merchant_button_icon_centered-6774028fa301dec6e54611cc2b1ff23eddca8cc8.png',
                ],
                KlarnaSignInWithKlarnaButtonShape::PILL => [
                    SignInWithKlarnaBadgeAlignment::DEFAULT => 'https://x.klarnacdn.net/plugin-build-sourcing/pill_shape/light_theme/siwk_merchant_button-6774028fa301dec6e54611cc2b1ff23eddca8cc8.png',
                    SignInWithKlarnaBadgeAlignment::LEFT => 'https://x.klarnacdn.net/plugin-build-sourcing/pill_shape/light_theme/siwk_merchant_button_icon_left-6774028fa301dec6e54611cc2b1ff23eddca8cc8.png',
                    SignInWithKlarnaBadgeAlignment::CENTER => 'https://x.klarnacdn.net/plugin-build-sourcing/pill_shape/light_theme/siwk_merchant_button_icon_centered-6774028fa301dec6e54611cc2b1ff23eddca8cc8.png',
                ],
            ],
            // Outlined Theme
            KlarnaSignInWithKlarnaButtonTheme::OUTLINED => [
                KlarnaSignInWithKlarnaButtonShape::DEFAULT => [
                    SignInWithKlarnaBadgeAlignment::DEFAULT => 'https://x.klarnacdn.net/plugin-build-sourcing/default_shape/light_outlined_theme/siwk_merchant_button_icon_left-6774028fa301dec6e54611cc2b1ff23eddca8cc8.png',
                    SignInWithKlarnaBadgeAlignment::LEFT => 'https://x.klarnacdn.net/plugin-build-sourcing/default_shape/light_outlined_theme/siwk_merchant_button-6774028fa301dec6e54611cc2b1ff23eddca8cc8.png',
                    SignInWithKlarnaBadgeAlignment::CENTER => 'https://x.klarnacdn.net/plugin-build-sourcing/default_shape/light_outlined_theme/siwk_merchant_button_icon_centered-6774028fa301dec6e54611cc2b1ff23eddca8cc8.png',
                ],
                KlarnaSignInWithKlarnaButtonShape::RECT => [
                    SignInWithKlarnaBadgeAlignment::DEFAULT => 'https://x.klarnacdn.net/plugin-build-sourcing/rectangular_shape/light_outlined_theme/siwk_merchant_button-6774028fa301dec6e54611cc2b1ff23eddca8cc8.png',
                    SignInWithKlarnaBadgeAlignment::LEFT => 'https://x.klarnacdn.net/plugin-build-sourcing/rectangular_shape/light_outlined_theme/siwk_merchant_button_icon_left-6774028fa301dec6e54611cc2b1ff23eddca8cc8.png',
                    SignInWithKlarnaBadgeAlignment::CENTER => 'https://x.klarnacdn.net/plugin-build-sourcing/rectangular_shape/light_outlined_theme/siwk_merchant_button_icon_centered-6774028fa301dec6e54611cc2b1ff23eddca8cc8.png',
                ],
                KlarnaSignInWithKlarnaButtonShape::PILL => [
                    SignInWithKlarnaBadgeAlignment::DEFAULT => 'https://x.klarnacdn.net/plugin-build-sourcing/pill_shape/light_outlined_theme/siwk_merchant_button-6774028fa301dec6e54611cc2b1ff23eddca8cc8.png',
                    SignInWithKlarnaBadgeAlignment::LEFT => 'https://x.klarnacdn.net/plugin-build-sourcing/pill_shape/light_outlined_theme/siwk_merchant_button_icon_left-6774028fa301dec6e54611cc2b1ff23eddca8cc8.png',
                    SignInWithKlarnaBadgeAlignment::CENTER => 'https://x.klarnacdn.net/plugin-build-sourcing/pill_shape/light_outlined_theme/siwk_merchant_button_icon_centered-6774028fa301dec6e54611cc2b1ff23eddca8cc8.png',
                ],
            ],
        ],
    ];

    public const KLARNA_PAYMENT_API_KEY = [
        'sandbox' => [
            'EU' => 'KLARNA_PAYMENT_SANDBOX_API_KEY_EU',
            'NA-CA' => 'KLARNA_PAYMENT_SANDBOX_API_KEY_NA-CA',
            'NA-US' => 'KLARNA_PAYMENT_SANDBOX_API_KEY_NA-US',
            'NA-MX' => 'KLARNA_PAYMENT_SANDBOX_API_KEY_NA-MX',
            'AP-AP' => 'KLARNA_PAYMENT_SANDBOX_API_KEY_AP-AP',
            'AP-NZ' => 'KLARNA_PAYMENT_SANDBOX_API_KEY_AP-NZ',
        ],
        'production' => [
            'EU' => 'KLARNA_PAYMENT_PRODUCTION_API_KEY_EU',
            'NA-CA' => 'KLARNA_PAYMENT_PRODUCTION_API_KEY_NA-CA',
            'NA-US' => 'KLARNA_PAYMENT_PRODUCTION_API_KEY_NA-US',
            'NA-MX' => 'KLARNA_PAYMENT_PRODUCTION_API_KEY_NA-MX',
            'AP-AP' => 'KLARNA_PAYMENT_PRODUCTION_API_KEY_AP-AP',
            'AP-NZ' => 'KLARNA_PAYMENT_PRODUCTION_API_KEY_AP-NZ',
        ],
    ];

    public const KLARNA_PAYMENT_API_USERNAME = [
        'sandbox' => [
            'EU' => 'KLARNA_PAYMENT_SANDBOX_API_USERNAME_EU',
            'NA-CA' => 'KLARNA_PAYMENT_SANDBOX_API_USERNAME_NA-CA',
            'NA-US' => 'KLARNA_PAYMENT_SANDBOX_API_USERNAME_NA-US',
            'NA-MX' => 'KLARNA_PAYMENT_SANDBOX_API_USERNAME_NA-MX',
            'AP-AP' => 'KLARNA_PAYMENT_SANDBOX_API_USERNAME_AP-AP',
            'AP-NZ' => 'KLARNA_PAYMENT_SANDBOX_API_USERNAME_AP-NZ',
        ],
        'production' => [
            'EU' => 'KLARNA_PAYMENT_PRODUCTION_API_USERNAME_EU',
            'NA-CA' => 'KLARNA_PAYMENT_PRODUCTION_API_USERNAME_NA-CA',
            'NA-US' => 'KLARNA_PAYMENT_PRODUCTION_API_USERNAME_NA-US',
            'NA-MX' => 'KLARNA_PAYMENT_PRODUCTION_API_USERNAME_NA-MX',
            'AP-AP' => 'KLARNA_PAYMENT_PRODUCTION_API_USERNAME_AP-AP',
            'AP-NZ' => 'KLARNA_PAYMENT_PRODUCTION_API_USERNAME_AP-NZ',
        ],
    ];

    public const KLARNA_PAYMENT_API_PASSWORD = [
        'sandbox' => [
            'EU' => 'KLARNA_PAYMENT_SANDBOX_API_PASSWORD_EU',
            'NA-CA' => 'KLARNA_PAYMENT_SANDBOX_API_PASSWORD_NA-CA',
            'NA-US' => 'KLARNA_PAYMENT_SANDBOX_API_PASSWORD_NA-US',
            'NA-MX' => 'KLARNA_PAYMENT_SANDBOX_API_PASSWORD_NA-MX',
            'AP-AP' => 'KLARNA_PAYMENT_SANDBOX_API_PASSWORD_AP-AP',
            'AP-NZ' => 'KLARNA_PAYMENT_SANDBOX_API_PASSWORD_AP-NZ',
        ],
        'production' => [
            'EU' => 'KLARNA_PAYMENT_PRODUCTION_API_PASSWORD_EU',
            'NA-CA' => 'KLARNA_PAYMENT_PRODUCTION_API_PASSWORD_NA-CA',
            'NA-US' => 'KLARNA_PAYMENT_PRODUCTION_API_PASSWORD_NA-US',
            'NA-MX' => 'KLARNA_PAYMENT_PRODUCTION_API_PASSWORD_NA-MX',
            'AP-AP' => 'KLARNA_PAYMENT_PRODUCTION_API_PASSWORD_AP-AP',
            'AP-NZ' => 'KLARNA_PAYMENT_PRODUCTION_API_PASSWORD_AP-NZ',
        ],
    ];

    public const KLARNA_PAYMENT_CLIENT_IDENTIFIER = [
        'sandbox' => [
            'EU' => 'KLARNA_PAYMENT_SANDBOX_CLIENT_IDENTIFIER_EU',
            'NA-CA' => 'KLARNA_PAYMENT_SANDBOX_CLIENT_IDENTIFIER_NA-CA',
            'NA-US' => 'KLARNA_PAYMENT_SANDBOX_CLIENT_IDENTIFIER_NA-US',
            'NA-MX' => 'KLARNA_PAYMENT_SANDBOX_CLIENT_IDENTIFIER_NA-MX',
            'AP-AP' => 'KLARNA_PAYMENT_SANDBOX_CLIENT_IDENTIFIER_AP-AP',
            'AP-NZ' => 'KLARNA_PAYMENT_SANDBOX_CLIENT_IDENTIFIER_AP-NZ',
        ],
        'production' => [
            'EU' => 'KLARNA_PAYMENT_PRODUCTION_CLIENT_IDENTIFIER_EU',
            'NA-CA' => 'KLARNA_PAYMENT_PRODUCTION_CLIENT_IDENTIFIER_NA-CA',
            'NA-US' => 'KLARNA_PAYMENT_PRODUCTION_CLIENT_IDENTIFIER_NA-US',
            'NA-MX' => 'KLARNA_PAYMENT_PRODUCTION_CLIENT_IDENTIFIER_NA-MX',
            'AP-AP' => 'KLARNA_PAYMENT_PRODUCTION_CLIENT_IDENTIFIER_AP-AP',
            'AP-NZ' => 'KLARNA_PAYMENT_PRODUCTION_CLIENT_IDENTIFIER_AP-NZ',
        ],
    ];

    public const KLARNA_PAYMENT_IS_PRODUCTION_ENVIRONMENT = 'KLARNA_PAYMENT_IS_PRODUCTION_ENVIRONMENT';

    public const KLARNA_PAYMENT_DEBUG_MODE = [
        'sandbox' => 'KLARNA_PAYMENT_SANDBOX_DEBUG_MODE',
        'production' => 'KLARNA_PAYMENT_PRODUCTION_DEBUG_MODE',
    ];

    public const KLARNA_PAYMENT_EUROPE_API_URL = [
        'sandbox' => 'https://api.playground.klarna.com',
        'production' => 'https://api.klarna.com',
    ];

    public const KLARNA_PAYMENT_NORTH_AMERICA_US_API_URL = [
        'sandbox' => 'https://api-na.playground.klarna.com',
        'production' => 'https://api-na.klarna.com',
    ];

    public const KLARNA_PAYMENT_NORTH_AMERICA_CA_API_URL = [
        'sandbox' => 'https://api-na.playground.klarna.com',
        'production' => 'https://api-na.klarna.com',
    ];

    public const KLARNA_PAYMENT_NORTH_AMERICA_MX_API_URL = [
        'sandbox' => 'https://api-na.playground.klarna.com',
        'production' => 'https://api-na.klarna.com',
    ];

    public const KLARNA_PAYMENT_ASIA_PACIFIC_API_URL = [
        'sandbox' => 'https://api-oc.playground.klarna.com',
        'production' => 'https://api-oc.klarna.com',
    ];

    public const KLARNA_PAYMENT_ASIA_PACIFIC_NZ_API_URL = [
        'sandbox' => 'https://api-oc.playground.klarna.com',
        'production' => 'https://api-oc.klarna.com',
    ];

    public const KLARNA_PAYMENT_MERCHANT_URL = [
        'sandbox' => 'https://portal.playground.klarna.com/orders/all/merchants/',
        'production' => 'https://portal.klarna.com/orders/all/merchants/',
    ];

    public const KLARNA_PAYMENT_MERCHANT_PORTAL_URL = [
        'sandbox' => 'https://portal.playground.klarna.com/',
        'production' => 'https://portal.klarna.com/',
    ];

    public const KLARNA_PAYMENT_ONSITE_MESSAGING_SDK_URL = 'https://js.klarna.com/web-sdk/v1/klarna.js';
    public const KLARNA_PAYMENT_EXPRESS_CHECKOUT_SDK_URL = 'https://x.klarnacdn.net/kp/lib/v1/api.js';

    public const KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_SDK_URL = 'https://x.klarnacdn.net/sign-in-with-klarna/v1/lib.js';

    public const KLARNA_PAYMENT_LOGIN_URL = [
        'sandbox' => 'https://login.playground.klarna.com/',
        'production' => 'https://login.klarna.com/',
    ];

    public const PS_CURRENCY_DEFAULT = 'PS_CURRENCY_DEFAULT';

    public const PS_COUNTRY_DEFAULT = 'PS_COUNTRY_DEFAULT';

    public const KLARNA_PAYMENT_AUTO_CAPTURE_ORDER = [
        'sandbox' => 'KLARNA_PAYMENT_SANDBOX_AUTO_CAPTURE_ORDER',
        'production' => 'KLARNA_PAYMENT_PRODUCTION_AUTO_CAPTURE_ORDER',
    ];

    public const KLARNA_PAYMENT_AUTO_CAPTURE_ORDER_STATUSES = [
        'sandbox' => 'KLARNA_PAYMENT_SANDBOX_AUTO_CAPTURE_ORDER_STATUSES',
        'production' => 'KLARNA_PAYMENT_PRODUCTION_AUTO_CAPTURE_ORDER_STATUSES',
    ];

    public const KLARNA_PAYMENT_ONSITE_MESSAGING_ACTIVE = [
        'sandbox' => 'KLARNA_PAYMENT_SANDBOX_ONSITE_MESSAGING_ACTIVE',
        'production' => 'KLARNA_PAYMENT_PRODUCTION_ONSITE_MESSAGING_ACTIVE',
    ];

    public const KLARNA_PAYMENT_ALLOW_ORDER_STATUS_CHANGE = [
        'sandbox' => 'KLARNA_PAYMENT_ALLOW_ORDER_STATUS_CHANGE',
        'production' => 'KLARNA_PAYMENT_ALLOW_ORDER_STATUS_CHANGE',
    ];

    public const KLARNA_PAYMENT_DEFAULT_LOCALE = [
        'sandbox' => 'KLARNA_PAYMENT_SANDBOX_DEFAULT_LOCALE',
        'production' => 'KLARNA_PAYMENT_PRODUCTION_DEFAULT_LOCALE',
    ];

    public const KLARNA_PAYMENT_HPP_SERVICE = [
        'sandbox' => 'KLARNA_PAYMENT_HPP_SERVICE',
        'production' => 'KLARNA_PAYMENT_HPP_SERVICE',
    ];

    public const KLARNA_PAYMENT_ORDER_MIN = [
        'sandbox' => 'KLARNA_PAYMENT_SANDBOX_ORDER_MIN',
        'production' => 'KLARNA_PAYMENT_PRODUCTION_ORDER_MIN',
    ];

    public const KLARNA_PAYMENT_ORDER_MAX = [
        'sandbox' => 'KLARNA_PAYMENT_SANDBOX_ORDER_MAX',
        'production' => 'KLARNA_PAYMENT_PRODUCTION_ORDER_MAX',
    ];

    public const KLARNA_PAYMENT_ONSITE_MESSAGING_THEME = [
        'sandbox' => 'KLARNA_PAYMENT_SANDBOX_ONSITE_MESSAGING_THEME',
        'production' => 'KLARNA_PAYMENT_PRODUCTION_ONSITE_MESSAGING_THEME',
    ];

    public const KLARNA_PAYMENT_ONSITE_MESSAGING_FOOTER_DATA_KEY = [
        'sandbox' => 'KLARNA_PAYMENT_SANDBOX_ONSITE_MESSAGING_FOOTER_DATA_KEY',
        'production' => 'KLARNA_PAYMENT_PRODUCTION_ONSITE_MESSAGING_FOOTER_DATA_KEY',
    ];

    public const KLARNA_PAYMENT_ONSITE_MESSAGING_TOP_OF_PAGE_DATA_KEY = [
        'sandbox' => 'KLARNA_PAYMENT_SANDBOX_ONSITE_MESSAGING_TOP_OF_PAGE_DATA_KEY',
        'production' => 'KLARNA_PAYMENT_PRODUCTION_ONSITE_MESSAGING_TOP_OF_PAGE_DATA_KEY',
    ];

    public const KLARNA_PAYMENT_ONSITE_MESSAGING_PRODUCT_PAGE_DATA_KEY = [
        'sandbox' => 'KLARNA_PAYMENT_SANDBOX_ONSITE_MESSAGING_PRODUCT_PAGE_DATA_KEY',
        'production' => 'KLARNA_PAYMENT_PRODUCTION_ONSITE_MESSAGING_PRODUCT_PAGE_DATA_KEY',
    ];

    public const KLARNA_PAYMENT_ONSITE_MESSAGING_CART_PAGE_DATA_KEY = [
        'sandbox' => 'KLARNA_PAYMENT_SANDBOX_ONSITE_MESSAGING_CART_PAGE_DATA_KEY',
        'production' => 'KLARNA_PAYMENT_PRODUCTION_ONSITE_MESSAGING_CART_PAGE_DATA_KEY',
    ];

    public const KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_ACTIVE = [
        'sandbox' => 'KLARNA_PAYMENT_SANDBOX_SIGN_IN_WITH_KLARNA_ACTIVE',
        'production' => 'KLARNA_PAYMENT_PRODUCTION_SIGN_IN_WITH_KLARNA_ACTIVE',
    ];
    public const KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_PLACEMENTS = [
        'sandbox' => 'KLARNA_PAYMENT_SANDBOX_SIGN_IN_WITH_KLARNA_PLACEMENTS',
        'production' => 'KLARNA_PAYMENT_PRODUCTION_SIGN_IN_WITH_KLARNA_PLACEMENTS',
    ];

    public const KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_BUTTON_THEME = [
        'sandbox' => 'KLARNA_PAYMENT_SANDBOX_SIGN_IN_WITH_KLARNA_BUTTON_THEME',
        'production' => 'KLARNA_PAYMENT_PRODUCTION_SIGN_IN_WITH_KLARNA_BUTTON_THEME',
    ];

    public const KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_BUTTON_SHAPE = [
        'sandbox' => 'KLARNA_PAYMENT_SANDBOX_SIGN_IN_WITH_KLARNA_BUTTON_SHAPE',
        'production' => 'KLARNA_PAYMENT_PRODUCTION_SIGN_IN_WITH_KLARNA_BUTTON_SHAPE',
    ];

    /**
     * @var array{authentication: string, cart: string} KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_PLACEMENT_OPTIONS
     */
    public const KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_PLACEMENT_OPTIONS = [
        'authentication' => 'Authentication',
        'cart' => 'Cart',
    ];

    public const KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_THEME = [
        'sandbox' => 'KLARNA_PAYMENT_SANDBOX_SIGN_IN_WITH_KLARNA_THEME',
        'production' => 'KLARNA_PAYMENT_PRODUCTION_SIGN_IN_WITH_KLARNA_THEME',
    ];

    public const KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_BUTTON_ALIGNMENT = [
        'sandbox' => 'KLARNA_PAYMENT_SANDBOX_SIGN_IN_WITH_KLARNA_ALIGNMENT',
        'production' => 'KLARNA_PAYMENT_PRODUCTION_SIGN_IN_WITH_KLARNA_ALIGNMENT',
    ];

    public const KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_ON_SIGN_IN_SUCCESS_CALLBACK_FUNCTION = 'onSuccess';
    public const KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_ON_SIGN_IN_FAILURE_CALLBACK_FUNCTION = 'onFailure';

    /** @see https://docs.klarna.com/sign-in-with-klarna/integrate-sign-in-with-klarna/scopes-and-claims/ */
    public const KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_MANDATORY_SCOPE = 'openid offline_access payment:request:create profile:email profile:name profile:phone';
    public const KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_ADDITIONAL_SCOPE = 'date_of_birth country billing_address profile:locale';
    public const KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_ON_SIGN_IN_SCOPE = [
        'sandbox' => 'KLARNA_PAYMENT_SANDBOX_SIGN_IN_WITH_KLARNA_ON_SIGN_IN_SCOPE',
        'production' => 'KLARNA_PAYMENT_PRODUCTION_SIGN_IN_WITH_KLARNA_ON_SIGN_IN_SCOPE',
    ];

    public const KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_DEFAULT_THEME = 'default';
    public const KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_DEFAULT_SHAPE = 'default';
    public const KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_DEFAULT_ALIGNMENT = '';

    public const KLARNA_PAYMENT_EXPRESS_CHECKOUT_ACTIVE = [
        'sandbox' => 'KLARNA_PAYMENT_SANDBOX_EXPRESS_CHECKOUT_ACTIVE',
        'production' => 'KLARNA_PAYMENT_PRODUCTION_EXPRESS_CHECKOUT_ACTIVE',
    ];

    public const KLARNA_PAYMENT_EXPRESS_CHECKOUT_PLACEMENTS = [
        'sandbox' => 'KLARNA_PAYMENT_SANDBOX_EXPRESS_CHECKOUT_PLACEMENTS',
        'production' => 'KLARNA_PAYMENT_PRODUCTION_EXPRESS_CHECKOUT_PLACEMENTS',
    ];

    public const KLARNA_PAYMENT_EXPRESS_CHECKOUT_BUTTON_THEME = [
        'sandbox' => 'KLARNA_PAYMENT_SANDBOX_EXPRESS_CHECKOUT_BUTTON_THEME',
        'production' => 'KLARNA_PAYMENT_PRODUCTION_EXPRESS_CHECKOUT_BUTTON_THEME',
    ];

    public const KLARNA_PAYMENT_EXPRESS_CHECKOUT_BUTTON_SHAPE = [
        'sandbox' => 'KLARNA_PAYMENT_SANDBOX_EXPRESS_CHECKOUT_BUTTON_SHAPE',
        'production' => 'KLARNA_PAYMENT_PRODUCTION_EXPRESS_CHECKOUT_BUTTON_SHAPE',
    ];

    public const KLARNA_PAYMENT_ENABLE_BOX = [
        'sandbox' => 'KLARNA_PAYMENT_SANDBOX_ENABLE_BOX',
        'production' => 'KLARNA_PAYMENT_PRODUCTION_ENABLE_BOX',
    ];

    public const KLARNA_PAYMENT_SECRET_KEY = 'KLARNA_PAYMENT_SECRET_KEY';

    public const HOOK_LIST = [
        'paymentOptions',
        'actionFrontControllerSetMedia',
        'actionAdminControllerSetMedia',
        'displayAdminOrder',
        'displayAdminOrderSide',
        'actionOrderHistoryAddAfter',
        'displayHeader',
        'displayProductPriceBlock',
        'displayShoppingCart',
        'displayBanner',
        'displayFooter',
        'displayExpressCheckout',
        'displayCustomerLoginFormAfter',
        'displayCustomerAccountFormTop',
        'displayTop',
        'moduleRoutes',
    ];

    public const KLARNA_PAYMENT_ONSITE_MESSAGING_VALID_CURRENCIES = [
        'SEK',
        'DKK',
        'GBP',
        'NOK',
        'USD',
        'CHF',
        'AUD',
        'NZD',
        'CAD',
        'PLN',
        'EUR',
        'CZK',
        'MXN',
        'RON',
    ];

    public const KLARNA_PAYMENT_VALID_COUNTRIES = [
        'AU',
        'AT',
        'BE',
        'CA',
        'CZ',
        'DK',
        'FI',
        'FR',
        'DE',
        'GR',
        'IE',
        'IT',
        'MX',
        'NL',
        'NZ',
        'NO',
        'PL',
        'PT',
        'RO',
        'ES',
        'SE',
        'CH',
        'GB',
        'US',
    ];

    public const KLARNA_PAYMENT_ORDER_MANAGEMENT_COOKIE_ERRORS = 'KLARNA_PAYMENT_ORDER_MANAGEMENT_COOKIE_ERRORS';
    public const KLARNA_PAYMENT_ORDER_MANAGEMENT_COOKIE_CANCELED = 'KLARNA_PAYMENT_ORDER_MANAGEMENT_COOKIE_CANCELED';
    public const KLARNA_PAYMENT_ORDER_MANAGEMENT_COOKIE_CAPTURED = 'KLARNA_PAYMENT_ORDER_MANAGEMENT_COOKIE_CAPTURED';
    public const KLARNA_PAYMENT_ORDER_MANAGEMENT_COOKIE_REFUNDED = 'KLARNA_PAYMENT_ORDER_MANAGEMENT_COOKIE_REFUNDED';

    public const LOG_SEVERITY_LEVEL_INFORMATIVE = 1;
    public const LOG_SEVERITY_LEVEL_WARNING = 2;
    public const LOG_SEVERITY_LEVEL_ERROR = 3;
    public const LOG_SEVERITY_LEVEL_MAJOR = 4;

    public const DAYS_TO_KEEP_LOGS = 14;

    public const LOCK_TIME_TO_LIVE = 60;

    public const KLARNA_PAYMENT_SUPPORTED_OPC_MODULES = ['thecheckout', 'onepagecheckoutps', 'supercheckout'];

    public const DEFAULT_COMPUTING_PRECISION = 2;

    public const DECIMAL_PRECISION_ADJUSTMENT = 'DECIMAL_PRECISION_ADJUSTMENT';

    public const KLARNA_ADDITIONAL_INFO_LINKS = [
        'nl' => [
            'termsAndConditionsUrl' => 'https://www.klarna.com/nl/voorwaarden',
            'privacyStatementUrl' => 'https://cdn.klarna.com/1.0/shared/content/legal/terms/Klarna/nl_nl/privacy/',
            'cookieStatementUrl' => 'https://cdn.klarna.com/1.0/shared/content/legal/terms/nl-NL/cookie_purchase',
        ],
        'en' => [
            'termsAndConditionsUrl' => 'https://www.klarna.com/uk/terms-and-conditions/',
            'privacyStatementUrl' => 'https://cdn.klarna.com/1.0/shared/content/legal/terms/Klarna/en_gb/privacy/',
            'cookieStatementUrl' => 'https://cdn.klarna.com/1.0/shared/content/legal/terms/en-UK/cookie_purchase',
        ],
    ];

    public const SUPPORTED_REGIONS = [
        'EU' => 'Europe',
        'NA-US' => 'North America - US',
        'NA-CA' => 'North America - Canada',
        'NA-MX' => 'North America - Mexico',
        'AP-AP' => 'Oceania - Australia',
        'AP-NZ' => 'Oceania - New Zealand',
    ];

    public const DEFAULT_REGION_ISO = 'EU';

    public const REGION_CURRENCY_ISO_MAP = [
        'EU' => ['EUR'],
        'NA-CA' => ['CAD'],
        'NA-US' => ['USD'],
        'NA-MX' => ['MXN'],
        'AP-AP' => ['AUD'],
        'AP-NZ' => ['NZD'],
    ];

    public const DEFAULT_ENDPOINT = 'EU';

    public const ENDPOINT_CURRENCY_ISO_MAP = [
        Endpoint::EUROPE => ['EUR'],
        Endpoint::NORTH_AMERICA_CA => ['CAD'],
        Endpoint::NORTH_AMERICA_US => ['USD'],
        Endpoint::NORTH_AMERICA_MX => ['MXN'],
        Endpoint::ASIA_PACIFIC => ['AUD'],
        Endpoint::ASIA_PACIFIC_NZ => ['NZD'],
    ];

    public const KLARNA_PAYMENT_CONNECTED_REGIONS = [
        'production' => [
            'EU' => 'KLARNA_PRODUCTION_EUROPE_CONNECTED',
            'NA-CA' => 'KLARNA_PRODUCTION_NA-CA_CONNECTED',
            'NA-US' => 'KLARNA_PRODUCTION_NA-US_CONNECTED',
            'NA-MX' => 'KLARNA_PRODUCTION_NA-MX_CONNECTED',
            'AP-AP' => 'KLARNA_PRODUCTION_AP-AP_CONNECTED',
            'AP-NZ' => 'KLARNA_PRODUCTION_AP-NZ_CONNECTED',
        ],
        'sandbox' => [
            'EU' => 'KLARNA_SANDBOX_EUROPE_CONNECTED',
            'NA-CA' => 'KLARNA_SANDBOX_NA-CA_CONNECTED',
            'NA-US' => 'KLARNA_SANDBOX_NA-US_CONNECTED',
            'NA-MX' => 'KLARNA_SANDBOX_NA-MX_CONNECTED',
            'AP-AP' => 'KLARNA_SANDBOX_AP-AP_CONNECTED',
            'AP-NZ' => 'KLARNA_SANDBOX_AP-NZ_CONNECTED',
        ],
    ];

    public const KLARNA_ALL_REGIONS = [
        'EU',
        'NA-CA',
        'NA-US',
        'NA-MX',
        'AP-AP',
        'AP-NZ',
    ];
    public const KLARNA_INFO_BOX_URLS = [
        'ENDPOINT_LEARN_MORE_URL' => 'https://addons.prestashop.com/en/other-payment-methods/43440-klarna-payments-official.html#overview',
        'ENDPOINT_DOCS_URL' => 'https://docs.klarna.com/',
        'KP_DOCS_URL' => 'https://docs.klarna.com/klarna-payments/',
        'ONSITE_MESSAGING_LEARN_MORE_URL' => 'https://docs.klarna.com/on-site-messaging/additional-resources/placements/',
        'ONSITE_MESSAGING_DOCS_URL' => 'https://docs.klarna.com/on-site-messaging/',
        'EC_LEARN_URL' => 'https://docs.klarna.com/conversion-boosters/klarna-express-checkout/additional-resources/button-placement/',
        'EC_DOCS_URL' => 'https://docs.klarna.com/express-checkout/',
    ];

    public const KLARNA_PAYMENT_ENABLE_BOX_DEFAULT = '1';

    public const KLARNA_PAYMENT_ENDPOINT_URLS = [
        'sandbox' => [
            'EU' => 'https://api.playground.klarna.com',
            'NA-CA' => 'https://api-na.playground.klarna.com',
            'NA-US' => 'https://api-na.playground.klarna.com',
            'NA-MX' => 'https://api-na.playground.klarna.com',
            'AP-AP' => 'https://api-oc.playground.klarna.com',
            'AP-NZ' => 'https://api-oc.playground.klarna.com',
        ],
        'production' => [
            'EU' => 'https://api.klarna.com',
            'NA-CA' => 'https://api-na.klarna.com',
            'NA-US' => 'https://api-na.klarna.com',
            'NA-MX' => 'https://api-na.klarna.com',
            'AP-AP' => 'https://api-oc.klarna.com',
            'AP-NZ' => 'https://api-oc.klarna.com',
        ],
    ];

    public const NON_EURO_CURRENCY_COUNTRIES = [
        'BG' => 'BGN', // Bulgaria
        'CZ' => 'CZK', // Czech Republic
        'DK' => 'DKK', // Denmark
        'HR' => 'HRK', // Croatia
        'HU' => 'HUF', // Hungary
        'PL' => 'PLN', // Poland
        'RO' => 'RON', // Romania
        'SE' => 'SEK', // Sweden
    ];

    public const SIGN_IN_WITH_KLARNA_CALLBACK_URL_PATH = 'siwk/klarna/callback';
    public const ON_SITE_MESSAGING_INFOPAGE_CONFIG = 'KLARNA_PAYMENT_ONSITE_MESSAGING_INFOPAGE_PATH';
    public const KLARNA_WEB_SDK_URL = 'https://js.klarna.com/web-sdk/v1/klarna.js';

    public const KLARNA_ENVIRONMENT_PRODUCTION = 'production';
    public const KLARNA_ENVIRONMENT_SANDBOX = 'sandbox';

    public const KLARNA_FEATURE_AVAILABILITY_BASE_URL = [
        'sandbox' => 'https://api-global.test.klarna.com',
        'production' => 'https://api-global.klarna.com',
    ];

    public const KLARNA_CLOUD_SYNC_DISMISS = 'KLARNA_CLOUD_SYNC_DISMISS';
    public const PRESTASHOP_CLOUDSYNC_CDC = 'https://assets.prestashop3.com/ext/cloudsync-merchant-sync-consent/latest/cloudsync-cdc.js';
    public const KLARNA_ORDER_PROCESSING_COLOR = '#F4C658';
    public const PS_ACCOUNT_VERSION = '7.0.0';
    public const KLARNA_MERCHANT_PRIVACY_NOTICE_URL = 'https://portal.klarna.com/privacy-policy';

    public const KLARNA_FEATURE_PAYMENTS_PAYMENTS = 'platform-plugin-payments:payments';
    public const KLARNA_FEATURE_PAYMENTS_RECURRING = 'platform-plugin-payments:recurring';
    public const KLARNA_FEATURE_ON_SITE_MESSAGING_PRODUCT_PAGE = 'platform-plugin-on-site-messaging:product-page';
    public const KLARNA_FEATURE_ON_SITE_MESSAGING_CART_PAGE = 'platform-plugin-on-site-messaging:cart-page';
    public const KLARNA_FEATURE_ON_SITE_MESSAGING_PROMOTIONAL_BANNER = 'platform-plugin-on-site-messaging:promotional-banner';
    public const KLARNA_FEATURE_EXPRESS_CHECKOUT_1_STEP = 'platform-plugin-klarna-express-checkout:1-step';
    public const KLARNA_FEATURE_EXPRESS_CHECKOUT_2_STEP = 'platform-plugin-klarna-express-checkout:2-step';
    public const KLARNA_FEATURE_SIGN_IN_ACCOUNT_CREATION_PAGE = 'platform-plugin-sign-in-with-klarna:account-creation-page';
    public const KLARNA_FEATURE_SIGN_IN_AUTHENTICATION_PAGE = 'platform-plugin-sign-in-with-klarna:authentication-page';
    public const KLARNA_FEATURE_SIGN_IN_CART_PAGE = 'platform-plugin-sign-in-with-klarna:cart-page';
    public const KLARNA_FEATURE_SUPPLEMENTARY_PURCHASE_DATA = 'platform-plugin-supplementary-purchase-data';

    public const KLARNA_PAYMENTS_FEATURES = [
        self::KLARNA_FEATURE_PAYMENTS_PAYMENTS => 'KLARNA_FEATURE_PAYMENTS_PAYMENTS',
        self::KLARNA_FEATURE_PAYMENTS_RECURRING => 'KLARNA_FEATURE_PAYMENTS_RECURRING',
        self::KLARNA_FEATURE_ON_SITE_MESSAGING_PRODUCT_PAGE => 'KLARNA_FEATURE_ON_SITE_MESSAGING_PRODUCT_PAGE',
        self::KLARNA_FEATURE_ON_SITE_MESSAGING_CART_PAGE => 'KLARNA_FEATURE_ON_SITE_MESSAGING_CART_PAGE',
        self::KLARNA_FEATURE_ON_SITE_MESSAGING_PROMOTIONAL_BANNER => 'KLARNA_FEATURE_ON_SITE_MESSAGING_PROMOTIONAL_BANNER',
        self::KLARNA_FEATURE_EXPRESS_CHECKOUT_1_STEP => 'KLARNA_FEATURE_EXPRESS_CHECKOUT_1_STEP',
        self::KLARNA_FEATURE_EXPRESS_CHECKOUT_2_STEP => 'KLARNA_FEATURE_EXPRESS_CHECKOUT_2_STEP',
        self::KLARNA_FEATURE_SIGN_IN_ACCOUNT_CREATION_PAGE => 'KLARNA_FEATURE_SIGN_IN_ACCOUNT_CREATION_PAGE',
        self::KLARNA_FEATURE_SIGN_IN_AUTHENTICATION_PAGE => 'KLARNA_FEATURE_SIGN_IN_AUTHENTICATION_PAGE',
        self::KLARNA_FEATURE_SIGN_IN_CART_PAGE => 'KLARNA_FEATURE_SIGN_IN_CART_PAGE',
        self::KLARNA_FEATURE_SUPPLEMENTARY_PURCHASE_DATA => 'KLARNA_FEATURE_SUPPLEMENTARY_PURCHASE_DATA',
    ];

    public const ON_SITE_MESSAGING_INFOPAGE_DEFAULT_PATH = 'osm/infopage';
    public const ON_SITE_MESSAGING_INFOPAGE_ACTIVE_CONFIG = [
        'sandbox' => 'KLARNA_PAYMENT_SANDBOX_ONSITE_MESSAGING_INFOPAGE_ACTIVE',
        'production' => 'KLARNA_PAYMENT_PRODUCTION_ONSITE_MESSAGING_INFOPAGE_ACTIVE',
    ];
}
