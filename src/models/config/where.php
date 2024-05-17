<?php
require_once __DIR__ . '/../../services/log.php';
require_once __DIR__ . '/../../services/helper.php';

class Where {
    /** @var string */
    private $field;
    /** @var string */
    private $condition;
    private $value;
    /** @var string */
    private $collate;

    /** @var string */
    private $group_clause;
    /** @var self[] */
    private $clauses = [];

    private function __construct() {}

    static function clause(string $field, string $condition, $value, bool $insensitive_accent_case = false) {
        if (strcasecmp($condition ?: '', 'in') == 0 && !is_array($value)) throw new Exception("Esparado um array", 1);
        
        $where = new self();

        $where->field = $field;
        $where->condition = $condition;
        $where->value = $value;
        if ($insensitive_accent_case) $where->collate = ' COLLATE Latin1_general_CI_AI';
        return $where;
    }

    /** @return self */
    static function and() {
        $where = new self();
        $where->group_clause = 'and';
        return $where;
    }
    /** @return self */
    static function or() {
        $where = new self();
        $where->group_clause = 'or';
        return $where;
    }
    /**
     * @param self
     * @return self
     */
    function add($clause) {
        if (!isset($this->group_clause)) throw new Exception("Not a group", 1);
        $this->clauses[] = $clause;
        return $this;
    }

    private function getParamName(array $params): string {
        $name = '';
        do {
            $name = Helper::randomStr();
        } while (array_key_exists($name, $params));
        return $name;
    }

    public function getString(array &$params) {
        if (isset($this->group_clause)) {
            $statements = [];
            foreach ($this->clauses as $clause) {
                $statements[] = $clause->getString($params);
            }
            return '(' . implode(' ' . $this->group_clause . ' ', $statements) . ')';
        } else {
            $str = $this->field . ' ' . $this->condition . ' ';
            if (strcasecmp($this->condition ?: '', 'in') === 0) {
                $inValues = [];
                foreach ($this->value as $value) {
                    if (gettype($value) === 'integer' || gettype($value) === 'double') $inValues[] = $value;
                    else {
                        $paramName = $this->getParamName($params);
                        $inValues[] = ':' . $paramName;
                        $params[$paramName] = $value;
                    }
                }
                return $str . '(' . implode(',', $inValues) . ')';
            } else {
                $paramName = $this->getParamName($params);
                $params[$paramName] = $this->value;

                return $str . ':' . $paramName . $this->collate;
            }
        }
    }
}