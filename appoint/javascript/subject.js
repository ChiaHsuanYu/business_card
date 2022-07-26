function get_template() {
    var data_obj = {};
    var templateSelect = document.getElementById('templateSelect');
    var result = call_api('mgt_subject_api/query_template', data_obj);
    if (result['status']) {
        var data = JSON.stringify(result['data']);
        data = JSON.parse(data);
        addOption(templateSelect, '請選擇模板', '');
        for (let i = 0; i < data.length; i++) {
            addOption(templateSelect, data[i]['template'], data[i]['id']);
        }
    } else {
        addOption(templateSelect, "查無模板", '');
    }
}

// 主題新增上傳
function add_subject() {
    var baseUrl = document.getElementById('base_url').value;
    var name = document.getElementById('name').value;
    var templateId = document.getElementById('templateSelect').value;
    var imageURL = $('#subject').prop('files')[0]; //取得上傳檔案屬性
    var subjectFile = $('#subjectFile').prop('files')[0]; //取得上傳檔案屬性
    var data = new FormData();
    data.append('name', name);
    data.append('templateId', templateId);
    data.append('imageURL', imageURL);
    data.append('subjectFile', subjectFile);
    var result = call_api_upload("mgt_subject_api/add_subject/", data);
    modal_show("msgModal");
    document.getElementById("model_body").innerHTML = string_replace(result['msg']);
    if (result['status']) {
        document.getElementById("modal_label").innerHTML = "系統訊息";
        sleep(1000).then(() => {
            location.href = baseUrl + "mgt_subject/index/";
        });
    } else {
        document.getElementById("modal_label").innerHTML = "格式錯誤";
    }
}

// 列表-呼叫取得所有主題資訊API
function subject_list() {
    var data_obj = {};
    var result = call_api('mgt_subject_api/query_all', data_obj);
    if (result['status']) {
        var data = JSON.stringify(result['data']);
        data = JSON.parse(data);
        // 繪製主題列表
        subject_data(data, data_obj);
    } else {
        subject_noData();
    }
}

// 列表-尚無主題資料
function subject_noData() {
    var tab = "",
        tab_phone = "";
    tab = "<table class='contentsTable' width='auto' cellpadding='0' cellspacing='0'>"
    tab += "<tr align='center'>";
    tab += "<td class='contentsTh' colspan='6'>查無主題資料</td>";
    tab += "</tr>";
    tab += "</table>";
    tab_phone += "<div class='phone_table' width='100%' cellpadding='0' cellspacing='0'>";
    tab_phone += "<div class='contentsTh'>查無主題資料</div>";
    tab_phone += "</div>";
    $("#subjectAll").html(tab);
    $("#subjectAll_phone").html(tab_phone);
    document.getElementById('total_count').innerHTML = '資料總筆數：0';
    var hideobj = document.getElementById("allPageCountBox");
    hideobj.style.display = "none"; //隱藏筆數頁數層 
}

