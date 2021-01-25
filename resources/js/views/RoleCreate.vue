<template>
    <b-row v-if="loaded" align-v="center" align-h="center">
        <b-col>
            <div v-if="errors || error">
                <b-alert
                    show
                    variant="danger"
                    dismissible
                    v-for="error in errors"
                    :key="error[0]"
                >
                    {{ error[0] }}
                </b-alert>
                <b-alert v-if="error" show variant="danger" dismissible>
                    {{ error }}
                </b-alert>
            </div>
            <b-card>
                <b-form @submit.prevent="createRole">
                    <b-form-group
                        id="input-group-1"
                        label="Role name:"
                        label-for="input-1"
                    >
                        <b-form-input
                            id="input-1"
                            v-model="name"
                            type="text"
                            placeholder="Enter role name"
                            required
                        ></b-form-input>
                    </b-form-group>
                    <b-list-group>
                        <b-list-group-item
                            class="d-flex justify-content-between align-items-center"
                            v-for="permission in permissions"
                            :key="permission.name"
                            >{{ permission.name }}
                            <b-form-checkbox
                                v-model="permission.want"
                                name="check-button"
                                switch
                            >
                            </b-form-checkbox
                        ></b-list-group-item>
                    </b-list-group>
                    <b-button type="submit" variant="primary" class="mt-3"
                        >Create Role</b-button
                    >
                </b-form>
            </b-card>
        </b-col>
    </b-row>
</template>
<script>
import { mapGetters } from "vuex";
import { USER_PERMISSIONS } from "../store/actions/user";
import { ALL_PERMISSIONS, CREATE_ROLE } from "../store/actions/routes";
export default {
    beforeCreate: function() {
        this.$store
            .dispatch(USER_PERMISSIONS)
            .then(resp => {
                let permissions = resp.data;
                if (permissions.indexOf("role-create") !== -1) {
                    return;
                } else {
                    this.$router.push("/roles");
                }
            })
            .catch(() => {
                this.$router.push("/roles");
            });
        this.$store
            .dispatch(ALL_PERMISSIONS)
            .then(resp => {
                let data = resp.data.map(perm => ({
                    name: perm,
                    want: false
                }));
                this.permissions = data;
            })
            .then(() => {
                this.loaded = true;
            });
    },
    data() {
        return {
            error: false,
            errors: false,
            loaded: false,
            name: "",
            permissions: []
        };
    },
    methods: {
        createRole: function() {
            const { name, permissions } = this;
            this.$store.dispatch(CREATE_ROLE, { name, permissions }).then(
                resp => {
                    this.$router.push("/roles");
                },
                error => {
                    if (error.response.data.errors) {
                        this.$set(this, ["errors"], error.response.data.errors);
                    } else {
                        this.$set(this, ["error"], error.response.data.message);
                    }
                }
            );
        }
    }
};
</script>
