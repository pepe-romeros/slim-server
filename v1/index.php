<?php
ini_set('display_errors', 1);
require_once '../include/DBHandler.php';
require '../libs/Slim/Slim.php';
require_once '../libs/Braintree/lib/Braintree.php';

\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();

require_once '../include/Config.php';
Braintree_Configuration::environment(BT_ENVIRONMENT);
Braintree_Configuration::merchantId(BT_MERCHANT_ID);
Braintree_Configuration::publicKey(BT_API_SECRET);
Braintree_Configuration::privateKey(BT_API_KEY);

$userId = 1;

/**
 * Echoing json response to client
 * @param String $status_code Http response code
 * @param Int $response Json response
 */
function echoResponse($status_code, $response) {
    $app = \Slim\Slim::getInstance();
    // Http response code
    $app->status($status_code);

    // setting response content type to json
    $app->contentType('application/json');

    echo json_encode($response);
}

/**
 * Lists all products
 * method - GET
 */
$app->get('/products', function() {
            $response = array();
            $db = new DbHandler();

            // fetching all products
            $result = $db->getAllProducts();

            $response["error"] = false;
            $response["products"] = array();

            // looping through result and preparing products array
            while ($task = $result->fetch_assoc()) {
                $tmp = array();
                $tmp["id"] = $task["id"];
                $tmp["name"] = $task["name"];
                $tmp["price"] = $task["price"];
                $tmp["description"] = $task["description"];
                $tmp["image"] = $task["image"];
                $tmp["sku"] = $task["sku"];
                $tmp["created_at"] = $task["created_at"];
                array_push($response["products"], $tmp);
            }

            echoResponse(200, $response);
        });
        $app->get('/clientToken', function(){
                  $response = array();
                  $clientToken = Braintree_ClientToken::generate();
                  $response["error"] = false;
                  $response["token"] = $clientToken;
                  echoResponse(200, $response);
                });

$app->run();
