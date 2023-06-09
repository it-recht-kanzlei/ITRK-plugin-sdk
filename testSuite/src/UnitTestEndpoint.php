<?php

namespace PluginSDKTestSuite;

$postData = $_POST;
require_once __DIR__ . "/../../sdk/LTI.php";

class MyLTIHandler extends \ITRechtKanzlei\LTIHandler {
    public function isTokenValid(string $token): bool {
        return $token == 'TEST_TOKEN';
    }

    /**
     * @throws \Exception
     */
    public function handleActionPush(\ITRechtKanzlei\LTIPushData $data): \ITRechtKanzlei\LTIPushResult {
        if ($data->getType() != 'impressum' && $data->hasPdf()) {
            $data->getPdf();
        }
        return new \ITRechtKanzlei\LTIPushResult('');
    }

    public function handleActionGetAccountList(): \ITRechtKanzlei\LTIAccountListResult {
        $accountList = new \ITRechtKanzlei\LTIAccountListResult();
        $accountList->addAccount('1', 'example store name 1');
        $accountList->addAccount('2', 'example store name 2');
        $accountList->addAccount('3', 'example store name 3');

        return $accountList;
    }
}

$ltiHandler = new MyLTIHandler();
$multishop = strtolower(getenv('MULTISHOP')) === "true";
$lti = new \ITRechtKanzlei\LTI($ltiHandler, '1.2', '1.0', $multishop);
$lti->handleRequest(@$postData['xml']);