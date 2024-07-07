<template>
    <div class="box" ref="box">
        <div class="left">
            <div class="info-box">
                <el-tabs v-model="textName">
                    <el-tab-pane label="题目描述" name="1">
                        <div class="title">题目 {{ String(info.id).padStart(4, '0') }}:{{ info.name }}</div>
                        <div class="text">
                            时间限制:{{ info.time_limit }}s 内存限制:{{ info.memory_limit }}kb
                            <span :style="{ color: getColor(info.degree) }">
                                {{ info.degree }}
                            </span>
                        </div>
                        <div class="title">描述</div>
                        <div class="text">{{ info.describe_pro }}</div>
                        <div class="title">输入描述</div>
                        <div class="text">{{ info.in_describe }}</div>
                        <div class="title">输出描述</div>
                        <div class="text">{{ info.out_describe }}</div>
                        <div class="title">样例输入</div>
                        <pre class="text">{{ info.in_sample }}</pre>
                        <div class="title">样例输出</div>
                        <pre class="text">{{ info.out_sample }}</pre>
                        <div class="title">提示</div>
                        <pre class="text">{{ info.tip }}</pre>
                    </el-tab-pane>
                    <el-tab-pane label="提交记录" name="2">
                        <div class="code-box">
                            <div class="code-head" v-if="visCodeSubmitShow" v-loading="visCodeSubmit">
                                <!-- {{ submitData }} -->
                                <div style="display: flex;flex-wrap: wrap;min-height:240px">

                                    <div v-for="st in submitData.data" :key="st.id" class="submit-Data"
                                        @click="visTestShow = true; stData = st;"
                                        :style="{ backgroundColor: st.isTrue ? '#5cb85c' : 'rgb(239 71 67)' }">

                                        {{ st.message }}

                                    </div>
                                    <el-dialog title="测试点信息" :visible.sync="visTestShow" destroy-on-close>
                                        <div style="height: 45vh;overflow: auto;">
                                            <h3>#输入数据</h3>
                                            <pre class="row-code" style="height: 30%;">{{ stData.testIn }}</pre>
                                            <h3>#输出数据</h3>
                                            <pre class="row-code" style="height: 50%;">{{ stData.testOut }}</pre>
                                            <h3>#你的输出</h3>
                                            <pre class="row-code" style="height: 50%;">{{ stData.userOut }}</pre>
                                        </div>
                                        <div slot="footer" class="dialog-footer">
                                            <el-button @click="visTestShow = false">取 消</el-button>
                                            <el-button type="primary" @click="visTestShow = false">确 定</el-button>
                                        </div>
                                    </el-dialog>
                                </div>
                            </div>
                            <div class="code-main">
                                <el-table ref="filterTable" :data="submitTiyData">
                                    <el-table-column prop="id" label="运行编号" width="120">
                                        <template slot-scope="scope">
                                            {{ String(scope.row.id).padStart(6, '0') }}
                                        </template>
                                    </el-table-column>
                                    <el-table-column prop="result" label="状态" width="80">
                                        <template slot-scope="scope">
                                            <el-tag effect="dark"
                                                :type="scope.row.result === 'AC' ? 'success' : scope.row.result === 'WE' ? 'danger' : 'warning'">
                                                {{ scope.row.result }}
                                            </el-tag>
                                        </template>
                                    </el-table-column>
                                    <el-table-column prop="language" label="语言" width="80">
                                        <template slot-scope="scope">
                                            <el-button type="text" style="color: #428bca;"
                                                @click="visCodeShow = true; rowCode = scope.row.code;">
                                                {{ getLanguage(scope.row.language) }}</el-button>
                                        </template>
                                    </el-table-column>
                                    <el-table-column prop="score" label="分数" width="60">
                                    </el-table-column>
                                    <el-table-column prop="run_ram" label="内存" width="80">
                                        <template slot-scope="scope">
                                            <div v-if="scope.row.run_ram != null">{{ scope.row.run_ram }}B</div>
                                            <div v-else>N/A</div>
                                        </template>
                                    </el-table-column>
                                    <el-table-column prop="run_time" label="耗时" width="80">
                                        <template slot-scope="scope">
                                            <div v-if="scope.row.run_time != null">{{ scope.row.run_time }}ms</div>
                                            <div v-else>N/A</div>
                                        </template>
                                    </el-table-column>
                                    <el-table-column prop="create_time" label="时间" width="200">
                                    </el-table-column>
                                </el-table>
                                <el-dialog title="提交的代码" :visible.sync="visCodeShow" destroy-on-close>
                                    <pre class="row-code">{{ rowCode }}</pre>
                                    <div slot="footer" class="dialog-footer">
                                        <el-button @click="visCodeShow = false">取 消</el-button>
                                        <el-button type="primary" @click="codeCopy">复 制</el-button>
                                    </div>
                                </el-dialog>
                            </div>
                        </div>
                    </el-tab-pane>
                </el-tabs>
            </div>

        </div>

        <div class="resize">
            <div style="position: relative;top: 45%;">⋮</div>
        </div>

        <div class="mid">
            <!--右侧div内容-->
            <div class="code-box">
                <div class="code-main">
                    <CodeEditAce v-model="content" @changeLanguage="changeLanguage"></CodeEditAce>
                </div>
                <div class="code-bot">
                    <div v-if="visCodeRunShow" style="padding: 8px;">
                        <el-tabs v-model="activeName" style="height: 42vh;">
                            <el-tab-pane label="测试数据" name="1">
                                <el-input type="textarea" :autosize="{ minRows: 1, maxRows: 12 }" placeholder="按输入格式输入内容"
                                    v-model="inTextarea" resize="none"></el-input>
                            </el-tab-pane>
                            <el-tab-pane label="执行结果" name="2">
                                <el-input type="textarea" :autosize="{ minRows: 12, maxRows: 12 }"
                                    :placeholder="outTextarea" v-model="outTextarea" v-loading="visCodeRun" readonly
                                    resize="none"></el-input>
                            </el-tab-pane>
                        </el-tabs>
                    </div>
                    <el-row :gutter="20">
                        <el-col :span="4">
                            <el-button @click="visCodeRunShow = !visCodeRunShow;" type="text"
                                style="color: #3c3c4399;">控制台</el-button>
                            <i v-if="!visCodeRunShow" class="el-icon-arrow-up"></i>
                            <i v-else class="el-icon-arrow-down eli"></i>
                        </el-col>
                        <el-col :span="3" :offset="13">
                            <el-tooltip class="item" effect="dark" content="根据测试数据测试你的代码，结果不会保存在服务器" placement="top">
                                <el-button type="text" :loading="visCodeRun" icon="el-icon-refresh-right"
                                    @click="codeRunText" style="color: #262626bf;">运行</el-button>
                            </el-tooltip>
                        </el-col>
                        <el-col :span="3">
                            <el-tooltip class="item" effect="dark" content="提交代码！" placement="top">
                                <el-button type="text" icon="el-icon-upload2" @click="codeSubmit" :loading="visCodeSubmit"
                                    style="color: rgb(45 181 93);">提交</el-button>
                            </el-tooltip>
                        </el-col>
                    </el-row>
                </div>
            </div>
        </div>
    </div>
