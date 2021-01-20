<template>
    <div>
        <!-- Sidebar -->
        <nav
            v-if="isAuthenticated"
            id="sidebar"
            class="vue-bootstrap-sidebar default-theme sidebar-visible"
        >
            <!-- eslint-disable vue/no-v-html -->
            <div class="sidebar-header" v-html="header" />
            <!-- eslint-enable -->
            <b-list-group class="items-wrapper">
                <template v-for="(link, index) in links">
                    <template v-if="link.href !== undefined">
                        <b-list-group-item :key="index">
                            <b-button
                                block
                                :to="link.href"
                                variant="info"
                                class="btn sidebar-menu-item"
                                :squared="true"
                            >
                                <div class="fa-icon">
                                    <component
                                        v-if="link.faIcon"
                                        :is="'font-awesome-icon'"
                                        :icon="link.faIcon"
                                    />
                                </div>
                                <div class="link-name">
                                    {{ link.name }}
                                </div>
                            </b-button>
                        </b-list-group-item>
                    </template>
                    <template v-else>
                        <b-list-group-item :key="index">
                            <b-button
                                v-b-toggle="`accordion-${index + 10}`"
                                block
                                href="#"
                                variant="info"
                                class="sidebar-menu-item dropdown-toggle"
                            >
                                <div class="fa-icon">
                                    <component
                                        v-if="link.faIcon"
                                        :is="'font-awesome-icon'"
                                        :icon="link.faIcon"
                                    />
                                </div>
                                <div class="link-name">
                                    {{ link.name }}
                                </div>
                            </b-button>
                        </b-list-group-item>
                        <b-collapse
                            :id="`accordion-${index + 10}`"
                            :key="index + 10"
                            accordion="my-accordion"
                            role="tabpanel"
                        >
                            <b-list-group>
                                <b-list-group-item
                                    v-for="(child, idx) in link.children"
                                    :key="idx"
                                >
                                    <b-button
                                        block
                                        variant="primary"
                                        class="sidebar-menu-item child-level-1"
                                        :to="child.href"
                                    >
                                        <div class="fa-icon">
                                            <component
                                                v-if="child.faIcon"
                                                :is="'font-awesome-icon'"
                                                :icon="child.faIcon"
                                            />
                                        </div>
                                        <div class="link-name">
                                            {{ child.name }}
                                        </div>
                                    </b-button>
                                </b-list-group-item>
                            </b-list-group>
                        </b-collapse>
                    </template>
                </template>
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
            show: state => state.auth.status == "success"
        })
    }
};
</script>
<style lang="scss">
@import "node_modules/@jurajkavka/vue-hamburger-button/src/scss/default-theme.scss";
</style>
