<?php
require_once __DIR__ . '/../../services/dotenv.php';

class Database {
    private static $instance;
    private $connection;

    private $db_driver;
    private $db_host;
    private $db_name;
    private $db_user;
    private $db_password;
    
    private function __construct() {
        $this->db_driver = __SGBD_PRETENDENTES_DRIVER__ ?: getenv('db_driver');
        $this->db_host = __SGBD_PRETENDENTES_SERVER__ ?: getenv('db_host');
        $this->db_name = __SGBD_PRETENDENTES_DB__ ?: getenv('db_name');
        $this->db_user = __SGBD_PRETENDENTES_USER__ ?: getenv('db_user');
        $this->db_password = __SGBD_PRETENDENTES_PASS__ ?: getenv('db_password');

        $dsn = $this->db_driver . ":Server=" . $this->db_host . ";Database=" . $this->db_name . '; Encrypt=false; TrustServerCertificate=yes;';
        try {
            $this->connection = new PDO($dsn, $this->db_user, $this->db_password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Verifica se uma transação já está em andamento
            if (!$this->connection->inTransaction()) {
                // Inicia a transação somente se não houver uma em andamento
                $this->connection->beginTransaction();
            }
        } catch (PDOException $e) {
            Log::new(Log::TYPE_EXCEPTION)->setException($e);
            die();
        }
    }

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }

    public function commit() {
        // Confirma a transação apenas se foi iniciada por esta instância
        if ($this->connection->inTransaction()) {
            $this->connection->commit();
        }
    }

    public function rollBack() {
        // Reverte a transação apenas se foi iniciada por esta instância
        if ($this->connection->inTransaction()) {
            $this->connection->rollBack();
        }
    }

    public function __destruct() {
        $this->commit();
    }
}