<?php

require_once 'vendor/autoload.php';

$report = new Quest\PaymentReport();
$report->initData();
$report->createReport();
$report->printResult('csv');
