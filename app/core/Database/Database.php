<?php

namespace App\Core\Database;

class Database
{
    private static ?\PDO $pdo = null;

    private static function initialize(String $env = 'test'): void
    {
        if (self::$pdo === null) {
            $dbConfig = self::loadDatabaseConfig($env);
            $url = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['dbname']}";

            try {
                self::$pdo = new \PDO($url, $dbConfig['dbuser'], $dbConfig['dbpass']);
            } catch (\PDOException $e) {
                throw new \Exception('Koneksi ke basis data gagal: ' . $e->getMessage());
            }
        }
    }

    private static function loadDatabaseConfig(String $env): array
    {
        $dbConfigFile = CONFIG . "database.json";

        if (!file_exists($dbConfigFile)) {
            throw new \Exception('File konfigurasi basis data tidak ditemukan.');
        }

        $dbConfig = json_decode(file_get_contents($dbConfigFile), true);

        if (!isset($dbConfig[$env])) {
            throw new \Exception("Konfigurasi basis data untuk env '{$env}' tidak ditemukan.");
        }

        return $dbConfig[$env];
    }

    public static function getConnection(String $env = 'test'): \PDO
    {
        self::initialize($env);
        return self::$pdo;
    }

    public static function beginTransaction()
    {
        self::$pdo->beginTransaction();
    }

    public static function commitTransaction()
    {
        self::$pdo->commit();
    }

    public static function rollbackTransaction()
    {
        self::$pdo->rollBack();
    }
}
