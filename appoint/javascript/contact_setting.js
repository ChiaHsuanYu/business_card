// 取得親密度累積設定預設值
function get_contact_setting() {
    var data_obj = {};
    var result = call_api('contact_setting_api/get_contact_setting', data_obj);
    if (result['status']) {
        var data = JSON.stringify(result['data']);
        data = JSON.parse(data);
        document.getElementById("id").value = data[0]['id'];
        document.getElementById("distance").value = data[0]['distance'];
        document.getElementById("max_contact_time").value = data[0]['max_contact_time'];
        document.getElementById("min_contact_time").value = data[0]['min_contact_time'];
    }
}

// 修改親密度累積設定
function update_contact_setting() {
    modal_hide('contactSettingModal');
    let id = document.getElementById('id').value;
    let distance = document.getElementById('distance').value;
    let max_contact_time = document.getElementById('max_contact_time').value;
    let min_contact_time = document.getElementById('min_contact_time').value;
    let data_obj = {
        id: id,
        distance: distance,
        max_contact_time: max_contact_time,
        min_contact_time: min_contact_time,
    };
    if(max_contact_time < min_contact_time){
        modal_show("msgModal");
        document.getElementById("model_body").innerHTML = "最小接觸時間不可大於最大接觸時間";
        document.getElementById("modal_label").innerHTML = "格式錯誤";
        return;
    }
    let result = call_api('contact_setting_api/update_contact_setting_by_id', data_obj);
    modal_show("msgModal");
    document.getElementById("model_body").innerHTML = string_replace(result['msg']);
    if(result['status']){
        document.getElementById("modal_label").innerHTML = "系統訊息";
    }else{
        document.getElementById("modal_label").innerHTML = "格式錯誤";
    }
}