<?php
  // Userland implementation of random_bytes, php version is 5.45 and random_bytes
  // was introduced in php 7.
  require_once "ext/random_compat/lib/random.php";

  header('Content-Type: text/html; charset=utf-8');

  // https://stackoverflow.com/a/15875555/5272567
  function format_uuidv4($data) {
    assert(strlen($data) == 16);

    $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
      
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
  }

  $errors = array();
  try {
    $conn = new PDO(/* here is some private information about connecting to the database! */);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  } catch(PDOException $e) {
    $errors[] = 'Connection failed: ' . $e->getMessage();
  }

  // only do all our stuff on a successful connection!!!
  if (sizeof($errors) === 0) {
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method === 'POST') {
      $data = json_decode(file_get_contents('php://input'), true);
      $results = array();

      if (!$data['unique_id']) {
        // Generate unique id and insert high score, return that unique id to js code
        $uuid = format_uuidv4(random_bytes(16));

        $query = $conn->prepare(
          'INSERT INTO high_scores (name, score, deaths, date, unique_id) ' .
          'VALUES (:name, :score, :deaths, :date, :unique_id)'
        );

        $result = $query->execute(array(
          'name' => $data['name'],
          'score' => $data['score'],
          'deaths' => $data['deaths'],
          'date' => $data['date'],
          'unique_id' => $uuid,
        ));

        // Print this for the javascript code
        echo $uuid;
      } else {
        // We already have a unique_id, modify database entry with that id
        $query = $conn->prepare(
          'UPDATE high_scores SET score = :score, deaths = :deaths, date = :date ' .
          'WHERE name = :name AND unique_id = :unique_id'
        );
        $result = $query->execute(array(
          'name' => $data['name'],
          'score' => $data['score'],
          'deaths' => $data['deaths'],
          'date' => $data['date'],
          'unique_id' => $data['unique_id'],
        ));
      }

      if (!$result) {
        $errors[] = 'Failed high score insert.';
      }
    } elseif ($method === 'GET') {
      // Display the high scores, top 50.
      $stmt = $conn->prepare(
        'SELECT * FROM high_scores ' .
        'ORDER BY score DESC ' .
        'LIMIT 50'
      );
      $stmt->execute();
      $results = $stmt->fetchAll();

      if (sizeof($results) === 0) {
        $errors[] = 'Failed to fetch high scores.';
      } else {
        echo '<ul class="highscore">';
        echo '<li><p>Name</p><p>Score</p><p>Deaths</p><p>Date</p></li>';
        echo '<hr />';
        foreach ($results as $result) {
          echo '<li>';
          echo  '<p>', $result['name'], '</p>',
                '<p>', $result['score'], '</p>',
                '<p>', $result['deaths'], '</p>',
                '<p>', $result['date'], '</p>';
          echo '</li>';
        }
        echo '</ul>';
      }
    }
  }

  if (sizeof($errors) !== 0) {
    header('HTTP/1.1 500 Internal Server Error');
    print_r($errors);
  }
?>
