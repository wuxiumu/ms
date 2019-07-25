<?php

namespace core\lib\drive\database;

use core\lib\conf;

class GodbModel
{
    private $dsn;
    private $sth;
    private $dbh;
    private $user;
    private $charset;
    private $password;

    public $lastSQL = NULL;

    public function __setup($config = array())
    {
        $option = conf::all('database');
        p($option);die;
        $this->dsn      = $option['mysql_default']['DSN'];
        $this->user     = $option['mysql_default']['USERNAME'];
        $this->password = $option['mysql_default']['PASSWORD'];
        $this->charset  = 'utf-8';
        $this->connect();
    }

    private function connect()
    {
        if(!$this->dbh){
            $options = array(
                \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . $this->charset,
            );
            $this->dbh = new \PDO($this->dsn, $this->user,
                $this->password, $options);
        }
    }

    public function beginTransaction()
    {
        return $this->dbh->beginTransaction();
    }

    public function inTransaction()
    {
        return $this->dbh->inTransaction();
    }

    public function rollBack()
    {
        return $this->dbh->rollBack();
    }

    public function commit()
    {
        return $this->dbh->commit();
    }

    function watchException($execute_state)
    {
        if(!$execute_state){
            throw new MySQLException("SQL: {$this->lastSQL}\n".$this->sth->errorInfo()[2], intval($this->sth->errorCode()));
        }
    }

    public function fetchAll($sql, $parameters=[])
    {
        $result = [];
        $this->lastSQL = $sql;
        $this->sth = $this->dbh->prepare($sql);
        $this->watchException($this->sth->execute($parameters));
        while($result[] = $this->sth->fetch(\PDO::FETCH_ASSOC)){ }
        array_pop($result);
        return $result;
    }

    public function fetchColumnAll($sql, $parameters=[], $position=0)
    {
        $result = [];
        $this->lastSQL = $sql;
        $this->sth = $this->dbh->prepare($sql);
        $this->watchException($this->sth->execute($parameters));
        while($result[] = $this->sth->fetch(\PDO::FETCH_COLUMN, $position)){ }
        array_pop($result);
        return $result;
    }

    public function exists($sql, $parameters=[])
    {
        $this->lastSQL = $sql;
        $data = $this->fetch($sql, $parameters);
        return !empty($data);
    }

    public function query($sql, $parameters=[])
    {
        $this->lastSQL = $sql;
        $this->sth = $this->dbh->prepare($sql);
        $this->watchException($this->sth->execute($parameters));
        return $this->sth->rowCount();
    }

    public function fetch($sql, $parameters=[], $type=\PDO::FETCH_ASSOC)
    {
        $this->lastSQL = $sql;
        $this->sth = $this->dbh->prepare($sql);
        $this->watchException($this->sth->execute($parameters));
        return $this->sth->fetch($type);
    }

    public function fetchColumn($sql, $parameters=[], $position=0)
    {
        $this->lastSQL = $sql;
        $this->sth = $this->dbh->prepare($sql);
        $this->watchException($this->sth->execute($parameters));
        return $this->sth->fetch(\PDO::FETCH_COLUMN, $position);
    }

    public function update($table, $parameters=[], $condition=[])
    {
        $table = $this->format_table_name($table);
        $sql = "UPDATE $table SET ";
        $fields = [];
        $pdo_parameters = [];
        foreach ( $parameters as $field=>$value){
            $fields[] = '`'.$field.'`=:field_'.$field;
            $pdo_parameters['field_'.$field] = $value;
        }
        $sql .= implode(',', $fields);
        $fields = [];
        $where = '';
        if(is_string($condition)) {
            $where = $condition;
        } else if(is_array($condition)) {
            foreach($condition as $field=>$value){
                $parameters[$field] = $value;
                $fields[] = '`'.$field.'`=:condition_'.$field;
                $pdo_parameters['condition_'.$field] = $value;
            }
            $where = implode(' AND ', $fields);
        }
        if(!empty($where)) {
            $sql .= ' WHERE '.$where;
        }
        return $this->query($sql, $pdo_parameters);
    }

    public function insert($table, $parameters=[])
    {
        $table = $this->format_table_name($table);
        $sql = "INSERT INTO $table";
        $fields = [];
        $placeholder = [];
        foreach ( $parameters as $field=>$value){
            $placeholder[] = ':'.$field;
            $fields[] = '`'.$field.'`';
        }
        $sql .= '('.implode(",", $fields).') VALUES ('.implode(",", $placeholder).')';

        $this->lastSQL = $sql;
        $this->sth = $this->dbh->prepare($sql);
        $this->watchException($this->sth->execute($parameters));
        $id = $this->dbh->lastInsertId();
        if(empty($id)) {
            return $this->sth->rowCount();
        } else {
            return $id;
        }
    }

    public function errorInfo()
    {
        return $this->sth->errorInfo();
    }

    protected function format_table_name($table)
    {
        $parts = explode(".", $table, 2);

        if(count($parts) > 1) {
            $table = $parts[0].".`{$parts[1]}`";
        } else {
            $table = "`$table`";
        }
        return $table;
    }

    function errorCode()
    {
        return $this->sth->errorCode();
    }
}

class MySQLException extends \Exception { }

