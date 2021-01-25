import VueRouter from "vue-router";
import TableIndex from "../../views/TableIndex";
import PageNotFound from "../../views/PageNotFound";
import UserView from "../../views/UserView";
import RoleView from "../../views/RoleView";

import {
    ROUTE_REQUEST,
    PROCESS_DATA,
    BUILD_TABLE,
    BUILD_USER,
    BUILD_USER_LOADING,
    BUILD_USER_SUCCESS,
    ALL_ROLES,
    EDIT_USER
} from "../actions/routes";
import router from "../../router";
const state = {
    loaded: false,
    routes: [],
    table: [],
    user: [],
    roles: []
};

const getAUser = (to, from, next) => {};

const getARole = (to, from, next) => {
    axios({
        url: `/api/${to.name}/${to.params.id}`,
        method: "GET"
    })
        .then(resp => {
            commit(BUILD_ROLE, resp);
        })
        .catch(err => {
            next("/");
        });
};

const getters = {
    isLoaded: state => state.loaded,
    allRoles: state => state.roles
};

const actions = {
    [ROUTE_REQUEST]: ({ commit, dispatch }) => {
        return new Promise(resolve => {
            axios({ url: "/api/routes", method: "GET" }).then(resp => {
                if (resp) {
                    dispatch(PROCESS_DATA, resp.data);
                    resolve(resp);
                }
            });
        });
    },
    [PROCESS_DATA]: ({ commit }, data) => {
        let routeArray = [];
        data.forEach(route => {
            if (route.group == "index") {
                let newRoute;
                newRoute = {
                    path: `/${route.name}`,
                    component: TableIndex,
                    name: `${route.name}`,
                    props: () => ({ call: route.uri })
                };
                let stateRoute = {
                    name: route.name,
                    group: route.group,
                    path: route.name
                };
                router.addRoutes([newRoute]);
                routeArray.push(stateRoute);
            } else if (route.group == "views") {
                let newRoute;
                if (route.name == "user") {
                    newRoute = {
                        path: `/${route.name}/:id`,
                        component: UserView,
                        name: `${route.name}`,
                        props: true
                    };
                    router.addRoutes([newRoute]);
                } else if (route.name == "role") {
                    newRoute = {
                        path: `/${route.name}/:id`,
                        component: RoleView,
                        name: `${route.name}`,
                        props: true
                    };
                    router.addRoutes([newRoute]);
                }
            }
        });
        router.addRoutes([
            {
                path: "*",
                component: PageNotFound
            }
        ]);
        commit(ROUTE_REQUEST, routeArray);
    },
    [BUILD_TABLE]: ({ commit }, route) => {
        return new Promise(resolve => {
            axios({ url: route, method: "GET" }).then(resp => {
                if (resp) {
                    commit(BUILD_TABLE, resp.data);
                    resolve(resp);
                }
            });
        });
    },
    [BUILD_USER]: ({ commit, dispatch }, id) => {
        return new Promise(resolve => {
            commit(BUILD_USER_LOADING);
            axios({
                url: `/api/user/${id}`,
                method: "GET"
            })
                .then(resp => {
                    //console.log(resp);
                    commit(BUILD_USER, resp.data);
                    dispatch(ALL_ROLES);
                    commit(BUILD_USER_SUCCESS);
                })
                .catch(err => {
                    router.push("/");
                });
        });
    },
    [ALL_ROLES]: ({ commit }) => {
        return new Promise(resolve => {
            axios({ url: "/api/roles", method: "GET" }).then(resp => {
                if (resp) {
                    let data = resp.data.filter(function(el) {
                        return el.role_name !== "Super Admin";
                    });
                    commit(ALL_ROLES, data);
                    resolve(resp);
                }
            });
        });
    },
    [EDIT_USER]: ({ commit }, data) => {
        return new Promise(resolve => {
            axios({
                url: `/api/user/edit`,
                data: data,
                method: "PUT"
            }).then(resp => {
                if (resp) {
                    resolve(resp);
                }
            });
        });
    }
};

const mutations = {
    [BUILD_USER_LOADING]: state => {
        state.loaded = false;
    },
    [BUILD_USER_SUCCESS]: state => {
        state.loaded = true;
    },
    [ROUTE_REQUEST]: (state, resp) => {
        state.routes = resp;
    },
    [BUILD_TABLE]: (state, resp) => {
        state.table = resp;
    },
    [BUILD_USER]: (state, resp) => {
        state.user = resp;
    },
    [ALL_ROLES]: (state, resp) => {
        state.roles = resp;
    }
};

export default {
    state,
    getters,
    actions,
    mutations
};
