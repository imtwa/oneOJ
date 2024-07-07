<?php
// 引入数据库管理文件      
include 'database.php';
// 获取GET请求的参数      
$page = isset($_GET['page']) ? intval($_GET['page']) : 0;
$position = isset($_GET['position']) ? $_GET['position'] : 10;
$limits = isset($_GET['limits']) ? $_GET['limits'] : 1;
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
$sql = "SELECT p.id, p.name, p.degree, COUNT(s.problem_id) as submit_count,
        COUNT(CASE WHEN s.result = 'AC' THEN s.problem_id END) as ac_count        
        FROM oj_problem p        
        LEFT JOIN oj_problem_submit s ON p.id = s.problem_id        
        WHERE p.visible >= ?        
        GROUP BY p.id, p.name, p.degree      
        LIMIT ?, ?";

$stmt = $databaseConn->prepare($sql);
$stmt->bind_param("sss", $limits, $offset, $position);
$stmt->execute();
$result = $stmt->get_result();
$total_data = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $row_data = $row;
        if (isset($user_id)) {

            $selSql = "SELECT COUNT(*) AS user_submit_count, 
                COUNT(CASE WHEN result = 'AC' THEN 1 END) AS user_ac_count  
                FROM oj_problem_submit WHERE user_id = ? AND problem_id = ?";

            $stmt2 = $databaseConn->prepare($selSql);
            // 绑定变量  
            $stmt2->bind_param("ss", $user_id, $row["id"]);
            // 执行预处理语句  
            $stmt2->execute();
            // 获取结果  
            $result2 = $stmt2->get_result();
            $user_submit = $result2->fetch_assoc();
            if ($user_submit["user_submit_count"] == 0) {
                $row_data["state"] = "";
            } else {
                if ($user_submit["user_ac_count"] > 0) {
                    $row_data["state"] = "AC";
                } else {
                    $row_data["state"] = "WE";
                }
            }
        } else {
            $row_data["state"] = "";
        }
        $total_data[] = $row_data;

    }
}

echo json_encode(array(
    'count' => $oj_problem_count,  // 这里使用独立的查询得到的总行数    
    'total_data' => $total_data
));
?>