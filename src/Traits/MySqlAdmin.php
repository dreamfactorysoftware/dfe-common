<?php namespace DreamFactory\Enterprise\Common\Traits;

use DreamFactory\Enterprise\Database\Exceptions\DatabaseException;
use Illuminate\Database\Connection;

/**
 * MySQL administrative methods
 */
trait MySqlAdmin
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type null|Connection
     */
    protected $mysqlConnection = null;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param string $user
     * @param string $pass
     * @param string $database
     * @param string $host
     *
     * @return bool
     */
    protected function mysqlGrantPrivileges($user, $pass, $database, $host)
    {
        //  Create users
        $_users = $this->mysqlGetUsers($user, $host);

        try {
            foreach ($_users as $_user) {
                $this->mysqlConnection->statement('GRANT ALL PRIVILEGES ON ' . $database . '.* TO ' . $_user . ' IDENTIFIED BY \'' . $pass . '\'');
            }

            return true;
        } catch (\Exception $_ex) {
            \Log::error('[dfe.mysql-admin.grant-privileges] issue grants - failure: ' . $_ex->getMessage());

            return false;
        }
    }

    /**
     * @param string $user
     *
     * @return bool
     */
    protected function mysqlDropUser($user)
    {
        try {
            if (!\DB::statement('DROP USER ' . $user)) {
                \Log::error('[dfe.mysql-admin.drop-user] cannot drop user: ' . $user);
            }

            return true;
        } catch (\Exception $_ex) {
            \Log::error('[dfe.mysql-admin.drop-user] exception: ' . $_ex->getMessage());

            return false;
        }
    }

    /**
     * Flush privileges on the server
     *
     * @return bool
     */
    protected function mysqlFlushPrivileges()
    {
        return $this->mysqlConnection->statement('FLUSH PRIVLEGES');
    }

    /**
     * Get/set the current connection
     *
     * @param Connection|string|null $mysqlConnection If null "database.default" config setting is used.
     *
     * @return $this|\Illuminate\Database\Connection
     * @throws \DreamFactory\Enterprise\Database\Exceptions\DatabaseException
     */
    protected function mysqlSetConnection($mysqlConnection = null)
    {
        if (!$mysqlConnection) {
            $mysqlConnection = config('database.default');
        }

        if (is_string($mysqlConnection)) {
            if (empty($_connection = \DB::connection($mysqlConnection))) {
                throw new DatabaseException('cannot connect to "' . $mysqlConnection . '", see help for more info.');
            }

            $mysqlConnection = $_connection;
        }

        if (!($mysqlConnection instanceOf Connection)) {
            throw new \InvalidArgumentException('The $mysqlConnection value is invalid.');
        }

        $this->mysqlConnection = $mysqlConnection;

        return $this;
    }

    /**
     * @param string $user
     * @param string $host
     * @param bool   $localhost If true, add a user@localhost user to the list
     * @param bool   $wildcard  If true, a wildcard user will be added
     *
     * @return array
     */
    protected function mysqlGetUsers($user, $host, $localhost = true, $wildcard = true)
    {
        $_users = [
            '\'' . $user . '\'@\'' . $host . '\'',
            '\'' . $user . '\'@\'' . gethostbyname($host) . '\'',
        ];

        $localhost && $_users[] = "'$user'@'localhost'";
        $wildcard && $_users[] = "'$user'@'%'";

        return $_users;
    }

    /**
     * Disconnect the current connection and release the variable, if connected.
     *
     * @return $this
     */
    protected function mysqlDisconnect()
    {
        if ($this->mysqlConnection) {
            $this->mysqlConnection->disconnect();
        }

        unset($this->mysqlConnection);
        $this->mysqlConnection = null;

        return $this;
    }
}
