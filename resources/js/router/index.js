import Vue from "vue";
import Router from "vue-router";
import multiguard from "vue-router-multiguard";
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
    store.dispatch(IS_SETUP).then(() => {
        if (!store.getters.isInstalled) {
            next();
            return;
        } else {
            next("/login");
        }
    });
};

const ifInstalled = (to, from, next) => {
    store.dispatch(IS_SETUP).then(() => {
        if (store.getters.isInstalled) {
            next();
            return;
        } else {
            next("/setup");
        }
    });
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
            beforeEnter: multiguard([ifNotAuthenticated, ifInstalled])
        },
        {
            path: "/setup",
            name: "Setup",
            component: Setup,
            beforeEnter: multiguard([ifNotAuthenticated, ifNotInstalled])
        }
    ]
});
