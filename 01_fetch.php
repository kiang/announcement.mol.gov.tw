<?php

$context = stream_context_create(["ssl" => array(
    "verify_peer" => false,
    "verify_peer_name" => false,
)]);
$basePath = __DIR__;

//勞工退休金條例
$fh = fopen('https://apiservice.mol.gov.tw/OdService/download/A17000000J-030227-POn', 'r', false, $context);
$head = fgetcsv($fh, 2048);
$oFh = [];
while ($line = fgetcsv($fh, 2048)) {
    if (!isset($oFh[$line[0]])) {
        $unitPath = $basePath . '/data/' . $line[0];
        if (!file_exists($unitPath)) {
            mkdir($unitPath, 0777, true);
        }
        $oFh[$line[0]] = fopen($unitPath . '/勞工退休金條例.csv', 'w');
        fputcsv($oFh[$line[0]], $head);
    }
    fputcsv($oFh[$line[0]], $line);
}

//性別工作平等法
$fh = fopen('https://apiservice.mol.gov.tw/OdService/download/A17000000J-030226-sop', 'r', false, $context);
$head = fgetcsv($fh, 2048);
$oFh = [];
while ($line = fgetcsv($fh, 2048)) {
    if (!isset($oFh[$line[0]])) {
        $unitPath = $basePath . '/data/' . $line[0];
        if (!file_exists($unitPath)) {
            mkdir($unitPath, 0777, true);
        }
        $oFh[$line[0]] = fopen($unitPath . '/性別工作平等法.csv', 'w');
        fputcsv($oFh[$line[0]], $head);
    }
    fputcsv($oFh[$line[0]], $line);
}

//勞動基準法
$fh = fopen('https://apiservice.mol.gov.tw/OdService/download/A17000000J-030225-svj', 'r', false, $context);
$head = fgetcsv($fh, 2048);
$oFh = [];
while ($line = fgetcsv($fh, 2048)) {
    if (!isset($oFh[$line[0]])) {
        $unitPath = $basePath . '/data/' . $line[0];
        if (!file_exists($unitPath)) {
            mkdir($unitPath, 0777, true);
        }
        $oFh[$line[0]] = fopen($unitPath . '/勞動基準法.csv', 'w');
        fputcsv($oFh[$line[0]], $head);
    }
    fputcsv($oFh[$line[0]], $line);
}

//就業服務法
$fh = fopen('https://apiservice.mol.gov.tw/OdService/download/A17000000J-030228-p2G', 'r', false, $context);
$head = fgetcsv($fh, 2048);
$oFh = [];
while ($line = fgetcsv($fh, 2048)) {
    if (!isset($oFh[$line[0]])) {
        $unitPath = $basePath . '/data/' . $line[0];
        if (!file_exists($unitPath)) {
            mkdir($unitPath, 0777, true);
        }
        $oFh[$line[0]] = fopen($unitPath . '/就業服務法.csv', 'w');
        fputcsv($oFh[$line[0]], $head);
    }
    fputcsv($oFh[$line[0]], $line);
}
