<?php
// 引入数据库管理文件  
include 'database.php';
// 获取GET请求的参数  
$user_id = $_GET['user_id'];
$problem_id = $_GET['problem_id'];

// 准备查询语句  
$sql = "SELECT * FROM oj_problem_submit WHERE user_id = ? AND problem_id = ?";
// 准备预处理语句  
$stmt = $databaseConn->prepare($sql);
// 绑定变量  
$stmt->bind_param("ss", $user_id, $problem_id);
// 执行预处理语句  
$stmt->execute();
// 获取结果  
$result = $stmt->get_result();
$data = []; 
// 检查查询结果是否为空  
if ($result->num_rows > 0) {
    // 输出数据  
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
        // $data[] = array(  
        //     "id"=> $row["id"],
        //     "user_id" => $row["user_id"], 
        //     "problem_id" => $row["problem_id"],
        //     "code"=> $row["code"],
        //     "language"=> $row["language"],
        //     "result"=> $row["result"],
        //     "score"=> $row["score"],
        //     "create_time"=> $row["create_time"]
        // );  
    }
}

echo json_encode($data);
?>