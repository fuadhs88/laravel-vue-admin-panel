require("./bootstrap");

import Vue from "vue";
import { BootstrapVue, IconsPlugin } from "bootstrap-vue";
//import VueRouter from "vue-router";
import App from "./views/App";
import router from "./router";
import store from "./store";
//import "bootstrap/dist/css/bootstrap.css";
//import "bootstrap-vue/dist/bootstrap-vue.css";
//import "../../public/css/app.css";
import "./app.scss";

Vue.use(BootstrapVue, IconsPlugin);

new Vue({
    el: "#app",
    router,
    store,
    template: "<App/>",
    components: { App }
});

Vue.directive("can", function(el, binding, vnode) {
    if (Permissions.indexOf(binding.value) !== -1) {
        return (vnode.elm.hidden = false);
    } else {
        return (vnode.elm.hidden = true);
    }
});
