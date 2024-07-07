<template>
    <div class="user-page">
        <div class="user-left">
            <div class="user-card" style="width: 60%;padding: 16px;">
                <div class="user-nick" style="text-align:center">
                    <h3>{{ userInfo.name }}</h3>
                </div>
                <div class="user-table">
                    <tr>
                        <td>签 名：</td>
                        <td>
                            <el-button type="text" icon="el-icon-edit-outline" @click="visUp = true"
                                style="color: rgb(45 181 93);"></el-button>
                            <el-dialog title="更新签名" :visible.sync="visUp" destroy-on-close>
                                <el-form :model="ruleForm" status-icon ref="ruleForm" label-width="80px"
                                    style="margin-right: 10%;">
                                    <el-form-item label="签名" prop="name">
                                        <el-input type="text" v-model="ruleForm.sign"></el-input>
                                    </el-form-item>
                                </el-form>
                                <div slot="footer" class="dialog-footer">
                                    <el-button @click="visUp = false">取 消</el-button>
                                    <el-button type="primary" @click="submitForm">确 定</el-button>
                                </div>
                            </el-dialog>
                        </td>
                    </tr>
                    <div style="font-size: 14px;">{{ userInfo.sign }}</div>
                </div>

                <table class="user-table">
                    <tbody>
                        <tr>
                            <td>等 级</td>
                            <td><button type="button" class="btn btn-xs"
                                    :style="{ backgroundColor: getBackgroundColor(userInfo.grade) }">P{{ userInfo.grade
                                    }}</button></td>
                        </tr>
                        <tr>
                            <td>经 验</td>
                            <td>{{ userInfo.exp }}</td>
                        </tr>
                        <tr>
                            <td>排 名</td>
                            <td>{{ userInfo.ranking }}</td>
                        </tr>
                        <tr>
                            <td>提 交</td>
                            <td>{{ userInfo.submit_all }}</td>
                        </tr>
                        <tr>
                            <td>正 确</td>
                            <td>{{ userInfo.submit_ac }}</td>
                        </tr>
                        <tr>
                            <td>解 决</td>
                            <td>{{ userInfo.submit_pro }}</td>
                        </tr>
                    </tbody>
                </table>
                <div style="margin-left: 20%;width: 60%;">
                    <el-progress type="circle" :percentage="percentage">
                    </el-progress>
                    <div>
                        当前进度 {{ userInfo.submit_pro }}/{{ totalCount }}
                    </div>
                </div>

            </div>
        </div>
        <div class="user-right">
            <div class="user-card" style="width: 90%;padding: 16px;">
                <h3>尝试过的题目</h3>
                <div class="user-problems">
                    <span v-for="pro in tryPro" :key="pro.id" class="problem-id">
                        <a :href="'#/ProblemInfo/' + pro.id"
                            :title="pro.name + '  提交' + pro.user_submit_count + '通过' + pro.user_ac_count" target="_blank"
                            class="pro-a">
                            {{ String(pro.id).padStart(4, '0') }}
                        </a>
                    </span>
                </div>
                <h3>已通过的题目</h3>
                <div class="user-problems">
                    <span v-for="pro in resolvedPro" :key="pro.id" class="problem-id">
                        <a :href="'#/ProblemInfo/' + pro.id"
                            :title="pro.name + '  提交' + pro.user_submit_count + '通过' + pro.user_ac_count" target="_blank"
                            class="pro-a">
                            {{ String(pro.id).padStart(4, '0') }}
                        </a>
                    </span>
                </div>
                <h3>未通过的题目</h3>
                <div class="user-problems">
                    <span v-for="pro in unResolvedPro" :key="pro.id" class="problem-id">
                        <a :href="'#/ProblemInfo/' + pro.id" :title="pro.name" target="_blank" class="pro-a">
                            {{ String(pro.id).padStart(4, '0') }}
                        </a>
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>
  
<script>

