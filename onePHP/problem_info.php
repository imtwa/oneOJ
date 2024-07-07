<?php
// 引入数据库管理文件  
include 'database.php';
// 获取GET请求的参数  
$id = isset($_GET['id']) ? intval($_GET['id']) : 1;

// 准备查询语句  
$sql = "SELECT * FROM oj_problem WHERE id = ?";
// 准备预处理语句  
$stmt = $databaseConn->prepare($sql);
// 绑定变量  
$stmt->bind_param("s", $id);
// 执行预处理语句  
$stmt->execute();
// 获取结果  
$result = $stmt->get_result();
$data = array(); 
// 检查查询结果是否为空  
if ($result->num_rows > 0) {
    // 输出数据  
    while ($row = $result->fetch_assoc()) {
        $data = array(  
            "id"=> $row["id"],
            "name" => $row["name"], 
            "degree" => $row["degree"],
            "time_limit"=> $row["time_limit"],
            "memory_limit"=> $row["memory_limit"],
            "describe_pro"=> $row["describe_pro"],
            "in_describe"=> $row["in_describe"],
            "out_describe"=> $row["out_describe"],
            "in_sample"=> $row["in_sample"],
            "out_sample"=> $row["out_sample"],
            "tip"=> $row["tip"],
            "create_time"=> $row["create_time"]
        );  
    }
}

echo json_encode($data);


?>