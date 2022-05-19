<!DOCTYPE html>
<html lang="zh-Hant-TW">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="expires" content="0">
    <meta http-equiv="cache-control" content="no-cache">
    <?php
    echo link_tag('appoint/bootstrap4/dist/css/bootstrap.min.css?');
    echo link_tag('appoint/tempusdominus-bootstrap-4/build/css/tempusdominus-bootstrap-4.min.css');
    ?>
    <?php
    $run = strtotime(date("Y-m-d H:i:s"));

    // 引入sidebars CSS
    echo link_tag('appoint/css/vendor/bootstrap.min.css?run=' . $run);
    echo link_tag('appoint/css/vendor/sidebars.css?run=' . $run);

    //引入css
    echo link_tag('appoint/css/common_style.css?run=' . $run);
    echo link_tag('appoint/css/style.css?run=' . $run);
    //網頁icon
    echo link_tag('appoint/images/webicon.png', 'icon', 'image/x-icon');
    ?>    
    <!-- 引入js 套件 -->
    <script type="text/javascript" src="<?php echo base_url(); ?>appoint/javascript/vendor/jquery-3.6.0.min.js"></script>
    <script src="<?php echo base_url(); ?>appoint/javascript/vendor/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="<?php echo base_url(); ?>appoint/javascript/vendor/sidebars.js"></script>

    <!-- tempusdominus-bootstrap-4 套件 -->
    <script src="<?php echo base_url(); ?>appoint/javascript/vendor/tempusdominus-bootstrap-4/moment.min.js"></script>
    <script src="<?php echo base_url(); ?>appoint/tempusdominus-bootstrap-4/build/js/tempusdominus-bootstrap-4.min.js"></script>
    <script src="<?php echo base_url(); ?>appoint/bootstrap4/js/bootstrap.min.js"></script>
    <?php
    echo link_tag('appoint/tempusdominus-bootstrap-4/build/css/tempusdominus-bootstrap-4.min.css');
    echo link_tag('appoint/fontawesome-free-5.15.4-web/css/all.css');
    ?>
    <title>電子名片後台系統</title>
</head>
<body id="container">
    <input type="hidden" id="base_url" value="<?php echo base_url(); ?>">
    <input type="hidden" id="SOCIAL_ICON_PATH" value="<?php echo SOCIAL_ICON_PATH; ?>">
    <input type="hidden" id="SUBJECT_IMAGE_PATH" value="<?php echo SUBJECT_IMAGE_PATH; ?>">
    <input type="hidden" id="AVATAR_PATH" value="<?php echo AVATAR_PATH; ?>">
    <input type="hidden" id="LOGO_PATH" value="<?php echo LOGO_PATH; ?>">
    <div class="menu">
        <a href="<?php echo base_url("users/index"); ?>" class="d-flex align-items-center pb-3 mb-3 link-dark text-decoration-none border-bottom test">
            <svg class="bi me-2" width="10" height="24"></svg>
            <span class="fs-5 fw-semibold">電子名片後台系統</span>
        </a>
        <ul class="list-unstyled ps-0">
            <li class="mb-1">
            <button class="btn btn-toggle align-items-center rounded collapsed" data-bs-toggle="collapse" data-bs-target="#users-collapse" aria-expanded="false">
                用戶管理
            </button>
            <div class="collapse" id="users-collapse">
                <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                <li><a href="<?php echo base_url("users/index"); ?>" class="link-dark rounded">會員帳號查詢</a></li>
                </ul>
            </div>
            </li>
            <li class="mb-1">
            <button class="btn btn-toggle align-items-center rounded collapsed" data-bs-toggle="collapse" data-bs-target="#mgt_subject-collapse" aria-expanded="false">
                主題管理
            </button>
            <div class="collapse" id="mgt_subject-collapse">
                <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                <li><a href="<?php echo base_url("mgt_subject/add"); ?>" class="link-dark rounded">主題新增上傳</a></li>
                <li><a href="<?php echo base_url("mgt_subject/index"); ?>" class="link-dark rounded">主題維護</a></li>
                </ul>
            </div>
            </li>
            
            <li class="border-top my-3"></li>
            <li class="mb-1">
            <button class="btn btn-toggle align-items-center rounded collapsed" data-bs-toggle="collapse" data-bs-target="#password-collapse" aria-expanded="false">
            系統設定
            </button>
            <div class="collapse" id="password-collapse">
                <ul class="btn-toggle-nav list-unstyled fw-normal pb-1 small">
                    <li><a onclick="modal_show('confirmPasswordModal');" class="link-dark rounded">管理員密碼修改</a></li>
                    <li><a onclick="modal_show('confirmLogoutModal');" class="link-dark rounded cursor_pointer">登出</a></li>
                </ul>
            </div>
            </li>
        </ul>
    </div>

    <div class="content">
        <input type="hidden" id="bar_btn_statu" value="0">
        <button class="bar_btn"><i class="fas fa-chevron-right fa-lg"></i></button>
        <div class="minBodyHeight">
            <div class="navUserAccount">
                登入帳號：<?php echo $this->session->mgt_user_info['account']; ?>
            </div>

            <!-- Confirm Modal -->
            <div class="modal fade" id="confirmPasswordModal" tabindex="-1" role="dialog" aria-labelledby="confirm_password_modal_label" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confirm_password_modal_label">系統訊息</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="modal_hide('confirmPasswordModal');">&times;</button>
                        </div>
                        <div class="modal-body textCenter" id="confirm_password_model_body">管理者是否重設密碼</div>
                        <div class="btnBox">
                            <button type="button" class="button width_60px" data-dismiss="modal" onclick="gotohref('<?php echo base_url('password/edit'); ?>')">確定</button>
                            <button type="button" class="button width_60px" data-dismiss="modal" onclick="modal_hide('confirmPasswordModal');">取消</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Logout Modal -->
            <div class="modal fade" id="confirmLogoutModal" tabindex="-1" role="dialog" aria-labelledby="confirm_logout_modal_label" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confirm_logout_modal_label">系統訊息</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="modal_hide('confirmLogoutModal');">&times;</button>
                        </div>
                        <div class="modal-body textCenter" id="confirm_logout_model_body">是否確定登出系統?</div>
                        <div class="btnBox">
                            <button type="button" class="button width_60px" data-dismiss="modal" onclick="gotohref('<?php echo base_url('mgt_login/logout'); ?>')">確定</button>
                            <button type="button" class="button width_60px" data-dismiss="modal" onclick="modal_hide('confirmLogoutModal');">取消</button>
                        </div>
                    </div>
                </div>
            </div>
    <script>
        localStorage.setItem('token', '<?php echo $this->session->mgt_user_info['token']; ?>');
        localStorage.setItem('UsertId', '<?php echo $this->session->mgt_user_info['id']; ?>');

        function gotohref(url){
            document.location.href = url;
        }

        $(function () {
            $(".bar_btn").click(function () {
                $(".menu").toggleClass("active");
                $(".fa-chevron-right").toggleClass("rotate");
                var bar_btn_statu = document.getElementById("bar_btn_statu");
                var content = document.querySelector('.content');
                if(bar_btn_statu.value == '0'){
                    content.style.left = "0";
                    content.style.width = "100%";
                    bar_btn_statu.value = '1';
                }else{
                    content.style.left = "200px";
                    content.style.width = "calc(100% - 200px)";
                    bar_btn_statu.value = '0';
                }
            });
        });
    </script>
</body>