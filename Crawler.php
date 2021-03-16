<?php

require 'vendor/autoload.php';
use Goutte\Client;
$client = new Client();

$crawler = $client->request('GET', 'https://announcement.mol.gov.tw/');
$form = $crawler->selectButton('下載')->form();

$client->setHeader('User-Agent', 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:59.0) Gecko/20100101 Firefox/59.0');
$client->setHeader('Host', 'announcement.mol.gov.tw');
$client->setHeader('Referer', 'https://announcement.mol.gov.tw/');

$form->getNode()->setAttribute('action', 'https://announcement.mol.gov.tw/Download/');

$crawler = $client->submit($form, array('DOCstartDate' => '0900101', 'DOCEndDate' => (date('Y') - 1911) . date('md'), 'downloadType' => '3'));

file_put_contents(__DIR__ . '/data.zip', $client->getResponse()->getContent());

$za = new ZipArchive();

if($za->open(__DIR__ . '/data.zip')) {
    for( $i = 0; $i < $za->numFiles; $i++ ){
        $stat = $za->statIndex( $i );
        $filename = $za->getNameIndex($i);
        $parts = explode('-', $filename);
        copy('zip://' . __DIR__ . '/data.zip#'.$filename, __DIR__ . '/' . $parts[1] . '.csv');
    }
}