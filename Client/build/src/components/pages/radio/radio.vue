<template>
    <div>
        <mt-header title="社区广播">
            <router-link to="/" slot="left">
                <mt-button icon="back">返回</mt-button>
            </router-link>
        </mt-header>
        <div class="pageContent mt20 radio">
            <div class="title">
                <img src="./../../../assets/广播图标@2x.png">
                世界喇叭
            </div>
            <div class="radio-text">
                <textarea id="radioText" placeholder="输入内容，全世界能看到" rows="6"></textarea>
            </div>
            <div class="radio-btn">
                <div class="radioIntro" disabled>花费xx金币全世界都能看到</div>
                <mt-button class="radioBtn">发送</mt-button>
            </div>
            <div class="attention">
                <p>说明:</p>
                <p>1.不能发送违法语言；</p>
                <p>2.不能发送与本游戏无关的内容；</p>
                <p>3.世界喇叭持续xx主题，不会被顶掉</p>
            </div>
        </div>
    </div>
</template>

<script>
console.log("PAGES_RADIO_RADIO_VUE");
    export default {
        data(){
            return {
                userRadio: ''
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
.title{
    height: 30px;
    line-height: 30px;
    font-size: 16px;
    text-align: left;
    padding: 0 10px;
    display: flex;
    flex-wrap: nowrap;
}
.title img{
 width: 20px;
 height: 20px;
 margin-top: 5px;
 margin-right: 10px;
}
.radio-text{
    border: 1px solid #e7e7e7;
    padding: 10px;
}
#radioText{
    width: 100%;
    border:none;
}
#radioText::-webkit-input-placeholder {
	text-align: center;
  line-height: 90px;
}
/* Mozilla Firefox 4 to 18 */
#radioText:-moz-placeholder {
	text-align: center;
  line-height: 90px;
}
/* Mozilla Firefox 19+ */
#radioText::-moz-placeholder {
  text-align: center;
  line-height: 90px;
}
/* Internet Explorer 10+ */
#radioText:-ms-input-placeholder {
	text-align: center;
  line-height: 90px;
}
.radio-btn{
    display: flex;
    flex-wrap: nowrap;
    justify-content: space-between;
}
.radioIntro{
    background-color: #FFBA00;
    color: #fff;
    font-size: 14px;
    width: 67%;
    height: 40px;
    line-height: 40px;
    border-radius: 5px;
}
.radioBtn{
    background-color: #57d6dd;
    color: #fff;
    font-size: 14px;
    width: 30%;
}
.attention{
    text-align: left;
    padding: 10px;
    margin-top: 100px;
    font-size: 12px;
    color: #a4a4a4;
}
.attention p:first-child{
    font-weight: 600;
}
</style>