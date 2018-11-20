<template>
    <div>
        <mt-header title="游戏充值" style="background:#57D6DD;">
            <!-- <router-link :to="goto" slot="left"> -->
                <mt-button icon="back" slot="left" @click="goto()">返回</mt-button>
            <!-- </router-link> -->
        </mt-header>
    </div>
</template>
<script>
export default {
  created: function() {
      this.$parent.tabbarShow = false;
  },
  methods:{
      goto:function(){
          console.log("sdfsdfsdf");
          if(this.$route.query.fromForum){
              this.$router.push('/forum');
          }else{
              this.$router.push('/user');
          }
      }
  }
};
</script>
<style scoped>
</style>


