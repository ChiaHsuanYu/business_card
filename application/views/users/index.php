<?php 
    // 預設條件
    if(!$page){
        $page = 1;
    }
    if(!$page_count){
        $page_count = 10;
    }
?>
<input type="hidden" id="search_account" value="<?php echo $account; ?>">
<input type="hidden" id="search_superID" value="<?php echo $superID; ?>">
<input type="hidden" id="search_company" value="<?php echo $company; ?>">
<input type="hidden" id="search_industryId" value="<?php echo $industryId; ?>">
<input type="hidden" id="search_startDT" value="<?php echo $startDT; ?>">
<input type="hidden" id="search_endDT" value="<?php echo $endDT; ?>">
<div class="contentsBox padding_0 margin_bottom_10 textLeft">
    <div class="contentsTitle blue">會員帳號查詢</div>
    <div class="contentsTitle ">篩選條件</div>
    <div class="padding_5">
        <div class="textCenter">
            <div class="textLeft block">
                <div class="textLeft inline_block align_top ">
                    <div class="textLeft width_350px align_top margin_0 inline_block">
                        <label class="label width_100px textRight">帳號： </label>
                        <input id="account" class="width_220px" value="<?php echo $account; ?>">
                    </div>
                    <div class="textLeft width_350px inline_block">
                        <label class="label width_100px textRight">SUPER ID： </label>
                        <input id="superID" class="width_220px" value="<?php echo $superID; ?>">
                    </div>
                    <div class="textLeft width_350px inline_block">
                        <label class="label width_100px textRight">公司名稱： </label>
                        <input id="company" class="width_220px" value="<?php echo $company; ?>">
                    </div>
                    <div class="textLeft width_350px inline_block">
                        <label class="label width_100px textRight">產業類別： </label>
                        <select id="industrySelect" class="width_220px"></select>
                    </div>
                    <div class="textLeft align_top inline_block">
                        <label class="label inline_block width_100px textRight phone_alignLeft">建立期間： </label>
                        <div class="DTBox">
                            <div class="container">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <div class="input-group" id="datetimepicker1" data-target-input="nearest">
                                                <input type="text" class="form-control datetimepicker-input height_30px margin_0" data-target="#datetimepicker1" id="startDT" value="<?php echo $startDT; ?>" />
                                                <div class="input-group-append" data-target="#datetimepicker1" data-toggle="datetimepicker">
                                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <script type="text/javascript">
                                        $(function() {
                                            $('#datetimepicker1').datetimepicker({
                                                format: 'LT',
                                                format: 'YYYY-MM-DD HH:mm:ss'
                                            });
                                        });
                                    </script>
                                </div>
                            </div>
                        </div>
                        至
                        <div class="DTBox">
                            <div class="container">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <div class="input-group date" id="datetimepicker2" data-target-input="nearest">
                                                <input type="text" class="form-control datetimepicker-input height_30px margin_0" data-target="#datetimepicker2" id="endDT" value="<?php echo $endDT; ?>" />
                                                <div class="input-group-append" data-target="#datetimepicker2" data-toggle="datetimepicker">
                                                    <div class="input-group-text "><i class="fa fa-calendar "></i></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <script type="text/javascript">
                                        $(function() {
                                            $('#datetimepicker2').datetimepicker({
                                                format: 'LT',
                                                format: 'YYYY-MM-DD HH:mm:ss'
                                            });
                                        });
                                    </script>
                                </div>
                            </div>
                        </div>
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
        <div class="padding_5 web_table" id="usersAll"></div>
        <div class="padding_5 phone_table" id="usersAll_phone"></div>
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
                    <button class="pageBtn" id="users_last_page" onclick="users_last_page('list_page','list_page_count');"> ←上頁 </button>
                    第 <select id="list_page" name="page"></select> 頁
                    <button class="pageBtn" id="users_next_page" onclick="users_next_page('list_page','list_page_count');"> 下頁→ </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src='<?php echo base_url();?>appoint/javascript/common_function.js'></script>
<script type="text/javascript" src='<?php echo base_url();?>appoint/javascript/users.js'></script>
<script>

    // 選單-寫入產業清單
    industry_select_option();
    document.getElementById("industrySelect").value="<?php echo $industryId; ?>";

    // 輸出列表
    users_list(1,10);

    //分頁-筆數函數 當筆數發生變化時觸發事件
    $('#list_page_count').change(function () {
        document.getElementById("page_status").innerHTML = '';
        var page_count = document.getElementById('list_page_count').value;
        users_list(1,page_count);
    });
    //分頁-頁數函數 當頁數發生變化時觸發事件
    $('#list_page').change(function () {
        document.getElementById("page_status").innerHTML = '';
        var page = document.getElementById('list_page').value;
        var page_count = document.getElementById('list_page_count').value;
        users_list(page,page_count);
    });
    
</script>