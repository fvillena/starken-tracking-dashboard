<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'config.php';
header('Content-Type: application/json');
function csvToJson($csv) {
    $rows = explode("\n", trim($csv));
    $data = array_slice($rows, 1);
    $keys = array_fill(0, count($data), $rows[0]);
    $json = array_map(function ($row, $key) {
        $row = str_getcsv($row,$delimiter=";");
        $key = str_getcsv($key,$delimiter=";");
        if (count($row) == count($key)) {
            return array_combine($key,$row);
        } else {
            $key_count = count($key);
            return array_combine($key,array_slice($row,0,$key_count));
        }
    }, $data, $keys);
    return json_encode(array("data" => $json));
}

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://mypartner.mystarken.cl/webCtaCte/scripts/control.php",
  CURLOPT_RETURNTRANSFER => 1,
  CURLOPT_VERBOSE => 1,
  CURLOPT_HEADER => 1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => "user=".$user."&pass=".$password,
  CURLOPT_HTTPHEADER => array(
    "X-Requested-With: XMLHttpRequest",
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
}

preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $response, $matches);
$cookies = array();
foreach($matches[1] as $item) {
    parse_str($item, $cookie);
    $cookies = array_merge($cookies, $cookie);
}

$PHPSESSID =  $cookies["PHPSESSID"];

$date_start = date("Y-m-d", strtotime("-1 months"));
$date_end = date("Y-m-d");

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://mypartner.mystarken.cl/webCtaCte/panel/informes/get_informe.php",
  CURLOPT_RETURNTRANSFER => 1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => "fecha_emi_ini=".$date_start."&fecha_emi_fin=".$date_end."&fecha_compro_ini=-1&fecha_compro_fin=-1&fecha_ent_ini=-1&fecha_ent_fin=-1&swDctos=0&t_ent=-1&t_pago=-1&t_servicio=-1&bultos=0,300&kgs=0,500&origins=-1&destinations=-1&estados=-1",
  CURLOPT_HTTPHEADER => array(
    "Cookie: PHPSESSID=".$PHPSESSID,
    "X-Requested-With: XMLHttpRequest",
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {
}

$data_url = json_decode($response, true)["url"];

$csv = file_get_contents($data_url);
$csv = mb_convert_encoding($csv,"ISO-8859-1","UTF-8");
echo csvToJson($csv);
?>