// 列表上一頁
function users_last_page(pageId, countId) {
    var data = check_last_page(pageId, countId);
    users_list(data['page'], data['page_count']);
}

// 列表下一頁
function users_next_page(pageId, countId) {
    var data = check_next_page(pageId, countId);
    users_list(data['page'], data['page_count']);
}

function search_list() {
    var account = document.getElementById('account').value;
    var superID = document.getElementById('superID').value;
    var company = document.getElementById('company').value;
    var industryId = document.getElementById('industrySelect').value;
    var startDT = document.getElementById('startDT').value;
    var endDT = document.getElementById('endDT').value;
    document.getElementById('search_account').value = account;
    document.getElementById('search_superID').value = superID;
    document.getElementById('search_company').value = company;
    document.getElementById('search_industryId').value = industryId;
    document.getElementById('search_startDT').value = startDT;
    document.getElementById('search_endDT').value = endDT;
    document.getElementById('page_status').innerHTML = "";
    users_list(1, 10);
}

function cancel_search(){
    document.getElementById('account').value = "";
    document.getElementById('superID').value = "";
    document.getElementById('company').value = "";
    document.getElementById('industrySelect').value = "";
    document.getElementById('startDT').value = "";
    document.getElementById('endDT').value = "";
    search_list();
}

// 列表-呼叫取得所有使用者資訊API
function users_list(page_num, page_count) {
    var account = document.getElementById('search_account').value;
    var superID = document.getElementById('search_superID').value;
    var company = document.getElementById('search_company').value;
    var industryId = document.getElementById('search_industryId').value;
    var startDT = document.getElementById('search_startDT').value;
    var endDT = document.getElementById('search_endDT').value;
    var data_obj = {
        account: account,
        superID: superID,
        company: company,
        industryId: industryId,
        startDT: startDT,
        endDT: endDT,
        page: page_num,
        page_count: page_count,
    };
    var result = call_api('mgt_users_api/query_users', data_obj);
    if (result['status']) {
        var data = JSON.stringify(result['data']);
        data = JSON.parse(data);
        // 繪製使用者列表
        users_data(data, data_obj);
    } else {
        users_noData();
    }
}

// 列表-尚無使用者資料
function users_noData() {
    var tab = "",
        tab_phone = "";
    tab = "<table class='contentsTable' width='auto' cellpadding='0' cellspacing='0'>"
    tab += "<tr align='center'>";
    tab += "<td class='contentsTh' colspan='10'>查無用戶資料</td>";
    tab += "</tr>";
    tab += "</table>";
    tab_phone += "<div class='phone_table' width='100%' cellpadding='0' cellspacing='0'>";
    tab_phone += "<div class='contentsTh'>查無用戶資料</div>";
    tab_phone += "</div>";
    $("#usersAll").html(tab);
    $("#usersAll_phone").html(tab_phone);
    document.getElementById('total_count').innerHTML = '資料總筆數：0';
    var hideobj = document.getElementById("allPageCountBox");
    hideobj.style.display = "none"; //隱藏筆數頁數層 
}

