<template>
    <div>
        <mt-header title="我的设置">
            <router-link to="/" slot="left">
                <mt-button icon="back">返回</mt-button>
            </router-link>
        </mt-header>
        <div class="pageContent">
            <div class="medalList">
                <div class="medalBox">
                    <div class="medalImg">
                        <img src="../../../assets/勋章.png">
                    </div>
                    <div class="medalTitle">
                        新兵
                    </div>
                </div>
                <div class="medalBox">
                    <div class="medalImg">
                        <img src="../../../assets/勋章.png">
                    </div>
                    <div class="medalTitle">
                        新兵
                    </div>
                </div>
                <div class="medalBox">
                    <div class="medalImg">
                        <img src="../../../assets/勋章.png">
                    </div>
                    <div class="medalTitle">
                        新兵
                    </div>
                </div>
                <div class="medalBox">
                    <div class="medalImg">
                        <img src="../../../assets/勋章.png">
                    </div>
                    <div class="medalTitle">
                        新兵
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
console.log("PAGES_MEDAL_MEDAL_VUE");
    export default {
        
    }
</script>

<style scoped>
.medalList{
    display: flex;
    flex-wrap: nowrap;
    margin: 15px 0;
    margin-left: -4%;
}
.medalBox{
    width: 22%;
    margin-left: 4%;
}

.medalImg img{
    width: 100%;
}
.medalTitle{
    margin-top: 5px;
    padding: 5px;
    font-size: 14px;
    border: 1px solid #999;
}
</style>