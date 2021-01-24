<template>
    <b-row align-v="center" align-h="center">
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

            <b-card v-if="loaded">
                <b-form @submit.prevent="createUser">
                    <b-form-group
                        id="input-group-1"
                        label="Name:"
                        label-for="input-1"
                    >
                        <b-form-input
                            id="input-1"
                            v-model="name"
                            type="text"
                            placeholder="Enter name and surname"
                            required
                        ></b-form-input>
                    </b-form-group>
                    <b-form-group
                        id="input-group-4"
                        label="Role:"
                        label-for="input-4"
                    >
                        <b-form-select
                            id="input-4"
                            v-model="role"
                            v-can="'user-edit'"
                            class="mb-2"
                        >
                            <b-form-select-option
                                v-for="role in roles"
                                :key="role.id"
                                :value="role.role_name"
                            >
                                {{ role.role_name }}
                            </b-form-select-option>
                        </b-form-select>
                    </b-form-group>
                    <b-form-group
                        id="input-group-2"
                        label="Email address:"
                        label-for="input-1"
                    >
                        <b-form-input
                            id="input-2"
                            v-model="email"
                            type="email"
                            placeholder="Enter email"
                            required
                        ></b-form-input>
                    </b-form-group>

                    <b-form-group
                        id="input-group-3"
                        label="Password:"
                        label-for="input-2"
                    >
                        <b-form-input
                            id="input-3"
                            type="password"
                            v-model="password"
                            placeholder="Password"
                            required
                        ></b-form-input>
                    </b-form-group>
                    <b-button type="submit" variant="primary" class="mt-3"
                        >Create User</b-button
                    >
                </b-form>
            </b-card>
        </b-col>
    </b-row>
</template>
<script>
import { USER_PERMISSIONS } from "../store/actions/user";
import { ALL_ROLES, CREATE_USER } from "../store/actions/routes";
export default {
    beforeCreate: function() {
        this.$store
            .dispatch(USER_PERMISSIONS)
            .then(resp => {
                let permissions = resp.data;
                if (permissions.indexOf("user-create") !== -1) {
                    this.loaded = true;
                    return;
                } else {
                    this.$router.push("/users");
                }
            })
            .catch(() => {
                this.$router.push("/roles");
            });
        this.$store.dispatch(ALL_ROLES).then(resp => {
            this.roles = resp;
            this.role = resp[0].role_name;
        });
        this.loaded = true;
    },
    data() {
        return {
            loaded: false,
            error: false,
            errors: false,
            name: "",
            email: "",
            role: "",
            password: "",
            roles: []
        };
    },
    methods: {
        createUser: function() {
            const { name, email, role, password } = this;
            this.$store
                .dispatch(CREATE_USER, { name, email, role, password })
                .then(
                    resp => {
                        this.$router.push("/users");
                    },
                    error => {
                        if (error.response.data.errors) {
                            this.$set(
                                this,
                                ["errors"],
                                error.response.data.errors
                            );
                        } else {
                            this.$set(
                                this,
                                ["error"],
                                error.response.data.message
                            );
                        }
                    }
                );
        }
    }
};
</script>
