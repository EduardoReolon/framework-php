<?php
require_once 'dotenv.php';
require_once __DIR__ . '/../models/config/entity.php';
require_once('auth.php');

Class Log {
    // Constantes de classe para os tipos permitidos
    const TYPE_ERROR = 'error';
    const TYPE_CHANGE = 'change';
    const TYPE_ALERT = 'alert';
    const TYPE_EXCEPTION = 'exception';
    private const TYPE_GENERIC = 'generic';
    const TYPE_CONTROL = 'control';
    // private $level = LOG_ERR;
    private $type;
    private $msg = '';
    private $path;

    private function __construct() {}

    // /**
    //  * @param int $level LOG_EMERG | LOG_ALERT | LOG_CRIT | LOG_ERR | LOG_WARNING | LOG_NOTICE | LOG_INFO | LOG_DEBUG 
    //  */
    // public function setLevel($level) {
    //     $this->level = $level;
    // }

    /**
     * @param string $type self::TIPO_ERROR|self::TIPO_CHANGE
     */
    public static function new($type = self::TYPE_GENERIC) {
        $log = new self();
        $log->type = $type;

        $log->path = getenv('logs_' . $type . '_folder');
        if (empty($log->path)) {
            $log_root_folder = getenv('logs_folder');
            if (empty($log_root_folder)) $log_root_folder = realpath(__DIR__ . '/../../') . '/logs/';
            else if (substr($log_root_folder, -1) !== '/') $log_root_folder .= '/';
            $log->path = $log_root_folder . $type;
        }

        return $log;
    }

    public function setTableName(string $tableName) {
        $this->msg = $tableName;
        return $this;
    }
    public function setMethod(string $method) {
        $this->msg .= '(' . $method . ')';
        return $this;
    }
    public function setValueChanged(string $value) {
        $this->msg .= ': ' . $value;
        return $this;
    }

    public function setMessage(string $msg) {
        $this->msg .= $msg;
        return $this;
    }

    public function setError($errno, $errstr, $errfile, $errline) {
        $this->msg = '(type=' . $errno . ') ' . $errstr . " em " . $errfile . " na linha " . $errline;
        return $this;
    }

    public function setException(Throwable $exception) {
        $this->msg = $exception->getMessage() . " em " . $exception->getFile() . " na linha " . $exception->getLine();
        return $this;
    }

    public function setThrowable(Throwable $throwable) {
        $this->msg = $throwable->getMessage() . " em " . $throwable->getFile() . " na linha " . $throwable->getLine();
        return $this;
    }

    public function __destruct() {
        if (Entity::$rollback && $this->type === self::TYPE_CHANGE) return;

        $now = new DateTime();

        // Definir o fuso horário para o Brasil
        $brasilTimeZone = new DateTimeZone('America/Sao_Paulo');
        $now->setTimezone($brasilTimeZone);

        $msg = $now->format('Y-m-d H:i:s') . ' - ' . (Auth::getUserId() ?: 0);
        $msg .= ' - ' . str_replace(["\r", "\n", "\r\n"], ' ', $this->msg);
        $msg .= "\n";

        $log_file = $this->path . '/' . $now->format('Y');
        if (!is_dir($log_file)) {
            mkdir($log_file, 0777, true);
        }
        $log_file .=  '/' . $now->format('M') . '.txt';

        error_log($msg, 3, $log_file);
    }
}