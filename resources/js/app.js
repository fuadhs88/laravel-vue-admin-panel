require("./bootstrap");

import Vue from "vue";
//import VueRouter from "vue-router";
import App from "./views/App";
import router from "./router";
import store from "./store";

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
