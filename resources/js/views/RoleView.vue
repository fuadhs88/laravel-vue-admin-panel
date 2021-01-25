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
                <b-form @submit.prevent="editRole">
                    <div
                        v-if="!roleEdit"
                        class="pr-2 pb-2 d-flex flex-row align-items-baseline"
                    >
                        <b-card-title class="pr-1">{{ name }} </b-card-title>
                        <div
                            v-if="
                                name !== loggedUser.role &&
                                    !roleEdit &&
                                    name !== 'Super Admin'
                            "
                            v-can="'role-edit'"
                        >
                            <b-icon
                                icon="pencil-fill"
                                variant="secondary"
                                @click="roleEdit = true"
                                class="edit-user"
                            ></b-icon>
                        </div>
                    </div>
                    <b-form-group
                        id="input-group-1"
                        label="Role name:"
                        label-for="input-1"
                        v-can="'role-edit'"
                        v-if="roleEdit"
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
                                v-can="'role-edit'"
                                name="check-button"
                                switch
                                v-if="
                                    name !== loggedUser.role &&
                                        name !== 'Super Admin'
                                "
                            >
                            </b-form-checkbox
                        ></b-list-group-item>
                    </b-list-group>
                    <b-list-group>
                        <b-button
                            type="submit"
                            variant="primary"
                            class="mt-3"
                            v-if="
                                name !== loggedUser.role &&
                                    name !== 'Super Admin'
                            "
                            >Edit Role</b-button
                        >
                        <b-button
                            variant="danger"
                            class="mt-3"
                            v-can="'role-delete'"
                            v-if="
                                name !== loggedUser.role &&
                                    name !== 'Super Admin'
                            "
                            @click.prevent="deleteRole"
                            >Delete Role</b-button
                        >
                    </b-list-group>
                </b-form>
            </b-card>
        </b-col>
    </b-row>
</template>
<script>
import { mapGetters, mapState } from "vuex";
import { USER_PERMISSIONS } from "../store/actions/user";
import { BUILD_ROLE, EDIT_ROLE, DELETE_ROLE } from "../store/actions/routes";
import { BIcon, BIconPencilFill } from "bootstrap-vue";

export default {
    beforeMount: function() {
        this.$store
            .dispatch(USER_PERMISSIONS)
            .then(resp => {
                let permissions = resp.data;
                if (permissions.indexOf("role-list") !== -1) {
                    return;
                } else {
                    this.$router.push("/");
                }
            })
            .catch(() => {
                this.$router.push("/");
            });
        this.$store
            .dispatch(BUILD_ROLE, this.id)
            .then(resp => {
                this.name = resp.data.name;
                this.oName = resp.data.name;
                let data = resp.data.permissions.map(perm => ({
                    name: perm.name,
                    want: perm.can
                }));
                this.permissions = data;
                this.oPermissions = data;
            })
            .then(() => {
                this.loaded = true;
            });
    },
    props: ["id"],
    components: { BIcon, BIconPencilFill },
    data() {
        return {
            error: false,
            errors: false,
            loaded: false,
            roleEdit: false,
            name: "",
            oName: "",
            permissions: [],
            oPermissions: []
        };
    },
    computed: {
        ...mapState({
            loggedUser: state => state.user.profile
        })
    },
    methods: {
        editRole: function() {
            const { id, name, permissions } = this;
            this.$store.dispatch(EDIT_ROLE, { id, name, permissions }).then(
                resp => {
                    this.$set(this, ["roleEdit"], false);
                    this.$router.push("/roles");
                },
                error => {
                    this.$set(this, ["roleEdit"], false);
                    this.$set(this, ["name"], this.oName);
                    this.$set(this, ["permissions"], this.oPermissions);
                    if (error.response.data.errors) {
                        this.$set(this, ["errors"], error.response.data.errors);
                    } else {
                        this.$set(this, ["error"], error.response.data.message);
                    }
                }
            );
        },
        deleteRole: function() {
            const { id } = this;
            this.$store.dispatch(DELETE_ROLE, { id }).then(
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
