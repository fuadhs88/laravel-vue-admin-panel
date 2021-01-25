<template>
    <b-row align-v="center" align-h="center">
        <b-col sm="12" v-show="isLoaded">
            <b-card>
                <b-row no-gutters align-v="baseline">
                    <b-card-title v-if="!nameEdit" class="pr-2">{{
                        thisUser.name
                    }}</b-card-title>
                    <b-icon
                        icon="pencil-fill"
                        variant="secondary"
                        @click="nameEdit = true"
                        v-if="thisUser.id !== loggedUser.id && !nameEdit"
                        v-can="'user-edit'"
                        class="edit-user"
                    ></b-icon>
                    <b-form
                        inline
                        @submit.prevent="editName"
                        v-if="nameEdit"
                        class="mb-3"
                    >
                        <b-form-input
                            id="input-1"
                            v-model="user.name"
                            type="text"
                            placeholder="Enter name and surname"
                            class="mr-2"
                            required
                        ></b-form-input>
                        <b-button type="submit" variant="primary"
                            >Change name</b-button
                        >
                    </b-form>
                </b-row>
                <b-row no-gutters align-v="baseline">
                    <b-card-sub-title class="mb-2">
                        {{ thisUser.role }}
                    </b-card-sub-title>
                    <b-form>
                        <b-form-select
                            v-model="selectedRole"
                            v-can="'user-edit'"
                            class="mb-2"
                        >
                            <b-form-select-option
                                v-for="role in roles"
                                :key="role.id"
                                :value="role.role_name"
                                :default="role.role_name == thisUser.role"
                                :disabled="
                                    thisUser.role == loggedUser.role ||
                                        role.role_name == thisUser.role
                                "
                                @click="editRole"
                            >
                                {{ role.role_name }}
                            </b-form-select-option>
                        </b-form-select>
                    </b-form>
                </b-row>
                <b-list-group flush class="px-0">
                    <b-list-group-item class="px-0">
                        <strong>User created on:</strong>
                        {{ thisUser.created_at | formatDate }}
                    </b-list-group-item>
                    <b-list-group-item class="px-0 d-flex flex-row">
                        <strong class="pr-2">Email address:</strong>
                        <p v-if="!emailEdit" class="pr-2">
                            {{ user.email ? user.email : thisUser.email }}
                        </p>
                        <b-form
                            inline
                            @submit.prevent="editMail"
                            v-if="emailEdit"
                            class="mb-3"
                        >
                            <b-form-input
                                id="input-1"
                                v-model="user.email"
                                type="text"
                                placeholder="Enter new email address"
                                class="mr-2"
                                required
                            ></b-form-input>
                            <b-button type="submit" variant="primary"
                                >Change email</b-button
                            >
                        </b-form>
                        <b-icon
                            icon="pencil-fill"
                            variant="secondary"
                            @click="emailEdit = true"
                            v-if="thisUser.id !== loggedUser.id && !emailEdit"
                            v-can="'user-edit'"
                            class="edit-user"
                        ></b-icon>
                    </b-list-group-item>
                    <b-list-group-item class="px-0">
                        <strong>{{ thisUser.role }} permissions:</strong>
                        <b-badge
                            variant="success"
                            class="mr-2"
                            v-for="permission in thisUser.permissions"
                            :key="permission.id"
                            >{{ permission.name }}</b-badge
                        >
                    </b-list-group-item>
                    <b-list-group-item class="px-0" v-can="'user-edit'">
                        <b-form inline @submit.prevent="editPassword">
                            <b-form-input
                                id="input-2"
                                v-model="user.password"
                                type="password"
                                placeholder="Enter new password"
                                class="mr-2"
                            ></b-form-input>
                            <b-button type="submit" variant="primary" class=""
                                >Change</b-button
                            >
                        </b-form>
                    </b-list-group-item>
                    <b-form inline @submit.prevent="deleteUser">
                        <b-button
                            v-if="thisUser.role !== 'Super Admin'"
                            v-can="'user-delete'"
                            type="submit"
                            variant="danger"
                            class="mt-2"
                            >Delete user</b-button
                        ></b-form
                    >
                </b-list-group>
            </b-card>
        </b-col>
    </b-row>
</template>
<script>
import { BUILD_USER, EDIT_USER } from "../store/actions/routes";
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
            error: null,
            selectedRole: null,
            nameEdit: false,
            emailEdit: false,
            user: {
                name: "",
                email: "",
                role: "",
                password: ""
            }
        };
    },
    props: ["id"],
    beforeMount() {
        this.$store.dispatch(BUILD_USER, this.id);
    },
    mounted: function() {
        this.$set(this.user, ["name"], this.thisUser.name);
        this.$set(this, ["selectedRole"], this.thisUser.role);
    },
    computed: {
        ...mapState({
            loggedUser: state => state.user.profile,
            thisUser: state => state.routes.user,
            roles: state => state.routes.roles
        }),
        ...mapGetters(["isLoaded"], ["allRoles"]),
        setStuff: function() {
            this.$set(this.user, ["name"], this.thisUser.name);
            this.$set(this, ["selectedRole"], this.thisUser.role);
        }
    },
    methods: {
        editName: function() {
            this.$set(this, ["nameEdit"], false);
            const { id } = this;
            this.$store
                .dispatch(EDIT_USER, { id, name: this.user.name })
                .catch(e => {
                    this.$set(this.user, ["name"], this.thisUser.name);
                    this.$set(this, ["error"], "Invalid name change request");
                });
        },
        editRole: function() {
            const { id } = this;
            this.$store
                .dispatch(EDIT_USER, { id, role: this.selectedRole })
                .catch(e => {
                    this.$set(this.user, ["role", this.selectedRole]);
                    this.$set(this, ["error"], "Invalid role change request");
                });
        },
        editMail: function() {
            const { id } = this;
            this.$store
                .dispatch(EDIT_USER, { id, name: this.user.email })
                .catch(e => {
                    this.$set(this.user, ["email", this.thisUser.email]);
                    this.$set(this, ["error"], "Invalid email change request");
                });
        }
    }
};
</script>
