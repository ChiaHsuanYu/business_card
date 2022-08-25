<!-- 表單欄位填寫區塊 -->
<div class="contentsBox">
    <div class="contentsTitle blue">親密度累積設定</div>
    <div class="ceContent width_80pc margin_auto">
        <input type="hidden" id="id">
        <div class="textCenter">
            <div class="margin_top_10px">
                <div class="label width_220px line_h_40px">
                    <div class="requiredTag">*</div><label>最小距離(公尺)：</label>
                </div>
                <div class="labelInput"><input class="width_220px" id="distance" maxlength="20" autocomplete="off" required></div>
            </div>
            <div>
                <div class="label width_220px line_h_40px">
                    <div class="requiredTag">*</div><label>最大接觸時間(分鐘)：</label>
                </div>
                <div class="labelInput"><input class="width_220px" id="max_contact_time" maxlength="20" autocomplete="off" required></div>
            </div>
            <div>
                <div class="label width_220px line_h_40px">
                    <div class="requiredTag">*</div><label>最小接觸時間(分鐘)：</label>
                </div>
                <div class="labelInput"><input class="width_220px" id="min_contact_time" maxlength="20" autocomplete="off" required></div>
            </div>
            <div class="alertMsg" id="alertMsg"></div>
        </div>
    </div>
</div>
<div class="btnBox">
    <button class='button width_60px' onclick="modal_show('contactSettingModal');">送出</button>
    <button class='button width_60px' onclick="gotoPage('users/index')">取消</button>
</div>

<script type="text/javascript" src='<?php echo base_url(); ?>appoint/javascript/common_function.js'></script>
<script type="text/javascript" src='<?php echo base_url(); ?>appoint/javascript/contact_setting.js'></script>
<script>
    // 取得親密度累積設定預設值
    get_contact_setting();
</script>