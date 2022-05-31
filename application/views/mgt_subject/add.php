<!-- 表單欄位填寫區塊 -->
<div class="contentsBox">
    <div class="contentsTitle blue">主題新增上傳</div>
    <div class="ceContent width_80pc margin_auto">
        <div class="textCenter">
            <div>
                <div class="label width_120px line_h_40px">
                    <div class="requiredTag">*</div><label>主題名稱：</label>
                </div>
                <div class="labelInput padding_top_5"><input type="text" class="width_220px" id="name" maxlength="20" autocomplete="off" required></div>
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
                    <input type="file" class="width_220px height_30px" id="subject" data-target="subject" accept="*/*" multiple="multiple">
                </div>
            </div>
            <div>
                <div class="file_font">檔案需小於OOMB(可上傳jpg,jpeg,png,gif,svg格式)</div> 
            </div>
            <div>
                <div class="label width_120px line_h_40px">
                    <div class="requiredTag">*</div><label>主題檔案：</label>
                </div>
                <div class="labelInput"><input type="file" class="width_220px height_30px" id="subjectFile" data-target="subjectFile" accept="*/*" multiple="multiple"></div>
            </div>
            <div>
                <div class="file_font padding_left_8pc">檔案需小於OOMB(可上傳CSS格式)</div> 
            </div>
            <div class="alertMsg" id="alertMsg"></div>
        </div>
    </div>
</div>
<div class="btnBox">
    <button class='button width_60px' onclick='add_subject()'>送出</button>
    <button class='button width_60px' onclick="gotoPage('users/index')">取消</button>
</div>

<script type="text/javascript" src='<?php echo base_url(); ?>appoint/javascript/common_function.js'></script>
<script type="text/javascript" src='<?php echo base_url();?>appoint/javascript/subject.js'></script>
<script>
    get_template();
</script>