// 列表-依序列出所有主題
function subject_data(subject, data_obj) {
    var baseUrl = document.getElementById('base_url').value;
    var data_obj = JSON.stringify(data_obj);
    var hideobj = document.getElementById("allPageCountBox");
    var count = json_count(subject); // 群組筆數
    var tab_phone = "",
        tab = "";
    if (count != 0) {

        //輸出主題列表
        tab += "<table class='rwd_table contentsTable' width='auto' cellpadding='0' cellspacing='0'>"
        tab += "<tr align='center'>";
        tab += "<td class='contentsTh'>No</td><td class='contentsTh'>主題名稱</td><td class='contentsTh'>模板名稱</td><td class='contentsTh'>主題縮圖</td><td class='contentsTh'>主題檔案</td>";
        tab += "<td class='contentsTh'>是否發布</td><td class='contentsTh'>建立時間</td><td class='contentsTh'>功能</td>";
        tab += "</tr>";
        //逐步輸出所有主題資料
        var no = 0;
        for (var i = 0; i < count; i++) {
            no++;
            var subject_data = JSON.stringify(subject[i]);
            var img = "<img class='img img_pointer' title='另開圖片視窗' src='" + subject[i]['imageURL'] + "' onclick='openImg(" + '"' + subject[i]['imageURL'] + '"' + ")'>";
            var file_btn = '<a href="' + subject[i]['subjectFile'] + '" class="inline_block margin_0 fault_a" download="subject.css">點選下載</a>';
            var releas_btn = "<button class='button width_80px inline_block' onclick='confirm_release_subject(" + subject_data + ")'>發布</button>";
            var edit_btn = "<button class='button width_80px inline_block' onclick='location_edit(" + subject[i]['id'] + ")'>修改</button>";
            var del_btn = "<button class='button width_80px inline_block' onclick='confirm_del_subject(" + subject_data + ")'>刪除</button>";

            tab += "<tr align='center' class='contentsTr'>";
            tab += "<td>" + no + "</td><td>" + subject[i]['name'] + "</td><td>" + subject[i]['template'] + "</td><td>" + img + "</td><td>" + file_btn + "</td>";
            tab += "<td>";
            if (subject[i]['isReleased'] == '1') {
                tab += "是(" + subject[i]['releaseTime'] + ")";
                releas_btn = "";
            } else {
                tab += "<p class='inline_block red'>否</p>";
            }
            tab += "</td>";
            tab += "<td>" + subject[i]['createTime'] + "</td><td>" + releas_btn + edit_btn + del_btn + "</td>";
            tab += "</tr>";
        }
        hideobj.style.display = "inline-block"; //隱藏筆數頁數層 
    } else {
        var tab = "";
        tab = "<table class='contentsTable' width='auto' cellpadding='0' cellspacing='0'>"
        tab += "<tr align='center'>";
        tab += "<td class='contentsTh' colspan='6'>查無主題資料</td>";
        tab += "</tr>";
        tab_phone += "<div class='contentsTable' width='100%' cellpadding='0' cellspacing='0'><div class='contentsTh'>查無主題資料</div></div>";
        hideobj.style.display = "none"; //隱藏筆數頁數層 
    }
    tab += "</table>";
    $("#subjectAll").html(tab);
    document.getElementById('total_count').innerHTML = '資料總筆數：' + count;
}

// 確認是否發布主題
function confirm_release_subject(subject_data) {
    modal_show("confirmModal");
    document.getElementById("confirm_modal_label").innerHTML = "系統訊息";
    document.getElementById("confirm_model_body").innerHTML = "是否確定發布主題「" + subject_data['name'] + "」?";
    document.getElementById("confirm_subjectId").value = subject_data['id'];
    document.getElementById("confirm_action").value = '1';
}

// 確認是否刪除主題
function confirm_del_subject(subject_data) {
    modal_show("confirmModal");
    document.getElementById("confirm_modal_label").innerHTML = "系統訊息";
    document.getElementById("confirm_model_body").innerHTML = "是否確定刪除主題「" + subject_data['name'] + "」?";
    document.getElementById("confirm_subjectId").value = subject_data['id'];
    document.getElementById("confirm_action").value = '2';
}

// 執行功能(發布/主題)
function subject_function() {
    modal_hide("confirmModal");
    var id = document.getElementById("confirm_subjectId").value;
    var action = document.getElementById("confirm_action").value;
    var data_obj = {
        subjectId: id,
    };
    if (action == '1') {
        var result = call_api('mgt_subject_api/update_isReleased_by_id', data_obj);
    } else {
        var result = call_api('mgt_subject_api/update_isDeleted_by_id', data_obj);
    }
    modal_show("msgModal");
    document.getElementById("modal_label").innerHTML = "系統訊息";
    document.getElementById("model_body").innerHTML = string_replace(result['msg']);
    if (result['status']) {
        // 重新呼叫列表
        subject_list();
    }
}

// 跳轉至修改主題頁面
function location_edit(id) {
    var baseUrl = document.getElementById('base_url').value;
    location.href = baseUrl + "mgt_subject/edit/" + id;
}

