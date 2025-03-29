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
    if(false !== strpos($company, chr(0))) {
        die('error: ' . $company);
    }
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
