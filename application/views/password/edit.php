<!-- 表單欄位填寫區塊 -->
<div class="contentsBox">
    <div class="contentsTitle blue">管理員密碼修改</div>
    <div class="ceContent width_80pc margin_auto">
        <div class="textCenter">
            <div>
                <div class="label width_120px line_h_40px">
                    <div class="requiredTag">*</div><label>舊密碼：</label>
                </div>
                <div class="labelInput"><input type="password" class="width_220px" id="password_old" maxlength="20" autocomplete="off" required></div>
            </div>
            <div>
                <div class="label width_120px line_h_40px">
                    <div class="requiredTag">*</div><label>新密碼：</label>
                </div>
                <div class="labelInput"><input type="password" class="width_220px" id="password_new" maxlength="20" autocomplete="off" required></div>
            </div>
            <div>
                <div class="label width_120px line_h_40px">
                    <div class="requiredTag">*</div><label>確認密碼：</label>
                </div>
                <div class="labelInput"><input type="password" class="width_220px" id="check_password" maxlength="20" autocomplete="off" required></div>
            </div>
            <div class="alertMsg" id="alertMsg"></div>
        </div>
    </div>
</div>
<div class="btnBox">
    <button class='button width_60px' onclick='update_password()'>送出</button>
    <button class='button width_60px' onclick="gotoPage('users/index')">取消</button>
</div>

<script type="text/javascript" src='<?php echo base_url(); ?>appoint/javascript/common_function.js'></script>
<script>
    // 管理員密碼修改
    function update_password() {
        var password_old = document.getElementById('password_old').value;
        var password_new = document.getElementById('password_new').value;
        var check_password = document.getElementById('check_password').value;
        var data_obj = {
            password_old: password_old,
            password_new: password_new,
            check_password: check_password,
        };
        var result = call_api('mgt_users_api/update_password', data_obj);
        console.log(result);
        document.getElementById("alertMsg").innerHTML = result['msg'];
    }
</script>