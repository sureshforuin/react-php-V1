<?php
/**
 * Database singleton — reads credentials from environment variables.
 * On Hostinger: set these in the Hostinger panel → PHP Config → Environment Variables,
 *               or place a .env loader here.
 */

declare(strict_types=1);

class Database
{
    private static ?Database $instance = null;
    private ?PDO $connection = null;

    private string $host;
    private string $dbName;
    private string $user;
    private string $pass;
    private string $charset = 'utf8mb4';

    private function __construct()
    {
        // Read from environment variables (set in Hostinger panel or .env)
        $this->host   = getenv('DB_HOST')   ?: 'localhost';
        $this->dbName = getenv('DB_NAME')   ?: 'your_db_name';
        $this->user   = getenv('DB_USER')   ?: 'your_db_user';
        $this->pass   = getenv('DB_PASS')   ?: 'your_db_password';
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        if ($this->connection === null) {
            $dsn = "mysql:host={$this->host};dbname={$this->dbName};charset={$this->charset}";
            $this->connection = new PDO($dsn, $this->user, $this->pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        }
        return $this->connection;
    }
}
