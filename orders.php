<?php
/*
* 2007-2013 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
* PrestaShop Webservice Library
* @package PrestaShopWebservice
*/

// Here we define constants /!\ You need to replace this parameters
header('Content-Type: application/json;charset=utf-8');
include 'config.php';

require_once('PSWebServiceLibrary.php');

$webService = new PrestaShopWebservice(PS_SHOP_PATH, PS_WS_AUTH_KEY, DEBUG);


$opt0['resource'] = 'states';
$opt0['display'] = '[id,name]';

// Call
$xml = $webService->get($opt0);
$states = json_decode(json_encode($xml,JSON_UNESCAPED_UNICODE),true,JSON_UNESCAPED_UNICODE)['states']['state'];
$states_dict = array();

foreach ($states as $key => $state) {
	$states_dict[$state["id"]] = $state["name"];
}

// Here we set the option array for the Webservice : we want customers resources
$opt['resource'] = 'orders';
$opt['display'] = '[id,id_customer,shipping_number,id_address_delivery,reference,date_add,current_state]';
$opt['limit'] = 50;
$opt['sort'] = '[id_DESC]';

// Call
$xml = $webService->get($opt);

$orders = json_decode(json_encode($xml,JSON_UNESCAPED_UNICODE),true,JSON_UNESCAPED_UNICODE)['orders']['order'];

foreach ($orders as $key => &$order) {
	$id_address_delivery = $order["id_address_delivery"];
	$opt3['resource'] = 'addresses';
	$opt3['display'] = '[firstname,lastname,address1,address2,id_state]';
	$opt3['limit'] = 1;
	$opt3['filter[id]'] = (int)$id_address_delivery;
	$xml = $webService->get($opt3);
	$address = json_decode(json_encode($xml,JSON_UNESCAPED_UNICODE),true,JSON_UNESCAPED_UNICODE)['addresses']['address'];
	$address["state"] = $states_dict[$address["id_state"]];
	$order['address'] = $address;
	if (is_string($order['shipping_number']) == false) {
		$order['shipping_number'] = "";
	}
	}
unset($order);
$orders = json_encode($orders, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

$fp = fopen('orders.json', 'w');
fwrite($fp, $orders);
fclose($fp);

echo $orders;
?>