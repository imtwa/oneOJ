<?php
/**  
 * JsonResponse 类  
 * 用于创建和返回 JSON 响应的类  
 */
class JsonResponse
{
    /**  
     * 初始化结果，默认为 false  
     * @var bool  
     */
    private $result = false;

    /**  
     * 初始化消息，默认为 "初始化错误"  
     * @var string  
     */
    private $message = "初始化错误";

    /**
     * 成功后返回的结果数组
     * @var array
     */
    private $data = array();

    /**
     * 判题结果成绩
     * @var double
     */
    private $score = 0.0;

    /**  
     * 构造函数 
     */
    public function __construct()
    {
        $this->result = false;
        $this->message = "初始化错误";
        $this->data = array();
        $this->score = 0.0;
    }

    /**  
     * 获取 JSON 响应的方法，返回一个包含结果和消息的 JSON 字符串  
     * @return string JSON 响应字符串  
     */
    public function get()
    {
        return json_encode([
            'result' => $this->result,
            'message' => $this->message,
            'data' => $this->data,
            'score' => $this->score
        ]);
    }

    /**  
     * 打印 JSON 响应字符串
     */
    public function printResponse()
    {
        echo $this->get(); // 打印 JSON 响应字符串  
    }

    /**  
     * printEcho() 方法，打印调试信息
     */
    public function printEcho()
    {
        // echo "{'result':" . var_export($this->result, true) . ","; // 打印结果属性调试信息  
        // echo "'message':" . $this->message . "}<br>";

        $responseArray = array(
            'result' => $this->result,
            'message' => $this->message,
            'data' => $this->data,
            'score' => $this->score
        );
        print_r($responseArray);
    }



    /**  
     * 设置结果值的方法  
     * @param bool $result 结果值  
     */
    public function setResult($result)
    {
        $this->result = $result;
    }

    /**  
     * 设置消息内容的方法  
     * @param string $message 消息内容  
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * 设置数据内容
     * @param array $data 数据内容
     */
    public function setData($data)
    {
        $this->data = $data;
        
        // 根据测试点进行成绩设置  
        $totalTestPoints = count($data);
        if ($totalTestPoints != 0) {
            $passedTestPoints = 0;
            foreach ($data as $isTrue) {
                if ($isTrue['isTrue']) {
                    $passedTestPoints++;
                }
            }
            
            $scoreS = ($passedTestPoints / $totalTestPoints) * 100;
            $scoreS = round($scoreS, 2); // 保留两位小数  
            $this->score = $scoreS;

            $this->result = true;
            if ($passedTestPoints >= $totalTestPoints) {
                $this->message = 'AC';
            } else {
                for ($i = 0; $i < $totalTestPoints; $i++) {
                    if ($data[$i]['isTrue'] != true) {
                        $this->message = $data[$i]['message'];
                        break;
                    }
                }
            }
        } else {
            $this->result = false;
            $this->message = '测试点为空，请联系管理员';
        }
    }

    /**
     * 设置成绩
     * @param double $score 成绩内容
     */
    public function setScore($score)
    {
        $this->score = $score;
    }

    public function getResult()
    {
        return $this->result;
    }
    public function getMessage()
    {
        return $this->message;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getScore()
    {
        return $this->score;
    }

}

?>