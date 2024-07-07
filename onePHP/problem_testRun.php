<?php
// 引入数据库管理文件  
include 'database.php';
include 'php\entity\JsonRespons.php';
include 'php\utils\submit.php';

$responseInfo = new JsonResponse();

// 获取参数
$code = $_POST['code'];
$language = isset($_POST['language']) ? $_POST['language'] : 1;
$input_text = $_POST['input_text'];

// 检查参数是否为空  
if (empty($code) || empty($language)) {
    $responseInfo->setMessage('参数不能为空');
    die($responseInfo->get());
}

$url = "https://www.dotcpp.com/oj/compiler_ol_submit.php?ajax";
$data = [
    'language' => $language,
    'ifO2[]' => 'O2',
    'source' => $code,
    'input_text' => $input_text,
];

// 使用 http_build_query 函数将 $data 数组转换为 URL 编码的字符串  
$postData = http_build_query($data);

// 设置请求选项  
$options = [
    'http' => [
        'method' => 'POST',
        'header' => 'Content-type: application/x-www-form-urlencoded',
        'content' => $postData,
    ],
];
// 发起 POST 请求并将响应保存在 $response 变量中  
$context = stream_context_create($options);
$uploadResponse = file_get_contents($url, false, $context);

// 延迟x秒再执行代码  
sleep(5);

// 网站更新 无法访问结果页面
// $getUrl = "https://www.dotcpp.com/run/" .$uploadResponse;
// $html = file_get_contents($getUrl); // 建立页面连接  
// $dom = new DOMDocument();           // 使用DOM扩展库加载HTML  
// @$dom->loadHTML($html);
// // 选择第二个`<textarea>`元素  
// $textarea = $dom->getElementsByTagName('textarea')->item(1);
// // 提取`<textarea>`标签内的内容  
// $convertedResponse = $textarea->nodeValue;

// 调用api返回结果
$getUrl = "https://www.dotcpp.com/oj/status-ajax_t.php?tr=1&solution_id=" .$uploadResponse;
$convertedResponse = file_get_contents($getUrl); // 获取返回值

$responseInfo->setMessage($convertedResponse);
$responseInfo->printResponse();

?>