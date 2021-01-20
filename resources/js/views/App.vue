<template>
    <div>
        <!-- <Navbar></Navbar> -->
        <Sidebar
            ><template v-slot:navbar><Navbar></Navbar></template>
            <template v-slot:content>
                <router-view></router-view>
            </template>
        </Sidebar>
    </div>
</template>
<script>
import Navbar from "../components/Navbar";
import Sidebar from "../components/Sidebar";
import { USER_REQUEST } from "../store/actions/user";
export default {
    name: "app",
    components: {
        Navbar,
        Sidebar
    },
    created: function() {
        if (this.$store.getters.isAuthenticated) {
            this.$store.dispatch(USER_REQUEST);
        }
    }
};
</script>
