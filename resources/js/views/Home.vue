<template>
    <p>Hello {{ name }}</p>
</template>

<script>
export default {
    methods: {},
    name: "Home",
    mounted: function() {
        this.$router.push("/users");
    },
    data() {
        return { name: "" };
    }
};
</script>
