<template>
    <b-row align-v="center" align-h="center">
        <b-col sm="12">
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
                <div class="d-flex flex-row align-items-baseline">
                    <div
                        v-if="!nameEdit"
                        class="pr-2 pb-2 d-flex flex-row align-items-baseline"
                    >
                        <b-card-title>{{ name }} </b-card-title>
                        <div v-can="'user-edit'">
                            <b-icon
                                icon="pencil-fill"
                                variant="secondary"
                                @click="nameEdit = true"
                                class="edit-user"
                            ></b-icon>
                        </div>
                    </div>

                    <b-form
                        inline
                        @submit.prevent="editName"
                        v-if="nameEdit"
                        class="mb-3"
                    >
                        <b-form-input
                            id="input-1"
                            v-model="name"
                            type="text"
                            placeholder="Enter name and surname"
                            class="mr-2"
                            required
                        ></b-form-input>
                        <b-button type="submit" variant="primary"
                            >Change name</b-button
                        >
                    </b-form>
                </div>
                <b-form-group align-v="baseline">
                    <div class="d-flex flex-row align-items-baseline">
                        <b-card-sub-title v-if="!roleEdit" class="pr-1">
                            {{ role }}
                        </b-card-sub-title>
                        <div
                            v-can="'user-edit'"
                            v-if="id !== loggedUser.id && !roleEdit"
                        >
                            <b-icon
                                icon="pencil-fill"
                                variant="secondary"
                                @click="roleEdit = true"
                                class="edit-user"
                            ></b-icon>
                        </div>
                    </div>
                    <b-form
                        inline
                        v-if="roleEdit"
                        @submit.prevent="editRole"
                        v-can="'user-edit'"
                        class="flex flex-row align-items-baseline"
                    >
                        <b-form-select
                            v-model="role"
                            class=" mr-2"
                            :disabled="id == loggedUser.id"
                        >
                            <b-form-select-option
                                v-for="newRole in roles"
                                :key="newRole.id"
                                :first="newRole.role_name == role"
                                :value="newRole.role_name"
                                @click.prevent="editRole"
                            >
                                {{ newRole.role_name }}
                            </b-form-select-option>
                        </b-form-select>
                        <b-button type="submit" variant="primary"
                            >Change role</b-button
                        >
                    </b-form>
                </b-form-group>
                <b-form-group
                    ><strong>User created on:</strong>
                    {{ created_at | formatDate }}</b-form-group
                >

                <div class=" d-flex flex-row align-items-baseline">
                    <strong class="pr-1">Email address:</strong>
                    <p v-if="!emailEdit" class="pr-2">
                        {{ email }}
                    </p>
                    <b-form
                        inline
                        @submit.prevent="editMail"
                        v-if="emailEdit"
                        class="mb-3"
                    >
                        <b-form-input
                            id="input-1"
                            v-model="email"
                            type="text"
                            placeholder="Enter new email address"
                            class="mr-2"
                            required
                        ></b-form-input>
                        <b-button type="submit" variant="primary"
                            >Change email</b-button
                        >
                    </b-form>
                    <div
                        v-can="'user-edit'"
                        v-if="id !== loggedUser.id && !emailEdit"
                    >
                        <b-icon
                            icon="pencil-fill"
                            variant="secondary"
                            @click="emailEdit = true"
                            class="edit-user"
                        ></b-icon>
                    </div>
                </div>
                <b-form-group class="px-0">
                    <strong>Role permissions:</strong>
                    <b-badge
                        variant="success"
                        class="mr-2"
                        v-for="permission in permissions"
                        :key="permission.id"
                        >{{ permission.name }}</b-badge
                    >
                </b-form-group>
                <b-form-group class="px-0" v-can="'user-edit'">
                    <b-form inline @submit.prevent="editPassword">
                        <b-form-input
                            id="input-2"
                            v-model="password"
                            type="password"
                            placeholder="Enter new password"
                            class="mr-2"
                        ></b-form-input>
                        <b-button type="submit" variant="primary" class=""
                            >Change</b-button
                        >
                    </b-form>
                </b-form-group>
                <b-form-group class="px-0" v-can="'user-delete'">
                    <b-form inline @submit.prevent="deleteUser">
                        <b-button
                            v-if="role !== 'Super Admin'"
                            type="submit"
                            variant="danger"
                            class="mt-2"
                            >Delete user</b-button
                        ></b-form
                    >
                </b-form-group>
            </b-card>
        </b-col>
    </b-row>
