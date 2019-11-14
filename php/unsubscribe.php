<?php

  $secret_code = "a secret !";

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
    if ($method === 'GET') {
      $email = $_GET['email'];

      // first test to see if the hash from the unsubscribe request matches our hash
      $hashvalue = hash('sha256', $secret_code.$email);

      // only proceed if they match
      if ($email && $hashvalue == $_GET['emailHash']) {
        $results = array();

        $query = $conn->prepare(
          'UPDATE emails ' .
          'SET deleted = "Yes" ' .
          'WHERE email = :email'
        );
        $results[] = $query->execute(array('email' => $email));

        if (sizeof($results) === 0) {
          echo 'Failed to unsubscribe email! Sorry try again, or just contact me directly, I\'ll remove you from the list. <a href="/">Back</a>';
        } else {
          echo 'You have been unsubscribed from my email list! :\'( Sorry to see you go. <a href="/">Back</a>';
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