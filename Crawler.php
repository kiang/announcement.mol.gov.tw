<?php

require 'vendor/autoload.php';

$zones = [
    '63' => '台北市',
    '65' => '新北市',
    '68' => '桃園市',
    '66' => '台中市',
    '67' => '台南市',
    '64' => '高雄市',
    '02' => '宜蘭縣',
    '04' => '新竹縣',
    '05' => '苗栗縣',
    '07' => '彰化縣',
    '08' => '南投縣',
    '09' => '雲林縣',
    '10' => '嘉義縣',
    '13' => '屏東縣',
    '14' => '台東縣',
    '15' => '花蓮縣',
    '16' => '澎湖縣',
    '17' => '基隆市',
    '25' => '新竹市',
    '26' => '嘉義市',
    '23' => '金門縣',
    '24' => '連江縣',
    '96' => '加工出口區管理處',
    '97' => '科技部新竹科學園區',
    '92' => '科技部中部科學工業園區',
    '95' => '科技部南部科學工業園區',
    'CA' => '職業安全衛生署',
    'BL' => '勞動部勞工保險局',
    'BA' => '勞動部勞動基金運用局',
];

use Goutte\Client;

$client = new Client();

$crawler = $client->request('GET', 'https://announcement.mol.gov.tw/');
$form = $crawler->selectButton('下載')->form();

$client->setHeader('User-Agent', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:59.0) Gecko/20100101 Firefox/59.0');
$client->setHeader('Host', 'announcement.mol.gov.tw');
$client->setHeader('Referer', 'https://announcement.mol.gov.tw/');

$form->getNode()->setAttribute('action', 'https://announcement.mol.gov.tw/Download/');

$tmpPath = __DIR__ . '/tmp';
if (!file_exists($tmpPath)) {
    mkdir($tmpPath, 0777);
}

$za = new ZipArchive();

foreach ($zones as $zoneId => $zone) {
    $dataPath = __DIR__ . '/data/' . $zone;
    if (!file_exists($dataPath)) {
        mkdir($dataPath, 0777, true);
    }
    $tmpFile = $tmpPath . '/' . $zone . '.zip';
    $crawler = $client->submit(
        $form,
        array(
            'CITYNO' => $zoneId,
            'DOCstartDate' => '0900101',
            'DOCEndDate' => (date('Y') - 1911) . date('md'),
            'downloadType' => '3'
        )
    );

    file_put_contents($tmpFile, $client->getResponse()->getContent());

    if ($za->open($tmpFile)) {
        for ($i = 0; $i < $za->numFiles; $i++) {
            $stat = $za->statIndex($i);
            $filename = $za->getNameIndex($i);
            $parts = explode('-', $filename);
            copy('zip://' . $tmpFile . '#' . $filename, $dataPath . '/' . $parts[1] . '.csv');
        }
    }
}
