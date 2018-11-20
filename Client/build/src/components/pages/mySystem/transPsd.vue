<template>
    <div>
        <mt-header title="交易密码">
            <router-link to="/mySystem" slot="left">
                <mt-button icon="back">返回</mt-button>
            </router-link>
        </mt-header>
        <div class="pageContent mt10">
            <div class="transAttention">
                <p>注意：交易密码必须为6位数的数字</p>
            </div>
            <div class="transPsd" v-show="!transPsdType">
                <mt-field label="原始交易密码" type="password" placeholder="请输入交易密码" v-model="transPsd"></mt-field>
                <div class="transError" :class="{transErrorActive:transError1}">*密码输入错误</div>
            </div>
            <div class="transPsd1">
                <mt-field label="设置交易密码" type="password" placeholder="请输入交易密码" v-model="transPsd1"></mt-field>
                <div class="transError"></div>
            </div>
            <div class="transPsd2">
                <mt-field label="确认交易密码" type="password" placeholder="请确认交易密码" v-model="transPsd2"></mt-field>
                <div class="transError" :class="{transErrorActive:transError}">*两次输入的密码不一致</div>
            </div>
            <mt-button size="large" class="transBtn" @click="transChange">确定修改</mt-button>
            <div class="transInfo">
                <p>交易密码说明：</p>
                <p>交易密码用于好友之间转账和在社区银行里面取金币的时候使用，若遗忘请联系管理员核对资料后修改。</p>
            </div>
        </div>
    </div>
</template>

<script>
console.log("PAGES_MYSYSTEM_TRANSPSD_VUE");
import root from '@/config/root.js'
    export default {
        data(){
            return{
                transPsd: '',
                transPsd1: '',
                transPsd2: '',
                transError: false,
                transError1: false,
                transPsdGet: this.$store.state.songInfo.user.trading,
                transPsdType: false,
            }
        },
        beforeCreate: function() {
            document.getElementsByTagName("body")[0].className="bgc-fff";
            console.log(this.$store.state.songInfo.user.trading);
            if(this.$store.state.songInfo.user.trading){
                this.transPsdType = false
            }
        },
        beforeDestroy: function() {
            document.body.removeAttribute("class","bgc-fff");
        },
        // watch: {
        //     transPsd1: function(){
        //         let test = new RegExp('^[0-9]{6}$');
        //         console.log(test.test(this.transPsd1))
        //     }
        // },
        methods: {
            transChange: function(){
                var transReg = new RegExp('^[0-9]{6}$');
                if(this.transPsdType){
                    if(this.transPsd1 == this.transPsd2){
                        if(transReg.test(this.transPsd1)){
                            this.$messagebox.alert("交易密码设置成功","提示")
                            this.transError = false;
                        }else{
                            this.$messagebox.alert("交易密码必须设置为6位数字！")
                        }
                    }else{
                        this.transError = true;
                    }
                }else{                   
                    if(parseInt(this.transPsd) == parseInt(this.transPsdGet)){
                        if(this.transPsd1 == this.transPsd2){
                            if(transReg.test(this.transPsd1)){
                                this.$axios.post(root.transPsd,{
                                    trading: this.transPsd1
                                }).then(response => {
                                    if(response.data.code) {
                                        this.$messagebox.alert("交易密码设置成功","提示");
                                        this.$store.state.songInfo.user.trading = this.transPsd1;
                                    }
                                })
                            }else{
                                this.$messagebox.alert('交易密码必须设置为6位数字！')
                            }
                        this.transError = false;
                        }else{
                            this.transError = true;
                        }
                        this.transError1 = false;
                    }else{
                        this.$messagebox.alert("密码输入错误，请重试！");
                        this.transError1 = true
                    }
                }
            }
        },
        
    }
</script>

<style scoped>
body{
    background-color: #fff;
}

.transBtn{
    margin-top: 20px;
    font-size: 14px;
    background-color: #57D6DD;
    color: #fff;
}
.transInfo{
    padding: 15px;
    margin-top: 50px;
}
.transInfo p{
    font-size: 12px;
    line-height: 20px;
    text-align: start;
    color: #7d7d7d;
}
.transInfo p:last-child{
    text-indent: 2em;
    color: #bdbdbd;
}
.transError{
    color: #E84120;
    font-size: 12px;
    opacity: 0;
    height: 16px;
}
.transErrorActive{
    opacity: 1;
}
.transPsd,.transPsd1,.transPsd2{
    background-color: #fff;
}
.transAttention{
    font-size: 12px;
    color: #E84120;
    text-align: left;
    padding: 10px;
}
</style>