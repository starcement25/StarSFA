<?php
/*******
 * Class based on the OTF class made by @lukevers
 * https://lukevers.com/2015/03/25/on-the-fly-database-connections-with-laravel-5
 * https://gist.github.com/lukevers/2e40dc2dc0cf4818b1ba#file-otf-php
 */
namespace App\Database;
/**
 * Class DbOnTheFly
 * @package App\Database
 */
use Config;
use DB;

class DbOnTheFly
{
    /**
     * The name of the database we're connecting to on the fly.
     *
     * @var string $database
     */
    protected $database;
    /**
     * The on the fly database connection.
     *
     * @var \Illuminate\Database\Connection
     */
    protected $connection;
    /**
     * Create a new on the fly database connection.
     *
     * @param  array $options
     * @return void
     */
    public function __construct($options = null)
    {
        // Set the database
        $database = $options['database'];
        $this->database = $database;
        // Figure out the driver and get the default configuration for the driver
        $driver  = isset($options['driver']) ? $options['driver'] : app('config')->get("database.default");
        $default = app('config')->get("database.connections.$driver");
        // Loop through our default array and update options if we have non-defaults
        foreach($default as $item => $value)
        {
            $default[$item] = isset($options[$item]) ? $options[$item] : $default[$item];
        }
        // Set the temporary configuration
        app('config')->set("database.connections.$database", $default);
        // Create the connection
        $this->connection = app('db')->connection($database);
    }
    /**
     * Get the on the fly connection.
     *
     * @return \Illuminate\Database\Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }
    /**
     * Get a table from the on the fly connection.
     *
     * @var    string $table
     * @return \Illuminate\Database\Query\Builder
     */
    public function getTable($table = null)
    {
        return $this->getConnection()->table($table);
    }
}

?>