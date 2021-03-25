<?php

  // User has to solve this before email is submitted
  $spamprotectionhtml = '
  <!doctype html>
  <html>
  <head>
    <meta charset="utf-8">
  </head>
  <body>
    <form action="/php/emaillist.php?gnaften=SUBMITTEDEMAIL" method="post" id="emaillist_form">
      <label for="answer">What\'s the first letter of the word Eyjafjallajökull?</label>
      <input type="text" id="answer" name="answer" size="18" required>
      <button type="submit" id="emaillist_button">Submit!</button>
    </form>
  </body>
  </html>
  ';

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
      if (!$email) {
        // On spam validation email is sent as url variable, get it here
        $email = $_GET['gnaften'];
      }      

      if ($email) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
          // For spam protection, have user answer a question, answer is 'E' or 'e'
          // (what's the first letter of Eyjafjallajökull)
          $answer = $_POST['answer'];
          if ($answer && ($answer == 'e' || $answer == 'E')) {
            // Passed the question, correct answer
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
            // Wrong answer or first time submitting email, so prompt for verification
            echo str_replace('SUBMITTEDEMAIL', $email, $spamprotectionhtml);
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