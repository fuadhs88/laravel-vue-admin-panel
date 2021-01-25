<template>
    <div>
        <!-- <Navbar></Navbar> -->
        <Sidebar
            ><template v-slot:navbar><Navbar></Navbar></template>
            <template v-slot:content>
                <router-view :key="currentRouteName"></router-view>
            </template>
        </Sidebar>
    </div>
</template>
<script>
import Navbar from "../components/Navbar";
import Sidebar from "../components/Sidebar";
import { USER_REQUEST } from "../store/actions/user";
import { ROUTE_REQUEST } from "../store/actions/routes";
export default {
    name: "app",
    components: {
        Navbar,
        Sidebar
    },
    created: function() {
        if (this.$store.getters.isAuthenticated) {
            this.$store
                .dispatch(USER_REQUEST)
                .then(this.$store.dispatch(ROUTE_REQUEST));
        }
    },
    computed: {
        currentRouteName() {
            return this.$route.name;
        }
    }
};
</script>
