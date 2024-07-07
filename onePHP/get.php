<?php

$filename = '1192.in'; // 替换为你的 .in 文件路径  

// 读取文件内容并按行存储到数组中  
$lines = file($filename, FILE_IGNORE_NEW_LINES);

// 将数组的每一项添加到字符串中，使用 \n 分隔  
$content = implode("\n", $lines);

// 输出连接后的字符串  
echo $content;

// 发起 POST 请求  
$url = "https://www.dotcpp.com/oj/compiler_ol_submit.php?ajax";
$data = [
    'language' => 1,
    'ifO2[]' => 'O2',
    'source' => '#include <bits/stdc++.h>
    using namespace std;
     
    int main() {
        int n;
        while(1);
        while(cin >> n) {
            cout << n << "-->";
            // 负数转成正数
            if(n < 0) {
                n = -n;
                cout << "-";
            }
            // 设置标志位 
            bool key = false;
            for(int i = 15; i >= 0; i--) {
                // 将n的二进制数中，第 i 位的数移到最低位，&1即是获取第 i 位的值 
                int z = n >> i & 1;
                // 两种情况 从高位向低位数，遇到第一个1才进入if
                // 然后设置key为真，以后都进入打印 
                if(z == 1 || key) {
                    key = true;
                    cout << z;
                }
            }
            // kye一直为假，说明n从高位到最低位一直为0，n也就是0 
            if(!key) {
                cout << 0;
            }
            cout << endl;
        }
        return 0;
    }',
    'input_text' => $content,
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
$response = file_get_contents($url, false, $context);

// 打印返回数据  
echo $response . '<br>';

// 延迟 10 秒执行以下代码  
sleep(10);

// 根据 POST 请求返回的结果，构建 GET 请求的 URL（这里假设 POST 请求返回的数据中包含 solution_id 值）  
$getUrl = "https://www.dotcpp.com/oj/status-ajax_t.php?tr=1&solution_id=" . $response;

// 发起 GET 请求并将响应保存在 $response 变量中  
$response = file_get_contents($getUrl);

$convertedResponse = htmlspecialchars_decode($response);

$filename = '1192.out'; // 文件路径，与 PHP 文件同级 
$filename1 = '1192_1.out';
file_put_contents($filename, $convertedResponse);

echo $convertedResponse.'<br>';

function compareFiles($file1, $file2)
{
    // 读取文件1的内容并按行存储到数组中  
    $lines1 = file($file1, FILE_IGNORE_NEW_LINES);

    // 读取文件2的内容并按行存储到数组中  
    $lines2 = file($file2, FILE_IGNORE_NEW_LINES);

    // 比较两个文件的行数是否相同  
    if (count($lines1) !== count($lines2)) {
        echo "文件行数不同";
        exit;
    }

    // 比较两个文件的内容是否相同  
    for ($i = 0; $i < count($lines1); $i++) {
        if ($lines1[$i] !== $lines2[$i]) {
            echo "文件内容不同";
            exit;
        }else{
            echo $lines1[$i]."  ".$lines2[$i]."<br>";
        }
    }

    echo "文件内容相同";
}

compareFiles($filename, $filename1)
?>