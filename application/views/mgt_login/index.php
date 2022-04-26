<!DOCTYPE html>
<html lang="zh-Hant-TW">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta http-equiv="expires" content="0">
        <meta http-equiv="cache-control" content="no-cache">
        <?php
        $run = strtotime(date("Y-m-d H:i:s"));
        //引入css
        echo link_tag('appoint/css/common_style.css?run=' . $run);
        echo link_tag('appoint/css/style.css?run=' . $run);
        ?>

        <!-- 引入js 套件 -->
        <script type="text/javascript" src="<?php echo base_url(); ?>appoint/javascript/vendor/jquery-3.1.1.min.js"></script>
        <script type="text/javascript" src="<?php echo base_url(); ?>appoint/javascript/vendor/jquery-1.12.4.js" crossorigin="anonymous"></script>

        <title>電子名片後台系統</title>
    </head>
    <input type="hidden" id="base_url" value="<?php echo base_url(); ?>">
    <input type="hidden" id="login" value="<?php echo $login; ?>">
    <body class="loginBody">
        <div class="loginTitle">電子名片後台系統</div>
        <div class="loginBox">
        <label for="account">帳號</label><input type="text" id="account" maxlength="30"><br />
        <label for="password">密碼</label><input type="password" id="password" maxlength="30"><br />
        <div class="alertMsg" id="alertMsg"></div>
        <input type="button" class="width_60px" value="登入" onclick="login();"/>
        </div>
    </body>
    <script type="text/javascript" src='<?php echo base_url();?>appoint/javascript/common_function.js'></script>
    <script type="text/javascript" src='<?php echo base_url();?>appoint/javascript/login.js'></script>
    <script>
        var login_state = document.getElementById('login').value;
        if(login_state == '1'){
            check_login();
        }
    </script>
</html>