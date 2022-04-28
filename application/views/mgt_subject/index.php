<div class="contentsBox margin_bottom_10 textLeft">
    <div class="contentsTitle blue">主題維護</div>
    <div class="ceContent margin_top_10px">
        <!-- 主題維護  -->
        <div class="padding_5" id="subjectAll"></div>
        <!-- <div class="padding_5" id="subjectAll_phone"></div> -->
        <div class="btnBox">
            <div class="alertMsg" id="page_status"></div>
        </div>
        <!-- 資料總筆數  -->
        <div class="paginationBox">
            <div class="totalCount" id="total_count"></div>
            <div class="pagination" id="allPageCountBox"></div>
        </div>
    </div>
</div>

<script type="text/javascript" src='<?php echo base_url();?>appoint/javascript/common_function.js'></script>
<script type="text/javascript" src='<?php echo base_url();?>appoint/javascript/subject.js'></script>
<script>

    // 輸出列表
    subject_list(1,10);
</script>