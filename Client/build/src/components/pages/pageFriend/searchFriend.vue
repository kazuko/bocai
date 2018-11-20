<template>
    <div>
        <mt-header title="添加好友">
            <router-link to="/addFriend" slot="left">
                <mt-button icon="back">返回</mt-button>
            </router-link>
        </mt-header>
        <div class="pageContent">
            <div class="searchBox">
                <div class="searchTitle">查询结果</div>
                <div class="searchList">
                    <div class="userName">
                        用户昵称
                    </div>
                    <div class="userId">
                        ID
                    </div>
                    <div class="userMedal">
                        <i class="iconfont icon-xunzhang"></i>
                        <i class="iconfont icon-tubiaozhizuomoban"></i>
                        <i class="iconfont icon-jiangpaixunzhang"></i>
                    </div>
                    <div class="addBtn">
                        <mt-button type="primary">添加</mt-button>
                    </div>
                </div>
                <div class="searchList">
                    <div class="userName">
                        用户昵称
                    </div>
                    <div class="userId">
                        ID
                    </div>
                    <div class="userMedal">
                        <i class="iconfont icon-xunzhang"></i>
                        <i class="iconfont icon-tubiaozhizuomoban"></i>
                        <i class="iconfont icon-jiangpaixunzhang"></i>
                    </div>
                    <div class="addBtn">
                        <mt-button type="primary">添加</mt-button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
console.log("PAGES_PAGEFRIEND_SEARCHFRIEND_VUE");
    export default {
        
    }
</script>

<style scoped>
.searchBox{
    border: 1px solid #999;
    padding: 5px;
    position: relative;
    margin-top: 30px;
    padding-top: 30px;
}
.searchTitle{
    border: 1px solid #999;
    position: absolute;
    top: -15px;
    left: 35%;
    width: 30%;
    height: 30px;
    line-height: 30px;
    background-color: #fff;
}
.searchList{
    display: flex;
    flex-wrap: nowrap;
    justify-content: space-between;
    margin: 10px 0;
}
.userName,.userId,.userMedal{
    height: 30px;
    line-height: 30px;
    font-size: 12px;
    width: 23%;
    border: 1px solid #999;
}

.addBtn{
    width: 20%;
    height: 30px;
    line-height: 30px;
}
.addBtn button{
    max-width: 100%;
    height: 30px;
    font-size: 12px;
}
</style>