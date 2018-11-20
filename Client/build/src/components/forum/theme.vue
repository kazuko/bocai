<template>
    <div>
        <mt-header title="主题区">
            <router-link to="/forum" slot="left">
                <mt-button icon="back">返回</mt-button>
            </router-link>
        </mt-header>
        <div class="pageContent">
            <!-- <div v-for="(item,index) in this.$store.state.theme.post" :key="index">
                <div class="list">
                    <div class="title">
                        <div class="head">
                            <img :src="'http://192.168.0.100'+item.head[0]">
                        </div>
                        <div class="userInfo">
                            <div class="nickname">{{item.user_name}}</div>
                            <div class="info">{{item.addtime}}</div>
                        </div>
                        <div class="icon">
                            <img src="./../../assets/箭头 右.png">
                        </div>
                    </div>
                    <div class="text-title">
                        <p>{{item.title}}</p>
                    </div>
                    <div class="text">
                        <p>{{item.content}}</p>
                    </div>
                    <div class="image" v-show="item.isimg">
                        <img src="./../../assets/图层11@2x.png">
                        <img src="./../../assets/图层11@2x.png">
                        <img src="./../../assets/图层11@2x.png">
                    </div>
                    <div class="bottom">
                        <div class="star like" @click="lile(item,index)"><i class="iconfont icon-xihuan"></i> {{item.like}}</div>
                        <div class="star comment"><i class="iconfont icon-pinglun"></i> {{item.commentcunt}}</div>
                        <div class="star see"><i class="iconfont icon-yanjing"></i> {{item.visitor}}</div>
                    </div>
                </div>
            </div> -->
            <div class="btn" @click="post">
                <div class="btn-img">
                    <img src="./../../assets/发帖@2x.png">
                </div>
            </div>
        </div>
    </div>
</template>

<script>
console.log("FORUM_THEME_VUE");
import root from '@/config/root'
    export default {
        data(){
            return{
                likeStatus: true
            }
        },
        methods: {
            post: function(){
                this.$axios.get(root.post,{
                    params: {
                        // zone_id: this.$store.state.theme.zone.id,
                        zone_id: 1,
                        user_id: 1
                    }      
                }).then(response => {
                    console.log(response)
                    if(!response.data.code){
                        this.$messagebox.alert(response.data.result)
                    }else{
                        this.$router.push('/post')
                    }
                })
            },
            like: function(item,index){
                this.$axios.post(root.forumLike,{
                    post_id: item.id,
                    user_id: 1
                }).then(response => {
                    if(this.likeStatus){
                        item.like++
                    }else{
                        item.like--
                    }
                })
            }
        },
        beforeCreate: function() {
            document.getElementsByTagName("body")[0].className="bgc-fff";
            
        },
        beforeDestroy: function() {
            document.body.removeAttribute("class","bgc-fff");
        },
    }
</script>

<style scoped>
a{
    display: block;
}
.list{
    border-bottom: 1px solid #e9e9e9;
}
.title{
    padding: 10px 0;
    display: flex;
    justify-content: space-between;
}
.head{
    border-radius: 50%;
    min-width: 50px;
    width: 50px;
    height: 50px;
    overflow: hidden;
}
.head img{
    width: 100%;
    height: 100%;
}
.userInfo{
    width: 100%;
    line-height: 25px;
    text-align: left;
    padding: 0 10px;
}
.icon{
    min-width: 15px;
}
.icon img{
    width: 15px;
    margin-top: 17px;
}
.nickname{
    font-size: 14px;
}
.info{
    font-size: 12px;
    color: #a5a5a5;
}
.text-title{
    font-size: 16px;
    margin-bottom: 5px;
    text-align: left;
}
.text-title p{
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
}
.text{
    font-size: 12px;
    line-height: 20px;
    text-align: left;
     
}
.text p{
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}
.image{
    padding: 5px 0;
    display: flex;
    flex-wrap: nowrap;
    margin-left: -3.33%;
}
.image img{
    display: block;
    width: 30%;
    max-height: 100px;
    margin-left: 3.33%;
}
.bottom{
    padding: 10px 0;
    display: flex;
    justify-content: space-between;
    font-size: 12px;
    color: #afafaf;
    position: relative;
}
.bottom .iconfont{
    font-size: 18px;
    position: relative;
    top: 2px;
}
.btn{
    position: fixed;
    transform-origin: center;
    bottom: 57px;
    left: 50%;
    margin-left: -35px;
}
.btn-img{
    height: 70px;
    width: 70px;
    border-radius: 50%;
}
.btn-img img{
    width: 100%;
    height: 100%;
}
</style>