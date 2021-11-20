<?php
  session_start();
  require_once("conn.php");
  require_once("utils.php");

  if (!empty($_GET["uid"])) {
    $user_id = $_GET["uid"];
  }


  $sql = "SELECT * FROM HLW_board_users
          WHERE id=?";
          
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $user_id);
  $result = $stmt->execute();
  if (!$result) {
    die("database error(index.php)".$conn->connect_error);
  }
  
  $result = $stmt->get_result();
  $row = $result->fetch_assoc();
  
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='./style.css' rel='stylesheet' />
    <title>Bulletin</title>
</head>

<body>
    <div class='board'>
        <div class='login_and_register_btn'>
            <a class='board_btn' href='admin.php'>Back</a>
        </div>
        <section class='leave_comments'>
            <h1>Change memeber's authority type</h1>
        </section>
        <div class='users_display'>
            <div class='users'>
                <div class='user_detail'>
                    <div class='user_info'>
                        <div class='user_id'>
                            ID: <?php echo escape($row["id"]);?>
                        </div>
                        <div class='username'>
                            account： <?php echo escape($row["username"]);?>
                        </div>
                        <div class='user_auth'>
                            <?php if (escape($row["auth_type"]) == 1) { ?>
                            authority type：<?php echo "member";?>
                            <?php } else if (escape($row["auth_type"]) == 2) { ?>
                            authority type：<?php echo "admin";?>
                            <?php } else if  (escape($row["auth_type"]) == 3) { ?>
                            authority type：<?php echo "unauthorized member";?>
                            <?php } ?>
                        </div>

                        <form class='update_user_auth_form' method='POST' action='handle_update_user_auth.php'>
                            <div> Change authority type：</div>
                            <div><label><input name='user_auth' value='1' type='radio' checked> member</input></label>
                            </div>
                            <div><label><input name='user_auth' value='2' type='radio'> admin</input></label></div>
                            <div><label><input name='user_auth' value='3' type='radio'> unauthorized
                                    member</input></label></div>
                            <input type='hidden' name='uid' value='<?php echo $user_id?>' />
                            <br>
                            <input class='btn_comment_submit' type='submit' name='comment_submit' value='submit'>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>