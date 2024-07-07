<?php
// 引入数据库管理文件  
include 'database.php';
include 'php\entity\JsonRespons.php';
include 'php\utils\submit.php';

$responseInfo = new JsonResponse();

// 获取参数
$user_id = $_POST['user_id'];
$problem_id = $_POST['problem_id'];
$code = $_POST['code'];
$language = isset($_POST['language']) ? $_POST['language'] : 1;

// 检查参数是否为空  
if (empty($user_id) || empty($problem_id) || empty($code)) {
    $responseInfo->setMessage('参数不能为空');
    die($responseInfo->get());
}

// 数据库查询，检查user_id是否存在  
$stmt = $databaseConn->prepare("SELECT * FROM oj_user WHERE id = ?");
$stmt->bind_param('s', $user_id);
$stmt->execute();

$user_result = $stmt->get_result();
if ($user_result->num_rows < 1) {
    $responseInfo->setMessage('用户ID不存在');
    die($responseInfo->get());
} else if ($user_result->num_rows > 1) {
    $responseInfo->setMessage('用户ID重复 请联系管理员');
    die($responseInfo->get());
}

// 数据库查询，检查problem_id是否存在  
$stmt = $databaseConn->prepare("SELECT * FROM oj_problem WHERE id = ?");
$stmt->bind_param('s', $problem_id);
$stmt->execute();

$problem_result = $stmt->get_result();

if ($problem_result->num_rows < 1) {
    $responseInfo->setMessage('题目ID不存在');
    die($responseInfo->get());
} else if ($problem_result->num_rows > 1) {
    $responseInfo->setMessage('题目ID有重复 请联系管理员');
    die($responseInfo->get());
}


// 获得php文件所在文件夹
$currentDirectoryPath = __DIR__;

// 获得题目文件夹的相对路径
$problemDirPath = "";
while ($row = $problem_result->fetch_assoc()) {
    $problemDirPath = $currentDirectoryPath . $row["path"];
    break;
}

// 获得测试数据所在文件夹
$testDirPath = $problemDirPath . "/test";
// 创建一个临时文件夹
$temDirPath = $problemDirPath . "/tem" . "_" . $user_id . "_" . $problem_id . "_" . time();

// 创建文件夹  
if (!mkdir($temDirPath)) {
    $responseInfo->setMessage('错误，无法创建临时文件夹');
    die($responseInfo->get());
}

// echo $testDirPath . "<br>";
// echo $temDirPath . "<br>";

// 获取in out数据
$testfiles = getFilesByExtension($testDirPath);

$uploadResponses = [];

// 获得测试点运行编号
for ($i = 0; $i < count($testfiles['in']); $i++) {
    $inFilePath = $testfiles['in'][$i];
    $uploadResponse = uploadCode($code, $inFilePath, $language);
    $uploadResponses[] = $uploadResponse;
}

// 延迟x秒再执行代码  
// sleep(5);

// 获得运行结果 并保存在文件里面
$run_ram = 0;
$run_time = 0;
for ($i = 0; $i < count($uploadResponses); $i++) {
    // // 获得in的文件名并去除后缀 只保留数字
    // // 因为文件读取时顺序和循环顺序不同 所以要确保输入和输出文件对应
    // $inFilePath = $testfiles['in'][$i];
    // $num = str_replace('.in', '', basename($inFilePath));
    $num = $i + 1;
    saveRequestToFile($uploadResponses[$i], $num, $temDirPath);

    $getUrl = "https://www.dotcpp.com/oj/status-ajax_t.php?solution_id=" . $uploadResponses[$i];
    // 发起 GET 请求并获取响应
    $getResponse = file_get_contents($getUrl);

    // 将字符串转换为数组，每个元素都是一个字符  
    $responseArray = explode(",", $getResponse);
    $run_ram += intval($responseArray[1]);
    $run_time += intval($responseArray[2]);
}

$run_ram = $run_ram / count($uploadResponses);
$run_time = $run_time / count($uploadResponses);


$run_ram = floor($run_ram);
$run_time = floor($run_time);

// 获取临时文件夹下的文件
$temfiles = getFilesByExtension($temDirPath);

// 创建一个数据数组  
$dataArray = array();

// 进行效验
for ($i = 0; $i < count($temfiles['out']); $i++) {
    $temFilePath = $temfiles['out'][$i];
    $testFilePath = $testfiles['out'][$i];
    // 进行检查
    $check = checkFile($temFilePath, $testFilePath);

    $item = array(
        'isTrue' => $check['isTrue'],
        'message' => $check['message'],
        'testIn' => $check['testIn'],
        'testOut' => $check['testOut'],
        'userOut' => $check['userOut'],
    );

    $dataArray[] = $item;
}

$responseInfo->setData($dataArray);
// 有的测试数据过大 json_encode无法转换 这里改成在前端转换
$responseInfo->printResponse();

// 将提交状态导入数据库
$sql = "INSERT INTO oj_problem_submit (user_id, problem_id, code, language ,result ,score,run_ram,run_time) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $databaseConn->prepare($sql);
//绑定参数并执行预处理语句
$result = $responseInfo->getMessage();
$score = $responseInfo->getScore();
$stmt->bind_param("ssssssss", $user_id, $problem_id, $code, $language, $result, $score, $run_ram, $run_time);
if (true === $stmt->execute()) {
    // echo '插入成功';F
}

// 将每次的测试点测试状态导入数据库


// 删除临时文件
deleteDirectory($temDirPath);

// 计算更新用户信息
count_up_user_info($databaseConn, $user_id);

?>