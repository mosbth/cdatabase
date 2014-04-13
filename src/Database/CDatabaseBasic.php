<?php

namespace Mos\Database;

/**
 * Database wrapper, provides a database API for the framework but hides details of implementation.
 *
 */
class CDatabaseBasic
{

    use TSQLQueryBuilderBasic;



    /**
     * Properties
     */
    private $options;                   // Options used when creating the PDO object
    private $db   = null;               // The PDO object
    private $stmt = null;               // The latest statement used to execute a query
    private static $numQueries = 0;     // Count all queries made
    private static $queries    = [];    // Save all queries for debugging purpose
    private static $params     = [];    // Save all parameters for debugging purpose



    /**
     * Constructor creating a PDO object connecting to a choosen database.
     *
     * @param array $options containing details for connecting to the database.
     *
     * @return void
     */
    public function __construct($options = [])
    {
        $this->setOptions($options);
    }



    /**
     * Set options and connection details.
     *
     * @param array $options containing details for connecting to the database.
     *
     * @return void
     */
    public function setOptions($options = [])
    {
        $default = [
            'dsn'             => null,
            'username'        => null,
            'password'        => null,
            'driver_options'  => null,
            'table_prefix'    => null,
            'verbose'         => null,
            'fetch_style'     => \PDO::FETCH_OBJ,
            'session_key'     => 'CDatabase',
        ];
        $this->options = array_merge($default, $options);

        if ($this->options['table_prefix']) {
            $this->setTablePrefix($this->options['table_prefix']);
        }

        if ($this->options['dsn']) {
            $dsn = explode(':', $this->options['dsn']);
            $this->setSQLDialect($dsn[0]);
        }
    }



    /**
     * Connect to the database.
     *
     * @return void
     */
    public function connect()
    {
        if (isset($this->options['dsn'])) {
            if ($this->options['verbose']) {
                echo "<p>Connecting to dsn:<br><code>" . $this->options['dsn'] . "</code>";
            }

            $this->db = new \PDO(
                $this->options['dsn'],
                $this->options['username'],
                $this->options['password'],
                $this->options['driver_options']
            );
            $this->db->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, $this->options['fetch_style']);

        } else {
            throw new \Exception("You can not connect, missing dsn.");
        }

