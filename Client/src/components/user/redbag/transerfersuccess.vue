<template>
    <div class="transfer-success-tips-page">
        <div class="success-flag-box">
            <img src="static/打勾.png" alt="">
        </div>
        <div class="success-tips-box">转账成功</div>
        <div class="transfer-jine-info">
            <span>￥</span>
            <span>{{this.$route.query.gold}}</span>
        </div>
        <div class="recived-user-info-box">“{{this.$route.query.recive_user}}”收</div>
        <router-link to="/chat" class="success-btn" >完成</router-link>
    </div>
</template>
<script>
export default {
    data() {
        return {

        }
    },
    mounted: function(){
        this.$parent.tabbarShow = false;
    }
}
</script>
<style>
.success-flag-box{
    margin-top: 15vw !important;
    background:#57D6DD;
    width: 18.5vw;
    height: 18vw;
    border-radius: 18vw;
    margin: auto;
    display: flex;
    align-items: center;
    justify-content: center;
    justify-items: center;
    overflow: hidden;
}
.success-flag-box img{
    max-width: 60%;
    max-height: 60%;
}
.success-tips-box{
    font-size: 5.5vw;
    height: 12vw;
    line-height: 12vw;
}
.transfer-jine-info{
    font-weight: bold;
    display: flex;
    justify-content: center;
    align-items: flex-start;
}
.transfer-jine-info span:nth-child(1){
    font-size: 6vw;
}
.transfer-jine-info span:nth-child(2){
    font-size: 9vw;
    margin-left: 1.5vw;
}
.recived-user-info-box{
    font-size: 6vw;
    margin-top: 20vw;
}
.success-btn{
    display: block;
    width: 60vw;
    height: 13vw;
    line-height: 13vw;
    margin: 20vw auto 0px;
    font-size: 6vw;
    color: #81E0E5;
    border: .5vw solid #81E0E5;
    background: #ffffff;
    border-radius: 2vw;
    font-weight: bold;
    letter-spacing: 3px;
    font-family: '宋体';
    outline: none;
}
</style>

