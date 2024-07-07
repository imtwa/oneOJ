<?php
// 引入数据库管理文件      
include 'database.php';
// 获取GET请求的参数      
$page = isset($_GET['page']) ? intval($_GET['page']) : 0;
$position = isset($_GET['position']) ? $_GET['position'] : 10;

// 计算查询的偏移量(起始值)    
$offset = ($page - 1) * $position + $position;

$sql = "SELECT COUNT(*) AS total_count FROM oj_user_info";
$stmt = $databaseConn->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
$user_count = $result->fetch_assoc()["total_count"];

// 查询语句  
$sql = "SELECT oui.*,ou.name
    FROM oj_user_info oui
    LEFT JOIN oj_user ou ON oui.id = ou.id
    ORDER BY oui.ranking ASC
    LIMIT ?, ?";

$stmt = $databaseConn->prepare($sql);
$stmt->bind_param("ss",$offset, $position);
$stmt->execute();
$result = $stmt->get_result();
$user_ranking = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $user_ranking[] = $row;
    }
}

echo json_encode(array(
    'count' => $user_count,  // 这里使用独立的查询得到的总行数    
    'user_ranking' => $user_ranking
));
?>