</template>
<script>
import {
    BUILD_USER,
    EDIT_USER,
    DELETE_USER,
    ALL_ROLES
} from "../store/actions/routes";
import { mapState, mapGetters } from "vuex";
import { BIcon, BIconPencilFill } from "bootstrap-vue";
export default {
    name: "UserView",
    components: {
        BIcon,
        BIconPencilFill
    },
    data() {
        return {
            loaded: false,
            error: false,
            errors: false,
            nameEdit: false,
            emailEdit: false,
            roleEdit: false,
            name: "",
            oName: "",
            email: "",
            oEmail: "",
            created_at: "",
            newRole: "",
            role: "",
            oRole: "",
            password: "",
            roles: [],
            permissions: []
        };
    },
    props: ["id"],
    beforeMount() {
        this.$store
            .dispatch(BUILD_USER, this.id)
            .then(resp => {
                this.name = resp.data.name;
                this.email = resp.data.email;
                this.role = resp.data.role;
                this.created_at = resp.data.created_at;
                this.permissions = resp.data.permissions;
                this.oName = resp.data.name;
                this.oEmail = resp.data.email;
                this.oRole = resp.data.role;
            })
            .catch(() => {
                this.$router.push("/users");
            });
        this.$store.dispatch(ALL_ROLES).then(resp => {
            this.roles = resp;
            this.loaded = true;
        });
    },
    computed: {
        ...mapState({
            loggedUser: state => state.user.profile
        })
    },
    methods: {
        editName: function() {
            this.$set(this, ["nameEdit"], false);
            const { id, name } = this;
            this.$store.dispatch(EDIT_USER, { id, name }).then(
                resp => {},
                error => {
                    this.$set(this, ["name"], this.oName);
                    if (error.response.data.errors) {
                        this.$set(this, ["errors"], error.response.data.errors);
                    } else {
                        this.$set(this, ["error"], error.response.data.message);
                    }
                }
            );
        },
        editRole: function() {
            const { id, role } = this;
            this.$store.dispatch(EDIT_USER, { id, role }).then(
                resp => {
                    this.$set(this, ["roleEdit"], false);
                },
                error => {
                    this.$set(this, ["role"], this.oRole);
                    this.$set(this, ["roleEdit"], false);

                    if (error.response.data.errors) {
                        this.$set(this, ["errors"], error.response.data.errors);
                    } else {
                        this.$set(this, ["error"], error.response.data.message);
                    }
                }
            );
        },
        editMail: function() {
            this.$set(this, ["emailEdit"], false);
            const { id, email } = this;
            this.$store.dispatch(EDIT_USER, { id, email }).then(
                resp => {},
                error => {
                    this.$set(this, ["email"], this.oEmail);
                    if (error.response.data.errors) {
                        this.$set(this, ["errors"], error.response.data.errors);
                    } else {
                        this.$set(this, ["error"], error.response.data.message);
                    }
                }
            );
        },
        editPassword: function() {
            const { id, password } = this;
            this.$store.dispatch(EDIT_USER, { id, password }).then(
                resp => {},
                error => {
                    if (error.response.data.errors) {
                        this.$set(this, ["errors"], error.response.data.errors);
                    } else {
                        this.$set(this, ["error"], error.response.data.message);
                    }
                }
            );
        },
        deleteUser: function() {
            const { id } = this;
            this.$store.dispatch(DELETE_USER, { id }).then(
                resp => {
                    this.$router.push("/users");
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
