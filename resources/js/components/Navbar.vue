<template>
    <div class="navigation">
        <ul>
            <li>
                <router-link class="brand" to="/">
                    <img src="" width="40px" /><strong>{{ title }}</strong>
                </router-link>
            </li>
        </ul>
        <ul>
            <li v-if="isProfileLoaded">
                <router-link to="/account">{{ name }}</router-link>
            </li>
            <li v-if="isAuthenticated" @click="logout">
                <span class="logout">Logout</span>
            </li>
            <li v-if="!isAuthenticated && !authLoading">
                <router-link to="/login">Login</router-link>
            </li>
        </ul>
    </div>
</template>

<script>
import { mapGetters, mapState } from "vuex";
import { AUTH_LOGOUT } from "../store/actions/auth";
export default {
    name: "Navbar",
    methods: {
        logout: function() {
            this.$store
                .dispatch(AUTH_LOGOUT)
                .then(() => this.$router.push("/login"));
        }
    },
    computed: {
        ...mapGetters(["getProfile", "isAuthenticated", "isProfileLoaded"]),
        ...mapState({
            authLoading: state => state.auth.status === "loading",
            name: state =>
                `${state.user.profile.name}, ${state.user.profile.role} `,
            title: state => state.app.title
        })
    }
};
</script>
