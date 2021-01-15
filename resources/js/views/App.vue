<template>
    <div>
        <h1>Vue Router Demo App</h1>

        <p>
            <router-link :to="{ name: 'home' }">Home</router-link> |
            <router-link :to="{ name: 'about' }">About</router-link> |
            <a href="/register">Register (non spa)</a> |
            <a href="/logout">Logout (non spa)</a>
        </p>

        <div class="container">
            <router-view></router-view>
        </div>
    </div>
</template>
<script>
export default {};
</script>
