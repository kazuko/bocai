<template>
    <div>
        <mt-header title="我的设置">
            <router-link to="/" slot="left">
                <mt-button icon="back">返回</mt-button>
            </router-link>
        </mt-header>
        <div class="mySystem">
            <div class="pageContent mt10">
                <div class="sysList">
                    <mt-cell title="修改个人资料" to="/personal" is-link>
                        <img slot="icon" src="./../../../assets/修改资料图标@2x.png" width="24" height="24">
                    </mt-cell>
                </div>
                <div class="sysList">
                    <mt-cell title="交易密码" to="/transPsd" is-link>
                        <img slot="icon" src="./../../../assets/交易密码图标@2x.png" width="24" height="24">
                    </mt-cell>
                </div>
            </div>
            <div class="pageContent mt10">
                <div class="sysList">
                    <mt-cell title="好友验证设置">
                        <span><mt-switch v-model="friendValidation"></mt-switch></span>
                        <img slot="icon" src="./../../../assets/好友验证图标@2x.png" width="24" height="24">
                    </mt-cell>
                </div>
                <div class="sysList">
                    <mt-cell title="陌生消息">
                        <span><mt-switch v-model="strangerMsg"></mt-switch></span>
                        <img slot="icon" src="./../../../assets/陌生消息图标@2x.png" width="24" height="24">
                    </mt-cell>
                </div>
                <div class="sysList">
                    <mt-cell title="广播消息">
                        <span><mt-switch v-model="radioMsg"></mt-switch></span>
                        <img slot="icon" src="./../../../assets/广播图标@2x.png" width="24" height="24">
                    </mt-cell>
                </div>
                <mt-button class="switchSubmit" size="large" @click="change">提交修改</mt-button>
            </div>
        </div>
    </div>
</template>

<script>
console.log("PAGES_MYSYSTEM_MYSYSTEM_VUE");
import root from '@/config/root.js'
    export default {
        data(){
            return{
                friendValidation: true,
                strangerMsg: true,
                radioMsg: true
            }
        },
        beforeMount: function(){
            if(this.$store.state.songInfo.user.frvalidation){
                this.friendValidation = true
            }else{
                this.friendValidation = false
            }
            if(this.$store.state.songInfo.user.stmessage){
                this.strangerMsg = true
            }else{
                this.strangerMsg = false
            }
            if(this.$store.state.songInfo.user.broadcast){
                this.radioMsg = true
            }else{
                this.radioMsg = false
            }
        },
        methods: {
            change: function(){

                this.$axios.post(root.mySystem,{
                    frvalidation: this.friendValidation,
                    stmessage: this.strangerMsg,
                    broadcast: this.radioMsg
                }).then(response => {
                    if(response.data.code) {
                        this.$messagebox.alert("修改设置成功！","提示");
                        this.$store.state.songInfo.user.frvalidation = this.friendValidation;
                        this.$store.state.songInfo.user.stmessage = this.strangerMsg;
                        this.$store.state.songInfo.user.broadcast = this.radioMsg;
                    }
                })
            }
        }
    }
</script>

<style scoped>
.pageContent img{
    display: inline-block;
}
.pageContent{
    text-align: left;
}
.sysList{
    border-top: 1px solid #e7e7e7;
}
.switchSubmit{
    background-color: #57d6dd;
    margin-top: 20px;
    color: #fff;
    font-size: 14px;
}
</style>