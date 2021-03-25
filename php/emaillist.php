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
      // gnaften = netfang backwards, spam protection
      $email = $_POST['gnaften'];
      if ($email) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
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
          $query = $conn->prepare(
            'INSERT INTO wrong_format_emails (email) ' .
            'VALUES (:email) ' .
            'ON DUPLICATE KEY UPDATE id = id'
          );
          $query->execute(array('email' => $email));

          echo 'Email was on wrong format, please try again. :( <a href="/">Back</a>';
        }
      } else {
        echo 'Something went wrong. :( <a href="/">Back</a>';
      }

      $spam = $_POST['email'];
      if ($spam) {
        $query = $conn->prepare(
          'INSERT INTO known_spam (email) ' .
          'VALUES (:email) ' .
          'ON DUPLICATE KEY UPDATE id = id'
        );
        $query->execute(array('email' => $spam));

        echo 'Nice spam bro. Or if you are not a spammer, sorry the real form input is the text field, not the hidden email field. <a href="/">Back</a>';
      }
    }
  }

  if (sizeof($errors) !== 0) {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode($errors);
  }

?>