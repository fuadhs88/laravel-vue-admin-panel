<template> </template>
<script>
import router from "../router";
export default {
    created: function() {
        // Redirect outside the app using plain old javascript
        router.push("/");
    }
};
</script>
