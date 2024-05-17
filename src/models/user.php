<?php
require_once('config/entity.php');

class User extends Entity {
    protected static $table = 'user';

    /**
     * @column
     * primary
     * @var int
     */
    public $id;
    /**
     * @column
     * @var string
     */
    public $username;
    /**
     * @column
     * @var string
     * log_only_prop_name
     */
    protected $password;
    /**
     * @column
     * @var string
     */
    public $name;
    /**
     * @column
     * @var string
     */
    public $surname;
    /**
     * @column
     * @var bool
     */
    public $active;
    /**
     * @var string[] | null
     * @column
     * json
     */
    public $roles;

    public function setPassword($password) {
        $this->password = $password;
    }

    protected function beforeSave() {
        if (!empty($this->password) && strlen($this->password) < 50) {
            $this->password = password_hash($this->password, PASSWORD_DEFAULT);
        }
    }

    public function passwordCheck($password) {
        if (empty($password)) return false;
        return password_verify($password, $this->password);
    }
}