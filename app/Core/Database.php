<?php

class Database {
    private $connection;
    private static $instance = null;

    private function __construct() {
        $config = require __DIR__ . '/../../config/config.php';
        $db = $config['db'];

        $dsn = "mysql:host={$db['host']};dbname={$db['dbname']};charset={$db['charset']}";

        try {
            $this->connection = new PDO($dsn, $db['username'], $db['password']);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Erro de conexão com o banco de dados: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }

    // Método para preparar e executar queries de forma segura
    public function query($sql, $params = []) {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}
