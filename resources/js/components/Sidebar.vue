<template>
    <div>
        <!-- Sidebar -->
        <nav
            v-if="isAuthenticated"
            id="sidebar"
            class="vue-bootstrap-sidebar default-theme sidebar-visible"
        >
            <!-- eslint-enable -->
            <b-list-group>
                <b-list-group-item
                    flush
                    class="py-3 px-2"
                    v-for="route in routes"
                    :to="`/${route.path}`"
                    :key="route.name"
                    >{{
                        route.name.charAt(0).toUpperCase() + route.name.slice(1)
                    }}</b-list-group-item
                >
            </b-list-group>
            <!--/ .items-wrapper -->
            <!-- <HamburgerButton
                id="sidebarButton"
                class="sidebar-button"
                :is-hamburger="!initialShow"
                :class="[show ? 'visible' : 'hidden']"
                @click="onButtonClick"
            /> -->
        </nav>
        <div id="navbar" :class="[isAuthenticated ? 'sidebar' : 'no-sidebar']">
            <slot name="navbar" />
        </div>
        <!-- Hamburger Menu -->
        <b-container
            fluid
            id="content"
            :class="[isAuthenticated ? 'sidebar' : 'no-sidebar']"
        >
            <slot name="content" />
        </b-container>
    </div>
</template>
<script>
import HamburgerButton from "@jurajkavka/vue-hamburger-button";
import { mapGetters, mapState } from "vuex";
export default {
    name: "Sidebar",
    components: {
        HamburgerButton
    },
    props: {
        links: {
            type: Array,
            default: null
        },
        initialShow: {
            type: Boolean,
            default: true
        },
        theme: {
            type: String,
            default: "default-theme"
        },
        header: {
            type: String,
            default: "Sidebar"
        }
    },
    data() {
        return {};
    },
    methods: {
        // onButtonClick() {
        //     this.show = !this.show;
        //     this.$emit("sidebarChanged", this.show);
        // }
    },
    computed: {
        ...mapGetters(["isAuthenticated"]),
        ...mapState({
            show: state => state.auth.status == "success",
            routes: state => state.routes.routes
        })
    }
};
</script>
<style lang="scss">
@import "node_modules/@jurajkavka/vue-hamburger-button/src/scss/default-theme.scss";
</style>
