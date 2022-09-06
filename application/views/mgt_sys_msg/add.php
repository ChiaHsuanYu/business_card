<!-- 表單欄位填寫區塊 -->
<div class="contentsBox">
    <div class="contentsTitle blue">新增系統通知訊息</div>
    <div class="ceContent width_80pc margin_auto">
        <div class="textCenter">
            <div>
                <div class="label width_120px line_h_40px">
                    <div class="requiredTag">*</div><label>系統通知標題：</label>
                </div>
                <div class="labelInput padding_top_5"><input type="text" class="width_350px" id="title" maxlength="20" autocomplete="off" required></div>
            </div>
            <div>
                <div class="label width_120px line_h_40px">
                    <div class="requiredTag">*</div><label>系統通知訊息：</label>
                </div>
                <div class="labelInput padding_top_5"><textarea id="msg" class="width_350px" rows="3"></textarea></div>
            </div>
            <div class="alertMsg" id="alertMsg"></div>
        </div>
    </div>
</div>
<div class="btnBox">
    <button class='button width_60px' onclick='confirm_add_sys_msg()'>送出</button>
    <button class='button width_60px' onclick="gotoPage('mgt_sys_msg/index')">取消</button>
</div>

<script type="text/javascript" src='<?php echo base_url(); ?>appoint/javascript/common_function.js'></script>
<script type="text/javascript" src='<?php echo base_url();?>appoint/javascript/sys_msg.js'></script>