</template>
      
<script>
import CodeEditAce from "@/components/codeEditAce.vue";
export default {
    name: "ProblemInfo",
    components: {
        'CodeEditAce': CodeEditAce,
    },
    data() {
        return {
            info: {},
            content: `#include <stdio.h>
int main(){
    printf("Hello, World!");
    return 0;
}`,
            visCodeRunShow: false,
            visCodeRun: false,
            visCodeSubmitShow: false,
            visCodeSubmit: false,
            visCodeShow: false,
            visTestShow: false,
            activeName: "1",
            textName: "1",
            inTextarea: "",
            outTextarea: "还没有执行结果",
            language: "1",
            submitData: {},
            submitTiyData: [],
            rowCode: "",
            stData: "",
        }
    },
    mounted() {
        this.dragControllerDiv();
        this.getInfo();
        this.getTiySubmit();
    },
    methods: {
        //点击了复制代码
        codeCopy() {
            this.visCodeShow = false;
            this.copyToClip(this.rowCode);
        },
        /**  
        * 复制内容到粘贴板  
        * contentArray: 需要复制的内容
        * message: 复制完后的提示，不传则默认提示"复制成功"  
        */
        copyToClip(contentArray, message = "复制成功") {
            // 使用异步函数来处理剪贴板操作  
            const than = this
            async function copyToClipboard() {
                try {
                    // 复制文本到剪贴板  
                    await navigator.clipboard.writeText(contentArray);
                    // 显示成功消息  
                    than.$message({
                        message: message,
                        type: 'success'
                    });
                } catch (err) {
                    // 复制失败，显示错误信息  
                    than.$message.error("复制失败: " + err.message);
                }
            }
            // 调用异步函数  
            copyToClipboard();
        },
        // 查询这题的历史提交
        getTiySubmit() {
            if (this.$store.state.userSession.id === null || this.$store.state.userSession.id === undefined) {
                this.$message.error("请先登录");
                return;
            }
            const url = `getTiyShumit.php?user_id=${this.$store.state.userSession.id}&problem_id=${this.$route.params.id}`;
            this.$axios.get(url)
                .then(response => {
                    // 请求成功时的回调函数    
                    let res = response.data;
                    this.submitTiyData = res.sort((a, b) => b.id - a.id);
                })
                .catch(error => {
                    // 请求失败时的回调函数    
                    this.$message.error(error);
                });
        },
        // 提交代码
        codeSubmit() {
            if (this.$store.state.userSession.id === null || this.$store.state.userSession.id === undefined) {
                this.$message.error("请先登录");
                return;
            }
            this.visCodeSubmit = true;
            this.visCodeSubmitShow = true;
            this.textName = "2";
            let params = new URLSearchParams();
            params.append('user_id', this.$store.state.userSession.id);
            params.append('problem_id', this.$route.params.id);
            params.append('code', this.content);
            params.append('language', this.language);

            this.$axios.post('/problem_submit.php', params)
                .then(response => {
                    // 请求成功时的回调函数  
                    const res = response.data;
                    // console.log(JSON.stringify(res));
                    // 转换json
                    this.submitData = res;
                    // console.log(this.submitData);
                    this.visCodeSubmit = false;
                    this.getTiySubmit()
                })
                .catch(error => {
                    // 请求失败时的回调函数  
                    console.error(error);
                    this.visCodeSubmit = false;
                    this.$message.error(error);
                    this.getTiySubmit()
                });

        },
        // 测试运行代码
        codeRunText() {
            if (this.$store.state.userSession.id === null || this.$store.state.userSession.id === undefined) {
                this.$message.error("请先登录");
                return;
            }
            this.visCodeRun = true;
            this.visCodeRunShow = true;
            this.activeName = "2";
            // 验证通过 进行提交
            // 只有这样封装参数服务器才能获得参数！！！
            let params = new URLSearchParams();
            params.append('code', this.content);
            params.append('language', this.language);
            params.append('input_text', this.inTextarea);

            this.$axios.post('/problem_testRun.php', params)
                .then(response => {
                    // 请求成功时的回调函数  
                    let res = response.data;
                    this.outTextarea = res.message;
                    this.visCodeRun = false;
                })
                .catch(error => {
                    // 请求失败时的回调函数  
                    console.error(error);
                    this.visCodeRun = false;
                    this.$message.error(error);
                });
        },
        changeLanguage(e) {
            this.language = e;
        },
        getColor(degree) {
            if (degree === '简单') return '#67c23a';
            if (degree === '中等') return 'orange';
            return '#f56c6c';
        },
        getLanguage(num) {
            const langs = ["c&c++", "java", "python", "php"];
            const nums = ["1", "3", "6", "7"];
            return langs[nums.indexOf(num)]
        },
        getInfo() {
            const url = `problem_info.php?id=${this.$route.params.id}`;
            this.$axios.get(url)
                .then(response => {
                    // 请求成功时的回调函数    
                    let res = response.data;
                    this.info = res;
                    // 测试数据默认为样例输入
                    this.inTextarea = this.info.in_sample
                })
                .catch(error => {
                    // 请求失败时的回调函数    
                    console.error(error);
                });
        },
        dragControllerDiv() {
            let resize = document.getElementsByClassName('resize');
            let left = document.getElementsByClassName('left');
            let mid = document.getElementsByClassName('mid');
            let box = document.getElementsByClassName('box');
            for (let i = 0; i < resize.length; i++) {
                // 鼠标按下事件
                resize[i].onmousedown = function (e) {
                    //颜色改变提醒
                    resize[i].style.background = 'rgb(0 122 255)';
                    let startX = e.clientX;
                    resize[i].left = resize[i].offsetLeft;
                    // 鼠标拖动事件
                    document.onmousemove = function (e) {
                        let endX = e.clientX;
                        let moveLen = resize[i].left + (endX - startX); // （endx-startx）=移动的距离。resize[i].left+移动的距离=左边区域最后的宽度
                        let maxT = box[i].clientWidth - resize[i].offsetWidth; // 容器宽度 - 左边区域的宽度 = 右边区域的宽度

                        if (moveLen < 200) moveLen = 200; // 左边区域的最小宽度为32px
                        if (moveLen > maxT - 200) moveLen = maxT - 200; //右边区域最小宽度为150px

                        resize[i].style.left = moveLen; // 设置左侧区域的宽度

                        for (let j = 0; j < left.length; j++) {
                            left[j].style.width = moveLen + 'px';
                            mid[j].style.width = (box[i].clientWidth - moveLen - 10) + 'px';
                        }
                    };
                    // 鼠标松开事件
                    document.onmouseup = function () {
                        //颜色恢复
                        resize[i].style.background = '';
                        document.onmousemove = null;
                        document.onmouseup = null;
                        resize[i].releaseCapture && resize[i].releaseCapture(); //当你不在需要继续获得鼠标消息就要应该调用ReleaseCapture()释放掉
                    };
                    resize[i].setCapture && resize[i].setCapture(); //该函数在属于当前线程的指定窗口里设置鼠标捕获
                    return false;
                };
            }
        },
    }
}      
</script>    
    
