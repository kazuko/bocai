<template>
    <div>
        <div class="pageContent" id="registPage">
            <div class="title">用户注册</div>
            <mt-field :placeholder="usernamep" v-model="username" align="left"></mt-field>
            <div class="yzm" v-show="isyzm">
                <mt-field placeholder="验证码" v-model="yzm"></mt-field>
                <mt-button  class="yzm-btn">获取验证码</mt-button>
            </div>
            <mt-field placeholder="密码" type="password" v-model="password" align="left"></mt-field>
            <mt-field placeholder="确认密码" type="password" v-model="password1" align="left"></mt-field>
            <mt-button class="regist-btn" size="large" @click="regist">注册</mt-button>
        </div>
    </div>
</template>

<script>
console.log("LOGIN_REGIST_VUE");

import root from '@/config/root.js'
    export default {
        data(){
            return{
                usernamep : '',
                username: '',
                yzm: '',
                password: '',
                password1: '',
                isyzm: false,
                registType: 1
            }
        },
        methods: {
            regist: function(){
                if(this.username.length>4 && this.username.length<13){
                    if(this.password == this.password1 && this.password != ''){
                        if(this.registType == 1){
                            this.$axios.post(root.regist,{
                                username: this.username,
                                password: this.password,
                                phonenumber: ''
                            }).then(response => {
                                this.$messagebox.alert(response.data.code);
                                console.log(response)
                            })
                        }
                        if(this.registType == 3 || this.registType == 2){
                            this.$axios.post(root.regist,{
                                username: '',
                                password: this.password,
                                phonenumber: this.username
                            }).then(response => {
                                this.$messagebox.alert(response.data.code);
                                console.log(response)
                            })
                        }
                    }
                }
            }
        },
        beforeCreate: function(){
            document.getElementsByTagName("html")[0].className="mg0";
            document.getElementsByTagName("body")[0].className="mg0";
            this.$axios.post(root.regist).then(response => {
                console.log(response)
                if(!response.data.code){
                    this.$messagebox.alert('目前不能注册！').then(action => {
                        this.$router.push('/')
                        this.registType = 0;
                    })
                }
                if(response.data.code == 1){
                    this.usernamep = '用户名';
                    this.isyzm = false;
                    this.registType = 1;
                }
                if(response.data.code == 2){
                    this.usernamep = '请输入手机号';
                    this.isyzm = false;
                    this.registType = 2;
                }
                if(response.data.code == 3) {
                    this.usernamep = '请输入手机号';
                    this.isyzm = true;
                    this.registType = 3;
                }
            });
        },
        beforeDestroy: function() {
            document.html.removeAttribute("class","mg0");
            document.body.removeAttribute("class","mg0");
        },
        beforeMount: function(){

        },
        mounted: function(){
            let h = window.screen.height;
            document.getElementById('registPage').style.height = h + 'px';
        },
        created: function(){
            // this.$store.state.isPageLogin = true
        },
        destroyed: function(){
            // this.$store.state.isPageLogin = false;
            document.html.removeAttribute("class","mg0");
            document.body.removeAttribute("class","mg0");
        }
    }
</script>

<style scoped>
#registPage{
    background: url('./../../assets/图层3@2x.png') no-repeat;
    background-size: 100% 100%; 
    padding: 0 20px;
}
.title{
    padding: 60px 0 20px 0;
    color: #fff;
    font-size: 20px;
}
.yzm{
    display: flex;
    justify-content: space-between
}
.yzm-btn{
    margin-top: 20px;
    width: 45%;
    height: 50px;
    border: 2px solid #fff;
    background-color: transparent;
    font-size: 14px;
    color: #F4DE43;
    text-decoration: underline;
    text-decoration-color: #F4DE43;
    margin-left: 20px;
}
.regist-btn{
    margin-top: 20px;
    font-size: 16px;
    background-color: #57d6dd;
    color: #fff;
}
</style>