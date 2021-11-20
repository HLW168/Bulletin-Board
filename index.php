<?php
  session_start();
  require_once("conn.php");
  require_once("utils.php");

  $page = 1;
  if (!empty($_GET["page"])) {
    $page = $_GET["page"];
  }
  $comment_per_page = 5;
  $offset = ($page - 1 )* $comment_per_page;
  
  $sql = "SELECT c.id AS 'id',u.id AS 'user_id',
          u.nickname AS 'nickname', 
          u.username AS 'username', c.content AS 'content', c.created_at AS 'created_at' 
          FROM HLW_board_comments AS c 
          LEFT JOIN HLW_board_users AS u ON u.id = c.user_id
          WHERE c.is_deleted IS NULL 
          ORDER BY c.id DESC
          LIMIT ?
          OFFSET ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ii", $comment_per_page, $offset);
  $result = $stmt->execute();
  if (!$result) {
    die("資料庫錯誤(index.php)".$conn->connect_error);
  }
  
  $result = $stmt->get_result();
  
  $user_id = NULL;
  $user_auth = 1;
  if(!empty($_SESSION["user_id"])) {
    $user_id = $_SESSION["user_id"];
    $user_info = getUserInfoFromUserId($user_id);
    $user_auth = $user_info["auth_type"];
  }

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='./style.css' rel='stylesheet' />
    <title>Bulletin Board</title>
</head>

<body>
    <div class='board'>
        <div class='login_and_register_btn'>
            <?php if (!$user_id) {?>
            <a class='board_btn' href='register.php'>Sign up</a>
            <a class='board_btn' href='login.php'>Log in</a>
            <?php } else { ?>
            <?php if ($user_auth === 2) {?>
            <a class='board_btn' href='admin.php'>Management</a>
            <?php }?>
            <a class='board_btn' href='logout.php'>Log out</a>
            <span class='board_btn update_nickname_btn'> Edit nickname </span>
            <?php } ?>
        </div>
        <section class='leave_comments'>
            <h1>Comments</h1>
            <?php if ($user_id && $user_auth !== 3) {?>
            <div class='say_hello'>Hello, <?php echo escape($user_info["nickname"]) ?>!</div>
            <form class='update_nickname hide' method='POST' action='handle_update_nickname.php'>
                <div>Please enter your new nickname：<input class='new_nickname' type='text' name='nickname'></div>
                <input class='btn_comment_submit' type='submit' name='comment_submit' value='Update'>
                <?php
              if (!empty($_GET["errCode"])) {
                $code = $_GET["errCode"];
                if ($code === '5') {
                  $errMsg = "Information missing, please input your nickname";
                  echo "<h3 class='error'>Error:".$errMsg."</h3>";
                }
              }
            ?>
            </form>
            <form class='leave_comment_form' method='POST' action='handle_add_comment.php'>
                <textarea name='content' rows='5' placeholder='Please leave your comment'></textarea>
                <div class='submit'>
                    <input class='btn_comment_submit' type='submit' name='comment_submit' value='Submit'>
                    <?php
              if (!empty($_GET["errCode"])) {
                $code = $_GET["errCode"];
                if ($code === '1') {
                  $errMsg = "Information missing, please input your nickname and content";
                  echo "<h3 class='error'>Error:".$errMsg."</h3>";
                }
              }
            ?>
                </div class='submit'>
            </form>
            <?php } else if ($user_id && $user_auth == 3)  { ?>
            <h3>You are not allowed to add comments.</h3>
            <?php } else { ?>
            <h3>Please log in to leave your comments.</h3>
            <?php } ?>
            <div class='divider'></div>
        </section>
        <div class='comment_display'>
            <?php 
        while ($row = $result->fetch_assoc()) {?>
            <div class='comments'>
                <div class='comment_photo'>
                </div>
                <div class='comment_detail'>
                    <div class='comment_info'>
                        <div class='comment_nickname'>
                            <?php echo escape($row['nickname']);?>
                            (@<?php echo escape($row['username']);?>)
                        </div>
                        <div class='comment_created_at'>
                            <?php echo $row['created_at'];?>
                        </div>
                        <?php if($row["user_id"] === $user_id || $user_auth === 2) { ?>
                        <div class='modify_comment'>
                            <a href='update_comment.php?id=<?php echo escape($row["id"]);?>'>Edit</a>
                            <a href='delete_comment.php?id=<?php echo escape($row["id"]);?>'>Delete</a>
                        </div>
                        <?php } ?>
                    </div>
                    <div class='comment_content'><?php echo escape($row['content']);?></div>
                </div>
            </div>
            <?php }?>
        </div>
        <div class='page-info'>
            <?php
                $sql = "SELECT count(id) AS 'count' FROM HLW_board_comments WHERE is_deleted IS NULL";
                $stmt = $conn->prepare($sql);
                $result = $stmt->execute();
                if (!$result) {
                die("database error(index.php)".$conn->connect_error);
                }
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();

                $count = $row["count"];
                $total_page = ceil($count / $comment_per_page);
                ?>
            <div class='page-detail'>
                Total <?php echo $count?> comment(s)，and you are on page <?php echo $page?>
            </div>
            <div class='paginator'>
                <?php
          if ($page != 1) { ?>
                <a href="index.php?page=1">
                    << </a>
                        <a href="index.php?page=<?php echo ($page - 1) ?>">
                            < </a>
                                <?php }
        ?>
                                <div> <?php echo $page ?> </div>
                                <?php
          if ($page != $total_page) { ?>
                                <a href="index.php?page=<?php echo ($page + 1) ?>">></a>
                                <a href="index.php?page=<?php echo $total_page ?>">>></a>
                                <?php }
        ?>
            </div>
        </div>
    </div>
    <script>
    const form = document.querySelector('.update_nickname_btn');
    form.addEventListener('click', (e) => {
        document.querySelector('.update_nickname').classList.toggle('hide');
    })
    </script>
</body>

</html>