// 列表-依序列出所有使用者
function users_data(seachText, data_obj) {
    var baseUrl = document.getElementById('base_url').value;
    var page_num = parseInt(data_obj['page']);
    var page_count = parseInt(data_obj['page_count']);
    var data_obj = JSON.stringify(data_obj);
    var hideobj = document.getElementById("allPageCountBox");
    var users = seachText['users'];
    var count = json_count(users); // 群組筆數
    var tab_phone = "",
        tab = "";
    if (count != 0) {

        //輸出使用者列表
        tab += "<table class='web_table contentsTable' width='auto' cellpadding='0' cellspacing='0'>"
        tab += "<tr align='center'>";
        tab += "<td class='contentsTh'>No</td><td class='contentsTh'>註冊類型</td><td class='contentsTh'>姓名</td><td class='contentsTh' colspan='2'>帳號</td><td class='contentsTh' colspan='2'>SUPER ID</td>";
        tab += "<td class='contentsTh' colspan='2'>註冊時間</td><td class='contentsTh'>會員資料</td><td class='contentsTh'>功能</td>";
        tab += "</tr>";
        //逐步輸出所有使用者資料
        var page_star = (page_num - 1) * page_count;
        var no = page_star;
        for (var i = 0; i < count; i++) {
            no++;
            var login_type = '手機號碼',
                name = '-',
                superID = '-';
            var user_detail_id = "user_detail" + users[i]['id'];
            var user_detail_id_phone = "user_detail_phone_" + users[i]['id'];
            var company_id = "company_" + users[i]['id'];
            var company_id_phone = "company_phone_" + users[i]['id'];
            var account = users[i]['account'];
            if(!users[i]['account']){
                account = users[i]['personal_email'];
                if(users[i]['google_uid']){
                    login_type = 'Google';
                    account = users[i]['google_uid'];
                }else if(users[i]['facebook_uid']){
                    login_type = 'Facebook';
                    account = users[i]['facebook_uid'];
                }else if(users[i]['line_uid']){
                    login_type = 'Line';
                    account = users[i]['line_uid'];
                }else{ login_type = ''; }
            }
            if (users[i]['personal_superID']) {
                superID = users[i]['personal_superID'];
            }
            if (users[i]['personal_name']) {
                name = users[i]['personal_name'];
            }
            var user_data = {
                id:users[i]['id'],
                account:account
            };
            if(users[i]['isDeleted'] == '1'){
                user_data['isDeleted'] = '0';
                user_data = JSON.stringify(user_data);
                var del_btn = "<button class='red_button width_80px' onclick='confirm_isDeleted(" + user_data + ")'>解凍帳號</button> ";
            }else{
                user_data['isDeleted'] = '1';
                user_data = JSON.stringify(user_data);
                var del_btn = "<button class='button width_80px' onclick='confirm_isDeleted(" + user_data + ")'>凍結帳號</button> ";
            }
            // <!-- 電腦版 -->
            tab += "<tr align='center' class='contentsTr'>";
            tab += "<td>" + no + "</td><td>" + login_type + "</td><td>" + name + "</td><td colspan='2'>" + account + "</td><td colspan='2'>" + superID + "</td>";
            tab += "<td colspan='2'>" + users[i]['createTime'] + "</td>";
            tab += "<td>";
            tab += "<div class='fault_a' onclick='show_user(" + '"' + user_detail_id + '"' + ")'>查看會員資料</div>";
            tab += "</td>";
            tab += "<td>"+del_btn+"</td>";
            tab += "</tr>";

            // <!-- 手機板 -->
            tab_phone += "<div class='phone_table margin_bottom_10px' width='100%' cellpadding='0' cellspacing='0'>";
            tab_phone += "<div class='contentsTh'>No." + no + "</div>";
            tab_phone += "<div class='content_phone'>註冊類型： " + login_type + "</div>";
            tab_phone += "<div class='content_phone'>姓名： " + name + "</div>";
            tab_phone += "<div class='content_phone'>帳號： " + account + "</div>";
            tab_phone += "<div class='content_phone'>SUPER ID： " + superID + "</div>";
            tab_phone += "<div class='content_phone'>註冊時間： " + users[i]['createTime'] + "</div>";
            tab_phone += "<div class='content_phone'>會員資料： <p class='inline_block margin_0 fault_a' onclick='show_user(" + '"' + user_detail_id_phone + '"' + ")'>查看會員資料</p></div>";            
            tab_phone += "<div class='content_phone'>功能： "+del_btn+"</div>";
            tab_phone += "</div>";

            var nickname = "-",
                avatar = "-",
                phone = "-",
                email = "-",
                social = "-",
                companyInfo_btn = "-",
                companyInfo_btn_phone = "-",
                modifiedTime = "-";
            if(users[i]['personal_nickname']){
                nickname = users[i]['personal_nickname'];
            }
            if(users[i]['personal_avatar']){
                // users[i]['personal_avatar'] = baseUrl + users[i]['personal_avatar'];
                avatar = "<img class='img img_pointer' title='另開圖片視窗' src='" + users[i]['personal_avatar'] + "' onclick='openImg(" + '"' + users[i]['personal_avatar'] + '"' + ")'>";
            }
            if(users[i]['personal_phone']){
                phone = "";
                for(var k=0;k<users[i]['personal_phone'].length;k++){
                    if(k>0){
                        phone += "<br>";
                    }
                    phone += users[i]['personal_phone'][k];
                }
            }
            if(users[i]['personal_email']){
                email = "";
                for(var k=0;k<users[i]['personal_email'].length;k++){
                    if(k>0){
                        email += "<br>";
                    }
                    email += users[i]['personal_email'][k];
                }
            }
            if(users[i]['modifiedTime']){
                modifiedTime = users[i]['modifiedTime'];
            }
            if(users[i]['personal_social']){
                social = "";
                for(var k=0;k<users[i]['personal_social'].length;k++){
                    if(k>0){
                        social += "<br>";
                    }
                    // users[i]['personal_social'][k]['iconURL'] = baseUrl + users[i]['personal_social'][k]['iconURL'];
                    social += "<img class='img img_pointer' title='另開圖片視窗' src='" + users[i]['personal_social'][k]['iconURL'] + "' onclick='openImg(" + '"' + users[i]['personal_social'][k]['iconURL'] + '"' + ")'> ";
                    social += "<a href='"+users[i]['personal_social'][k]['socialURL']+"'>"+users[i]['personal_social'][k]['socialTitle']+"</a>";
                }
            }
            if(users[i]['companyInfo'].length){
                companyInfo_btn = "<p class='inline_block margin_0 fault_a' onclick='look_slideToggle(" + '"' + company_id + '"' + ")'>檢視</p>";
                companyInfo_btn_phone = "<p class='inline_block margin_0 fault_a' onclick='look_slideToggle(" + '"' + company_id_phone + '"' + ")'>檢視</p>";
            }
            tab += "<div id='" + user_detail_id + "'>";
            tab_phone += "<div id='" + user_detail_id_phone + "'></div>";
            tab_phone += "<div class='medium_orange " + user_detail_id_phone + "' hidden='true'>會員詳細資料</div>";
            tab_phone += "</div>";
            tab += "<tr align='center' class='contentsTr orange " + user_detail_id + "' hidden='true'>";
            tab += "<td>個人頭像</td><td>暱稱</td><td colspan='2'>連絡電話</td><td colspan='2'>信箱</td><td colspan='2'>個人社群</td><td>公司資訊</td><td colspan='2'>最後一次更新時間</td>";
            tab += "</tr>";

            tab += "<tr class='light_orange " + user_detail_id + "' hidden='true'>";
            tab += "<td>"+avatar+"</td>";
            tab += "<td>"+nickname+"</td>";
            tab += "<td colspan='2'>"+phone+"</td>";
            tab += "<td colspan='2'>"+email+"</td>";;
            tab += "<td colspan='2'>"+social+"</td>";;
            tab += "</td>";
            tab += "<td>"+companyInfo_btn+"</td>";
            tab += "<td colspan='2'>"+modifiedTime+"</td>";
            tab += "</tr>";

            // 手機板
            tab_phone += "<div class='margin_bottom_10px light_orange " + user_detail_id_phone + "' width='100%' cellpadding='0' cellspacing='0' hidden='true'>";
            tab_phone += "<div>個人頭像： " + avatar + "</div>";
            tab_phone += "<div>暱稱： " + nickname + "</div>";
            tab_phone += "<div>連絡電話： " + phone + "</div>";
            tab_phone += "<div>信箱： " + email + "</div>";
            tab_phone += "<div>個人社群： " + social + "</div>";
            tab_phone += "<div>公司資訊："+companyInfo_btn_phone+"</div>";
            tab_phone += "<div>最後一次更新時間： " + modifiedTime + "</div>";
            tab_phone += "</div>";
            tab += "</div>";

            var company_no = 0;
            var companyInfo = users[i]['companyInfo'];
            for (var k = 0; k < companyInfo.length; k++) {
                var div_class = 'medium_red margin_bottom_10px ';
                if (k % 2 == 0) {
                    div_class = 'light_red ';
                }
                if (k == 0) {
                    tab += "<div id='" + company_id + "'>";
                    tab_phone += "<div id='" + company_id_phone + "'></div>";
                    tab_phone += "<div class='grey " + company_id_phone + " " + user_detail_id_phone + "' hidden='true'>公司詳細資訊</div>";
                    tab_phone += "</div>";
                    tab += "<tr align='center' class='contentsTr grey " + company_id + " " + user_detail_id + "' hidden='true'>";
                    tab += "<td>公司名稱</td><td>公司LOGO</td><td>產業類別</td><td>職位</td><td>服務介紹</td><td>電話分機</td><td>地址</td><td>信箱</td><td>統一編號</td><td>公司社群</td><td>最後一次更新時間</td>";
                    tab += "</tr>";
                }
                company_no++;
                var name = "-",
                    logo_img = '-',
                    industry = '-',
                    position = '-',
                    aboutus = '-',
                    gui = '-',
                    phone = '-',
                    address = '-',
                    email = '-',
                    social = '-',
                    modifiedTime = companyInfo[k]['createTime'];
                if (companyInfo[k]['company_logo']) {
                    // companyInfo[k]['company_logo'] =  baseUrl + companyInfo[k]['company_logo'];
                    logo_img = "<img class='img img_pointer' title='另開圖片視窗' src='" + companyInfo[k]['company_logo'] + "' onclick='openImg(" + '"' + companyInfo[k]['company_logo'] + '"' + ")'>";
                }
                if(companyInfo[k]['company_industryName']){
                    industry = companyInfo[k]['company_industryName'];
                }
                if(companyInfo[k]['company_position']){
                    position = companyInfo[k]['company_position'];
                }
                if(companyInfo[k]['company_aboutus']){
                    aboutus = companyInfo[k]['company_aboutus'];
                }
                if(companyInfo[k]['company_gui']){
                    gui = companyInfo[k]['company_gui'];
                }
                if(companyInfo[k]['company_phone']){
                    phone = "";
                    for(var m=0;m<companyInfo[k]['company_phone'].length;m++){
                        if(m>0){
                            phone += "<br>";
                        }
                        phone += companyInfo[k]['company_phone'][m];
                    }
                }
                if(companyInfo[k]['company_address']){
                    address = "";
                    for(var m=0;m<companyInfo[k]['company_address'].length;m++){
                        if(m>0){
                            address += "<br>";
                        }
                        address += companyInfo[k]['company_address'][m];
                    }
                }
                if(companyInfo[k]['company_email']){
                    email = "";
                    for(var m = 0 ; m < companyInfo[k]['company_email'].length ; m++){
                        if(m>0){
                            email += "<br>";
                        }
                        email += companyInfo[k]['company_email'][m];
                    }
                }
                if(companyInfo[k]['company_social']){
                    social = "";
                    for(var m=0;m<companyInfo[k]['company_social'].length;m++){
                        if(m>0){
                            social += "<br>";
                        }
                        // companyInfo[k]['company_social'][m]['iconURL'] = baseUrl + companyInfo[k]['company_social'][m]['iconURL'];
                        social += "<img class='img img_pointer' title='另開圖片視窗' src='" + companyInfo[k]['company_social'][m]['iconURL'] + "' onclick='openImg(" + '"' + companyInfo[k]['company_social'][m]['iconURL'] + '"' + ")'> ";
                        social += "<a href='"+companyInfo[k]['company_social'][m]['socialURL']+"'>"+companyInfo[k]['company_social'][m]['socialTitle']+"</a>";
                    }
                }
                if(companyInfo[k]['modifiedTime']){
                    modifiedTime = companyInfo[k]['modifiedTime'];
                }
                tab += "<tr align='center' class='contentsTr light_grey " + company_id + " " + user_detail_id + "' hidden='true'>";
                tab += "<td>" + companyInfo[k]['company_name'] + "</td>";
                tab += "<td>" + logo_img + "</td><td>" +industry + "</td><td>" + position + "</td><td>" + aboutus + "</td><td>" + phone + "</td>";
                tab += "<td>" + address + "</td><td>" + email + "</td><td>" + gui + "</td><td>" + social + "</td><td>"+modifiedTime+"</td>";
                tab += "</tr>";
                // 手機板
                tab_phone += "<div class=' " + div_class + company_id_phone + " " + user_detail_id_phone + "' width='100%' cellpadding='0' cellspacing='0' hidden='true'>";
                tab_phone += "<div>公司名稱： " + companyInfo[k]['company_name'] + "</div>";
                tab_phone += "<div>公司LOGO： " + logo_img + "</div>";
                tab_phone += "<div>產業類別： " + industry + "</div>";
                tab_phone += "<div>職位： " + position + "</div>";
                tab_phone += "<div>服務介紹	： " + aboutus + "</div>";
                tab_phone += "<div>電話分機： " + phone + "</div>";
                tab_phone += "<div>地址： " + address + "</div>";
                tab_phone += "<div>信箱	： " + email + "</div>";
                tab_phone += "<div>統一編號	： " + gui + "</div>";
                tab_phone += "<div>公司社群	： " + social + "</div>";
                tab_phone += "<div>最後一次更新時間	： " + modifiedTime + "</div>";
                tab_phone += "</div>";
            }
            tab += "</div>";
        }
        hideobj.style.display = "inline-block"; //隱藏筆數頁數層 
    } else {
        var tab = "";
        tab = "<table class='contentsTable' width='auto' cellpadding='0' cellspacing='0'>"
        tab += "<tr align='center'>";
        tab += "<td class='contentsTh' colspan='10'>查無用戶資料</td>";
        tab += "</tr>";
        tab_phone += "<div class='contentsTable' width='100%' cellpadding='0' cellspacing='0'><div class='contentsTh'>查無用戶資料</div></div>";
        hideobj.style.display = "none"; //隱藏筆數頁數層 
    }
    tab += "</table>";
    $("#usersAll").html(tab);
    $("#usersAll_phone").html(tab_phone);

    // 清除筆數頁數select裡的所有option
    document.getElementById("list_page").innerHTML = "";
    document.getElementById("list_page_count").innerHTML = "";
    if (seachText['total_page']) {
        var total_page = seachText['total_page'];
    } else {
        var total_page = 1;
    }
    // 輸出資料筆數及頁數
    page_count_select(total_page, page_num, page_count)
    document.getElementById('total_count').innerHTML = '資料總筆數：' + seachText['total_count'];
}

