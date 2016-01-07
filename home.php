<?php
  session_start();
  if(!isset($_SESSION['user'])) {
    header("Location: index.php");
  }
  require_once 'inc/dbconnect.php';
  include_once 'inc/functions.php';
  $pageTitle = "Guestbook Home Page";
  include_once 'inc/head.php';

  $query = "SELECT * FROM users WHERE user_id=" . $_SESSION['user'];
  $result = queryMysql($query);
  $row = $result->fetch_array();

  if(isset($_POST['submit'])) {
    $userMessage = sanitizeString($_POST['usermessage']);
    $userId = $_SESSION['user'];
    $currentTime = time();

    $query = "INSERT INTO messages (user_id, user_message, message_date) VALUES('$userId', '$userMessage', '$currentTime')";
    $result = queryMysql($query);
  }
?>
    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <a href="#" class="navbar-brand">GuestBook</a>
        </div>
        <div class="pull-right">
          <ul class="navbar-nav nav">
            <li>Hi there! You're currently logged as <span class="user-name"><?php echo $row['username']; ?></span>&nbsp;<a href="logout.php?logout">Sign Out</a></li>
          </ul>
        </div>
      </div>
    </div>

    <div class="container container-message">
      <div class="row">
        <div class="col-xs-12 col-md-6 col-md-offset-3">
          <form method="post">
            <div class="form-group">
              <label for="message">Message:</label>
              <textarea id="message" class="form-control" rows="10" cols="40" name="usermessage" required></textarea>
            </div>
            <input type="submit" name="submit" class="btn btn-success btn-lg" value="Post">
          </form>
    
          <?php
            $query = "SELECT * FROM messages";
            $result = queryMysql($query);
            $rows = $result->num_rows;

            if($rows) {
              $query = "SELECT * FROM users JOIN messages ON users.user_id=messages.user_id ORDER BY message_date";
              $result = queryMysql($query);
              $rows = $result->num_rows;
              for($j = 0; $j < $rows; $j++) {
                $result->data_seek($j);
                $chunk_row = $result->fetch_array(MYSQLI_ASSOC);

                if($chunk_row['is_visible']) {
                  $date_submitted = date("jS F Y", $chunk_row['message_date']);
                  echo <<< _END
                  <div class="user-post">
                  <p>
                    <strong>Posted by <a href="mailto:{$chunk_row['email']}">{$chunk_row['username']}</a> on $date_submitted</strong>
                  </p>
                  <p>
                    {$chunk_row['user_message']}
                  </p>
                  </div>
_END;
                }
              } 
            }
          ?>
        </div>
      </div>
    </div>

<?php include_once 'inc/foot.php'; ?>