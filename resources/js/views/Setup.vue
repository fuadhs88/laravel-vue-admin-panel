<template>
    <b-row align-v="center" align-h="center">
        <b-col sm="12" md="8" lg="6" style="max-width:50rem">
            <b-card>
                <b-form @submit.prevent="setup">
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

                    <b-form-group
                        id="input-group-4"
                        label="Confirm Password:"
                        label-for="input-2"
                    >
                        <b-form-input
                            id="input-4"
                            type="password"
                            v-model="password_confirmation"
                            placeholder="Repeat your password"
                            required
                        ></b-form-input>
                    </b-form-group>

                    <b-button type="submit" variant="primary" class="mt-3"
                        >Signup</b-button
                    >
                </b-form>
            </b-card>
        </b-col>
    </b-row>
</template>

<script>
import { AUTH_SETUP } from "../store/actions/auth";
import { AUTH_REQUEST } from "../store/actions/auth";

export default {
    /*     created: function isInstalled() {
        this.$store.dispatch(IS_SETUP);
    }, */
    name: "Setup",
    data() {
        return {
            name: "",
            email: "",
            password: "",
            password_confirmation: ""
        };
    },
    methods: {
        setup: function() {
            const { name, email, password, password_confirmation } = this;
            this.$store
                .dispatch(AUTH_SETUP, {
                    name,
                    email,
                    password,
                    password_confirmation
                })
                .then(() => {
                    this.$store
                        .dispatch(AUTH_REQUEST, {
                            email,
                            password
                        })
                        .then(() => {
                            this.$router.push("/");
                        });
                });
        }
    }
};
</script>
