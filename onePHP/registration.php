<?php
// 开始一个PHP会话，用于变量传递 
// session_start();
// 引入数据库管理文件  
include 'database.php';

// 获取POST请求中的参数  
$username = $_POST['username'];
$password = md5($_POST['password']);
$email = $_POST['email'];
$captcha = $_POST['captcha'];
$capt_t = isset($_POST["t"]) ? $_POST["t"] : 1234;
$limits = 0;

// 检验验证码
$response = check_captcha($captcha, $capt_t);

// 如果验证码为真
if (true === $response["result"]) {
    // 执行查询语句来检查名字是否已经存在  
    $sqlCheck = "SELECT * FROM oj_user WHERE name = '$username'";
    $resultCheck = mysqli_query($databaseConn, $sqlCheck);

    // 检查查询结果  
    if (mysqli_num_rows($resultCheck) > 0) {
        $response["result"] = false;
        $response["message"] = "昵称重复";
    } else {
        // 名字不存在，执行插入语句  
        $sql = "INSERT INTO oj_user (name, password, email, limits) VALUES (?, ?, ?, ?)";
        $stmt = $databaseConn->prepare($sql);
        //绑定参数并执行预处理语句
        $stmt->bind_param("ssss", $username, $password, $email, $limits);
        if (true === $stmt->execute()) {
            $response["message"] = "用户注册成功！";
        } else {
            $response["result"] = false;
            $response["message"] = "用户注册失败:" . $stmt->error;
        }
    }

}

echo json_encode($response);

?>