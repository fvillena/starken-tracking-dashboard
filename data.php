<?php
header('Content-Type: application/json');


$shipments = json_decode(file_get_contents("shipments.json"),true);
$orders = json_decode(file_get_contents("orders.json"),true);

$data = array();
$shipping_numbers = array();
foreach ($orders as $order) {
  $shipping_numbers[$order["shipping_number"]] = $order["id"];
}

$unbinded_shipments = array();

foreach ($shipments as $shipment) {
  if (array_key_exists($shipment['orden'],$shipping_numbers)) {
    $id = $shipping_numbers[$shipment['orden']];
  } else {
    $id = "";
    if ($shipment["estado"] != "ANULADO") {
      $unbinded_shipments[] = $shipment;
    }
    
  }
  $data[] = array(
    "orden" => $shipment['orden'],
    "emision" => $shipment['emision'],
    "destinatario" => $shipment['destinatario'],
    "estado" => $shipment['estado'],
    "id" => $id,
    "destino" => $shipment["destino"]
  );
};

$binded_pairs = array();
foreach ($unbinded_shipments as $key => $unbinded_shipment) {
  $candidates = array();
  foreach ($orders as $key => $order) {
    if ($order["shipping_number"] == "") {
      $candidates[] = $order;
    }
  }
  $shipping_number = $unbinded_shipment["orden"];
  $shipping_created = $unbinded_shipment["emision"];
  $shipping_created = strtotime($shipping_created);
  $shipping_state = $unbinded_shipment["destino"];
  $shipping_state = strtolower($shipping_state);
  $shipping_fullname = $unbinded_shipment["destinatario"];
  $shipping_firstname = strtolower(explode(" ", $shipping_fullname)[0]);
  $estado = $unbinded_shipment["estado"];
  if ($estado != "ANULADO") {
    $current_candidates = array();
    foreach ($candidates as $key => $candidate) {
      $candidate_created = strtotime("-1 day", strtotime($candidate["date_add"]));
      if (($candidate_created <= $shipping_created)&&($candidate_created >= (strtotime("-7 day", $shipping_created)))) {
        $current_candidates[] = $candidate;
      }
    }
    $candidates = $current_candidates;
    if (count($candidates) > 1) {
      $current_candidates = array();
      foreach ($candidates as $key => $candidate) {
        $candidate_state = strtolower($candidate["address"]["state"]);
        $candidate_state = str_replace('á', 'a', $candidate_state);
        $candidate_state = str_replace('é', 'e', $candidate_state);
        $candidate_state = str_replace('í', 'i', $candidate_state);
        $candidate_state = str_replace('ó', 'o', $candidate_state);
        $candidate_state = str_replace('ú', 'u', $candidate_state);
        $candidate_state = str_replace('Ñ', 'n', $candidate_state);
        $candidate_state = str_replace('ñ', 'n', $candidate_state);
        if ($candidate_state == $shipping_state) {
          $current_candidates[] = $candidate;
        }
      }
    $candidates = $current_candidates;
    if (count($candidates) > 1) {
      // echo "ambiguous";
    } elseif (count($candidates) == 0) {
      // echo "not found";
      // var_dump($unbinded_shipment);
    } else {
      $binded_pairs[] = array("id" => $candidates[0]["id"],"shipping_number"=>$unbinded_shipment["orden"]);
    }
    } elseif (count($candidates) == 0) {
      // echo "not found";
    } else {
      $binded_pairs[] = array("id" => $candidates[0]["id"],"shipping_number"=>$unbinded_shipment["orden"]);
    }
  }
}

$fp = fopen('data.json', 'w');
fwrite($fp, json_encode(array(
  "data"=>$data,
  "unbinded_shipments" => $unbinded_shipments,
  "binded_pairs" => $binded_pairs,
), JSON_PRETTY_PRINT));
fclose($fp);

echo json_encode(array(
  "data"=>$data,
  "unbinded_shipments" => $unbinded_shipments,
  "binded_pairs" => $binded_pairs,
), JSON_PRETTY_PRINT);

?>