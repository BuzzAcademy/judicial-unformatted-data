<?php
if (count($argv) < 3) {
    echo "Usage: ". basename(__FILE__) ." user password\n\n";
    return;
}
$user = $argv[1];
$password = $argv[2];

// login
$data = [
    "user" => $user,
    "password" => $password,
];
$json = pj("https://data.judicial.gov.tw/jdg/api/Auth", $data);
$token = $json['Token'];

// get list
$data = [
    'token' => $token,
];
$list = p("https://data.judicial.gov.tw/jdg/api/JList", $data);
file_put_contents(__DIR__. '/../jlist/'. date('Ymd'), $list);

// get doc
$json = json_decode($list, true);
foreach ($json as $obj) {
    echo '>> ', $obj['date'], ': ', count($obj['list']), "\n";
    foreach ($obj['list'] as $jid) {
        $jid_arr = explode(',', $jid); //CCEV,110,潮補,644,20210917,1
        $data = [
            'token' => $token,
            'j' => $jid,
        ];
        $doc = p("https://data.judicial.gov.tw/jdg/api/JDoc", $data);
        $dir = __DIR__. '/../jdoc/'. $jid_arr[0] .'/'. $jid_arr[4];
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        file_put_contents($dir .'/'. str_replace(',', '-', $jid), $doc);
        echo '.';
    }
    echo "\n";
}
echo "\n";

function p($url, $data) {
    $ch = curl_init($url);
    $payload = json_encode($data);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}
function pj($url, $data) {
    $data = p($url, $data);
    return json_decode($data, true);
}
