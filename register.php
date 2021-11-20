<?php
  require_once("conn.php");
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='./style.css' rel='stylesheet' />
    <title>Bulletin Board - Signup</title>
</head>

<body>
    <div class='board'>
        <div class='login_and_register_btn'>
            <a class='board_btn' href='index.php'>Back</a>
            <a class='board_btn' href='login.php'>Log in</a>
        </div>
        <section class='leave_comments'>
            <h1>Sign up</h1>
            <form class='leave_comment_form' method='POST' action='handle_register.php'>
                <div class='input_block'>
                    <span>nickname： </span>
                    <input type='text' name='nickname' />
                </div>
                <div class='input_block'>
                    <span>account： </span>
                    <input type='text' name='username' />
                </div>
                <div class='input_block'>
                    <span>password： </span>
                    <input type='password' name='password' />
                </div>
                <div class='submit'>
                    <input class='btn_comment_submit' type='submit' name='comment_submit' value='submit'>
                    <?php
            if (!empty($_GET["errCode"])) {
              $code = $_GET["errCode"];
              if ($code === '1') {
                $errMsg = "Information missing, please input your nickname, account, and password";
              } else if ($code === '2') {
                $errMsg = "The account has been used, please try again!";
              }
              echo "<h3 class='error'>Error:".$errMsg."</h3>";
            }
          ?>
                </div class='submit'>
            </form>
        </section>
    </div>
    </div>
</body>

</html>