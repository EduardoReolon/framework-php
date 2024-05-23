<?php
require_once('config/entity.php');
require_once('user.php');

class Refresh_token extends Entity {
    protected static $table = 'refresh_token';
    protected $change_logs = false;

    /**
     * @column
     * primary
     * @var string
     */
    public $token;
    /**
     * @column
     * @var int
     */
    public $user_id;
    /**
     * @column
     * custom
     * @var datetime
     */
    public $created_at;

    public function getUser() {
        $usuarios = User::fetchSimpler([['id', '=', $this->user_id]]);

        if (empty($usuarios)) return false;

        return $usuarios[0];
    }
}