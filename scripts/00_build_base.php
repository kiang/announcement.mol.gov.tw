<?php
/**
 * extract all commits using command
 * ```git rev-list --all | xargs -L1 sh -c 'mkdir "/home/kiang/public_html/mol-rev/$0" && git archive --format=tar $0 | tar -C "/home/kiang/public_html/mol-rev/$0" -xf -'```
 * 
 * get all csv files
 * ```find /home/kiang/public_html/mol-rev/ -type f | grep csv$ > /home/kiang/public_html/announcement.mol.gov.tw/scripts/csvfiles```
 */
$fh = fopen(__DIR__ . '/csvfiles', 'r');
$basePath = dirname(__DIR__);
$baseHead = ['主管機關', '公告日期', '處分日期', '處分字號', '事業單位名稱或負責人', '違法法規法條', '違反法規內容', '罰鍰金額', '備註說明'];
while ($line = fgets($fh)) {
    $csvFile = trim($line);
    if ('text/csv' !== mime_content_type($csvFile)) {
        continue;
    }
    $csvFh = fopen($csvFile, 'r');
    $head = fgetcsv($csvFh, 2048);
    $countHead = count($head);
    while ($csvLine = fgetcsv($csvFh, 20000)) {
        if ($countHead !== count($csvLine)) {
            continue;
        }
        $data = array_combine($head, $csvLine);
        if (!isset($data['罰鍰金額'])) {
            $data['罰鍰金額'] = '';
        }
        if (isset($data['處分金額或滯納金'])) {
            $data['罰鍰金額'] = $data['處分金額或滯納金'];
        }
        $finalLine = [];
        foreach ($baseHead as $key) {
            $finalLine[$key] = preg_replace('/\s+/u', '', $data[$key]);
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
        file_put_contents("{$dataPath}/{$finalLine['處分字號']}_{$finalLine['事業單位名稱或負責人']}.json", json_encode($finalLine, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }
}
