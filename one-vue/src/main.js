import Vue from 'vue'
import App from './App.vue'
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';
import axios from 'axios';
import router from './router';
import store from './store';

axios.defaults.baseURL = 'http://localhost:8081'
// axios.defaults.baseURL = 'http://192.168.100.192:8081'

Vue.prototype.$axios = axios;
Vue.use(ElementUI);

// Vue.config.productionTip = false

new Vue({
  router,
  store,
  render: h => h(App),
}).$mount('#app')
