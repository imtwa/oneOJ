<template>
    <el-form :model="ruleForm" status-icon :rules="rules" ref="ruleForm" label-width="80px" style="margin-right: 10%;">
        <el-form-item label="用户名" prop="name">
            <el-input type="text" v-model="ruleForm.username"></el-input>
        </el-form-item>
        <el-form-item label="密码" prop="pass">
            <el-input type="password" v-model="ruleForm.password"></el-input>
        </el-form-item>
        <el-form-item prop="email" label="邮箱" :rules="[
            { required: true, message: '请输入邮箱地址', trigger: 'blur' },
            { type: 'email', message: '请输入正确的邮箱地址', trigger: ['blur', 'change'] }
        ]">
            <el-input v-model="ruleForm.email"></el-input>
        </el-form-item>
        <el-form-item label="验证码" prop="capt">
            <div style="display: flex; justify-content: space-between;">
                <el-input type="text" v-model="ruleForm.captcha"></el-input>
                <img :src="chatSrc" @click="getChat()" style="margin-left: 10px;width:35%;border-radius:5px;">
            </div>
        </el-form-item>
        <el-form-item>
            <el-button type="primary" @click="submitForm('ruleForm')" round :loading="isSubmitLoading"
                style="width:180px;margin-top: 20px;margin-left:-40px;">注册</el-button>
        </el-form-item>
    </el-form>
</template>  
  
<script>
export default {
    name: "UserRegistration",
    data() {
        return {
            ruleForm: {
                username: '',
                password: '',
                email: '',
                captcha: ''
            },
            rules: {
                name: [{ required: true, validator: this.checkName, trigger: 'blur' }],
                pass: [{ required: true, validator: this.checkPass, trigger: 'blur' }],
                capt: [{ required: true, validator: this.checkCapt, trigger: 'blur' }]
            },
            chatSrc: "",
            t: 1234,
            isSubmitLoading: false,
        };
    },
    mounted() {
        this.getChat();
    },
    methods: {
        // 获取验证码
        getChat() {
            const url = this.$axios.defaults.baseURL + "/captcha.php?t=";
            this.t = Date.now();
            let getUrl = url + this.t;
            this.chatSrc = getUrl;
        },

        submitForm(formName) {

            this.isSubmitLoading = true;
            this.$refs[formName].validate((valid) => {

                if (valid) {
                    // 验证通过 进行提交
                    // 只有这样封装参数服务器才能获得参数！！！
                    let params = new URLSearchParams();
                    params.append('username', this.ruleForm.username);
                    params.append('password', this.ruleForm.password);
                    params.append('email', this.ruleForm.email);
                    params.append('captcha', this.ruleForm.captcha);
                    params.append('t', this.t);

                    this.$axios.post('/registration.php', params)
                        .then(response => {
                            // 请求成功时的回调函数  
                            let res = response.data;
                            if (res.result) {
                                this.$message({
                                    message: '注册成功',
                                    type: 'success'
                                });
                                this.$router.push('/Problem');
                            } else {
                                this.$message.error(res.message);
                            }

                            this.isSubmitLoading = false;
                        })
                        .catch(error => {
                            // 请求失败时的回调函数  
                            console.error(error);
                            this.isSubmitLoading = false;
                        });

                } else {
                    this.isSubmitLoading = false;
                    return false;
                }
            });
        },
        checkName(rule, value, callback) {
            if (!this.ruleForm.username) {
                return callback(new Error('用户名不能为空'));
            }
            callback();
        },
        checkPass(rule, value, callback) {
            if (!this.ruleForm.password) {
                callback(new Error('请输入密码'));
            } else {
                callback();
            }
        },
        checkCapt(rule, value, callback) {
            if (!this.ruleForm.captcha) {
                return callback(new Error('验证码不能为空'));
            }
            callback();
        }
    }
};  
</script>

<style scoped></style>