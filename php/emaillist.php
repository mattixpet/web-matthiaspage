<?php

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
      $email = $_POST['email'];
      if ($email) {
        $results = array();

        $query = $conn->prepare(
          'INSERT INTO emails (email) ' .
          'VALUES (:email) ' .
          'ON DUPLICATE KEY UPDATE id = id'
        );
        $results[] = $query->execute(array('email' => $email));

        if (sizeof($results) === 0) {
          echo 'Failed to add email! Sorry try again, or just contact me directly, I\'ll add you to the list. <a href="/">Back</a>';
        } else {
          echo 'Thank you! You have been added to my email list! :D <a href="/">Back</a>';
        }
      } else {
        echo 'Something went wrong. :( <a href="/">Back</a>';
      }
    }
  }

  if (sizeof($errors) !== 0) {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode($errors);
  }

?>