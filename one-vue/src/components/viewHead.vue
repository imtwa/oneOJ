<template>
  <div class="top-bar">
    <div class="top-bar_inner">
      <div class="top-bar_left">
        <router-link class="top-bar_title" to="/">首页</router-link>
        <ul class="top-bar_list">
          <a href="#/Problem" class="top-bar_item top-bar_item--rank">题库</a>
          <a href="#/Ranking" class="top-bar_item top-bar_item--rank">排行</a>
        </ul>
      </div>
      <div class="top-bar_right">
        <ul class="top-bar_list">
          <div v-if="this.$store.state.visLogin" @mouseover="showExit = true" @mouseleave="showExit = false"
            style="height: 44px;margin-top: 8px;">
            <a href="#/User" class="top-bar_item top-bar_item--rank" style="padding: 8px;">
              {{ this.$store.state.userSession.name }}
            </a>
            <div v-if="showExit" class="posit">
              <el-button type="text" icon="el-icon-back" @click="exitLogin">退出</el-button>
            </div>
          </div>

          <a href="#/Login" v-else class="top-bar_item top-bar_item--rank">登录</a>
        </ul>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ViewHead',
  data() {
    return {
      router: this.$router,
      showExit: false,
    }
  },
  mounted() {
    this.$nextTick(function () {
      // 仅在整个视图都被渲染之后才会运行的代码
      this.showExit = false;
    })

  },
  methods: {
    goTo(path) {
      if (path && path !== this.router.currentRoute.path) {
        this.router.push(path);
      }
    },
    exitLogin() {
      this.$message({
        message: '退出登录ing',
        type: 'success'
      });
      this.$store.commit("exitLogin");
      this.router.push("Login");
    }
  },
}
</script>

<style scoped>
/* 顶部栏样式 */

.posit {
  position: absolute;
  top: 44px;
  width: 76px;
  height: 40px;
  box-shadow: 0 1px 3px rgba(26, 26, 26, .3);
}

.top-bar {
  width: 100%;
  height: 44px;
  /* 标题栏高度为60px */
  display: flex;
  justify-content: center;
  align-items: center;
  position: relative;
  /*下划线*/
  border-bottom: 1px solid rgba(224, 224, 216, 0.6);
}

/* 顶部栏内部容器样式 */
.top-bar_inner {
  display: flex;
  width: 100%;
  justify-content: space-between;
  align-items: center;
  padding: 0 10%;
}

/* 标题样式 */
.top-bar_title {
  left: 0;
  width: 100px;
  height: 48px;
  font-size: 26px;
  /* 字体大小 */
  font-weight: bold;
  /* 字体加粗 */
  border-radius: 10px;
  /* 圆角 */
  line-height: 48px;
  /* 设置与元素高度相同的高度  垂直居中*/
  text-decoration: none;
  color: #2c3e50;
  /* 去掉链接的下划线 */
}

.top-bar_left {
  width: 70%;
  display: flex;
}

.top-bar_right {
  display: flex;
}

/* 链接列表样式 */
.top-bar_list {
  display: flex;
  list-style-type: none;
  /* 去掉列表的符号 */
  margin: 0;
  padding: 0;
  align-items: center;
}

/* 链接项样式 */
.top-bar_item {
  /*margin: 0 10px;*/
  color: #2c3e50;
  width: 64px;
  height: 32px;
  line-height: 32px;
  font-size: 20px;
  /* 链接颜色为白色 */
  border-radius: 8px;
  /* 圆角 */
  text-decoration: none;
  /* 去掉链接的下划线 */
}

/* 鼠标悬浮在链接项上时的样式 */
.top-bar_item:hover,
.top-bar_title:hover {
  color: #41b883;
  /* 鼠标悬浮时链接变为绿色 */
  background-color: rgba(16, 185, 129, 0.1);
}
</style>
