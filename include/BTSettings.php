<?php
require_once dirname(__FILE__) .'/Config.php';
Braintree_Configuration::environment(BT_ENVIRONMENT);
Braintree_Configuration::merchantId(BT_MERCHANT_ID);
Braintree_Configuration::publicKey(BT_API_SECRET);
Braintree_Configuration::privateKey(BT_API_KEY);
