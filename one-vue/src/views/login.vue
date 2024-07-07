
<template>
    <div>
        <div class="center">
            <!-- <h1>Sign in/ Sign up</h1> -->
            <div class="logon">
                <div :class="overlaylong">
                    <div class="overlaylong-Signin" v-if="disfiex == 0">
                        <UserLogin></UserLogin>
                        <!-- <h2 class="overlaylongH2">登录</h2>
					<input type="text" placeholder="user">
					<input type="text" placeholder="password">
					<h3>忘记密码？</h3>
					<button class="inupbutton">登录</button> -->
                    </div>
                    <div class="overlaylong-Signup" v-if="disfiex == 1">
                        <UserRegistration></UserRegistration>
                        <!-- <h2 class="overlaylongH2">注册账号</h2>
					<input type="text" placeholder="user">
					<input type="text" placeholder="password">
					<button class="inupbutton">注册</button> -->
                    </div>

                </div>
                <div :class="overlaytitle">
                    <div class="overlaytitle-Signin" v-if="disfiex == 0">
                        <h2 class="overlaytitleH2">欢迎使用OneOJ！</h2>
                        <p class="overlaytitleP">
                            没有账号？点击这里注册
                        </p>
                        <div class="buttongohs" @click="switchSignin">注册</div>
                    </div>
                    <div class="overlaytitle-Signup" v-if="disfiex == 1">
                        <h2 class="overlaytitleH2">OneOJ注册</h2>
                        <p class="overlaytitleP">已有账号？点击这里登录</p>
                        <div class="buttongohs" @click="switchSignup">登录</div>
                    </div>
                </div>
            </div>

        </div>
        <ViewBottom></ViewBottom>
    </div>
</template>
 
<script>
import UserLogin from '@/components/userLogin.vue';
import UserRegistration from '@/components/userRegistration.vue';
import ViewBottom from "@/components/viewBottom.vue";

export default {
    name: "LoginComponent",
    components: {
        'UserLogin': UserLogin,
        'UserRegistration': UserRegistration,
        'ViewBottom': ViewBottom
    },
    data() {
        return {
            overlaylong: 'overlaylong',
            overlaytitle: 'overlaytitle',
            disfiex: 0
        }
    },
    methods: {
        switchSignin() {
            this.overlaylong = "overlaylongleft"
            this.overlaytitle = "overlaytitleright"
            setTimeout(() => {
                this.disfiex = 1
            }, 200)
        },
        switchSignup() {
            this.overlaylong = "overlaylongright"
            this.overlaytitle = "overlaytitleleft"
            setTimeout(() => {
                this.disfiex = 0
            }, 200)
        }
    }
}
</script>

<style scoped>
.center {
    width: 100%;
    height: 85vh;
    background-size: 100% 100%;
    background-repeat: no-repeat;
    display: flex;
    align-items: center;
    /*justify-content: center;*/
    flex-direction: column;
}

h1 {
    font-size: 30px;
    color: black;
}

.logon {
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 14px 28px rgba(0, 0, 0, 0.25), 0 10px 10px rgba(0, 0, 0, 0.22);
    /* position: relative;
		overflow: hidden; */
    min-width: 480px;
    width: 40%;
    min-height: 400px;
    height: 65%;
    margin-top: 80px;
    display: flex;
    background: -webkit-linear-gradient(right, #4284db, #29eac4);
}

.overlaylong {
    border-radius: 10px 0 0 10px;
    width: 50%;
    height: 100%;
    background-color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
}

.overlaylongleft {
    border-radius: 0px 10px 10px 0px;
    width: 50%;
    height: 100%;
    background-color: #fff;
    transform: translateX(100%);
    transition: transform 0.6s ease-in-out;
    display: flex;
    align-items: center;
    justify-content: center;
}

.overlaylongright {
    border-radius: 10px 0 0 10px;
    width: 50%;
    height: 100%;
    background-color: #fff;
    transform: translateX(0%);
    transition: transform 0.6s ease-in-out;
    display: flex;
    align-items: center;
    justify-content: center;
}

.overlaytitle {
    border-radius: 0px 10px 10px 0px;
    width: 50%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0);
    display: flex;
    align-items: center;
    justify-content: center;
}


.overlaytitleH2 {
    font-size: 30px;
    color: #fff;
    margin-top: 20px;
}

.overlaytitleP {
    font-size: 15px;
    color: #fff;
    margin-top: 20px;
}

.overlaytitleleft {
    border-radius: 0px 10px 10px 0px;
    width: 50%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0);
    display: flex;
    align-items: center;
    justify-content: center;
    transform: translateX(0%);
    transition: transform 0.6s ease-in-out;
}

.overlaytitleright {
    border-radius: 0px 10px 10px 0px;
    width: 50%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0);
    display: flex;
    align-items: center;
    justify-content: center;
    transform: translateX(-100%);
    transition: transform 0.6s ease-in-out;
}

.overlaytitle-Signin {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
}

.overlaytitle-Signup {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
}

.buttongohs {
    width: 180px;
    height: 40px;
    border-radius: 50px;
    border: 1px solid #fff;
    color: #fff;
    font-size: 15px;
    text-align: center;
    line-height: 40px;
    margin-top: 40px;
}

.overlaylongH2 {
    font-size: 25px;
    color: black;
    /* width: 250px; */
}

.overlaylong-Signin {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
}

.overlaylong-Signup {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
}

input {
    background-color: #eee;
    border: none;
    padding: 12px 15px;
    margin: 10px 0;
    width: 240px;
}

h3 {
    font-size: 10px;
    margin-top: 10px;
    cursor: pointer;
}

.inupbutton {
    background-color: #29eac4;
    border: none;
    width: 180px;
    height: 40px;
    border-radius: 50px;
    font-size: 15px;
    color: #fff;
    text-align: center;
    line-height: 40px;
    margin-top: 30px;
}
</style>