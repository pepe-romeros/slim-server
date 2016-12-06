<?php
/*******************************************************************************
 * MarketMe API Server
 *
 * Implementation of all default API available endpoints for handling the users
 * shopping flow, as well of the Merchants Store management flows.
 *
 * @author Josh Suchowitzki
 * @version 1.0
 * @created 12/01/2016
 * @updated 12/06/2016
 *
 *******************************************************************************/

ini_set('display_errors', 1); // Set flag to 0 in production environment.

// Loading config files and helper libraries.
require_once realpath(__DIR__ . '/..').'/include/DBHandler.php';
require      realpath(__DIR__ . '/..').'/libs/Slim/Slim.php';
require_once realpath(__DIR__ . '/..').'/libs/Braintree/lib/Braintree.php';
require_once realpath(__DIR__ . '/..').'/include/BTSettings.php';

// AutoLoad Slim framework library and get and Slim App instance.
\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();

$userId = 1; // TO-DO implement authentication with API Keys.

// MarketMe m-commerce bindings ------------------------------------------------

/*******************************************************************************
 * Lists a store products catalog retrieved from the database.
 * HTTP Method: GET
 * Params: None
 * Endpoint: /products
 * Full URL: https://api.quoragroup.com/products
 *******************************************************************************/
$app->get('/products', function() {
    $response = array(); // Holds the respose
    $db = new DbHandler(); // Holds the database handler instance

    // fetching all products
    $result = $db->getAllProducts();

    // Begin forming the response
    $response["error"] = false;
    $response["products"] = array();

    // looping through result and preparing products array
    while ($task = $result->fetch_assoc()) {
        $tmp = array();
        $tmp["id"]          = $task["id"];
        $tmp["name"]        = $task["name"];
        $tmp["price"]       = $task["price"];
        $tmp["description"] = $task["description"];
        $tmp["image"]       = $task["image"];
        $tmp["sku"]         = $task["sku"];
        $tmp["created_at"]  = $task["created_at"];
        array_push($response["products"], $tmp);
    }

    // Send response to be encoded with JSON and returned to the client.
    echoResponse(200, $response);
});

// BrainTree bindings ----------------------------------------------------------

/*******************************************************************************
 * Returns a BrainTreeSDK generated Client token.
 * HTTP Method: GET
 * Params: None
 * Endpoint: /clientToken
 * Full URL: https://api.quoragroup.com/clientToken
 *******************************************************************************/
$app->get('/clientToken', function(){
    $response = array();
    $clientToken = Braintree_ClientToken::generate();
    $response["error"] = false;
    $response["token"] = $clientToken;
    echoResponse(200, $response);
});

/*******************************************************************************
 * Returns a BrainTreeSDK generated Transaction result object.
 *
 * HTTP Method: POST
 * Params: Paymentnonce, clientId
 * Endpoint: /payment
 * Full URL: https://api.quoragroup.com/payment
 *******************************************************************************/
$app->get('/payment', function(){
    $response = array();
    // TO-DO Implement payment transaction functionallity.
    echoResponse(200, $response);
});

// API Helper functions --------------------------------------------------------

/*******************************************************************************
 * Helper function for echoing a json response and Http response code to client.
 *
 * @param String $status_code Http response code
 * @param Int $response Json response
 *******************************************************************************/
function echoResponse($status_code, $response) {
    // With Slim app instance set response code contentType and body using JSON.
    $app = \Slim\Slim::getInstance();
    $app->status($status_code);
    $app->contentType('application/json');
    echo json_encode($response);
}

// Finally execute Slim Framework app.
$app->run();
