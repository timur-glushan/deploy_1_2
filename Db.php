<?php

require_once("config.php");

class Db
{
  private static $migrations = [];
  private static $_db = NULL;
  private static $_stmt = NULL;
  private static $_columns = NULL;
  private static $_rows = NULL;
  private static $_affected_rows = NULL;

  private static function _dbh() {
    if (!self::$_db) {
      self::$_db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8',
          DB_USER, DB_PASSWORD);
      self::$_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      self::$_db->setAttribute(PDO::ATTR_EMULATE_PREPARES, FALSE);
      self::$_db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    return self::$_db;
  }

  public static function sql($query, $args=NULL) {
    $_db = self::_dbh();

//    try {
      self::$_stmt = $_db->prepare($query);
      self::$_stmt->execute($args);
      self::$_rows = [];
      self::$_columns = array();
      for ($i=0; $i<self::$_stmt->columnCount(); $i++) {
        $column = self::$_stmt->getColumnMeta($i);
        self::$_columns[$column['name']] = $column;
      }

      self::$_affected_rows = self::$_stmt->rowCount();

//    } catch (Exception $e) {
//      self::$_rows = NULL;
//    }

    return self::$_stmt;
  }

  public static function register_migration($version, Migration $migrationInstance) {
    self::$migrations[$version] = $migrationInstance; 
  }

  public static function migrate($version) {
    if (!isset(self::$migrations[$version])) {
      throw new Exception("DB Migration Version Does Not Exist: {$version}");
    }

    $migration_status = NULL;

    echo "\n[MIGRATION:{$version}] Running...";

    try {
      $migration_status = self::$migrations[$version]->up();
    } catch (Exception $e) {
      $migration_status = FALSE;
      echo "\n" . $e->getMessage();
    }

    if ($migration_status === TRUE) {
      echo "\nSUCCESS [MIGRATION:{$version}]";
    } else {
      echo "\nFAILED [MIGRATION:{$version}]";

      self::rollback($version);
    }
  }

  public static function rollback($version) {
    if (!isset(self::$migrations[$version])) {
      throw new Exception("DB Rollback Version Does Not Exist: {$version}");
    }

    $rollback_status = NULL;

    echo "\n[ROLLBACK:{$version}] Running...";

    try {
      $rollback_status = self::$migrations[$version]->down();
    } catch (Exception $e) {
      echo "\n" . $e->getMessage();
    }

    if ($rollback_status === TRUE) {
      echo "\nSUCCESS [ROLLBACK:{$version}]";
    } else {
      echo "\nFAILED [ROLLBACK:{$version}]";
    }
  }
}

abstract class Migration
{
  abstract public function up();

  abstract public function down();
}



class MigrationCreatePostsTable extends Migration
{
  public function up() {
    Db::sql("CREATE TABLE posts (id INT(11) AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255) NOT NULL, body TEXT DEFAULT NULL)");
    Db::sql("INSERT INTO posts (name, body) VALUES ('Test 1', 'Test 1 Post.'), ('Test 2', 'Test 2 Post.')");

    return TRUE;
  }

  public function down() {
    Db::sql("DROP TABLE posts");

    return TRUE;
  }
}

$migrationCreatePostsTable = new MigrationCreatePostsTable();
Db::register_migration("version_1", $migrationCreatePostsTable);
