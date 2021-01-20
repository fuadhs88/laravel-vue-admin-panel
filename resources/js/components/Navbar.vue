<template>
    <div>
        <b-navbar toggleable="lg" type="dark" variant="dark">
            <b-navbar-brand to="/">{{ title }}</b-navbar-brand>

            <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>

            <b-collapse id="nav-collapse" is-nav>
                <!-- Right aligned nav items -->
                <b-navbar-nav class="ml-auto">
                    <b-nav-item-dropdown right v-if="isAuthenticated">
                        <!-- Using 'button-content' slot -->
                        <template #button-content variant="text-white">
                            <strong>User</strong>
                        </template>
                        <b-dropdown-item v-if="isProfileLoaded" to="/account">{{
                            name
                        }}</b-dropdown-item>
                        <b-dropdown-item v-if="isAuthenticated" @click="logout"
                            >Sign Out</b-dropdown-item
                        >
                    </b-nav-item-dropdown>
                </b-navbar-nav>
            </b-collapse>
        </b-navbar>
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
        ...mapGetters(["isAuthenticated", "isProfileLoaded"]),
        ...mapState({
            authLoading: state => state.auth.status === "loading",
            name: state =>
                `${state.user.profile.name} (${state.user.profile.role}) `,
            title: state => state.app.title
        })
    }
};
</script>
