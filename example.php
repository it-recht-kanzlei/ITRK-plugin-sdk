<?php
/*
 * This is an example on how to use the new ITRechtkanzlei plugin SDK.
 *
 * This SDK is made to be used for developing own plugins in connection with the document push/getaccoutlist/version calls of our system.
 * It is very easy to use because only 3 methods of the ITRechtKanzlei\LTIHandler class have to be overwritten here:
 *  - public function isTokenValid(string $token): bool;
 *  - public function handleActionPush(ITRechtKanzlei\LTIPushData $data): ITRechtKanzlei\LTIPushResult;
 *  - publich function handleActionGetAccountList(): ITRechtKanzlei\LTIGetAccountListResult;
 * More detailed information on the methods can be found in the class ITRechtKanzlei\LTIHandler.
 *
 * The SDK can be used in just 2 steps (see example below):
 *  1. Override all abstract ITRechtKanzlei\LTIHandler methods
 *  2. Create object of ITRechtKanzlei\LTI and call public function handleRequest($postData) method like seen in the example below
 *
 * There is no need to send any response back to the server. All these mechanisms are automatically taken over by the LTI class.
 *
 * Please do NOT edit any class like ITRechtKanzlei\LTI or any of the "Results" classes to ensure that the code remains executable.
 *
 * The example below shows you how to use the SDK properly.
 */
require_once __DIR__ . "/src/LTI.php";

class MyLTIHandler extends \ITRechtKanzlei\LTIHandler {
    public function isTokenValid(string $token): bool {
        // Validate your token here
        return $token == '12345678';
    }

    public function handleActionGetVersion(): \ITRechtKanzlei\LTIVersionResult {
        $result = new \ITRechtKanzlei\LTIVersionResult();

        // Inludes the list of installed apache2 modules if php is running as an
        // apache2 module. This helps the support of IT-Recht Kanzlei to troubleshoot
        // problematic interactions between the modules and this plugin.
        $result->includeApacheModules(true);

        // Get a list of all available plugins that might interfere with this plugin.
        // This helps with troubleshooting problematic interactions between those plugins.
        $plugins = [
            'plugin-image-enhancer' => '2.4.2',
            'plugin-multilingual' => '5.23',
            'plugin-herpderp' => '9001.3.1415',
        ];
        foreach ($plugins as $plugin => $version) {
            $result->addPluginInfo($plugin, $$version);
        }

        return $result;
    }

    public function handleActionPush(\ITRechtKanzlei\LTIPushData $data): \ITRechtKanzlei\LTIPushResult {
        // Implement the logic to store your pushed document to the shop here and return an object of ITRechtKanzlei\LTIPushResult with
        // an url where to find the currently uploaded document. Replace the url with your document url.
        return new \ITRechtKanzlei\LTIPushResult('https://www.examplep.com/policies/imprint');
    }

    // This method only has to be created, if your system is a multishop system.
    public function handleActionGetAccountList(): \ITRechtKanzlei\LTIAccountListResult {
        // add all your shops here to the $accoutlist like seen in the example.
        $accountList = new \ITRechtKanzlei\LTIAccountListResult();
        $accountList->addAccount('1', 'example store name 1');
        $accountList->addAccount('2', 'example store name 2');
        $accountList->addAccount('3', 'example store name 3');

        return $accountList;
    }
}

// 1. Instantiate object of class that overrides abstract methods
$ltiHandler = new MyLTIHandler();

// 2. Instantiate of ITRechtKanzlei\LTI and call handleRequest(...) method
$lti = new \ITRechtKanzlei\LTI($ltiHandler, '1.2', '1.0', true /* is multishop system */);

// 3. Handle the request.
$lti->handleRequest($_POST['xml']);

// This should be the end of your plugin code
