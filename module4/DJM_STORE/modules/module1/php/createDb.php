<?php

class CreateDb {
    public $servername;
    public $username;
    public $password;
    public $dbname;
    public $tablename;
    public $con;

    public function __construct(
        $dbname = "djmtech",
        $tablename = "tbl_users",
        $servername = "localhost",
        $username = "root",
        $password = ""
    ) {
        $this->dbname = $dbname;
        $this->tablename = $tablename;
        $this->servername = $servername;
        $this->username = $username;
        $this->password = $password;

        // Create connection
        $this->con = new mysqli($servername, $username, $password);

        // Check connection
        if ($this->con->connect_error) {
            die("Connection Failed: " . $this->con->connect_error);
        }

        // Create database if it does not exist
        $sql = "CREATE DATABASE IF NOT EXISTS $dbname";
        if ($this->con->query($sql) === TRUE) {
            $this->con->close(); // Close the initial connection
            $this->con = new mysqli($servername, $username, $password, $dbname);
        } else {
            die("Error creating database: " . $this->con->error);
        }

        // Create table if it does not exist
        $sql = "CREATE TABLE IF NOT EXISTS $tablename (
            id INT AUTO_INCREMENT PRIMARY KEY,
            first_name VARCHAR(40) NOT NULL,
            last_name VARCHAR(40) NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            address VARCHAR(255)
        )";

        if (!$this->con->query($sql)) {
            die("Error creating table: " . $this->con->error);
        }
    }

    // Method to return the database connection
    public function connect() {
        return $this->con;
    }

    // Destructor: Prevent double closing of the connection
    public function __destruct() {
        if ($this->con instanceof mysqli) { // Check if it's still a valid mysqli object
            $this->con->close();
        }
    }
}
?>
