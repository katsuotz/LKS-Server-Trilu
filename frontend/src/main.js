import Vue from 'vue'
import App from './App.vue'

import './assets/style.css'
import './assets/home.css'
import './assets/board.css'
import './assets/header.css'
import './assets/login.css'

Vue.config.productionTip = false

new Vue({
  render: h => h(App),
}).$mount('#app')
