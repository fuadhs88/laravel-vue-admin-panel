import {
    USER_REQUEST,
    USER_ERROR,
    USER_SUCCESS,
    USER_PERMISSIONS
} from "../actions/user";
import Vue from "vue";
import { AUTH_LOGOUT } from "../actions/auth";

const state = { status: "", profile: {}, permissions: [] };

const getters = {
    getProfile: state => state.profile,
    isProfileLoaded: state => !!state.profile.name,
    getPermissions: state => state.permissions
};

const actions = {
    [USER_REQUEST]: ({ commit, dispatch }) => {
        commit(USER_REQUEST);
        const userToken = localStorage.getItem("user-token");
        axios.defaults.headers.common["Authorization"] = userToken;
        axios({ url: "api/account", method: "GET" })
            .then(resp => {
                dispatch(USER_PERMISSIONS);
                commit(USER_SUCCESS, resp);
            })
            .catch(() => {
                commit(USER_ERROR);
                // if resp is unauthorized, logout, to
                dispatch(AUTH_LOGOUT);
            });
    },
    [USER_PERMISSIONS]: ({ commit }) => {
        const userToken = localStorage.getItem("user-token");
        axios.defaults.headers.common["Authorization"] = userToken;
        axios({ url: "api/account/permissions", method: "GET" })
            .then(resp => {
                commit(USER_PERMISSIONS, resp);
            })
            .catch(() => {
                commit(USER_ERROR);
                // if resp is unauthorized, logout, to
                //dispatch(AUTH_LOGOUT);
            });
    }
};

const mutations = {
    [USER_REQUEST]: state => {
        state.status = "loading";
    },
    [USER_SUCCESS]: (state, resp) => {
        state.status = "success";
        Vue.set(state, "profile", resp.data);
    },
    [USER_PERMISSIONS]: (state, resp) => {
        state.status = "success";
        Vue.set(state, "permissions", resp.data);
    },
    [USER_ERROR]: state => {
        state.status = "error";
    },
    [AUTH_LOGOUT]: state => {
        state.profile = {};
        state.permissions = [];
    }
};

export default {
    state,
    getters,
    actions,
    mutations
};
