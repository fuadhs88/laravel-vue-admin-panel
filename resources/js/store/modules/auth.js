import VueRouter from "vue-router";
import {
    AUTH_REQUEST,
    AUTH_ERROR,
    AUTH_SUCCESS,
    AUTH_LOGOUT,
    AUTH_SETUP,
    SETUP_ERROR,
    IS_SETUP
} from "../actions/auth";
import { USER_REQUEST } from "../actions/user";
import { ROUTE_REQUEST } from "../actions/routes";
import router from "../../router";

const state = {
    token: localStorage.getItem("user-token") || "",
    status: "",
    hasLoadedOnce: false,
    isInstalled: null
};

const getters = {
    isAuthenticated: state => !!state.token,
    authStatus: state => state.status,
    isInstalled: state => state.isInstalled
};

const actions = {
    [AUTH_REQUEST]: ({ commit, dispatch }, user) => {
        return new Promise((resolve, reject) => {
            commit(AUTH_REQUEST);
            axios({ url: "/api/auth/login", data: user, method: "POST" })
                .then(resp => {
                    const token = `Bearer ${resp.data.access_token}`;
                    localStorage.setItem("user-token", token);
                    axios.defaults.headers.common["Authorization"] = token;
                    commit(AUTH_SUCCESS, token);
                    dispatch(USER_REQUEST);
                    dispatch(ROUTE_REQUEST);
                    resolve(token);
                })
                .catch(err => {
                    commit(AUTH_ERROR, err);
                    localStorage.removeItem("user-token");
                    reject(err);
                });
        });
    },
    [AUTH_LOGOUT]: ({ commit, dispatch }) => {
        return new Promise(resolve => {
            commit(AUTH_LOGOUT);
            //router.push("/login");
            axios({ url: "/api/auth/logout", method: "GET" });
            localStorage.removeItem("user-token");
            delete axios.defaults.headers.common["Authorization"];
            resolve();
        });
    },
    [AUTH_SETUP]: ({ commit, dispatch }, user) => {
        return new Promise(resolve => {
            commit(AUTH_SETUP);
            axios({ url: "/api/auth/signup", data: user, method: "POST" })
                .then(resp => {
                    resolve();
                })
                .catch(err => {
                    commit(SETUP_ERROR, err);
                    reject(err);
                });
        });
    },
    [IS_SETUP]: ({ commit }) => {
        return new Promise(resolve => {
            axios({
                url: "/api/auth/installed",
                method: "GET"
            }).then(resp => {
                if (resp) {
                    commit(IS_SETUP, resp.data.isInstalled);
                    resolve(resp);
                }
            });
        });
    }
};

const mutations = {
    [AUTH_REQUEST]: state => {
        state.status = "loading";
    },
    [AUTH_SUCCESS]: (state, resp) => {
        state.status = "success";
        state.token = `${resp}`;
        state.hasLoadedOnce = true;
    },
    [AUTH_ERROR]: state => {
        state.status = "error";
        state.hasLoadedOnce = true;
    },
    [AUTH_LOGOUT]: state => {
        state.token = "";
    },
    [AUTH_SETUP]: state => {
        state.status = "Installing";
    },
    [SETUP_ERROR]: state => {
        state.status = "";
    },
    [IS_SETUP]: (state, resp) => {
        state.isInstalled = resp;
    }
};

export default {
    state,
    getters,
    actions,
    mutations
};
