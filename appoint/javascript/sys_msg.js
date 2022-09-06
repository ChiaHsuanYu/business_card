// 跳轉至修改通知訊息頁面
function location_edit(id) {
    let baseUrl = document.getElementById('base_url').value;
    location.href = baseUrl + "mgt_sys_msg/edit/" + id;
}

// 列表上一頁
function sys_msg_last_page(pageId, countId) {
    let data = check_last_page(pageId, countId);
    sys_msg_list(data['page'], data['page_count']);
}

// 列表下一頁
function sys_msg_next_page(pageId, countId) {
    let data = check_next_page(pageId, countId);
    sys_msg_list(data['page'], data['page_count']);
}

// 搜尋列表
function search_list() {
    let msg_title = document.getElementById('msg_title').value;
    let msg = document.getElementById('msg').value;
    document.getElementById('search_title').value = msg_title;
    document.getElementById('search_msg').value = msg;
    document.getElementById('page_status').innerHTML = "";
    sys_msg_list(1, 10);
}

// 取消搜尋
function cancel_search() {
    document.getElementById('msg_title').value = "";
    document.getElementById('msg').value = "";
    search_list();
}

// 取得通知訊息資料
function get_sys_msg() {
    document.getElementById("alertMsg").innerHTML = "";
    let id = document.getElementById("edit_id").value;
    let data_obj = {
        id: id
    };
    let result = call_api("mgt_sys_msg_api/get_sys_msg/", data_obj);
    if (result['status']) {
        let data = result['data'];
        document.getElementById("title").value = data[0]['title'];
        document.getElementById("msg").value = data[0]['msg'];
    } else {
        modal_show("msgModal");
        document.getElementById("modal_label").innerHTML = "格式錯誤";
        document.getElementById("model_body").innerHTML = "查無通知訊息資料";
    }
}

// 確認是否新增通知訊息
function confirm_add_sys_msg() {
    modal_show("confirmModal");
    document.getElementById("action").value = 'add';
}

// 確認是否修改通知訊息
function confirm_edit_sys_msg() {
    modal_show("confirmModal");
    let id = document.getElementById("edit_id").value;
    document.getElementById("confirm_sys_msgId").value = id;
    document.getElementById("confirm_model_body").innerHTML = "修改後將發布訊息通知，是否確定修改?";
    document.getElementById("action").value = 'edit';
}

// 確認是否刪除通知訊息
function confirm_del_sys_msg(sys_msg_data) {
    modal_show("confirmModal");
    document.getElementById("confirm_sys_msgId").value = sys_msg_data['id'];
    document.getElementById("confirm_model_body").innerHTML = "是否確定刪除通知訊息「" + sys_msg_data['title'] + "」?";
    document.getElementById("action").value = 'del';
}

// 執行功能(新增/修改/刪除)
function sys_msg_function() {
    modal_hide("confirmModal");
    let action = document.getElementById("action").value;
    switch (action){
        case 'del':
            del_sys_msg();
            break;
        case 'add':
            add_sys_msg();
            break;
        case 'edit':
            edit_sys_msg();
            break;
        default:break;
    }
}

// 通知訊息新增上傳
function add_sys_msg() {
    modal_hide("confirmModal");
    let baseUrl = document.getElementById('base_url').value;
    let title = document.getElementById('title').value;
    let msg = document.getElementById('msg').value;
    let data_obj = {
        title: title,
        msg: msg,
    };
    let result = call_api('mgt_sys_msg_api/add_sys_msg', data_obj);
    modal_show("msgModal");
    document.getElementById("model_body").innerHTML = string_replace(result['msg']);
    if (result['status']) {
        document.getElementById("modal_label").innerHTML = "系統訊息";
        sleep(1000).then(() => {
            location.href = baseUrl + "mgt_sys_msg/index/";
        });
    } else {
        document.getElementById("modal_label").innerHTML = "格式錯誤";
    }
}

// 修改通知訊息
function edit_sys_msg() {
    let baseUrl = document.getElementById('base_url').value;
    let id = document.getElementById('edit_id').value;
    let title = document.getElementById('title').value;
    let msg = document.getElementById('msg').value;
    let data_obj = {
        id: id,
        title: title,
        msg: msg
    };
    let result = call_api("mgt_sys_msg_api/update_sys_msg_by_id/", data_obj);
    modal_show("msgModal");
    document.getElementById("model_body").innerHTML = string_replace(result['msg']);
    if (result['status']) {
        document.getElementById("modal_label").innerHTML = "系統訊息";
        sleep(1000).then(() => {
            location.href = baseUrl + "mgt_sys_msg/index/";
        });
    } else {
        document.getElementById("modal_label").innerHTML = "格式錯誤";
    }
}

