<?php

require_once("config.php");

class TestSuite
{
  private $_testCases = [];

  public function register($testCaseName, TestCase $testCaseInstance) {
    $this->_testCases[$testCaseName] = $testCaseInstance;
  }

  public function execute() {
    foreach ($this->_testCases as $testCaseName => $testCaseInstance) {
      $result = NULL;
      echo "\n[CASE:{$testCaseName}] Running... ";

      try {
        $result = $testCaseInstance->run();
      } catch (Exception $e) {
        $result = NULL;
      }

      if (!$result) {
        echo "\nFAILED [CASE:{$testCaseName}] ";
        exit(1);
      }

      echo "\nSUCCESS [CASE:{$testCaseName}] ";
      var_export($result);
    }

   exit(0); 
  }
}



abstract class TestCase
{
  private function up() {
    // this code will do the processing before the test is executed. Normally that is for creating the test data in DB, etc.
  }

  private function down() {
    // this code will do the processing after the test is executed. Normally that is for cleaning the test data in DB, etc.
  }

  abstract public function run();

  public function get($url) {
    $req = curl_init($url);
    curl_setopt($req, CURLOPT_RETURNTRANSFER, TRUE);
    $response = curl_exec($req);
    $info = curl_getinfo($req);
    curl_close($req);
    return $response;
  }
}



class TestDbVersion extends TestCase
{
  public function run() {
    $page = $this->get(SERVER_HOST_URL . SERVER_HOST_PATH_HOME);
    $json = json_decode($page, TRUE);
    return is_array($json) && count($json) === 2;
  }
}



$testSuite = new TestSuite();
$testDbVersion = new TestDbVersion();
$testSuite->register("There should be 2 posts in JSON object printed at the index page", $testDbVersion);
//class TestBlank extends TestCase { public function run() { return TRUE; } } $testBlank = new TestBlank(); $testSuite->register("Blank", $testBlank);
$testSuite->execute();
