<template>
    <b-row align-v="center" align-h="center">
        <b-col sm="12">
            <b-card no-body :header="currentRouteName">
                <b-table striped hover :fields="fields" :items="tableRows">
                    <template #cell(user_name)="data">
                        <b-link :to="`/${name}/${data.item.id}`">
                            {{ data.value }}</b-link
                        >
                    </template>
                    <template #cell(created_at)="data">
                        {{ data.value | formatDate }}
                    </template>
                    <template #cell(role_name)="data">
                        <b-link :to="`/role/${data.item.id}`">
                            {{ data.value }}</b-link
                        >
                    </template>
                    <template #cell(permissions)="data">
                        <b-badge
                            class="mr-2"
                            v-for="permission in data.value"
                            :key="permission.id"
                            :variant="permission.can ? 'success' : 'danger'"
                            >{{ permission.name }}</b-badge
                        >
                    </template>
                </b-table>
            </b-card>
        </b-col>
    </b-row>
</template>

<script>
import { BUILD_TABLE } from "../store/actions/routes";
import { mapState } from "vuex";
export default {
    name: "TableIndex",
    props: ["call"],
    data() {
        switch (this.$route.name) {
            case "users":
                return {
                    name: "user",
                    fields: [
                        {
                            key: "user_name",
                            label: "Full Name"
                        },
                        {
                            key: "email",
                            label: "Email Address"
                        },
                        {
                            key: "created_at",
                            label: "Created"
                        },
                        {
                            key: "role",
                            label: "Role"
                        }
                    ]
                };
            case "roles":
                return {
                    name: "role",
                    fields: [
                        {
                            key: "role_name",
                            label: "Name"
                        },
                        {
                            key: "permissions",
                            label: "Permissions"
                        }
                    ]
                };
            case "permissions":
                return {
                    name: "permission",
                    fields: [
                        {
                            key: "permission_name",
                            label: "Name"
                        }
                    ]
                };
        }
    },
    beforeMount() {
        this.$store.dispatch(BUILD_TABLE, this.call);
    },
    computed: {
        currentRouteName() {
            return (
                this.$route.name.charAt(0).toUpperCase() +
                this.$route.name.slice(1)
            );
        },
        ...mapState({
            tableRows: state => state.routes.table
        })
        /*         tableHeaders() {
            return Object.keys(this.tableData[0]).map((header, index) => {
                return {
                    index: index,
                    identifier: header
                };
            });
        }, */
        /*         tableRows() {
            return this.routes.table.map((obj, idx) => {
                return Object.keys(obj).map((key, idx2) => {
                    return {
                        key: key,
                        value: Object.values(obj)[idx2]
                    };
                });
            });
        } */
    }
};
</script>
