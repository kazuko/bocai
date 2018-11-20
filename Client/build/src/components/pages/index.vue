<template>
    <div>
        <mt-header title="大力水手用户页面">
            <router-link to="/login" slot="left">
                <mt-button icon="back">返回</mt-button>
            </router-link>
        </mt-header>
        <div class="homeContent">
            <div class="pageContent">
                <div class="userInfo">
                    <div class="userPic">
                        <img src="./../../assets/pic1.png">
                    </div>
                    <div class="userName">
                        {{this.$store.state.songInfo.user.username}}
                    </div>
                    <div class="userId">
                        ID:{{this.$store.state.songInfo.user.id}}
                    </div>
                    <div class="property">
                        <div class="userGold">
                            <div class="proVal">{{this.$store.state.songInfo.user.gold}}</div>
                            金币
                        </div>
                        |
                        <div class="userGold">
                            <div class="proVal">{{this.$store.state.songInfo.user.integral}}</div>
                            积分
                        </div>
                    </div>
                </div>
                <div class="userMedal">
                    勋章：
                    <img src="./../../assets/medal1.png">
                    <img src="./../../assets/medal1.png">
                    <img src="./../../assets/medal1.png">
                    <img src="./../../assets/medal1.png">
                    <img src="./../../assets/medal1.png">
                </div>
                <div class="userMenu">
                    <div class="user-menu-list">
                        <router-link class="user-menu-box" to="/friend">
                            <div class="menu-icon">
                                <img src="./../../assets/我的好友icon@2x.png">
                                <mt-badge type="error" size="small" class="badge friend-badge">{{this.$store.state.songInfo.messagenum.sum}}</mt-badge>
                            </div>
                            <div class="menu-label">
                                我的好友
                            </div>
                        </router-link>
                        <router-link class="user-menu-box" to="/bank">
                            <div class="menu-icon">
                                <img src="./../../assets/银行社区@2x.png">
                            </div>
                            <div class="menu-label">
                                我的银行
                            </div>
                        </router-link>
                    </div>
                    <div class="user-menu-list">
                        <router-link class="user-menu-box" to="/mySystem">
                            <div class="menu-icon">
                                <img src="./../../assets/我的设置@2x.png">
                            </div>
                            <div class="menu-label">
                                我的设置
                            </div>
                        </router-link>
                        <router-link class="user-menu-box" to="/">
                            <div class="menu-icon">
                                <img src="./../../assets/我的红包@2x.png">
                            </div>
                            <div class="menu-label">
                                我的红包
                            </div>
                        </router-link>
                    </div>
                    <div class="user-menu-list">
                        <router-link class="user-menu-box" to="/radio">
                            <div class="menu-icon">
                                <img src="./../../assets/社区广播@2x.png">
                            </div>
                            <div class="menu-label">
                                社区广播
                            </div>
                        </router-link>
                        <router-link class="user-menu-box" to="/">
                            <div class="menu-icon">
                                <img src="./../../assets/我的帖子@2x.png">
                            </div>
                            <div class="menu-label">
                                我的帖子
                            </div>
                        </router-link>
                    </div>
                </div>
                <div class="userMenu">
                    <div class="user-menu-list menu2-list">
                        <router-link class="user-menu2-box" to="/">
                            <div class="menu-icon">
                                <img src="./../../assets/充值@2x.png">
                            </div>
                            <div class="menu-label">
                                充值
                            </div>
                        </router-link>
                        <router-link class="user-menu2-box" to="/putForward">
                            <div class="menu-icon">
                                <img src="./../../assets/提现@2x.png">
                            </div>
                            <div class="menu-label">
                                提现
                            </div>
                        </router-link>
                        <router-link class="user-menu2-box" to="/medal">
                            <div class="menu-icon">
                                <img src="./../../assets/勋章@2x.png">
                            </div>
                            <div class="menu-label">
                                勋章
                            </div>
                        </router-link>
                    </div>
                </div>
                <div class="userMenu">
                    <div class="user-menu-list">
                        <router-link class="user-menu-box" to="/">
                            <div class="menu-icon">
                                <img src="./../../assets/安全离线icon.png">
                            </div>
                            <div class="menu-label">
                                安全离线
                            </div>
                        </router-link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
console.log("PAGES_INDEX_VUE");
    export default {
        data(){
            return{
                
            }
        },
        computed: {
            info: function(){
                return this.$store.state.songInfo
            }
        },
        methods: {
            offline: function(){
                
            }
        },
        
    }
</script>

<style scoped>
.homeContent{
    background: url("./../../assets/homebg.png") no-repeat;
    background-size: 100%; 
}
.userInfo{
    height: 150px;
    color: #fff;
    padding: 5px;
}
.userPic{
    width: 60px;
    height: 60px;
    border-radius: 50%;
    overflow: hidden;
    margin: 0 auto;
}
.userPic img{
    height: 100%;
    width: 100%;
}
.userId{
    font-size: 10px;
    transform: scale(0.9);
    color: rgba(255, 255, 255, 0.8);
    line-height: 20px;
}
.property{
    display: flex;
    flex-wrap: nowrap;
    justify-content: space-between;
    height: 50px;
    line-height: 50px;
    font-size: 20px;
    padding: 5px 0;
}
.userGold{
    width: 49%;
    line-height: 20px;
    font-size: 12px;
}
.proVal{
    font-size: 16px;
    font-weight: 500;
}
.userMedal{
    height: 50px;
    line-height: 50px;
    padding: 5px 20px;
    background-color: #fff;
    border-radius: 5px;
    text-align: left;
    font-size: 14px;
    display: flex;
    flex-wrap: nowrap;
}
.userMedal img{
    width: 30px;
    height: 30px;
    margin-top: 8px;
}
.userMenu{
    background-color: #fff;
    border-radius: 5px;
    margin-top: 10px;
    padding: 10px 0;
}
.user-menu-list{
    display: flex;
    flex-wrap: nowrap;
    justify-content: space-between;
    padding: 5px 20px;
    height: 50px;
}
.user-menu-box{
    width: 48%;
    display: flex;
    height: 100%;
    flex-wrap: nowrap;
}
.user-menu2-box{
    width: 33%;
    display: flex;
    height: 100%;
    flex-wrap: nowrap;
}
.menu-icon{
    padding: 10px;
    position: relative;
}
.menu-icon img{
    width: 30px;
    height: 30px;
}
.menu-label{
    font-size: 14px;
    height: 50px;
    line-height: 50px;
    padding-left: 5px;
}
.friend-badge{
    display: block;
    width: 16px;
    height: 16px;
    font-size: .75rem;
    padding: 1px 1px;
    line-height: normal;
    position: absolute;
    left: 30px;
    top: 5px;
}
.user-menu2-box .menu-icon{
    padding: 10px 5px;
}

</style>