export default {
    name: 'UserC',
    data() {
        return {
            userInfo: {},
            tryPro: [],
            resolvedPro: [],
            unResolvedPro: [],
            percentage: 0,
            totalCount: 0,
            visUp: false,
            ruleForm: {
                sign: ''
            },
        }
    },
    mounted() {
        this.getUser();
    },
    methods: {
        getBackgroundColor(i) {
            const colorS = [
                "#cccccc", // 白色 
                "#ADD8E6", // 淡蓝色
                "#00BFFF", // 深天蓝色  
                "#90EE90", // 淡绿色 
                "#00FF7F", // 亮绿色   
                "#FFA500", // 橙色 
                "#8B00FF", // 紫色  
                "#800080", // 紫罗兰色
            ];

            return colorS[i - 1];
        },
        submitForm() {
            // 只有这样封装参数服务器才能获得参数！！！
            let params = new URLSearchParams();
            params.append('id', this.$store.state.userSession.id);
            params.append('sign', this.ruleForm.sign);

            this.$axios.post('/user_up.php', params)
                .then(response => {
                    // 请求成功时的回调函数  
                    let res = response.data;
                    if (res.result) {
                        this.$message({
                            message: '修改成功',
                            type: 'success'
                        });
                        this.visUp = false;
                        this.getUser()

                    } else {
                        this.$message.error(res.message);
                    }
                })
                .catch(error => {
                    // 请求失败时的回调函数  
                    console.error(error);
                });
        },
        getUser() {
            if (this.$store.state.userSession.id === null || this.$store.state.userSession.id === undefined) {
                return;
            }
            let url = `/user_info.php?user_id=${this.$store.state.userSession.id}`;

            this.$axios.get(url)
                .then(response => {
                    // 请求成功时的回调函数    
                    let res = response.data;
                    this.totalCount = res.count;
                    this.userInfo = res.user_data;
                    this.tryPro = res.tryPro;
                    this.resolvedPro = res.resolvedPro;
                    this.unResolvedPro = res.unResolvedPro;

                    this.percentage = res.user_data.submit_pro * 100 / res.count;
                    this.percentage = this.percentage.toFixed(2);
                    this.percentage = parseFloat(this.percentage);
                })
                .catch(error => {
                    // 请求失败时的回调函数    
                    console.error(error);
                });
        },
    }
}
</script>
  
<style scoped>
.pro-a {
    color: #3498db;
    /* 浅蓝色 */
    text-decoration: none;
    position: relative;
}

.pro-a:hover {
    color: #0d4971;
    /* 深蓝色 */
}

.problem-id {
    /* text-align: center; */
    /* white-space: nowrap; */
    letter-spacing: 0.05em;
    /* 增加字之间的间距 */
    margin-right: 1em;
    width: 48px;
    display: flex;
    justify-content: center;
}

.btn-xs {
    padding: 4px 5px;
    font-size: 12px;
    line-height: 1;
    border-radius: 3px;
}

.btn {
    display: inline-block;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    -ms-touch-action: manipulation;
    touch-action: manipulation;
    cursor: pointer;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    background-image: none;
    border: 1px solid transparent;
}

td {
    padding-right: 16px;
}

.user-problems {
    display: flex;
    flex-wrap: wrap;
}

.user-table {
    line-height: 30px;
    margin-left: 8px;
    margin-top: 8px;
}

.user-nick {
    background-image: url(https://blog.dotcpp.com/template/bs3/img/icon008.png);
    background-repeat: no-repeat;
    background-size: 24px;
}

.user-left {
    width: 30%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
}

.user-right {
    width: 70%;
    min-height: 85vh;
    margin-left: 16px;
}

.user-card {
    width: 70%;
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(26, 26, 26, .3);
    margin-top: 16px;
    padding: 8px;
    text-align: left;
}

.user-page {
    width: 98%;
    height: 100%;
    padding: 8px;
    display: flex;
}
</style>
  