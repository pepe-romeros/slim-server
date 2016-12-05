<?php

/**
 * Handling database connection
 *
 * @author Josh Suchowitzki
 */
class DbConnect {

    private $conn;

    /**
     * Default class constructor.
     */
    function __construct() { }

    /**
     * Method to get a database connection handler with config settings.
     * @return database connection handler
     */
    function connect() {
        include_once dirname(__FILE__) . '/Config.php';

        // Connecting to mysql database
        $this->conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

        // Check for database connection error
        if (mysqli_connect_errno()) {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
        }

        // returing connection resource
        return $this->conn;
    }

}
