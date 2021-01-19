import Vue from "vue";
import Router from "vue-router";
import Home from "../views/Home";
import About from "../views/About";
import Login from "../views/Login";
import Setup from "../views/Setup";
import store from "../store";
import { IS_SETUP } from "../store/actions/auth";
Vue.use(Router);

const ifNotAuthenticated = (to, from, next) => {
    if (!store.getters.isAuthenticated) {
        next();
        return;
    }
    next("/");
};

const ifAuthenticated = (to, from, next) => {
    if (store.getters.isAuthenticated) {
        next();
        return;
    }
    next("/login");
};

const ifNotInstalled = (to, from, next) => {
    if (!store.getters.isAuthenticated) {
        store.dispatch(IS_SETUP).then(() => {
            if (store.getters.isInstalled == false) {
                next();
                return;
            } else {
                next("/login");
            }
        });
    } else {
        next("/");
    }
};

const ifInstalled = (to, from, next) => {
    if (!store.getters.isAuthenticated) {
        store.dispatch(IS_SETUP).then(() => {
            if (store.getters.isInstalled == true) {
                next();
                return;
            } else {
                next("/setup");
            }
        });
    } else {
        next("/");
    }
};

export default new Router({
    mode: "history",
    routes: [
        {
            path: "/",
            name: "Home",
            component: Home,
            beforeEnter: ifAuthenticated
        },
        {
            path: "/about",
            name: "About",
            component: About,
            beforeEnter: ifAuthenticated
        },
        {
            path: "/login",
            name: "Login",
            component: Login,
            beforeEnter: ifInstalled
        },
        {
            path: "/setup",
            name: "Setup",
            component: Setup,
            beforeEnter: ifNotInstalled
        }
    ]
});
