<?php
  session_start();
  require_once("conn.php");
  require_once("utils.php");

  $user_id = NULL;
  if(!empty($_SESSION["user_id"])) {
    $user_id = $_SESSION["user_id"];
    $user_info = getUserInfoFromUserId($user_id);
    $user_auth = $user_info["auth_type"];
  }

  $id = $_GET["id"];
  $user_id = $_SESSION["user_id"];

  if ($user_auth === 2) {
    $sql = "SELECT * FROM HLW_board_comments WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
  } else {
    $sql = "SELECT * FROM HLW_board_comments WHERE id=? AND `user_id`=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id, $user_id);
  }
  
  $result = $stmt->execute();
  if (!$result) {
    die("資料庫錯誤(index.php)".$conn->connect_error);
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
        <section class='leave_comments'>
            <h1>Edit comment</h1>
            <?php if ($user_id) {?>
            <form class='leave_comment_form' method='POST' action='handle_update_comment.php'>
                <textarea name='content' rows='5' placeholder='please leave your comment...><?php
          if(!empty($row["content"])){echo escape($row["content"]);}
          ?></textarea>
                <input type=' hidden' name='id' value='<?php echo $row["id"];?>' />
                <div class='submit'>
                    <input class='btn_comment_submit' type='submit' name='comment_submit' value='confirm'>
                    <?php
              if (!empty($_GET["errCode"])) {
                $code = $_GET["errCode"];
                if ($code === '7') {
                  $errMsg = "Information missing, please input the edited comment";
                  echo "<h3 class='error'>Error:".$errMsg."</h3>";
                }
              }
            ?>
                </div class='submit'>
            </form>
            <?php } else { ?>
            <h3>Please log in to leave your comments.</h3>
            <?php } ?>
        </section>
    </div>
    </div>
</body>

</html>