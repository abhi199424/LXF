<?php
/**
 * NOTICE OF LICENSE
 *
 * This file is licenced under the Software License Agreement.
 * With the purchase or the installation of the software in your application
 * you accept the licence agreement.
 *
 * You must not modify, adapt or create derivative works of this source code
 *
 * @author    Doofinder
 * @copyright Doofinder
 * @license   GPLv3
 */

use PrestaShop\Module\Doofinder\Src\Entity\DfCmsBuild;
use PrestaShop\Module\Doofinder\Src\Entity\DfTools;

if (!defined('_PS_VERSION_')) {
    exit;
}

if (function_exists('set_time_limit')) {
    @set_time_limit(3600 * 2);
}

// To prevent printing errors or warnings that may corrupt the feed.
$debug = DfTools::getBooleanFromRequest('debug', false);
if ($debug) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// OUTPUT
if (isset($_SERVER['HTTPS'])) {
    header('Strict-Transport-Security: max-age=500');
}

header('Content-Type:text/plain; charset=utf-8');

DfTools::validateSecurityToken(Tools::getValue('dfsec_hash'));

// CONTEXT
$context = Context::getContext();
$shop = new Shop((int) $context->shop->id);
if (!$shop->id) {
    exit('NOT PROPERLY CONFIGURED');
}
$lang = DfTools::getLanguageFromRequest();
$context->language = $lang;

// CMS DATA
$cms_pages = DfTools::getCmsPages($lang->id, $shop->id);
$builder = new DfCmsBuild($shop->id, $lang->id);
$builder->setCmsPages($cms_pages);
$rows = $builder->build(false);

// HEADERS
$header = ['id', 'title', 'description', 'meta_title', 'meta_description', 'tags', 'content', 'link'];
echo implode(DfTools::TXT_SEPARATOR, $header) . PHP_EOL;

// CMS Pages
foreach ($rows as $row) {
    echo $row['id'] . DfTools::TXT_SEPARATOR;
    echo $row['title'] . DfTools::TXT_SEPARATOR;
    echo $row['description'] . DfTools::TXT_SEPARATOR;
    echo $row['meta_title'] . DfTools::TXT_SEPARATOR;
    echo $row['meta_description'] . DfTools::TXT_SEPARATOR;
    // Tags does not seem to exist on PrestaShop 9
    echo !empty($row['tags']) ? $row['tags'] : '' . DfTools::TXT_SEPARATOR;
    echo $row['content'] . DfTools::TXT_SEPARATOR;
    echo $row['link'];
    echo PHP_EOL;
}
