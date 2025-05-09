<?php

$companyPath = dirname(__DIR__) . '/docs/company';
if (!is_dir($companyPath)) {
    mkdir($companyPath, 0777, true);
}

$tr = [
    '\u0000' => '',
    '(' => '',
    ')' => '',
    '（' => '',
    '）' => '',
    '/' => '',
    '、' => '',
    chr(0) => '',
    '?' => '',
    '_' => '',
    '-' => '',
    '：' => '',
    '�' => '',
    '' => '',
];
$pool = [];
foreach (glob('data/*/*/*.json') as $file) {
    $data = json_decode(file_get_contents($file), true);
    $codePointPos = strpos($data['事業單位名稱或負責人'], '&#');
    while(false !== $codePointPos) {
        $codePointEnd = strpos($data['事業單位名稱或負責人'], ';', $codePointPos);
        if (false === $codePointEnd) {
            break;
        }
        $codePoint = substr($data['事業單位名稱或負責人'], $codePointPos, $codePointEnd - $codePointPos + 1);
        $codePointResult = html_entity_decode($codePoint);
        if($codePointResult === $codePoint) {
            $codePointResult = '';
        }
        $data['事業單位名稱或負責人'] = str_replace($codePoint, $codePointResult, $data['事業單位名稱或負責人']);
        $codePointPos = strpos($data['事業單位名稱或負責人'], '&#');
    }
    $data['事業單位名稱或負責人'] = strtr($data['事業單位名稱或負責人'], $tr);
    $pos = strpos($data['事業單位名稱或負責人'], '有限公司');
    $company = '';
    if ($pos !== false) {
        $company = substr($data['事業單位名稱或負責人'], 0, $pos + 12);
    } else {
        $parts = explode('即', $data['事業單位名稱或負責人']);
        if (count($parts) === 2) {
            $company = $parts[1];
        }
    }
    if (empty($company)) {
        continue;
    }
    // Fetch and save API result if not already present
    $apiDir = dirname(__DIR__) . '/docs/gcis.nat.g0v.tw';
    if (!is_dir($apiDir)) {
        mkdir($apiDir, 0777, true);
    }
    $apiFile = $apiDir . '/' . $company . '.json';
    if (!file_exists($apiFile)) {
        $encodedName = urlencode($company);
        $apiUrl = "http://gcis.nat.g0v.tw/api/search/?q={$encodedName}";
        $apiResult = @file_get_contents($apiUrl);
        if ($apiResult !== false) {
            file_put_contents($apiFile, $apiResult);
        }
    }
    $pool[$company] = true;
    $targetFile = $companyPath . '/' . $company . '.csv';
    if (!file_exists($targetFile)) {
        $fh = fopen($targetFile, 'w');
        fputcsv($fh, array_keys($data));
        fclose($fh);
    }
    $fh = fopen($targetFile, 'a');
    fputcsv($fh, $data);
    fclose($fh);
}

file_put_contents(dirname(__DIR__) . '/docs/company.csv', implode("\n", array_keys($pool)));