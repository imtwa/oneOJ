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
$selSql = "SELECT oui.*,ou.name
    FROM oj_user_info oui
    LEFT JOIN oj_user ou ON oui.id = ou.id
    WHERE oui.id = ?";

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

// 得到尝试的题目
$trySql = "SELECT op.id,op.name,
    COUNT(CASE WHEN ops.result = 'AC' THEN 1 END) AS user_ac_count,
    COUNT(*) AS user_submit_count
    FROM oj_problem op  
    LEFT JOIN oj_problem_submit ops ON op.id = ops.problem_id
    WHERE ops.user_id = ? 
    GROUP BY ops.problem_id  
    HAVING user_ac_count = 0";

$stmt = $databaseConn->prepare($trySql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$tryPro = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tryPro[] = $row;
    }
}

// 得到已解决的题目
$resolvedSql = "SELECT op.id,op.name,
    COUNT(CASE WHEN ops.result = 'AC' THEN 1 END) AS user_ac_count,
    COUNT(*) AS user_submit_count
    FROM oj_problem op  
    LEFT JOIN oj_problem_submit ops ON op.id = ops.problem_id
    WHERE ops.user_id = ? 
    GROUP BY ops.problem_id  
    HAVING user_ac_count > 0";

$stmt = $databaseConn->prepare($resolvedSql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$resolvedPro = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $resolvedPro[] = $row;
    }
}

// 得到未解决的题目
$unResolvedSql = "SELECT op.id,op.name
    FROM oj_problem op  
    LEFT JOIN oj_problem_submit
    ops ON op.id = ops.problem_id AND ops.user_id = ?  
    WHERE ops.problem_id IS NULL;";

$stmt = $databaseConn->prepare($unResolvedSql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$unResolvedPro = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $unResolvedPro[] = $row;
    }
}

echo json_encode(array(
    'count' => $oj_problem_count,
    'user_data' => $user_data,
    'tryPro' => $tryPro,
    'resolvedPro' => $resolvedPro,
    'unResolvedPro' => $unResolvedPro
));
?>