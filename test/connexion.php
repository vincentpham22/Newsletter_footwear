<?php

// try {

//     $hostname = 'localhost';
//     $dbname = 'footwear';
//     $port = '3306';
//     $username = 'root';
//     $password = '';

//     $connexion = new PDO("mysql:host=$hostname;dbname=$dbname;charset=utf8;port=$port", $username, $password);
//     $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//     $connexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
      
// } catch (PDOException $e) {
//     echo "erreur de connexion : " . $e->getMessage() . "</br>";
//     die();
// }

class Singleton
{
    private static $config = [
        'host' => '',
        'port' => 0,
        'dbname' => '',
        'user' => '',
        'password' => '',
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    ];

    private static $connexion = null;

    private function __construct()
    {
        // Empêche l'instanciation de la classe
    }

    public static function setConfiguration(string $host, int $port, string $dbname, string $user, string $password, array $options = [])
    {
        self::$config['host'] = $host;
        self::$config['port'] = $port;
        self::$config['dbname'] = $dbname;
        self::$config['user'] = $user;
        self::$config['password'] = $password;
        self::$config['options'] += $options;
    }

    public static function hasConfiguration(): bool
    {
        return !empty(self::$config['host']) && self::$config['port'] !== 0 && !empty(self::$config['dbname']);
    }

    public static function getPDO(): PDO
    {
        if (!self::$connexion) {
            if (!self::hasConfiguration()) {
                throw new Exception(__CLASS__ . ' : Vous devez définir une configuration (host, port, dbname).');
            }

            $dsn = 'mysql:host=' . self::$config['host'] . ';port=' . self::$config['port'] . ';dbname=' . self::$config['dbname'] . ';charset=utf8';

            try {
                self::$connexion = new PDO($dsn, self::$config['user'], self::$config['password'], self::$config['options']);
            } catch (PDOException $err) {
                throw new Exception($err->getMessage());
            }
        }

        return self::$connexion;
    }

    public static function disconnect()
    {
        self::$connexion = null;
    }

    public function __destruct()
    {
        self::disconnect();
    }

    private function __clone()
    {
        throw new Exception(__CLASS__ . ' : Clonage de cette classe interdit.');
    }
}