// 模板新增上傳
function add_template() {
    var baseUrl = document.getElementById('base_url').value;
    var template = document.getElementById('template').value;
    var data_obj = {
        template: template
    };
    var result = call_api('mgt_template_api/add_template', data_obj);
    modal_show("msgModal");
    document.getElementById("model_body").innerHTML = string_replace(result['msg']);
    if (result['status']) {
        document.getElementById("modal_label").innerHTML = "系統訊息";
        sleep(1000).then(() => {
            location.href = baseUrl + "mgt_template/index/";
        });
    } else {
        document.getElementById("modal_label").innerHTML = "格式錯誤";
    }
}

// 列表-呼叫取得所有模板資訊API
function template_list() {
    var data_obj = {};
    var result = call_api('mgt_template_api/query_all', data_obj);
    if (result['status']) {
        var data = JSON.stringify(result['data']);
        data = JSON.parse(data);
        // 繪製模板列表
        template_data(data, data_obj);
    } else {
        template_noData();
    }
}

// 列表-尚無模板資料
function template_noData() {
    var tab = "",
        tab_phone = "";
    tab = "<table class='contentsTable' width='auto' cellpadding='0' cellspacing='0'>"
    tab += "<tr align='center'>";
    tab += "<td class='contentsTh' colspan='3'>查無模板資料</td>";
    tab += "</tr>";
    tab += "</table>";
    tab_phone += "<div class='phone_table' width='100%' cellpadding='0' cellspacing='0'>";
    tab_phone += "<div class='contentsTh'>查無模板資料</div>";
    tab_phone += "</div>";
    $("#templateAll").html(tab);
    $("#templateAll_phone").html(tab_phone);
    document.getElementById('total_count').innerHTML = '資料總筆數：0';
    var hideobj = document.getElementById("allPageCountBox");
    hideobj.style.display = "none"; //隱藏筆數頁數層 
}

// 列表-依序列出所有模板
function template_data(template, data_obj) {
    var baseUrl = document.getElementById('base_url').value;
    var data_obj = JSON.stringify(data_obj);
    var hideobj = document.getElementById("allPageCountBox");
    var count = json_count(template); // 群組筆數
    var tab_phone = "",
        tab = "";
    if (count != 0) {

        //輸出模板列表
        tab += "<table class='rwd_table contentsTable' width='auto' cellpadding='0' cellspacing='0'>"
        tab += "<tr align='center'>";
        tab += "<td class='contentsTh'>No</td><td class='contentsTh'>模板名稱</td><td class='contentsTh'>建立時間</td><td class='contentsTh'>功能</td>";
        tab += "</tr>";
        //逐步輸出所有模板資料
        var no = 0;
        for (var i = 0; i < count; i++) {
            no++;
            var template_data = JSON.stringify(template[i]);
            var edit_btn = "<button class='button width_80px inline_block' onclick='location_edit(" + template[i]['id'] + ")'>修改</button>";
            var del_btn = "<button class='button width_80px inline_block' onclick='confirm_del_template(" + template_data + ")'>刪除</button>";

            tab += "<tr align='center' class='contentsTr'>";
            tab += "<td>" + no + "</td><td>" + template[i]['template'] + "</td>";
            tab += "<td>" + template[i]['createTime'] + "</td><td>" + edit_btn + del_btn + "</td>";
            tab += "</tr>";
        }
        hideobj.style.display = "inline-block"; //隱藏筆數頁數層 
    } else {
        var tab = "";
        tab = "<table class='contentsTable' width='auto' cellpadding='0' cellspacing='0'>"
        tab += "<tr align='center'>";
        tab += "<td class='contentsTh' colspan='3'>查無模板資料</td>";
        tab += "</tr>";
        tab_phone += "<div class='contentsTable' width='100%' cellpadding='0' cellspacing='0'><div class='contentsTh'>查無模板資料</div></div>";
        hideobj.style.display = "none"; //隱藏筆數頁數層 
    }
    tab += "</table>";
    $("#templateAll").html(tab);
    document.getElementById('total_count').innerHTML = '資料總筆數：' + count;
}

// 確認是否刪除模板
function confirm_del_template(template_data) {
    modal_show("confirmModal");
    document.getElementById("confirm_modal_label").innerHTML = "系統訊息";
    document.getElementById("confirm_model_body").innerHTML = "是否確定刪除模板「" + template_data['template'] + "」?";
    document.getElementById("confirm_templateId").value = template_data['id'];
}

// 執行功能(刪除)
function template_function() {
    modal_hide("confirmModal");
    var id = document.getElementById("confirm_templateId").value;
    var data_obj = {
        id: id,
    };
    var result = call_api('mgt_template_api/update_isDeleted_by_id', data_obj);
    modal_show("msgModal");
    document.getElementById("modal_label").innerHTML = "系統訊息";
    document.getElementById("model_body").innerHTML = string_replace(result['msg']);
    if (result['status']) {
        // 重新呼叫列表
        template_list();
    }
}

// 跳轉至修改模板頁面
function location_edit(id) {
    var baseUrl = document.getElementById('base_url').value;
    location.href = baseUrl + "mgt_template/edit/" + id;
}

// 取得模板資料
function get_template() {
    document.getElementById("alertMsg").innerHTML = "";
    var id = document.getElementById("edit_id").value;
    var data_obj = {
        id: id
    };
    var result = call_api("mgt_template_api/get_template/", data_obj);
    if (result['status']) {
        var data = result['data'];
        document.getElementById("template").value = data[0]['template'];
    } else {
        document.getElementById("alertMsg").innerHTML = "格式錯誤";
    }
}

// 修改模板
function edit_template() {
    var baseUrl = document.getElementById('base_url').value;
    var id = document.getElementById('edit_id').value;
    var template = document.getElementById('template').value;
    var data_obj = {
        id: id,
        template: template
    };
    var result = call_api("mgt_template_api/edit_template/", data_obj);
    modal_show("msgModal");
    document.getElementById("model_body").innerHTML = string_replace(result['msg']);
    if (result['status']) {
        document.getElementById("modal_label").innerHTML = "系統訊息";
        sleep(1000).then(() => {
            location.href = baseUrl + "mgt_template/index/";
        });
    } else {
        document.getElementById("modal_label").innerHTML = "格式錯誤";
    }
}