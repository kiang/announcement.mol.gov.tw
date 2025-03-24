<?php
/**
 * api reference
 * https://apiservice.mol.gov.tw/OdService/openapi/OAS.html
 * 
 * metadata
 * 勞動基準法 A17000000J-030225-svj
 * https://apiservice.mol.gov.tw/OdService/rest/dataset/109896
 * 就業服務法 A17000000J-030228-p2G
 * https://apiservice.mol.gov.tw/OdService/rest/dataset/110908
 * 性別工作平等法 A17000000J-030226-sop
 * https://apiservice.mol.gov.tw/OdService/rest/dataset/109897
 * 中高齡者及高齡者就業促進法 A17000000J-030472-iw5
 * https://apiservice.mol.gov.tw/OdService/rest/dataset/156904
 * 工會法 A17000000J-030542-tEK
 * https://apiservice.mol.gov.tw/OdService/rest/dataset/166670
 * 
 * 違反勞動法令事業單位
 */

$context = stream_context_create(["ssl" => array(
    "verify_peer" => false,
    "verify_peer_name" => false,
)]);
$basePath = dirname(__DIR__);

$resources = [
    'A17000000J-030225-svj' => '勞動基準法',
    'A17000000J-030228-p2G' => '就業服務法',
    'A17000000J-030226-sop' => '性別工作平等法',
    'A17000000J-030472-iw5' => '中高齡者及高齡者就業促進法',
    'A17000000J-030466-h0a' => '職業安全衛生法',
    'A17000000J-030542-tEK' => '工會法',
];

$baseHead = ['主管機關', '公告日期', '處分日期', '處分字號', '事業單位名稱或負責人', '違法法規法條', '違反法規內容', '罰鍰金額', '備註說明'];

foreach($resources AS $resourceId => $resourceName) {
    $fh = fopen('https://apiservice.mol.gov.tw/OdService/download/' . $resourceId, 'r', false, $context);
    $head = fgetcsv($fh, 2048);
    while ($line = fgetcsv($fh, 8000)) {
        $data = array_combine($head, $line);
        if (!isset($data['罰鍰金額'])) {
            $data['罰鍰金額'] = '';
        }
        if (isset($data['處分金額或滯納金'])) {
            $data['罰鍰金額'] = $data['處分金額或滯納金'];
        }
        $finalLine = [];
        foreach ($baseHead as $key) {
            $finalLine[$key] = html_entity_decode($data[$key]);
            $finalLine[$key] = preg_replace('/\s+/u', '', $finalLine[$key]);
            $finalLine[$key] = str_replace('/', '_', $finalLine[$key]);
        }
        $year = substr($finalLine['處分日期'], 0, 4);
        $dataPath = "{$basePath}/data/{$year}/{$finalLine['主管機關']}";
        if (!file_exists($dataPath)) {
            mkdir($dataPath, 0777, true);
        }
        $pos = strpos($finalLine['處分字號'], '號');
        if(false !== $pos) {
            $finalLine['處分字號'] = substr($finalLine['處分字號'], 0, $pos + 3);
        }
        $filename = $finalLine['處分字號'] . '_' . mb_substr($finalLine['事業單位名稱或負責人'], 0, 10, 'utf-8') . '_' . mb_substr($finalLine['違法法規法條'], 0, 10, 'utf-8') . '.json';
        $targetFile = str_replace(chr(0), '', "{$dataPath}/{$filename}");
        file_put_contents($targetFile, json_encode($finalLine, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }
}