function show_user(divId){
    var box = document.querySelectorAll('.'+divId);
    for(var i=0;i<box.length;i++){
        if(box[i].hidden){
            box[i].hidden = false;
        }else{
            box[i].hidden = true;
        }
    }
}

// 選單-寫入產業清單
function industry_select_option() {
    var industrySelect = document.getElementById('industrySelect');
    industrySelect.innerHTML = ""; // 清除select裡的所有option
    var data_obj = {};
    var result = call_api('mgt_users_api/get_industry', data_obj);
    if (result['status']) {
        var data = result['data'];
        addOption(industrySelect, '請選擇產業類別', '');
        for (var i = 0; i < data.length; i++) {
            addOption(industrySelect, data[i]['industryName'], data[i]['industryId']);
        }
    } else {
        addOption(industrySelect, "查無產業類別", '');
    }
}

// 確認是否更改帳號狀態
function confirm_isDeleted(user_data) {
    var action = "解凍";
    if(user_data['isDeleted'] == '1'){
        action = "凍結";
    }
    modal_show("confirmModal");
    document.getElementById("confirm_modal_label").innerHTML = "系統訊息";
    document.getElementById("confirm_model_body").innerHTML = "是否確定"+action+"帳號「" + user_data['account'] + "」?";
    document.getElementById("confirm_userId").value = user_data['id'];
    document.getElementById("confirm_isDeleted").value = user_data['isDeleted'];
}

// 更改帳號狀態
function update_isDeleted(){
    modal_hide("confirmModal");
    var page = document.getElementById('list_page').value;
    var page_count = document.getElementById('list_page_count').value;
    var userId = document.getElementById("confirm_userId").value;
    var isDeleted = document.getElementById("confirm_isDeleted").value;
    var data_obj = {
        userId: userId,
        isDeleted: isDeleted
    };
    var result = call_api('mgt_users_api/update_isDeleted_by_id', data_obj);
    modal_show("msgModal");
    document.getElementById("modal_label").innerHTML = "系統訊息";
    document.getElementById("model_body").innerHTML = string_replace(result['msg']);
    if (result['status']) {
        // 重新呼叫帳號列表
        users_list(page,page_count);
    }
}