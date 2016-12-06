<?php

/**
 * Class to handle all database operations available.
 * This class will have CRUD methods for the MarketMe app database tables.
 *
 * @author Josh Suchowitzki
 */
class DbHandler {

    private $conn; // Holds the database connection handler instance.

    function __construct() {
        require_once dirname(__FILE__) . '/DBConnect.php';
        // opening db connection
        $db = new DbConnect();
        $this->conn = $db->connect();
    }

    /**
     * Listing all products
     * */
     public function getAllProducts() {
         $stmt = $this->conn->prepare("SELECT * FROM products");
         $stmt->execute();
         $products = $stmt->get_result();
         $stmt->close();
         return $products;
     }

    /*
     * Fetches a product by its sku
     */
    public function getProductBySku($sku) {
        $stmt = $this->conn->prepare("SELECT * FROM products where sku = ?");
        $stmt->bind_param("s", $sku);

        if ($stmt->execute()) {
            $product = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            return $product;
        } else {
            $stmt->close();
            return NULL;
        }
    }

    /**
     * Stores paypal payment in payments table
     */
    public function storePayment($paypalPaymentId, $userId, $create_time, $update_time, $state, $amount, $currency) {
        $stmt = $this->conn->prepare("INSERT INTO payments(paypalPaymentId, userId, create_time, update_time, state, amount, currency) VALUES(?,?,?,?,?,?,?)") or die(mysql_error());
        $stmt->bind_param("sisssds", $paypalPaymentId, $userId, $create_time, $update_time, $state, $amount, $currency);
        $result = $stmt->execute();
        $stmt->close();

        if ($result) {
            // Payment record created, return payment record to Android client.
            $payment_id = $this->conn->insert_id;
            return $payment_id;
        } else {
            // Payment failed.
            return NULL;
        }
    }

    /**
     * Stores item sale in sales table
     */
    public function storeSale($payment_id, $product_id, $state, $salePrice, $quantity) {
        $stmt = $this->conn->prepare("INSERT INTO sales(paymentId, productId, state, salePrice, quantity) VALUES(?,?,?,?,?)");
        $stmt->bind_param("iisdi", $payment_id, $product_id, $state, $salePrice, $quantity);
        $result = $stmt->execute();
        $stmt->close();

        if ($result) {
            $sale_id = $this->conn->insert_id;
            return $sale_id;
        } else {
            // Failed to register sales record.
            return NULL;
        }
    }

}
