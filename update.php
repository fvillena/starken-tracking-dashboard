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
include dirname(__FILE__).'/config.php';
openlog("starken-tracking-dashboard", LOG_PID | LOG_PERROR, LOG_LOCAL0);
require_once(dirname(__FILE__).'/PSWebServiceLibrary.php');


function update_tracking_number($id,$tracking_number)
{
    $webService = new PrestaShopWebservice(PS_SHOP_PATH, PS_WS_AUTH_KEY, DEBUG);
    $xml = $webService->get(array('resource' => 'orders', 'id' => $id));
    $xml->order->shipping_number = $tracking_number;
    $xml->order->current_state = 4;
    $opt['putXml'] = $xml->asXML();
    $opt['id'] = $id;
    $opt['resource'] = 'orders';
    $xml = $webService->edit($opt);
}

function mark_as_delivered($id)
{
    $webService = new PrestaShopWebservice(PS_SHOP_PATH, PS_WS_AUTH_KEY, DEBUG);
    $xml = $webService->get(array('resource' => 'orders', 'id' => $id));
    $xml->order->current_state = 5;
    $opt['putXml'] = $xml->asXML();
    $opt['id'] = $id;
    $opt['resource'] = 'orders';
    $xml = $webService->edit($opt);
}

$data = json_decode(file_get_contents(dirname(__FILE__)."/data.json"),true);

foreach ($data["binded_pairs"] as $key => $pair) {
    update_tracking_number($pair["id"],$pair["shipping_number"]);
    syslog(LOG_INFO, "updating order ".$pair["id"]." with shipping number ".$pair["shipping_number"]);
}

foreach ($data["data"] as $key => $order) {
    if (($order["current_state"] == "4")&&($order["id"] != "")&&($order["estado"] == "ENTREGADO")) {
        mark_as_delivered($order["id"]);
        syslog(LOG_INFO, "order ".$order["id"]." marked as delivered");
    };
}

?>