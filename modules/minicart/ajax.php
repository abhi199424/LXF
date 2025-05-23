<?php
require_once '../../config/config.inc.php';
require_once '../../init.php';

$context = Context::getContext();

$carTview = array();

$id_product = (int)Tools::getValue('id_product');
$id_product_attribute = (int)Tools::getValue('id_product_attribute');
$operation = Tools::getValue('op');
$quantity = 1;
$quantity_in = Tools::getValue('qty');


$cart = Context::getContext()->cart;

$current_qty = $cart->getProductQuantity($id_product, $id_product_attribute);
$diff = $quantity_in - $current_qty['quantity'];

if (!$cart->id) {
    $cart->add();
    Context::getContext()->cookie->id_cart = (int)$cart->id;
}

if ($operation === 'down') {
    $cart->updateQty($quantity, $id_product, $id_product_attribute, false, 'down');
} elseif ($operation === 'up') {
    $cart->updateQty($quantity, $id_product, $id_product_attribute, false, 'up');
}
elseif ($operation === 'input') {
    if ($diff > 0) {
        $cart->updateQty($diff, $id_product, $id_product_attribute, false, 'up');
    } elseif ($diff < 0) {
        $cart->updateQty(abs($diff), $id_product, $id_product_attribute, false, 'down');
    }
}
elseif ($operation === 'delete') {
    $cart->updateQty($current_qty['quantity'], $id_product, $id_product_attribute, false, 'down');
}


$moduleCartview = Module::getInstanceByName('minicart');
$carTview['cart'] = $moduleCartview->hookDisplayMinicartInner();
$carTview['cart_count'] = $moduleCartview->hookDisplayMinicartcount();

echo json_encode($carTview);