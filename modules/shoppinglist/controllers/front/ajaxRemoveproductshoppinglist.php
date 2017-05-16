<?php

/**
* Ajax File called in product page Remove From ShoppingList
* 
* @author Marc Collin
* @copyright  MarcCollin
* Thanks to Olivier Michaud
* @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

require_once(dirname(__FILE__).'../../../../../config/config.inc.php');
require_once(dirname(__FILE__).'../../../../../init.php');

/**
 * This page permit to add a product to a shopping list
 */
$logger = new FileLogger(0); //0 == debug level, logDebug() won’t work without this.
$logger->setFilename(_PS_ROOT_DIR_."/log/debug.log");
$logger->logDebug(Tools::getAllValues());

if (strcmp(Tools::getToken(false), Tools::getValue('static_token')))
	die(Tools::jsonEncode(array('result'=>$module->l('Invalid Token', 'ajaxproductshoppinglist'))));


$idShoppingList = Tools::getValue('id_shopping_list');
$idProduct = Tools::getValue('id_product');
$idProductAttribute = Tools::getValue('id_product_attribute');
$title = Tools::getValue('title');

$context = Context::getContext();
$module = new ShoppingList();
$customer = $context->cookie->id_customer;
$shoppingListObj = ShoppingListObject::loadByIdAndCustomer($idShoppingList, $customer);

if($shoppingListObj == null) {
    die(Tools::jsonEncode(array('result'=>$module->l('An error occur', 'ajaxproductshoppinglist'))));
}

$result = $shoppingListObj->deleteProduct($idProduct, $idProductAttribute);
if($result) {
    die(Tools::jsonEncode(array('result'=>'Produit déréférencé')));
}
else {
    die(Tools::jsonEncode(array('result'=>$module->l('Produit non référencé ?', 'ajaxproductshoppinglist'))));
}

function phpAlert($msg) {
    echo '<script type="text/javascript">alert("' . $msg . '")</script>';
}