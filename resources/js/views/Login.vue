<template>
    <b-row align-v="center" align-h="center">
        <b-col sm="12" md="8" lg="6" style="max-width:50rem">
            <b-card>
                <b-form @submit.prevent="login">
                    <b-form-group
                        id="input-group-1"
                        label="Email address:"
                        label-for="input-1"
                    >
                        <b-form-input
                            id="input-1"
                            v-model="email"
                            type="email"
                            placeholder="Enter email"
                            required
                        ></b-form-input>
                    </b-form-group>

                    <b-form-group
                        id="input-group-2"
                        label="Password:"
                        label-for="input-2"
                    >
                        <b-form-input
                            id="input-2"
                            type="password"
                            v-model="password"
                            placeholder="Password"
                            required
                        ></b-form-input>
                    </b-form-group>

                    <b-form-checkbox
                        id="input-3"
                        v-model="remember_me"
                        :true-value="true"
                        :false-value="false"
                        >Remember me</b-form-checkbox
                    >
                    <b-button type="submit" variant="primary" class="mt-3"
                        >Login</b-button
                    >
                </b-form>
            </b-card>
        </b-col>
    </b-row>
</template>

<script>
import { AUTH_REQUEST } from "../store/actions/auth";

export default {
    name: "Login",
    data() {
        return {
            email: "",
            password: "",
            remember_me: false
        };
    },
    methods: {
        login: function() {
            const { email, password, remember_me } = this;
            this.$store
                .dispatch(AUTH_REQUEST, { email, password, remember_me })
                .then(() => {
                    this.$router.push("/");
                });
        }
    }
};
</script>
