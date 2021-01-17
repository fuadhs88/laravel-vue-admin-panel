<template>
    <div>
        <h1>{{ title }}</h1>

        <p>
            <router-link :to="{ name: 'home' }">Home</router-link> |
            <router-link :to="{ name: 'about' }">About</router-link> |
            <a href="/logout">Logout (non spa)</a>
        </p>

        <div class="container">
            <router-view></router-view>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            title: process.env.MIX_APP_NAME
        };
    }
};
</script>