        $this->loadHistory();
    }



    /**
     * Set and unset verbose mode to display queries made.
     *
     * @param boolean $on set true to display queries made through echo, false to disable.
     *
     * @return void
     */
    public function setVerbose($on = true)
    {
        $this->options['verbose'] = $on;
    }



    /**
     * Load query-history from session if available.
     *
     * @return int number of database queries made.
     */
    public function loadHistory()
    {
        $key = $this->options['session_key'];
        if (isset($_SESSION['CDatabase'])) {
            self::$numQueries = $_SESSION[$key]['numQueries'];
            self::$queries    = $_SESSION[$key]['queries'];
            self::$params     = $_SESSION[$key]['params'];
            unset($_SESSION[$key]);
        }
    }



    /**
     * Save query-history in session, useful as a flashmemory when redirecting to another page.
     * 
     * @param string $debug enables to save some extra debug information.
     *
     * @return void
     */
    public function saveHistory($extra = null)
    {
        if ($extra) {
            self::$queries[] = $extra;
            self::$params[] = null;
        }

        self::$queries[] = 'Saved query-history to session.';
        self::$params[] = null;

        $key = $this->options['session_key'];
        $_SESSION[$key]['numQueries'] = self::$numQueries;
        $_SESSION[$key]['queries']    = self::$queries;
        $_SESSION[$key]['params']     = self::$params;
    }



    /**
     * Get how many queries have been processed.
     *
     * @return int number of database queries made.
     */
    public function getNumQueries()
    {
        return self::$numQueries;
    }



    /**
     * Get all the queries that have been processed.
     *
     * @return array with queries.
     */
    public function getQueries()
    {
        return [self::$queries, self::$params];
    }



    /**
     * Get a html representation of all queries made, for debugging and analysing purpose.
     * 
     * @return string with html.
     */
    public function dump()
    {
        $html  = '<p><i>You have made ' . self::$numQueries . ' database queries.</i></p><pre>';
        
        foreach (self::$queries as $key => $val) {
            $params = empty(self::$params[$key]) ? null : htmlentities(print_r(self::$params[$key], 1), null, 'UTF-8') . '<br/><br/>';
            $html .= htmlentities($val, null, 'UTF-8') . '<br/><br/>' . $params;
        }
        
        return $html . '</pre>';
    }



    /**
     * Extend params array to support arrays in it, extract array items and add to $params and insert ? for each entry.
     *
     * @param string $query  as the query to prepare.
     * @param array  $params the parameters that may contain arrays.
     *
     * @return array with query and params.
     */
    protected function expandParamArray($query, $params)
    {
        $param = [];
        $offset = -1;

        foreach ($params as $val) {

            $offset = strpos($query, '?', $offset + 1);

            if (is_array($val)) {
            
                $nrOfItems = count($val);
            
                if ($nrOfItems) {
                    $query = substr($query, 0, $offset) . str_repeat('?,', $nrOfItems  - 1) . '?' . substr($query, $offset + 1);
                    $param = array_merge($param, $val);
                } else {
                    $param[] = null;
                }
            } else {
                $param[] = $val;
            }
        }

        return array($query, $param);
    }



    /**
     * Execute a select-query with arguments and return the resultset.
     * 
     * @param string  $query      the SQL query with ?.
     * @param array   $params     array which contains the argument to replace ?.
     * @param boolean $debug      defaults to false, set to true to print out the sql query before executing it.
     * @param int     $fetchStyle can be changed by sending in arguments.
     *
     * @return array with resultset.
     */
    public function executeFetchAll(
        $query = null,
        $params = [],
        $debug = false,
        $fetchStyle = null
    ) {

        $this->execute($query, $params, $debug);
        return $this->fetchAll($fetchStyle);
    }



    /**
     * Fetch all resultset from previous select statement.
     * 
     * @param int $fetchStyle can be changed by sending in arguments.
     *
     * @return array with resultset.
     */
    public function fetchAll($fetchStyle = null)
    {
        return $this->stmt->fetchAll($fetchStyle);
    }



    /**
     * Fetch one resultset from previous select statement.
     * 
     * @param int $fetchStyle can be changed by sending in arguments.
     *
     * @return array with resultset.
     */
    public function fetchOne($fetchStyle = null)
    {
        return $this->stmt->fetch($fetchStyle);
    }



    /**
     * Execute a SQL-query and ignore the resultset.
     *
     * @param string  $query  the SQL query with ?.
     * @param array   $params array which contains the argument to replace ?.
     * @param boolean $debug  defaults to false, set to true to print out the sql query before executing it.
     *
     * @return boolean returns TRUE on success or FALSE on failure. 
     */
    public function execute(
        $query = null,
        $params = []
    ) {

        if (!$query) {
            $query = $this->getSQL();
        } else if (is_array($query)) {
            $params = $query;
            $query = $this->getSQL();
        }

        list($query, $params) = $this->expandParamArray($query, $params);

        self::$queries[] = $query;
        self::$params[]  = $params;
        self::$numQueries++;

        if ($this->options['verbose']) {
            echo "<p>Num query = "
                . self::$numQueries
                . "</p><p>Query = </p><pre>"
                . htmlentities($query)
                . "</pre>"
                . (empty($params)
                    ? null
                    : "<p>Params:</p><pre>" . htmlentities(print_r($params, 1)) . "</pre>"
                );
        }

        $this->stmt = $this->db->prepare($query);

        if (!$this->stmt) {
            echo "Error in preparing query: "
                . $this->db->errorCode()
                . " "
                . htmlentities(print_r($this->db->errorInfo(), 1));
        }

        $res = $this->stmt->execute($params);

        if (!$res) {
            echo "Error in executing query: "
                . $this->stmt->errorCode()
                . " "
                . htmlentities(print_r($this->stmt->errorInfo(), 1));
        }

        return $res;
    }



    /**
     * Return last insert id.
     */
    public function lastInsertId()
    {
        return $this->db->lastInsertid();
    }



    /**
    * Return rows affected of last INSERT, UPDATE, DELETE
    */
    public function rowCount()
    {
        return is_null($this->stmt)
            ? $this->stmt
            : $this->stmt->rowCount();
    }
}
