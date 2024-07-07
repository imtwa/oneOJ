<?php

/**  
 * 检查两个文件是否相同  
 *  
 * @param string $temFilePath - 临时文件路径  
 * @param string $testFilePath - 测试数据文件路径  
 * @return array  
 */
function checkFile($temFilePath, $testFilePath)
{
    $item = array(
        'isTrue' => false,
        'message' => "错误",
        "testIn" => "testIn",
        "testOut" => "testOut",
        "userOut" => "userOut",
    );

    $inFilePath = str_replace(".out", ".in", $testFilePath);

    // 按行读取放至数组中 file读取忽略末尾空行
    $lines1 = file($temFilePath, FILE_IGNORE_NEW_LINES);
    $lines2 = file($testFilePath, FILE_IGNORE_NEW_LINES);
    $lines3 = file($inFilePath, FILE_IGNORE_NEW_LINES);

    $item['testIn'] = implode("\n", $lines3);
    $item["testOut"] = implode("\n", $lines2);
    $item["userOut"] = implode("\n", $lines1);

    if ($lines1[0] == ":Alarm clock") {
        $item['message'] = "TLE";
        return $item;
    }

    $count_lines1 = count($lines1);

    // 检查行数是否大于1且最后一行为空  
    if ($count_lines1 > 1 && trim(end($lines1)) === '') {
        $count_lines1--;
    }
    // 比较两个文件的行数是否相同  
    if ($count_lines1 !== count($lines2)) {
        // echo "文件行数不同 格式错误<br>";
        $item['message'] = "PE";
        return $item;
    }

    // 比较两个文件的内容是否相同  
    for ($k = 0; $k < count($lines1); $k++) {
        // rtrim不检查末尾空格
        if (rtrim($lines1[$k]) !== rtrim($lines2[$k])) {
            // echo $lines1[$k] . "<<--->>" . $lines2[$k] . "<br>";
            $item['message'] = "WE";
            // echo "测试点" .$testFilePath."错误";
            return $item;
        } else {
            // echo $lines1[$k] . "<<--->>" . $lines2[$k] . "<br>";
        }
    }

    $item['isTrue'] = true;
    $item['message'] = 'AC';

    return $item;
}


/**  
 * 递归删除指定目录及其内部所有文件和子文件夹。  
 *  
 * @param string $directoryPath 要删除的目录路径。  
 * @return void  
 */
function deleteDirectory($directoryPath)
{
    if (!is_dir($directoryPath)) {
        return;
    }

    $files = array_diff(scandir($directoryPath), array('.', '..'));

    foreach ($files as $file) {
        $filePath = $directoryPath . '/' . $file;

        if (is_dir($filePath)) {
            deleteDirectory($filePath);
        } else {
            unlink($filePath);
        }
    }

    rmdir($directoryPath);
}


/**  
 * 通过后缀名获取指定目录下的所有文件路径 按数字名称排行
 *  
 * @param string $dirPath 目录路径  
 * @return array 包含按后缀名分类的文件路径的数组，格式为 array( 'in' => array( file1, file2, ... ), 'out' => array( file1, file2, ... ) )  
 */
function getFilesByExtension($dirPath)
{
    $inFiles = array();
    $outFiles = array();

    // 获取指定目录下的所有文件  
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dirPath)
    );

    // 遍历文件并按照后缀名分类  
    foreach ($files as $name => $file) {
        if (!$file->isDir()) {
            $extension = pathinfo($name, PATHINFO_EXTENSION);
            if ($extension === 'in') {
                $inFiles[] = $file->getRealPath();
            } elseif ($extension === 'out') {
                $outFiles[] = $file->getRealPath();
            }
        }
    }
    // 按文件名内数字排序
    $inFiles = sortFilePaths($inFiles);
    $outFiles = sortFilePaths($outFiles);

    // 返回包含按后缀名分类的文件路径的数组  
    return array(
        'in' => $inFiles,
        'out' => $outFiles
    );
}

/**  
 * 根据文件名数字对文件路径进行排序  
 *  
 * @param array $filePaths 包含文件路径的数组  
 * @return array 排序后的文件路径数组  
 */
function sortFilePaths(array $filePaths): array
{
    // 根据文件名数字进行排序  
    usort($filePaths, function ($a, $b) {
        $fileA = intval(basename($a));
        $fileB = intval(basename($b));
        return $fileA - $fileB;
    });

    return $filePaths;
}



/**  
 * 将请求结果保存到文件  
 *  
 * @param string $uploadResponse - 上传代码的响应结果  
 * @param int $i - 索引，用做文件名  
 * @param string $temDirPath - 临时目录路径  
 */
