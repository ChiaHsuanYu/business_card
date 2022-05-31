<!-- 表單欄位填寫區塊 -->
<input type="hidden" id="edit_id" value="<?php echo $id;?>">
<div class="contentsBox">
    <div class="contentsTitle blue">主題修改</div>
    <div class="ceContent width_80pc margin_auto">
        <div class="textCenter">
            <div>
                <div class="label width_120px line_h_40px">
                    <div class="requiredTag">*</div><label>主題名稱：</label>
                </div>
                <div class="labelInput padding_top_5">
                    <input type="text" class="width_220px" id="name" maxlength="20" autocomplete="off" required>
                    <p class="inline_block"></p>
                </div>
            </div>
            <div>
                <div class="label width_120px line_h_40px">
                    <div class="requiredTag">*</div><label>主題模板：</label>
                </div>
                <div class="labelInput padding_top_5">
                    <select class="width_220px" id="templateSelect" autocomplete="off" required></select>
                </div>
            </div>
            <div>
                <div class="label width_120px line_h_40px">
                    <div class="requiredTag">*</div><label>主題縮圖：</label>
                </div>
                <div class="labelInput">
                    <div class='inline_block width_220px textLeft' id='img_data_Box'></div>
                </div>
            </div>
            <div>
                <div class="file_font">檔案需小於OOMB(可上傳jpg,jpeg,png,gif,svg格式)</div> 
            </div>
            <div>
                <div class="label width_120px line_h_40px">
                    <div class="requiredTag">*</div><label>主題檔案：</label>
                </div>
                <div class="labelInput">
                    <div class='inline_block width_220px textLeft' id='file_data_Box'></div>
                </div>
            </div>
            <div>
                <div class="file_font padding_left_8pc">檔案需小於OOMB(可上傳CSS格式)</div> 
            </div>
            <div class="alertMsg" id="alertMsg"></div>
        </div>
    </div>
</div>
<div class="btnBox">
    <button class='button width_60px' onclick='edit_subject()'>送出</button>
    <button class='button width_60px' onclick="gotoPage('mgt_subject/index')">取消</button>
</div>

<script type="text/javascript" src='<?php echo base_url(); ?>appoint/javascript/common_function.js'></script>
<script type="text/javascript" src='<?php echo base_url();?>appoint/javascript/subject.js'></script>
<script>
    get_template();
    get_subject();
</script>