<style scoped >
.submit-Data {
    padding: 8px;
    margin: 8px;
    border-radius: 8px;
    width: 100px;
    height: 100px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #efefef;
}

.row-code {
    background-color: #efefef;
    padding: 8px;
    border-radius: 8px;
    height: 45vh;
    overflow: auto;
    /* 生成滚动条 */
}

.eli {
    animation: rotate 0.5s ease forwards;
    color: #3c3c4399;
}

@keyframes rotate {

    /* 0%时元素未旋转 */
    0% {
        transform: rotate(-180deg);
    }

    /* 100%时元素旋转180度 */
    100% {
        transform: rotate(0deg);
    }
}

.code-box {
    width: 100%;
    height: 98%;
    margin-top: auto;
    margin-bottom: auto;
    margin-left: auto;
    margin-right: auto;
    display: flex;
    flex-direction: column;
}

.code-main {
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(26, 26, 26, .3);
    flex-grow: 1;
    /* 子元素占据剩余空间 */
}

.code-head {
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(26, 26, 26, .3);
    margin-bottom: 8px;
}

.code-bot {
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(26, 26, 26, .3);
    margin-top: 8px;
}

.title {
    font-weight: bold;
    color: black;
    font-size: 20px;
    margin-top: 8px;
    padding: 8px;
}

