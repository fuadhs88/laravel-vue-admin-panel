<template>
    <div>
        <Navbar></Navbar>
        <div class="container">
            <router-view></router-view>
        </div>
    </div>
</template>
<script>
import Navbar from "../components/Navbar";
import { USER_REQUEST } from "../store/actions/user";
export default {
    name: "app",
    components: {
        Navbar
    },
    created: function() {
        if (this.$store.getters.isAuthenticated) {
            this.$store.dispatch(USER_REQUEST);
        }
    }
};
</script>
