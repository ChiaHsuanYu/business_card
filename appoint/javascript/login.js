// 確認登入紀錄
function check_login() {
    var baseUrl = document.getElementById('base_url').value;
    var data_obj = {};
    var result = call_api('mgt_login_api/check_login', data_obj);
    console.log(result);
    if (result['status']) {
        location.href = baseUrl + "users/index/";
    }
}

// 登入
function login() {
    var baseUrl = document.getElementById('base_url').value;
    var account = document.getElementById('account').value;
    var password = document.getElementById('password').value;
    var data_obj = {
        account: account,
        password: password,
    };
    var result = call_api('mgt_login_api/login', data_obj);
    if (result['status']) {
        localStorage.setItem('token', result['data']['token']);
        localStorage.setItem('UsertId', result['data']['id']);
        document.getElementById("alertMsg").innerHTML = result['msg'];
        sleep(1000).then(() => {
            location.href = baseUrl + "users/index/";
        });
    } else {
        document.getElementById("alertMsg").innerHTML = result['msg'];
    }
}
