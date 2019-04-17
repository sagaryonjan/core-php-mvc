<?php

namespace System;

use PDO;
use PDOException;

class Database
{
    /**
     * Application Object
     *
     * @var \System\Application
     */
    private $app;

    /**
     * Data Container
     *
     * @var array
     */
    private $data = [];

    /**
     * Bindings Container
     *
     * @var array
     */
    private $bindings = [];

    /**
     * Last Insert ID
     *
     * @var int
     */
    private $lastID;

    /**
     * Wheres
     *
     * @var array
     */
    private $wheres = [];

    /**
     * Selects
     *
     * @var array
     */
    private $selects = [];

    /**
     * Limit
     *
     * @var int
     */
    private $limit;

    /**
     * Offset
     *
     * @var array
     */
    private $offset = [];

    /**
     * Joins
     *
     * @var array
     */
    private $joins = [];

    /**
     * Order By
     *
     * @array
     */
    private $orderBy = [];

    /**
     * database table
     *
     * @var string
     */
    private $table;

    /**
     * PDO Connection
     *
     * @var \PDO
     */
    private static $connection;

    /**
     * Constructor
     *
     * @param \System\Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;

        if(! $this->isConnected()) {
            $this->connect();
        }

    }

    /**
     * Determine if there is any connection to the database\
     *
     * @return bool
     */
    private function isConnected()
    {
        return static::$connection instanceof PDO;
    }

    /**
     * Connect to database
     *
     * @return void
     */
    private function connect()
    {
        $connectionData = require $this->app->file->to('config.php');

        extract($connectionData);

        try {

            static::$connection = new PDO('mysql:host='. $server . ';dbname='. $dbname,$dbuser, $dbpass );

            static::$connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

            static::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            static::$connection->exec('SET NAMES utf8');

        } catch (PDOException $e)
        {
            die($e->getMessage());
        }

    }

    /**
     * Get Database Connection Object PDO Objec
     *
     * @return \PDO
     */
    public function connection()
    {
        return static::$connection;
    }

    /**
     * Set select clause
     *
     * @param string $select
     * @return $this
     */
    public function select($select)
    {
        $this->selects[] = $select;

        return $this;
    }

    /**
     * Set join clause
     *
     * @param string $join
     * @return $this
     */
    public function join($join)
    {
        $this->joins[] = $join;

        return $this;
    }

    /**
     * Set Order By clause
     *
     * @param string $column
     * @param string $sort
     * @return $this
     */
    public function orderBy($column, $sort = 'ASC')
    {
        $this->orderBy = [$column, $sort];

        return $this;
    }

    /**
     * Set Limit and offset
     *
     * @param int $limit
     * @param int $offset
     * @return $this
     */
    public function limit($limit, $offset = 0)
    {
        $this->limit  = $limit;
        $this->offset = $offset;

        return $this;
    }

    /**
     * Fetch table
     * THis will return only one record
     *
     * @param string $table
     * @return \stdClass / null
     */
    public function first($table = null)
    {
        if($table)
            $this->table($table);

        $sql = $this->fetchStatement();

        $result = $this->query($sql)->fetch();

        return $result;
    }

    /**
     * Fetch all records form table
     *
     * @param string $table
     * @return array
     */
    public function get($table = null)
    {
        if($table)
            $this->table($table);

        $sql = $this->fetchStatement();

        $result = $this->query($sql)->fetchALl();

        return $result;
    }

    /**
     * Prepare Select statement
     *
     * @return string
     */
    private function fetchStatement()
    {
        $sql = 'SELECT ';

        if($this->selects)
        {
            $sql .=implode(',', $this->selects);
        } else {
            $sql .= '*';
        }

        $sql .= ' FROM '.$this->table .' ';

        if($this->joins)
        {
            $sql .= implode(' ', $this->joins);
        }

        if($this->wheres)
        {
            $sql .= implode(' ', $this->joins);
        }

        if($this->wheres)
        {
            $sql .= ' WHERE ' . implode(' ', $this->wheres);
        }

        if($this->limit)
        {
            $sql .= ' LIMIT ' . $this->limit;
        }

        if ($this->offset)
        {
            $sql .= ' OFFSET ' . $this->offset;
        }

        if ($this->orderBy)
        {
            $sql .= ' ORDER BY ' . implode(' ', $this->orderBy);
        }


        return $sql;
    }

    /**
     * Set the table name
     *
     * @param string $table
     * @return $this
     */
    public function table($table)
    {
        $this->table = $table;

        return $this;
    }

    /**
     * Set the table name
     *
     * @param string $table
     * @return $this
     */
    public function from($table)
    {
        return $this->table($table);
    }

    /**
     * Set the data that will be stored in database table
     *
     * @param mixed $key
     * @param mixed $value
     * @return $this
     */
    public function data($key, $value = null)
    {

        if(is_array($key))
        {
            $this->data = array_merge($this->data, $key);

        } else {

            $this->data[$key]= $value;

        }

        return $this;

    }

    /**
     * Insert Data to database
     *
     * @param string $table
     * @return $this
     */
    public function insert($table = null)
    {
        if($table)
            $this->table($table);

        $sql = "INSERT INTO ".$this->table.' SET ';

        $sql .= $this->setFields();

        $this->query($sql, $this->bindings);

        $this->lastID = $this->connection()->lastInsertId();

        return $this;
    }

    /**
     * Set the fields for insert and update
     *
     * @return string
     */
    private function setFields()
    {

        $sql = '';

        $param = [];
        foreach ($this->data as $key => $value)
        {
            $sql .= '`' . $key . '` = ? , ';
            $param[] = $value;
        }

        $this->addToBindings($param);

        $sql = rtrim($sql, ', ');

        return $sql;

       /* $sql = '';
        foreach (array_keys($this->data) as $key)
        {
            $sql .= '`' . $key . '` = ? , ';
        }

        $sql = rtrim($sql, ', ');
        return $sql;*/
    }


    /**
     * Update Data In database
     *
     * @param string $table
     * @return $this
     */
    public function update($table = null)
    {
        if($table)
            $this->table($table);

        $sql = "UPDATE ".$this->table.' SET ';

        $sql .= $this->setFields();

        dd($this->wheres);

        if($this->wheres)
            $sql .= ' WHERE '. implode('', $this->wheres);

        $this->query($sql, $this->bindings);

        return $this;
    }

    /**
     * Add New Where clauses
     *
     * @return $this
     */
    public function where()
    {
        $bindings = func_get_args();

        $sql = array_shift($bindings);

        $this->addToBindings($bindings);

        $this->wheres[] = $sql;

        return $this;
    }

    /**
     * Add the given value to bindings
     *
     * @param mixed $value
     * @return void
     */
    private function addToBindings($value)
    {
        if(is_array($value)) {

            $this->bindings = array_merge($this->bindings, $value);

        } else {

            $this->bindings[] = $value;

        }

    }

    /**
     * Execute the given sql statement
     *
     * @return \PDOStatement
     */
    public function query()
    {

        $bindings = func_get_args();

        $sql      = array_shift($bindings);

        if(count($bindings) == 1 && is_array($bindings[0]))
            $bindings = $bindings[0];

       try {

           $query = $this->connection()->prepare($sql);

           foreach ($bindings as $key => $value) {

               $query->bindValue( $key + 1 , $value);
           }

           $query->execute();

           return $query;
       } catch (PDOException $e) {
            die($e->getMessage());
       }
    }

    /**
     * Get the last insert id
     *
     * @return int
     */
    public function lastId()
    {
        return $this->lastID;
    }
}