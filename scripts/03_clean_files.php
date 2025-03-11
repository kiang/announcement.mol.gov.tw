<?php
$basePath = dirname(__DIR__);

foreach (glob('data/*/*/*.json') as $file) {
    $data = json_decode(file_get_contents($file), true);
    $before = $data['處分字號'];
    $data['處分字號'] = preg_replace('/\s+/u', '', $data['處分字號']);
    if($before !== $data['處分字號']) {
        $year = substr($data['處分日期'], 0, 4);
        $dataPath = "{$basePath}/data/{$year}/{$data['主管機關']}";
        file_put_contents("{$dataPath}/{$data['處分字號']}.json", json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        unlink($file);
    }
}