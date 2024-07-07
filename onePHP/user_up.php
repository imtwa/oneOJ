<?php
// 引入数据库管理文件      
include 'database.php';

$user_id = $_POST['id'];
$sign = $_POST['sign'];

if (!isset($user_id) || !isset($sign)) {
    $response["result"] = false;
    $response["message"] = "参数错误";
} else {
    $upSql = "UPDATE oj_user_info  
    SET sign = ? 
    WHERE id = ?;";
    $stmt = $databaseConn->prepare($upSql);
    $stmt->bind_param("ss", $sign, $user_id);
    if (true === $stmt->execute()) {
        $response["result"] = true;
        $response["message"] = "更新成功！";
    } else {
        $response["result"] = false;
        $response["message"] = "更新失败:" . $stmt->error;
    }
}

echo json_encode($response);

?>