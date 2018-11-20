<template>
    <div>
        <mt-header title="微信提现">
            <router-link to="/putForward" slot="left">
                <mt-button icon="back">返回</mt-button>
            </router-link>
        </mt-header>
    </div>
</template>

<script>
console.log("USER_CASH_WECHAT_VUE");
    export default {
        data(){
            return {

            }
        },
        mounted: function(){
            this.$axios.post('http://www.bai.com').then(response => {
                console.log(response)
            })
        }
    }
</script>

<style scoped>

</style>