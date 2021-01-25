<template>
    <b-row align-v="center" align-h="center">
        <b-col sm="12">
            <b-card :title="profile.name">
                <b-card-sub-title class="mb-2">
                    {{ profile.role }}
                </b-card-sub-title>
                <b-list-group flush class="px-0">
                    <b-list-group-item class="px-0">
                        <strong>User created on:</strong>
                        {{ profile.created_at | formatDate }}
                    </b-list-group-item>
                    <b-list-group-item class="px-0">
                        <strong>{{ profile.role }} permissions:</strong>
                        <b-badge
                            variant="secondary"
                            class="mr-2"
                            v-for="permission in permissions"
                            :key="permission"
                            >{{ permission }}</b-badge
                        >
                    </b-list-group-item>
                </b-list-group>
            </b-card>
        </b-col>
    </b-row>
</template>
<script>
import { mapGetters, mapState } from "vuex";
export default {
    name: "Account",
    methods: {},
    computed: {
        ...mapGetters(["isAuthenticated", "isProfileLoaded"]),
        ...mapState({
            authLoading: state => state.auth.status === "loading",
            profile: state => state.user.profile,
            permissions: state => state.user.permissions
        })
    }
};
</script>
