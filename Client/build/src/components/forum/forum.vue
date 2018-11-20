<template>
    <div>
        <mt-header title="社区">
            <router-link to="/" slot="left">
                <mt-button icon="back">返回</mt-button>
            </router-link>
        </mt-header>
        <div class="pageContent">
            <!-- <router-link class="box" to="/theme">
                <div class="list">
                    <div class="icon">
                        <img src="./../../assets/普通@2x.png">
                    </div>
                    <div class="text">
                        <div class="title"><p>主题主题...</p></div>
                        <div class="tips">帖数：261万</div>
                    </div>
                    <div class="tag">普通区</div>
                </div>
                <div class="display">
                    <div class="new"><p>士大夫撒发顺丰大发送到士大夫撒发顺丰大发送到</p></div>
                    <div class="time">6分钟前</div>
                </div>
            </router-link> -->
            <div class="box" v-for="(item,index) in forum" :key="index" @click="toTheme(item,index)">
                <div class="list">
                    <div class="icon">
                        <img :src="'http://192.168.0.100'+item.img">
                    </div>
                    <div class="text">
                        <div class="title"><p>{{item.title}}</p></div>
                        <div class="tips">帖数：{{item.COUNT}}</div>
                    </div>
                    <div class="tag">{{item.name}}</div>
                </div>
                <div class="display">
                    <div class="new"><p>{{item.post_title}}</p></div>
                    <div class="time">{{item.ADDTIME}}分钟前</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
console.log("FORUM_FORUM_VUE");
import root from '@/config/root.js'
    export default {
        data(){
            return {
                forum: ''
            }
        },
        created: function(){
            this.$axios.post(root.forum).then(response => {
                console.log(response);
                this.forum = response.data.zone
            })
            // this.$axios.post('/mock/forum').then(response => {
            //     this.forum = response.data.forum;
            // })
        },
        methods: {
            toTheme: function(item,index){
                this.$axios.post(root.theme,{
                    user_id: 1,
                    id: item.id
                }).then(response => {
                    console.log(response)
                    this.$router.push('/theme');
                    // this.$store.commit('getTheme', response.data)
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
.box{
    display: block;
    border-bottom: 1px solid #eaeaea;
    padding: 10px 0;
}
.list{
    display: flex;
    justify-content: space-between;
    padding: 5px 0;
    
}
.icon{
    padding: 10px;
    border: 1px solid #eaeaea;
}
.icon img{
    width: 40px;
    height: 40px;
}
.text{
    width: 100%;
    padding: 10px;
    line-height: 20px;
    text-align: left;
}
.title{
    font-size: 14px;
    color: #000;
}
.tips{
    font-size: 12px;
    color: #999;
}
.tag{
    min-width: 80px;
    height: 30px;
    line-height: 30px;
    color: #F8C02D;
    border: 1px solid #F8C02D;
    border-radius: 5px;
    margin-top: 15px;
    font-size: 14px;
    overflow: hidden;
}
.display{
    padding: 0 10px;
    display: flex;
    justify-content: space-between;
    font-size: 12px;
}
.new{
    padding-left: 10px;
     
}
p{
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
}
.time{
    color: #999;
    min-width: 60px;
}
</style>