<?php
// 引入数据库管理文件      
include 'database.php';
// 获取GET请求的参数      
$user_id = $_GET['user_id'];

// 计算查询的偏移量(起始值)    
$offset = ($page - 1) * $position + $position;

// 查询oj_problem表的总行数  
$oj_problem_count_sql = "SELECT COUNT(*) AS total_count FROM oj_problem";
$stmt = $databaseConn->prepare($oj_problem_count_sql);
$stmt->execute();
$result = $stmt->get_result();
$oj_problem_count = $result->fetch_assoc()["total_count"];

// 查询语句  
$selSql = "SELECT * FROM oj_user_info WHERE id = ?";

$stmt = $databaseConn->prepare($selSql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $user_data = $row;
    }
}

echo json_encode(array(
    'count' => $oj_problem_count,
    'user_data' => $user_data,
));
?>