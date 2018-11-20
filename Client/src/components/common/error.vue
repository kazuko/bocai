<template>
    <h4>
        {{this.GLOBAL.appInfo.error_str}}
    </h4>
</template>

<script>
export default {
    data(){
        return {

        }
    },
    beforeCreate: function(){
        this.$parent.tabbarShow = false;
    }
}
</script>
<style>

</style>