// 刪除通知訊息
function del_sys_msg(){
    let id = document.getElementById("confirm_sys_msgId").value;
    let page = parseInt(document.getElementById("list_page").value);
    let page_count = parseInt(document.getElementById("list_page_count").value);
    let total_count = document.getElementById("total_count").innerHTML;
    let arr = total_count.split('資料總筆數：');
    total_count = arr[1];
    let data_obj = {
        id: id,
    };
    let result = call_api('mgt_sys_msg_api/delete_sys_msg_by_id', data_obj);
    modal_show("msgModal");
    document.getElementById("modal_label").innerHTML = "系統訊息";
    document.getElementById("model_body").innerHTML = string_replace(result['msg']);
    if (result['status']) {
        // 判斷刪除的資料是否為該頁面最後一筆
        if((total_count-1) % page_count == 0){
            page = page-1;
        }
        // 重新呼叫列表
        sys_msg_list(page,page_count);
    }
}

// 列表-呼叫取得所有通知訊息資訊API
function sys_msg_list(page_num, page_count) {
    let title = document.getElementById('search_title').value;
    let msg = document.getElementById('search_msg').value;
    let data_obj = {
        title: title,
        msg: msg,
        page: page_num,
        page_count: page_count
    };
    let result = call_api('mgt_sys_msg_api/query_sys_msg', data_obj);
    if (result['status']) {
        let data = JSON.stringify(result['data']);
        data = JSON.parse(data);
        // 繪製通知訊息列表
        sys_msg_data(data, data_obj);
    } else {
        sys_msg_noData();
    }
}

// 列表-尚無通知訊息資料
function sys_msg_noData() {
    let tab = "",
        tab_phone = "";
    tab = "<table class='contentsTable' width='auto' cellpadding='0' cellspacing='0'>"
    tab += "<tr align='center'>";
    tab += "<td class='contentsTh' colspan='3'>查無通知訊息資料</td>";
    tab += "</tr>";
    tab += "</table>";
    tab_phone += "<div class='phone_table' width='100%' cellpadding='0' cellspacing='0'>";
    tab_phone += "<div class='contentsTh'>查無通知訊息資料</div>";
    tab_phone += "</div>";
    $("#sysmsgAll").html(tab);
    $("#sysmsgAll_phone").html(tab_phone);
    document.getElementById('total_count').innerHTML = '資料總筆數：0';
    let hideobj = document.getElementById("allPageCountBox");
    hideobj.style.display = "none"; //隱藏筆數頁數層 
}

// 列表-依序列出所有通知訊息
function sys_msg_data(data, data_obj) {
    let baseUrl = document.getElementById('base_url').value;
    let page_num = parseInt(data_obj['page']);
    let page_count = parseInt(data_obj['page_count']);
    let hideobj = document.getElementById("allPageCountBox");
    let total_count = data['total_count']
    let tab_phone = "",
        tab = "";
    if (total_count != 0) {
        //輸出通知訊息列表
        tab += "<table class='contentsTable' width='auto' cellpadding='0' cellspacing='0'>"
        tab += "<tr align='center'>";
        tab += "<td class='contentsTh'>No</td><td class='contentsTh'>系統通知標題</td><td class='contentsTh'>系統通知訊息</td><td class='contentsTh'>建立時間</td><td class='contentsTh'>功能</td>";
        tab += "</tr>";
        //逐步輸出所有通知訊息資料
        let no = 0;
        let sys_msg = data['sys_msg'];
        for (let i = 0; i < sys_msg.length; i++) {
            no++;
            let sys_msg_data = JSON.stringify(sys_msg[i]);
            let edit_btn = "<button class='button width_80px inline_block' onclick='location_edit(" + sys_msg[i]['id'] + ")'>修改</button>";
            let del_btn = "<button class='button width_80px inline_block' onclick='confirm_del_sys_msg(" + sys_msg_data + ")'>刪除</button>";
            tab += "<tr align='center' class='contentsTr'>";
            tab += "<td>" + no + "</td><td>" + sys_msg[i]['title'] + "</td><td>" + sys_msg[i]['msg'] + "</td>";
            tab += "<td>" + sys_msg[i]['createTime'] + "</td><td>" + edit_btn + del_btn + "</td>";
            tab += "</tr>";
        }
        hideobj.style.display = "inline-block"; //隱藏筆數頁數層 
    } else {
        let tab = "";
        tab = "<table class='contentsTable' width='auto' cellpadding='0' cellspacing='0'>"
        tab += "<tr align='center'>";
        tab += "<td class='contentsTh' colspan='5'>查無通知訊息資料</td>";
        tab += "</tr>";
        tab_phone += "<div class='contentsTable' width='100%' cellpadding='0' cellspacing='0'><div class='contentsTh'>查無通知訊息資料</div></div>";
        hideobj.style.display = "none"; //隱藏筆數頁數層 
    }
    tab += "</table>";
    $("#sysmsgAll").html(tab);

    // 清除筆數頁數select裡的所有option
    document.getElementById("list_page").innerHTML = "";
    document.getElementById("list_page_count").innerHTML = "";
    let total_page = 1;
    if (data['total_page']) {
        total_page = data['total_page'];
    }
    // 輸出資料筆數及頁數
    page_count_select(total_page, page_num, page_count)
    document.getElementById('total_count').innerHTML = '資料總筆數：' + total_count;
}
