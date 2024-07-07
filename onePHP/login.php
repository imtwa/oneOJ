<?php

// 引入数据库管理文件  
include 'database.php';

// 获取POST请求中的参数  
$username = $_POST['username'];
$password = $_POST['password'];
$captcha = $_POST['captcha'];
$capt_t = isset($_POST["t"]) ? $_POST["t"] : 1234;

// 检验验证码
$response = check_captcha($captcha, $capt_t);

// 如果验证码为真
if (true === $response["result"]) {
    // 执行查询  
    // 准备查询语句  
    $sql = "SELECT * FROM oj_user WHERE name = ?";

    // 准备预处理语句  
    $stmt = $databaseConn->prepare($sql);
    // 绑定变量  
    $stmt->bind_param("s", $username);
    // 执行预处理语句  
    $stmt->execute();
    // 获取结果  
    $result = $stmt->get_result();

    // 检查查询结果是否为空  
    if ($result->num_rows > 0) {
        // 输出数据  
        while ($row = $result->fetch_assoc()) {
            if (md5($password) != $row["password"]) {
                $response["result"] = false;
                $response["message"] = "密码错误";
            } else {
                $response["message"] = array(
                    "id" => $row["id"],
                    "name" => $row["name"],
                    "limits" => $row["limits"]
                );
            }
            break;
        }
    } else {
        $response["result"] = false;
        $response["message"] = "没有这个用户";
    }
}


echo json_encode($response);

if (isset($response["message"]['id'])) {
    // 计算更新用户信息
    count_up_user_info($databaseConn, $response["message"]['id']);
}

//var str = String.fromCharCode(parseInt(code, 16));  
// console.log(str);  // 输出 "密码错误"

?>