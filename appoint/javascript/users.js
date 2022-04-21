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
    console.log(result);
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
        tab += "<td class='contentsTh'>No</td><td class='contentsTh'>姓名</td><td class='contentsTh'>帳號</td><td class='contentsTh'>SUPER ID</td>";
        tab += "<td class='contentsTh'>註冊時間</td><td class='contentsTh'>會員資料</td><td class='contentsTh'>功能</td>";
        tab += "</tr>";
        //逐步輸出所有使用者資料
        var no = 0;
        var isEnable = "";

        for (var i = 0; i < count; i++) {
            no++;
            var name = '-',
                account = '-',
                superID = '-',
                createTime = "";
            if (users[i]['personal_superID']) {
                superID = users[i]['personal_superID'];
            }
            if (users[i]['personal_name']) {
                name = users[i]['personal_name'];
            }
            // if (users[i]['email']) {
            //     email = users[i]['email'];
            // }
            // if (users[i]['remark']) {
            //     var note = users[i]['remark'].replace(/\r\n|\n/g, "");
            // }
            // <!-- 電腦版 -->
            tab += "<tr align='center' class='contentsTr'>";
            tab += "<td>" + no + "</td><td>" + name + "</td><td>" + users[i]['account'] + "</td><td>" + superID + "</td>";
            tab += "<td>" + users[i]['createTime'] + "</td>";
            tab += "<td>";
            tab += "<div class='fault_a' onclick='look_slideToggle(" + users+ ")'>查看會員資料</div>";
            tab += "</td>";
            tab += "<td>";
            if(users[i]['isDeleted'] == '1'){
                tab += "<button class='button width_80px' onclick='locationDetail(" + users[i]['id'] + ',' + data_obj + ")'>解凍帳號</button> ";
            }else{
                tab += "<button class='button width_80px' onclick='locationDetail(" + users[i]['id'] + ',' + data_obj + ")'>凍結帳號</button> ";
            }
            tab += "</tr>";

            // <!-- 手機板 -->
            // tab_phone += "<div class='phone_table margin_bottom_10px' width='100%' cellpadding='0' cellspacing='0'>";
            // tab_phone += "<div class='contentsTh'>No." + no + "</div>";
            // tab_phone += "<div class='content_phone'>帳號： " + users[i]['account'] + "</div>";
            // tab_phone += "<div class='content_phone'>狀態： " + isEnable + "</div>";
            // tab_phone += "<div class='content_phone'>廠區： " + users[i]['areaName'] + "</div>";
            // tab_phone += "<div class='content_phone'>群組名稱： " + users[i]['groups'] + "</div>";
            // tab_phone += "<div class='content_phone'>人員編號： " + users[i]['staffNo'] + "</div>";
            // tab_phone += "<div class='content_phone'>人員名稱： " + users[i]['name'] + "</div>";
            // tab_phone += "<div class='content_phone'>聯絡電話： " + phone + "</div>";
            // tab_phone += "<div class='content_phone'>E-mail： " + email + "</div>";
            // if (note) {
            //     tab_phone += "<div class='content_phone'>備註： <button class='button width_60px' onclick='USER_NOTE(" + '"' + note + '"' + ")'>備註</button></div>";
            // } else {
            //     tab_phone += "<div class='content_phone'>備註： - </div>";
            // }
            // tab_phone += "<div class='content_phone'>功能： ";
            // tab_phone += "<button class='button width_60px' onclick='locationDetail(" + users[i]['id'] + ',' + data_obj + ")'>檢視</button> ";
            // tab_phone += "<button class='button width_60px' onclick='locationEdit(" + users[i]['id'] + ',' + data_obj + ")'>修改</button> ";
            // tab_phone += "<button class='button width_60px' onclick='del_acc(" + users[i]['id'] + ',' + '"' + users[i]['account'] + '"' + ")'>刪除</button> ";
            // tab_phone += "</div></div>";
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
    document.getElementById('total_count').innerHTML = '資料總筆數：' + seachText['total_count'];
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

// 刪除帳號
function del_acc(id, account) {
    var page = document.getElementById('list_page').value;
    var page_count = document.getElementById('list_page_count').value;
    if (id == "") {
        alert("執行錯誤");
    } else {
        if (confirm("確定刪除「" + account + "」?")) {
            var data_obj = {
                id: id,
            };
            if (id == localStorage.getItem('UsertId')) {
                alert("不可刪除，此帳號您正在使用中");
            } else {
                var result = call_api('mgt_users_api/del_acc_by_id', data_obj);
                if (result['status']) {
                    alert(result['message']);
                    // 重新呼叫帳號列表
                    users_list(page, page_count);
                } else {
                    document.getElementById('page_status').innerHTML = result['message'];
                }
            }
        }
    }
}