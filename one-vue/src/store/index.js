import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex)

const store = new Vuex.Store({
    state: {
        visLogin: false,
        userSession: {},
    },
    mutations: {
        upVisLogin(state, visLogin) {
            state.visLogin = visLogin;
            localStorage.setItem('visLogin', visLogin);
        },
        upUserSession(state, userSession) {
            state.userSession = userSession;
            localStorage.setItem('userSession', JSON.stringify(userSession));
        },
        exitLogin(state){
            state.visLogin = false;
            state.userSession = {};
            this.commit("removeLocalStorage");
        },
        getLocalStorage(state) {
            const visLogin = localStorage.getItem('visLogin');
            if(visLogin != null){
                state.visLogin = visLogin;
            }

            const userSession = JSON.parse(localStorage.getItem('userSession'));
            if (userSession != null) {
                state.userSession = userSession;
            }
        },
        removeLocalStorage() {
            localStorage.removeItem('visLogin');
            localStorage.removeItem('userSession');
        }
    }
})

export default store