function saveRequestToFile($uploadResponse, $i, $temDirPath)
{

    // echo "测试点运行编号：" . $uploadResponse . '<br>';
    // // 构建 GET 请求的 URL 
    // $getUrl = "https://www.dotcpp.com/oj/status-ajax_t.php?tr=1&solution_id=" . $uploadResponse;
    // // 创建流上下文  
    // $options = array(
    //     'http' => array(
    //         'method' => 'GET',
    //         'header' => 'Content-type: application/x-www-form-urlencoded'
    //     ),
    // );

    // // 发起 GET 请求并获取响应
    // $getResponse = file_get_contents($getUrl, true, stream_context_create($options));
    // // 将特殊的 HTML 实体转换回普通字符
    // $convertedResponse = htmlspecialchars_decode($getResponse);

    // $getUrl = "https://www.dotcpp.com/run/" . $uploadResponse;

    // $html = file_get_contents($getUrl); // 建立页面连接  
    // $dom = new DOMDocument();           // 使用DOM扩展库加载HTML  
    // @$dom->loadHTML($html);
    // // 选择第二个`<textarea>`元素  
    // $textarea = $dom->getElementsByTagName('textarea')->item(1);
    // // 提取`<textarea>`标签内的内容  
    // $convertedResponse = $textarea->nodeValue;
    // // 将特殊的 HTML 实体转换回普通字符
    // $convertedResponse = htmlspecialchars_decode($convertedResponse);

    // 调用api返回结果
    $getUrl = "https://www.dotcpp.com/oj/status-ajax_t.php?tr=1&solution_id=" . $uploadResponse;
    // $convertedResponse = file_get_contents($getUrl); // 获取返回值
    $maxRetries = 5;
    $retryDelay = 1; // 秒  
    $convertedResponse = '';
    $retries = 0;

    // 重新请求maxRetries次 等待api接口响应
    while ($retries < $maxRetries) {
        $convertedResponse = file_get_contents($getUrl);
        if (strlen($convertedResponse) === 1) {
            $retries++;
            sleep($retryDelay);
        }else{
            break;
        }
    }

    // 将特殊的 HTML 实体转换回普通字符  
    $convertedResponse = html_entity_decode($convertedResponse, ENT_QUOTES, 'UTF-8');

    $filename = $temDirPath . '/' . $i . '.out';
    file_put_contents($filename, $convertedResponse);
}


/**  
 * 上传代码
 * @param string $source - 源代码字符串  
 * @param string $inFilePath - 输入文件路径  
 * @param string $language - 源代码语言类型  
 * @return string - 返回服务器响应的字符串结果  
 */
function uploadCode($source, $inFilePath, $language)
{
    // 读取文件内容并按行存储到数组中  
    $lines = file($inFilePath, FILE_IGNORE_NEW_LINES);
    // 将数组的每一项添加到字符串中，使用 \n 分隔  
    $content = implode("\n", $lines);

    $url = "https://www.dotcpp.com/oj/compiler_ol_submit.php?ajax";
    $data = [
        'language' => $language,
        'ifO2[]' => 'O2',
        'source' => $source,
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
    return $response;
}



/**  
 * 将代码保存到指定文件夹中，根据不同的语言保存为不同的文件扩展名  
 *  
 * @param string $code 要保存的代码内容  
 * @param string $folder 保存文件的文件夹路径  
 * @param string $language 编程语言，用于确定文件扩展名  
 * @return string 生成的文件路径  
 */
function saveCodeToFile($code, $folder, $language)
{
    $extensions = array(
        '0' => '.c',
        '1' => '.cpp',
        '2' => '.java',
        '3' => '.py',
        '4' => '.php'
    );

    $extension = $extensions[$language];

    $filepath = $folder . '/Main' . $extension; // 生成完整的文件路径  

    // 写入文件  
    file_put_contents($filepath, $code);

    return $filepath; // 返回生成的文件路径  
}


/**  
 * 执行可执行文件并获取输出结果  
 *  
 * @param string $executableFile 可执行文件的路径  
 * @param string $inputFile 输入文件的路径  
 * @return string 执行结果  
 */
function executeFile($executableFile, $inputFile)
{
    $pathInfo = pathinfo($executableFile);
    // 获取可执行文件的文件后缀
    $extension = $pathInfo['extension'];

    // 根据文件后缀执行不同的命令  
    switch ($extension) {
        case 'exe': // .exe文件  
            $command = $executableFile . '<' . $inputFile;
            break;
        case 'class': // .class文件（Java）  
            $command = 'java -cp ' . dirname($executableFile) . ' ' . $pathInfo['filename'] . '<' . $inputFile;
            break;
        case 'py': // .py文件（Python）  
            $command = 'python ' . $executableFile . ' ' . $inputFile;
            break;
        case 'php': // .php文件（PHP）  
            $command = 'php ' . $executableFile . ' ' . $inputFile;
            break;
        default:
            return '未知的可执行文件类型';
    }

    echo $command . "<br>";

    // 执行命令并获取输出结果  
    $output = shell_exec($command);

    echo $output . "<br>";

    // 返回执行结果  
    return $output;
}

/**  
 * @brief 编译文件函数  
 *  
 * 该函数用于编译指定路径的文件，并生成可执行文件。支持C、C++、Java、Python和PHP文件。  
 *  
 * @param $filePath string 文件路径  
 *  
 * @return mixed 如果编译成功，则返回生成的可执行文件路径；否则返回false。  
 */
function compileFile($filePath)
{
    $pathInfo = pathinfo($filePath);
    // 获取后缀名
    $extension = $pathInfo['extension'];
    $outputDir = $pathInfo['dirname'];

    // 获得输出文件名
    $outputFile = $outputDir . '/' . $pathInfo['filename'] . '.exe';

    switch ($extension) {
        case 'c':
            $command = 'gcc ' . $filePath . ' -o ' . $outputFile;
            break;
        case 'cpp':
            $command = 'g++ ' . $filePath . ' -o ' . $outputFile;
            break;
        case 'java':
            $outputFile = $outputDir . '/' . $pathInfo['filename'] . '.class';
            $command = 'javac ' . $filePath;
            break;
        case 'py':
            return $filePath;
        case 'php':
            return $filePath;
        default:
            return false;  // 未知的文件类型，无法编译  
    }

    // java -cp D:\OJ\A-vue-php-OneOJ\onePHP\dataOJ\1.1.9字符菱形\ Main
    // ' && java -cp ' .$pathInfo['dirname'] .' ' . $pathInfo['filename']

    echo $command . "<br>";

    // 执行编译命令  
    $result = exec($command, $output, $returnCode);

    echo $result . "<br>";
    print_r($output);
    echo "<br>";

    if ($returnCode === 0) {
        // 编译成功  
        return $outputFile;
    } else {
        // 编译失败  
        return false;
    }
}

?>