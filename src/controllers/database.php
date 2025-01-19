<?php
class Database
{
  private static ?Database $instance = null;
  public PDO $connection;

  public function __construct()
  {
    $ini = parse_ini_file('../../php.ini');

    try {
      $this->connection = new PDO($ini['database_url']);
    } catch (PDOException $e) {
      die("Erreur : " . $e->getMessage());
    }
  }

  // singleton pour éviter de créer plusieurs connexions à la base de données
  public static function getPDO(): PDO
  {
    if (self::$instance === null) {
      self::$instance = new Database();
    }

    return self::$instance->connection;
  }
}