<?php
require_once '../../config/config.inc.php';
require_once '../../init.php';

$context = Context::getContext();

$carTview = array();

$moduleCartview = Module::getInstanceByName('minicart');
$carTview['cart'] = $moduleCartview->hookDisplayMinicart();
$carTview['cart_count'] = $moduleCartview->hookDisplayMinicartcount();

echo Tools::jsonEncode($carTview);