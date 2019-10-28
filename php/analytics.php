<?php
  header('Content-Type: application/json;charset=utf-8');

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
      // decide what to do depending on the type of post request we got
      switch ($data['type']) {
        case 'pageload':
          // user loaded the scripts, receive country and user agent to save
          $query = $conn->prepare('UPDATE general_stats SET count = count + 1 WHERE label LIKE \'pageloads\'');
          $results[] = $query->execute();

          $query = $conn->prepare(
            'INSERT INTO countries (country, count) ' .
            'VALUES (:country, count) ' .
            'ON DUPLICATE KEY UPDATE count = count + 1'
          );
          $results[] = $query->execute(array('country' => $data['country']));

          $query = $conn->prepare(
            'INSERT INTO cities (city, count) ' .
            'VALUES (:city, count) ' .
            'ON DUPLICATE KEY UPDATE count = count + 1'
          );
          $results[] = $query->execute(array('city' => $data['city']));

          $query = $conn->prepare(
            'INSERT INTO user_agents (user_agent, count) ' .
            'VALUES (:user_agent, count) ' .
            'ON DUPLICATE KEY UPDATE count = count + 1'
          );
          $results[] = $query->execute(array('user_agent' => $data['user_agent']));
          break;
        case 'download':
          // song or songs was downloaded
          if ($data['song']) {
            $query = $conn->prepare(
              'INSERT INTO song_downloads (song, count) ' .
              'VALUES (:song, count) ' .
              'ON DUPLICATE KEY UPDATE count = count + 1'
            );
            $results[] = $query->execute(array('song' => $data['song']));
          }
          
          $query = $conn->prepare(
            'UPDATE general_stats SET count = count + :number WHERE label LIKE \'songs_downloaded\''
          );
          $results[] = $query->execute(array('number' => $data['number']));
          break;
        case 'milestone':
          // over 50 songs, over 100 songs or beat the game
          $query = $conn->prepare('UPDATE general_stats SET count = count + 1 WHERE label LIKE :value');
          $results[] = $query->execute(array('value' => $data['value']));
          break;
        case 'code':
          // user typed a code into the Enter code in settings (send every code for fun and keepsakes)
          $query = $conn->prepare(
            'INSERT INTO codes_typed (code, count) ' .
            'VALUES (:code, count) ' .
            'ON DUPLICATE KEY UPDATE count = count + 1'
          );
          $results[] = $query->execute(array('code' => $data['code']));
          break;
      }

      if (sizeof($results) === 0) {
        $errors[] = 'Failed insert of type: ' . $data['type'];
      } else {
        echo json_encode($results);
      }
    }
  }

  if (sizeof($errors) !== 0) {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode($errors);
  }
?>