// 取得主題資料
function get_subject() {
    document.getElementById("alertMsg").innerHTML = "";
    var id = document.getElementById("edit_id").value;
    var data_obj = {
        id: id
    };
    var result = call_api("mgt_subject_api/get_subject/", data_obj);
    if (result['status']) {
        var data = result['data'];
        document.getElementById("name").value = data[0]['name'];
        document.getElementById("templateSelect").value = data[0]['templateId'];
        var tab = "";
        if (data[0]['imageURL']) {
            tab += "<input type='hidden' id='edit_img' value='" + data[0]['imageURL'] + "'>";
            tab += "<img class='subject_img' title='另開圖片視窗' src='" + data[0]['imageURL'] + "' onclick='openImg(" + '"' + data[0]['imageURL'] + '"' + ")'> <input type='button' class='width_80px' onclick='del_img(" + '"' + data[0]['imageURL'] + '"' + ")' value='刪除圖片'>";
        } else {
            tab += '<input type="file" class="width_220px height_30px" id="subject" data-target="subject" accept="*/*" multiple="multiple">';
        }
        document.getElementById("img_data_Box").innerHTML = tab;
        tab = "";
        if (data[0]['subjectFileName']) {
            tab += "<input type='hidden' id='edit_file' value='" + data[0]['subjectFile'] + "'>";
            tab += "<div class='inline_block'>" + data[0]['subjectFileName'] + "</div> <input type='button' class='width_80px' onclick='del_file(" + '"' + data[0]['subjectFile'] + '"' + ")' value='刪除檔案'>";
        } else {
            tab += '<input type="file" class="width_220px height_30px" id="subjectFile" data-target="subjectFile" accept="*/*" multiple="multiple">';
        }
        document.getElementById("file_data_Box").innerHTML = tab;
    } else {
        document.getElementById("alertMsg").innerHTML = "格式錯誤";
    }
}

// 刪除圖片
function del_img(imageURL) {
    var tab = '';
    tab += "<input type='hidden' id='edit_img' value='" + imageURL + "'>";
    tab += '<input type="file" class="width_220px height_30px" id="subject" data-target="subject" accept="*/*" multiple="multiple">';
    document.getElementById("img_data_Box").innerHTML = tab;
}

// 刪除檔案
function del_file(subjectFile) {
    var tab = '';
    tab += "<input type='hidden' id='edit_file' value='" + subjectFile + "'>";
    tab += '<input type="file" class="width_220px height_30px" id="subjectFile" data-target="subjectFile" accept="*/*" multiple="multiple">';
    document.getElementById("file_data_Box").innerHTML = tab;
}

// 修改主題
function edit_subject() {
    var baseUrl = document.getElementById('base_url').value;
    var id = document.getElementById('edit_id').value;
    var name = document.getElementById('name').value;
    var templateId = document.getElementById('templateSelect').value;
    var edit_imageURL = document.getElementById('edit_img').value;
    var edit_subjectFile = document.getElementById('edit_file').value;
    var subject = document.getElementById('subject');
    var subjectFile = document.getElementById('subjectFile');
    if (subject) {
        imageURL = $('#subject').prop('files')[0]; //取得上傳檔案屬性
    } else {
        imageURL = "";
    }
    if (subjectFile) {
        subjectFile = $('#subjectFile').prop('files')[0]; //取得上傳檔案屬性
    } else {
        subjectFile = "";
    }
    var data = new FormData();
    data.append('id', id);
    data.append('name', name);
    data.append('templateId', templateId);
    data.append('edit_imageURL', edit_imageURL);
    data.append('edit_subjectFile', edit_subjectFile);
    data.append('imageURL', imageURL);
    data.append('subjectFile', subjectFile);
    var result = call_api_upload("mgt_subject_api/edit_subject/", data);
    modal_show("msgModal");
    document.getElementById("model_body").innerHTML = string_replace(result['msg']);
    if (result['status']) {
        document.getElementById("modal_label").innerHTML = "系統訊息";
        sleep(1000).then(() => {
            location.href = baseUrl + "mgt_subject/index/";
        });
    } else {
        document.getElementById("modal_label").innerHTML = "格式錯誤";
    }
}