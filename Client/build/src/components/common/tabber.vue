<template>
    <div>
        <div class="tabbar">
            <router-link to="/" class="tabbar-box">
                <div class="tabbar-icon">
                    <img src="./../../assets/iconhome.png">
                </div>
                <div class="tabbar-label">
                    主页
                </div>
            </router-link>
            <router-link to="/friend" class="tabbar-box">
                <div class="tabbar-icon">
                    <img src="./../../assets/iconforum.png">
                </div>
                <div class="tabbar-label">
                    好友
                </div>
            </router-link>
            <router-link to="/" class="tabbar-box">
                <div class="tabbar-icon">
                    <img src="./../../assets/iconchat.png">
                </div>
                <div class="tabbar-label">
                    社区
                </div>
            </router-link>
            <router-link to="/mySystem" class="tabbar-box">
                <div class="tabbar-icon">
                    <img src="./../../assets/iconmy.png">
                </div>
                <div class="tabbar-label">
                    我的
                </div>
            </router-link>
            
        </div>
    </div>
</template>

<script>
console.log("COMMON_TABBER_VUE");
    export default {
        name: 'tabber',
        data(){
            return {
                
            }
        }
    }
</script>

<style scoped>
.tabbar{
    height: 55px;
    position: fixed;
    right: 0;
    bottom: 0;
    left: 0;
    z-index: 1;
    background-color: #fff;
    display: flex;
    flex-wrap: nowrap;
    border-top: 2px solid #e7e7e7;
}
.tabbar-box{
    width: 25%;
    height: 100%;
    padding: 7px 0;
    display: block;
}
.tabbar-icon{
    width: 24px;
    height: 24px;
    margin: 0 auto 5px;
}
.tabbar-icon img{
    width: 100%;
    height: 100%;
}
.tabbar-label{
    font-size: 12px;
    line-height: 1;
    color: #64D7E3;
}
</style>