.text {
    margin-top: 8px;
    padding: 8px;
}

.info-box {
    width: 98%;
    height: 96%;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(26, 26, 26, .3);
    margin-top: auto;
    margin-bottom: auto;
    margin-left: auto;
    margin-right: auto;
    padding: 8px;
    text-align: start;
    overflow: auto;
    /* 生成滚动条 */
}

/* 拖拽相关样式 */
/*包围div样式*/
.box {
    width: 100%;
    height: 91vh;
    overflow: hidden;
    /*box-shadow: -1px 9px 10px 3px rgba(0, 0, 0, 0.11);*/
}

/*左侧div样式*/
.left {
    width: calc(50% - 8px);
    /*左侧初始化宽度*/
    height: 100%;
    float: left;
    display: flex;
}

/*右侧div'样式*/
.mid {
    display: flex;
    float: left;
    width: calc(50% - 8px);
    /*右侧初始化宽度*/
    height: 100%;
    background: #fff;
    /*box-shadow: -1px 4px 5px 3px rgba(0, 0, 0, 0.11);*/
}

/*拖拽区div样式*/
.resize {
    cursor: col-resize;
    float: left;
    position: relative;
    /*background-color: #d6d6d69b;*/
    width: 10px;
    top: 16px;
    height: 95%;
    border-radius: 4px;
    background-size: cover;
    background-position: center;
    /*z-index: 99999;*/
    font-size: 32px;
    color: white;
}

/*拖拽区鼠标悬停样式*/
.resize:hover {
    color: #444444;
    background-color: rgb(0 122 255);
}
</style>