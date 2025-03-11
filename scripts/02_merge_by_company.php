<?php

$companyPath = dirname(__DIR__) . '/docs/company';
if (!is_dir($companyPath)) {
    mkdir($companyPath, 0777, true);
}

foreach (glob('data/*/*/*.json') as $file) {
    $data = json_decode(file_get_contents($file), true);
    $pos = strpos($data['事業單位名稱或負責人'], '有限公司');
    if ($pos !== false) {
        $company = substr($data['事業單位名稱或負責人'], 0, $pos + 12);
        $company = preg_replace('/[\\(\\)\\/]+/', '', $company);
        $company = preg_replace('/&#(\d+);/', '', $company);
        $targetFile = $companyPath . '/' . $company . '.csv';
        if (!file_exists($targetFile)) {
            $fh = fopen($targetFile, 'w');
            fputcsv($fh, array_keys($data));
            fclose($fh);
        }
        $fh = fopen($targetFile, 'a');
        fputcsv($fh, $data);
        fclose($fh);
    } else {
        $parts = explode('即', $data['事業單位名稱或負責人']);
        if (count($parts) === 2) {
            $company = $parts[1];
            $company = preg_replace('/[\\(\\)\\/]+/', '', $company);
            $company = preg_replace('/&#(\d+);/', '', $company);
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
    }
}