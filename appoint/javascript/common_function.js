//呼叫API
function call_api(api_name, data_obj) {
    var baseUrl = document.getElementById('base_url').value;
    var result = [];
    $.ajax({
        cache: false,
        async: false,
        url: baseUrl + api_name,
        headers: {
            Authorization: 'Bearer ' + localStorage.getItem('token')
        },
        type: "POST",
        data: data_obj,
        success: function(json) {
            json = JSON.stringify(json);
            json = JSON.parse(json);
            result = json;
        },
        error: function(xhr, status, error) {
            result['status'] = 0;
            result['message'] = "連線失敗";
            console.log("Error    ==================    API Response    ==================");
            console.log(xhr.responseText);
            console.log(error);
        }
    });
    return result;
}

function call_api_upload(api_name, data_obj) {
    var baseUrl = document.getElementById('base_url').value;
    var result = [];
    $.ajax({
        cache: false,
        async: false,
        contentType: false,
        processData: false,
        url: baseUrl + api_name,
        headers: {
            Authorization: 'Bearer ' + localStorage.getItem('token')
        },
        type: "POST",
        data: data_obj,
        success: function(json) {
            json = JSON.stringify(json);
            json = JSON.parse(json);
            result = json;
        },
        error: function(xhr, status, error) {
            result['status'] = 0;
            result['message'] = "連線失敗";
            console.log("Error    ==================    API Response    ==================");
            console.log(xhr.responseText);
            console.log(error);
        }
    });
    return result;
}

// 筆數頁數選單
function page_count_select(total_page, page, count) {
    var pageSelect = document.getElementById('list_page');
    var countSelect = document.getElementById('list_page_count');
    if (total_page > 0) {
        for (var i = 1; i <= total_page; i++) {
            addOption(pageSelect, i, i);
        }
        display_block("last_page", "next_page");
    } else {
        display_none("last_page", "next_page");
    }
    $('#list_page').val(page);
    for (var i = 5; i <= 20; i = i + 5) {
        addOption(countSelect, i, i);
    }
    $('#list_page_count').val(count);
}

// 動態新增optgroup函式
function addOptgroup(pageSelect, text) {
    var optgroup = document.createElement("optgroup");
    optgroup.label = text;
    pageSelect.options.add(optgroup);
}

// 動態新增option函式
function addOption(pageSelect, text, value) {
    var option = document.createElement("option");
    option.text = text;
    option.value = value;
    pageSelect.options.add(option);
}

// 隱藏上下頁button
function display_none(last_page_id, next_page_id) {
    $("#" + last_page_id).css("display", "none");
    $("#" + next_page_id).css("display", "none");
}

// 顯示上下頁button
function display_block(last_page_id, next_page_id) {
    $("#" + last_page_id).css("display", "inline-block");
    $("#" + next_page_id).css("display", "inline-block");
}

// 列表上一頁
function check_last_page(pageId, countId) {
    document.getElementById("page_status").innerHTML = '';
    var page = document.getElementById(pageId).value;
    var page_count = document.getElementById(countId).value;
    page = parseInt(page);
    // 檢查是否已在第1頁
    if (page > 1) {
        page = page - 1;
    } else {
        document.getElementById("page_status").innerHTML = '查無上頁';
    }
    var data = {
        'page': page,
        'page_count': page_count,
    };
    return data;
}

// 列表下一頁
function check_next_page(pageId, countId) {
    document.getElementById("page_status").innerHTML = '';
    var page = document.getElementById(pageId).value;
    var page_count = document.getElementById(countId).value;
    var array = new Array(); //定义数组 
    // 取得所有頁數
    $("#" + pageId + " option").each(function() {
        var txt = $(this).val(); //获取option值 
        if (txt != '') {
            txt = parseInt(txt);
            array.push(txt); //添加到数组中
        }
    });

    var max_page = Math.max.apply(null, array); // 取得最終頁
    page = parseInt(page);
    // 檢查是否已在最終頁
    if (page < max_page) {
        page = page + 1;
    } else {
        document.getElementById("page_status").innerHTML = '查無下頁';
    }
    var data = {
        'page': page,
        'page_count': page_count,
    };
    return data;
}

// 函式-取得陣列or物件元素數量
function json_count(x) {
    var t = typeof x;
    if (t == 'string') {
        return x.length;
    } else if (t == 'object') {
        var n = 0;
        for (var i in x) {
            n++;
        }
        return n;
    }
    return false;
}

//延遲執行(秒)
function sleep(time) {
    return new Promise((resolve) => setTimeout(resolve, time));
}

//轉跳頁面
function gotoPage(path) {
    var baseUrl = document.getElementById('base_url').value;
    location.href = baseUrl + path;
}

// 取代換行符號
function note_replace(data) {
    if (data) {
        var note = data.replace(/<br \/>/g, '');
        note = note.replace(/<br>/g, '');
    } else {
        var note = '';
    }
    return note;
}

// 取得當天日期
function getDate() {
    var NowTime = new Date();
    var year = NowTime.getFullYear();
    var month = (NowTime.getMonth() + 1 < 10 ? '0' : '') + (NowTime.getMonth() + 1);
    var date = (NowTime.getDate() < 10 ? '0' : '') + NowTime.getDate();
    var date = year + "-" + month + "-" + date + " 00:00:00";
    return date;
}

// 另開啟圖片視窗 for color
function openImg(imgUrl) {
    window.open(imgUrl, 'Img', config = 'height=500,width=500');
}

// 檢視下滑資訊
function look_slideToggle(divId) {
    if (document.getElementById(divId)) {
        $('.' + divId).slideToggle(300);
    }
}