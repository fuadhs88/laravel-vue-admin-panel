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
import moment from "moment";
import { mapState } from "vuex";

Vue.use(BootstrapVue, IconsPlugin);

Vue.directive("can", function(el, binding, vnode) {
    const store = vnode.context.$store;
    let permissions = store.state.user.permissions;
    if (permissions.indexOf(binding.value) !== -1) {
        return (vnode.elm.hidden = false);
    } else {
        return (vnode.elm.hidden = true);
    }
});

Vue.filter("formatDate", function(value) {
    if (value) {
        return moment(String(value)).format("DD/MM/YYYY hh:mm");
    }
});

new Vue({
    el: "#app",
    router,
    store,
    template: "<App/>",
    components: { App },
    computed: {
        ...mapState({
            permissions: state => state.user.permissions
        })
    }
});
