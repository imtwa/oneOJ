<template>
  <div style="width: 100%;height:100%">
    <div class="ace-head">
      <!-- 编辑器风格选择框 -->
      <el-select v-model="aceConfig.selectTheme" @change="handleThemeChange" :placeholder="aceConfig.themes[0]"
        class="ace-select" size="mini">
        <el-option v-for="item in aceConfig.themes" :key="item" :value="item">
        </el-option>
      </el-select>
      <!-- 代码语言选择框 -->
      <el-select v-model="aceConfig.selectLang" @change="handleLangChange" :placeholder="aceConfig.langs[0]"
        class="ace-select" size="mini">
        <el-option v-for="item in aceConfig.langs" :key="item" :value="item">
        </el-option>
      </el-select>
      <el-button style="flex: 1; margin-left: 30%;" type="text" icon="el-icon-s-tools" size="medium"
        @click="showSettingModal">
      </el-button>
    </div>
    <div class="ace-main">
      <!--editor插件-->
      <!--其中的@input中的方法就是子组件值改变时调用的方法，该方法会给父组件传入改变值-->
      <editor :value="content" @input="handleInput" @init="editorInit" :lang="aceConfig.selectLang"
        :theme="aceConfig.selectTheme" :options="aceConfig.options" width="100%" height="100%" />
    </div>

    <el-dialog title="代码编辑器设置" :visible.sync="visible">
      <div style="padding: 16px;">
        <div style="display: flex;">
          <div style="flex: 2;">
            <h3 style="color: rgb(38 38 38);">字体设置</h3>
            <h3 style="color: #3c3c4399;">调整适合你的字体大小。</h3>
          </div>
          <div style="flex: 1;">
            <el-select v-model="aceConfig.options.fontSize" @change="handleFontChange"
              :placeholder="aceConfig.fontSizes[0]" class="ace-select" size="mini" style="width: 50%;">
              <el-option v-for="item in aceConfig.fontSizes" :key="item" :value="item">
              </el-option>
            </el-select>
          </div>
        </div>
      </div>
      <div style="padding: 16px;">
        <div style="display: flex;">
          <div style="flex: 2;">
            <h3 style="color: rgb(38 38 38);">Tab 长度</h3>
            <h3 style="color: #3c3c4399;">选择你想要的 Tab 长度，默认设置为4个空格。</h3>
          </div>
          <div style="flex: 1;">
            <el-select v-model="aceConfig.options.tabSize" @change="handleTabChange" :placeholder="aceConfig.tabs[0]"
              class="ace-select" size="mini" style="width: 50%;">
              <el-option v-for="item in aceConfig.tabs" :key="item" :value="item">
              </el-option>
            </el-select>
          </div>
        </div>
      </div>

      <div slot="footer" class="dialog-footer">
        <el-button @click="visible = false">取 消</el-button>
        <el-button type="primary" @click="visible = false">确 定</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>

// 编辑器主题
const themes = [
  'xcode',
  'eclipse',
  'chrome',
  'ambiance',
  'tomorrow',
  'monokai',
  'cobalt',
]
// 编辑器语言
const langs = [
  'c_cpp',
  'java',
  'python',
  'php'
]
// tabs
const tabs = [2, 4, 8]
// 字体大小
const fontSizes = [14, 16, 18, 20, 22, 24]
// 编辑器选项
const options = {
  tabSize: 4, // tab默认大小
  showPrintMargin: false, // 去除编辑器里的竖线
  fontSize: 16, // 字体大小
  highlightActiveLine: true, // 高亮配置
  showGutter: true,// 显示行号
  enableBasicAutocompletion: true, //启用基本自动完成
  enableSnippets: true, // 启用代码段
  enableLiveAutocompletion: true, // 启用实时自动完成
}
export default {
  name: "CodeEditAce",
  components: {
    editor: require('vue2-ace-editor'),
  },
  data() {
    return {
      visible: false, // 模态窗口显示控制
      aceConfig: { // 代码块配置
        langs, // 语言
        themes, // 主题
        tabs, // tab空格
        fontSizes,
        options, // 编辑器属性设置
        selectTheme: 'xcode', // 默认选择的主题
        selectLang: 'c_cpp', // 默认选择的语言
        readOnly: false, // 是否只读
        enableLiveAutocompletion: true,
      },
    }
  },
  // 接收父组件v-model传来的值
  model: {
    prop: 'content',
    event: 'change'
  },
  props: {
    content: String // content就是上面prop中声明的值，要保持一致
  },
  mounted() {
  },
  methods: {
    // 当该组件中的值改变时，通过该方法将该组件值传给父组件，实现组件间双向绑定
    handleInput(e) {
      this.$emit('change', e) // 这里e是每次子组件修改的值，change就是上面event中声明的，不要变
    },

    // 显示'编辑器设置'模态窗口
    showSettingModal() {
      this.visible = true
    },
    //分割线：以下为该代码组件的配置
    // 代码块主题切换
    handleThemeChange(value) {
      this.aceConfig.selectTheme = value
      this.editorInit()
    },
    // 代码块语言切换
    handleLangChange(value) {
      this.aceConfig.selectLang = value
      this.editorInit()
      const lan = [1,3,6,7];
      this.$emit('changeLanguage', lan[langs.indexOf(value)])
    },
    // tab切换
    handleTabChange(value) {
      this.aceConfig.options.tabSize = value
      this.editorInit()
    },
    // 字体大小切换
    handleFontChange(value) {
      this.aceConfig.options.fontSizes = value
      this.editorInit()
    },
    // 代码块初始化
    editorInit() {
      require('brace/ext/language_tools') // language extension prerequsite...
      require(`brace/mode/${this.aceConfig.selectLang}`) // 语言
      require(`brace/theme/${this.aceConfig.selectTheme}`) // 主题
    },
  }

}
</script>

<style scoped>
.ace-head {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 36px;
  height: 6%
}

.ace-main {
  height: 94%;
}

.ace-select {
  width: 20%;
  margin: 8px;
}
</style>
