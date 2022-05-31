<!-- 表單欄位填寫區塊 -->
<input type="hidden" id="edit_id" value="<?php echo $id;?>">
<div class="contentsBox">
    <div class="contentsTitle blue">模板修改</div>
    <div class="width_80pc margin_auto">
        <div class="textCenter margin_top_10px">
            <div>
                <div class="label width_120px line_h_40px">
                    <div class="requiredTag">*</div><label>模板名稱：</label>
                </div>
                <div class="labelInput padding_top_5">
                    <input type="text" class="width_220px" id="template" maxlength="20" autocomplete="off" required>
                    <p class="inline_block"></p>
                </div>
            </div>
            <div class="alertMsg" id="alertMsg"></div>
        </div>
    </div>
</div>
<div class="btnBox">
    <button class='button width_60px' onclick='edit_template()'>送出</button>
    <button class='button width_60px' onclick="gotoPage('mgt_template/index')">取消</button>
</div>

<script type="text/javascript" src='<?php echo base_url(); ?>appoint/javascript/common_function.js'></script>
<script type="text/javascript" src='<?php echo base_url();?>appoint/javascript/template.js'></script>
<script>
    get_template();
</script>
