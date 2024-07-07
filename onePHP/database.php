<?php

// 数据库配置信息  
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "oneOJ";

// 创建数据库连接  
$conn = new mysqli($servername, $username, $password, $dbname);

// 检查连接是否成功  
if ($conn->connect_error) {
    die("数据库连接失败: " . $conn->connect_error);
}

// 将连接对象存储在全局变量中  
global $databaseConn;
$databaseConn = $conn;

/**  
 * 检查验证码函数  
 *  
 * @param string $code 验证码代码  
 * @param int $capt_t 验证码时间戳  
 * @return array 包含布尔值结果的关联数组，'result'键对应检查结果，'message'键对应消息字符串  
 */
function check_captcha($code, $capt_t)
{

    $result = false;
    $message = "初始化";

    $captcha_code = substr(md5($capt_t), -4);
    // 比较验证码和时间条件  
    if ($captcha_code == $code && time() - $capt_t <= 60 * 3) {
        // 如果验证码正确且时间条件满足
        $result = true;
        $message = "CORRECT";
    } else {
        // 如果验证码错误或时间超时，则根据错误类型设置消息  
        $message = ($captcha_code != $code) ? "验证码错误" : "时间超时，验证码失效";
    }


    // // 验证后删除会话中的验证码，防止暴力破解  
    // unset($_SESSION['captcha_code']);  
    // unset($_SESSION['captcha_time']);  

    return array('result' => $result, 'message' => $message);
}

/**  
 * 计算更新用户信息 经验 提交 排名等数据  
 * 
 * @param  $databaseConn 数据库连接信息
 * @param  $user_id 用户id
 */
function count_up_user_info($databaseConn, $user_id)
{
    // 获得该用户的总提交数 总通过数 总解决数
    $sql = "SELECT   
        COUNT(*) AS submit_all,  
        COUNT(CASE WHEN result = 'AC' THEN 1 END) AS submit_ac, 
        (SELECT COUNT(DISTINCT problem_id) FROM oj_problem_submit WHERE user_id = ? AND result = 'AC') AS submit_pro  
        FROM oj_problem_submit   
        WHERE user_id = ?";
    $stmt = $databaseConn->prepare($sql);
    $stmt->bind_param("ss", $user_id, $user_id);
    $stmt->execute();
    // 获取结果  
    $result = $stmt->get_result();
    $user_submit = $result->fetch_assoc();

    $submit_all = $user_submit["submit_all"];
    $submit_ac = $user_submit["submit_ac"];
    $submit_pro = $user_submit["submit_pro"];
    // 计算经验值
    $exp = $submit_ac * 10 - ($submit_all - $submit_ac) * 3;
    // 计算等级
    $exps = [0, 10, 100, 200, 500, 1000, 200, 5000, 10000, 20000, 50000];
    $grade = 0;
    for ($i = 0; $i < count($exps); $i++) {
        if ($exp <= $exps[$i]) {
            $grade = $i + 1;
            break;
        }
    }

    // $sqlUserId = "SELECT COUNT(*) AS user_count FROM oj_user_info WHERE id = ?";
    // $stmt = $databaseConn->prepare($sqlUserId);
    // $stmt->bind_param("s", $user_id);
    // $stmt->execute();
    // // 获取结果  
    // $result = $stmt->get_result();
    // $user_count = $result->fetch_assoc()["user_count"];
    // // 如果用户不存在 排名需要+1
    // if ($user_count == 0) {
    //     $ranking += 1;
    // }

    // 更新or插入数据

    $sqlUp = "INSERT INTO oj_user_info (id, submit_all, submit_ac, submit_pro, exp, grade)  
    VALUES (?, ?, ?, ?, ?, ?)  
    ON DUPLICATE KEY UPDATE  
    submit_all = VALUES(submit_all),  
    submit_ac = VALUES(submit_ac),  
    submit_pro = VALUES(submit_pro),  
    exp = VALUES(exp),
    grade = VALUES(grade)";

    // echo $user_id  ." " . $submit_all ." ". $submit_ac ." ". $submit_pro ." ". $exp ." " .$grade ."<br>";

    $stmt = $databaseConn->prepare($sqlUp);
    $stmt->bind_param("ssssss", $user_id, $submit_all, $submit_ac, $submit_pro, $exp, $grade);
    // 更新数据!
    if ($stmt === false) {
        die('SQL 语句准备失败: ' . $databaseConn->error);
    }

    if ($stmt->execute() === false) {
        die('执行更新失败: ' . $stmt->error);
    }

    // 获得排名
    $sqlRanking = "SELECT id  
        FROM oj_user_info
        ORDER BY exp DESC";
    $ranking = 0;
    $stmt = $databaseConn->prepare($sqlRanking);
    $stmt->execute();
    // 获取结果  
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $ranking++;
        if ($user_id == $row["id"]) {
            break;
        }
    }

    $upSql = "UPDATE oj_user_info  
        SET ranking = ? 
        WHERE id = ?;";
    $stmt = $databaseConn->prepare($upSql);
    $stmt->bind_param("ss",$ranking, $user_id);
    $stmt->execute();

}

?>