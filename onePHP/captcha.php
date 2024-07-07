<?php
// 开始一个PHP会话，用于变量传递 
// session_start();

$capt_t = isset($_GET["t"]) ? $_GET["t"] : "1234";
// 调用create_captcha函数生成验证码
// $code = create_captcha(4, $capt_t);
// 使用参数加密获得验证码
$code = substr(md5($capt_t), -4);
// 返回png图像
captcha_img($code);

// // 存储在$_SESSION['captcha_code']变量中
// $_SESSION['captcha_code'] = $code;
// $_SESSION['captcha_time'] = time();

/**  
 * 创建验证码的函数  
 *  
 * @param int $count 验证码的长度，默认为4  
 * @return string 生成的验证码  
 */
function create_captcha($count = 4, $capt_t = "1234") {
    $code = '';  // 初始化验证码为空字符串  
    $charset = '123456789abcdefghigklmnpqrstuvwxyz';  // 定义验证码字符集，包含数字和字母  
    $len = strlen($charset) - 1;  // 获取字符集的长度，减1是为了不超出数组索引范围  

    // 根据指定的长度循环生成验证码  
    for($i = 0; $i < $count; $i++) {
        $code .= $charset[rand(0, $len)];  // 从字符集中随机选取一个字符并添加到验证码字符串末尾  
    }

    return $code;  // 返回生成的验证码  
}

/**  
 * 生成验证码图片的函数  
 * 并输出在浏览器上
 * @param string $code 要生成的验证码  
 * @return void  
 */
function captcha_img($code) {
    $im = imagecreate($x = 180, $y = 50);  // 创建一个新的图像，宽180像素，高50像素  
    imagecolorallocate($im, rand(50, 200), rand(0, 155), rand(0, 155));  // 为图像分配颜色，随机RGB值  
    $fontColor = imagecolorallocate($im, 255, 255, 255);  // 为字体设置颜色，白色  

    // 获得php所在文件夹
    $currentDirectoryPath = __DIR__;
    // 拼接成字体文件所在路径
    $fontStyle = $currentDirectoryPath.'\static\fonts\msyh.ttc';

    // 根据验证码字符串的长度循环生成每个字符的图像，并添加到总图像上  
    for($i = 0, $len = strlen($code); $i < $len; $i++) {
        imagettftext(
            $im,  // 图像句柄  
            30,  // 字体大小  
            rand(0, 20) - rand(0, 25),  // x坐标偏移，产生随机偏移效果  
            32 + $i * 40,  // y坐标，每个字符垂直居中  
            rand(30, 50),  // 字体的旋转角度，产生随机旋转效果  
            $fontColor,  // 字体颜色  
            $fontStyle,  // 字体样式  
            $code[$i]  // 当前字符  
        );
    }

    // 在图像上随机绘制8条线，增加图像的复杂度，防止机器自动识别  
    for($i = 0; $i < 8; ++$i) {
        $lineColor = imagecolorallocate($im, rand(0, 255), rand(0, 255), rand(0, 255));  // 为线条分配颜色，随机RGB值  
        imageline($im, rand(0, $x), 0, rand(0, $x), $y, $lineColor);  // 在图像上随机绘制一条线  
    }

    // // 在图像上随机绘制250个像素点，增加图像的复杂度，防止机器自动识别  
    // for ($i = 0; $i < 250; ++$i) {  
    //     imagesetpixel($im, rand(0, $x), rand(0, $y), $fontColor);  // 在图像上随机绘制一个像素点  
    // }  

    ob_clean();  // 清空输出缓冲区，确保图像数据不会被前面的输出覆盖  
    header('Content-type:image/png');  // 设置HTTP响应头，告诉浏览器输出的内容类型是PNG图像  
    imagepng($im);  // 将图像输出为PNG格式的数据，直接发送给浏览器显示出来  
    imagedestroy($im);  // 销毁图像资源，释放内存空间
}

?>