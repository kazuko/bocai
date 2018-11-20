<template>
    <div>
        <mt-header title="发布帖子">
            <router-link to="/theme" slot="left">
                <mt-button icon="back">返回</mt-button>
            </router-link>
            <mt-button slot="right" @click="post">发布</mt-button>
        </mt-header>
        <div class="pageContent">
            <div class="post">
                <div class="title">
                    <mt-field placeholder="请输入主题..." v-model="title"></mt-field>
                </div>
                <div class="postContent">
                    <div class="extend">
                        <img src="./../../assets/表情@2x.png">
                        <img src="./../../assets/截图@2x.png">
                    </div>
                    <div class="text">
                        <textarea id="postText" placeholder="请输入内容" rows="10" v-model="postText"></textarea>
                    </div>
                    <div class="image">
                        <img class="preview" src="./../../assets/添加相册@2x.png">
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
console.log("FORUM_POST_VUE");
import root from '@/config/root'
    export default {
        data(){
            return{
                title: '',
                postText: ''
            }
        },
        mounted: function(){
            for (let index = 0; index < document.getElementsByClassName('preview').length; index++) {
                const element = document.getElementsByClassName('preview')[index];
                element.style.maxHeight = element.offsetWidth + 'px';
            }
        },
        methods: {
            post: function(){
                this.$axios.post(root.post,{
                    title: this.title,
                    user_id: 1,
                    content: this.postText,
                    zone_id: 1
                }).then(response => {
                    console.log(response)
                    this.$messagebox.alert(response.data.result)
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
.post{
    padding: 10px;
}
.title{
    border: 1px solid #d1d1d1;
    margin: 10px 0;
}
.extend{
    padding: 10px;
    display: flex;
    flex-wrap: nowrap;
    border: 1px solid #d1d1d1;
}
.extend img{
    width: 24px;
    height: 24px;
    margin-right: 20px;
}
.text{
    border: 1px solid #d1d1d1;
    border-top: none;
}
.image{
    display: flex;
    flex-wrap: nowrap;
    padding: 10px 0;
    margin-left: -3%;
}
.image img{
    width: 30%;
    margin-left: 3%;
}
.text{
    padding: 10px;
}
#postText{
    width: 100%;
    border:none;
}
#postText:focus{
    border: none;
    outline: none;
}
#postText::-webkit-input-placeholder {
	text-align: center;
  line-height: 154px;
}
/* Mozilla Firefox 4 to 18 */
#postText:-moz-placeholder {
	text-align: center;
  line-height: 154px;
}
/* Mozilla Firefox 19+ */
#postText::-moz-placeholder {
  text-align: center;
  line-height: 154px;
}
/* Internet Explorer 10+ */
#postText:-ms-input-placeholder {
	text-align: center;
  line-height: 154px;
}
</style>