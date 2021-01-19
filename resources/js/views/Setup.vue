<template>
    <div>
        <form class="login" @submit.prevent="login">
            <h1>Sign in</h1>
            <label>Name</label>
            <input
                required
                v-model="name"
                type="text"
                placeholder="Mario Rossi"
            />
            <label>Email Address</label>
            <input
                required
                v-model="email"
                type="text"
                placeholder="ciao@ciao.it"
            />
            <label>Password</label>
            <input
                required
                v-model="password"
                type="password"
                placeholder="Password"
            />
            <label>Confirm Password</label>
            <input
                required
                v-model="password_confirmation"
                type="password"
                placeholder="Repeat your password"
            />
            <hr />
            <button type="submit">Setup</button>
        </form>
    </div>
</template>

<script>
import { AUTH_SETUP } from "../store/actions/auth";
import { AUTH_REQUEST } from "../store/actions/auth";

export default {
    /*     created: function isInstalled() {
        this.$store.dispatch(IS_SETUP);
    }, */
    name: "Login",
    data() {
        return {
            name: "",
            email: "",
            password: "",
            password_confirmation: ""
        };
    },
    methods: {
        login: function() {
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
