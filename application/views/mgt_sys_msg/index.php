<?php 
    // 預設條件
    if(!$page){
        $page = 1;
    }
    if(!$page_count){
        $page_count = 10;
    }
?>
<input type="hidden" id="search_title" value="<?php echo $msg_title; ?>">
<input type="hidden" id="search_msg" value="<?php echo $msg; ?>">
<div class="contentsBox padding_0 margin_bottom_10 textLeft">
    <div class="contentsTitle blue">通知訊息維護</div>
    <div class="contentsTitle ">篩選條件</div>
    <div class="padding_5">
        <div class="textCenter">
            <div class="textLeft block">
                <div class="textLeft inline_block align_top ">
                    <div class="textLeft width_400px align_top margin_0 inline_block">
                        <label class="label width_120px textRight">系統通知標題： </label>
                        <input id="msg_title" class="width_220px" value="<?php echo $msg_title; ?>">
                    </div>
                    <div class="textLeft width_400px inline_block">
                        <label class="label width_120px textRight">系統通知訊息： </label>
                        <input id="msg" class="width_220px" value="<?php echo $msg; ?>">
                    </div>
                </div>
            </div>
            <div class="btnBox">
                <div class="alertMsg" id="search_error"></div>
                <button class='button width_60px inline_block' onclick='search_list();'>搜尋</button>
                <button class='button width_60px inline_block' onclick='cancel_search();'>取消</button>
            </div>
        </div>
    </div>
</div>
<div class="contentsBox">
    <div class="ceContent margin_auto">
        <!-- 帳號列表  -->
        <div class="padding_5" id="sysmsgAll"></div>
        <div class="btnBox">
            <div class="alertMsg" id="page_status"></div>
        </div>
        <!-- 資料總筆數  -->
        <div class="paginationBox">
            <div class="totalCount" id="total_count"></div>
            <div class="pagination" id="allPageCountBox">
                <!-- 筆數頁數  -->
                <div>
                    每頁：<select id="list_page_count" name="page_count"></select> 筆 
                    <button class="pageBtn" id="sys_msg_last_page" onclick="sys_msg_last_page('list_page','list_page_count');"> ←上頁 </button>
                    第 <select id="list_page" name="page"></select> 頁
                    <button class="pageBtn" id="sys_msg_next_page" onclick="sys_msg_next_page('list_page','list_page_count');"> 下頁→ </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src='<?php echo base_url();?>appoint/javascript/common_function.js'></script>
<script type="text/javascript" src='<?php echo base_url();?>appoint/javascript/sys_msg.js'></script>
<script>

    // 輸出列表
    sys_msg_list(1,10);

    //分頁-筆數函數 當筆數發生變化時觸發事件
    $('#list_page_count').change(function () {
        document.getElementById("page_status").innerHTML = '';
        var page_count = document.getElementById('list_page_count').value;
        sys_msg_list(1,page_count);
    });
    //分頁-頁數函數 當頁數發生變化時觸發事件
    $('#list_page').change(function () {
        document.getElementById("page_status").innerHTML = '';
        var page = document.getElementById('list_page').value;
        var page_count = document.getElementById('list_page_count').value;
        sys_msg_list(page,page_count);
    });
    
</script>