<?php
  $errors = array();
  try {
    $conn = new PDO(/* here is some private information about connecting to the database! */);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  } catch(PDOException $e) {
    $errors[] = 'Connection failed: ' . $e->getMessage();
  }

  function displayTableData($table, $conn) {
    $query = $conn->prepare('SELECT * FROM ' . $table);
    $query->execute();
    $results = $query->fetchAll();

    echo $table;
    echo '<br>';
    if (sizeof($results) === 0) {
      echo 'No data found.';
      echo '<br><br>';
      return false;
    } else {
      foreach ($results as $result) {
          // always only 2 columns, usually slomething like label and count
          echo $result[0] . ': ' . $result[1];
          echo '<br>';
      }
    }

    echo '<br>';
    return true;
  }

  // only do all our stuff on a successful connection!!!
  if (sizeof($errors) === 0) {
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method === 'GET') {
      // just grab all the info from all the tables we have and output it fairly nicely
      $tables = array( 'general_stats', 'countries', 'cities', 'song_downloads', 'codes_typed', 'user_agents');
      foreach ($tables as $table) {
        displayTableData($table, $conn);
      }
    }
  } else {
    echo 'Cannot connect to database.';
  }
?>