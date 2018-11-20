<template>
  <transition name="fadeIn">
    <div v-show="value"
         @click.self="closeModal"
         class="modal-mask">
      <div class="modal example-modal">
        <div class="modal-title">
          玩法规则
        </div>
        <div class="modal-body">
          <div class="example-tips">
            <p class="example-title">
              <span class="iconfont icon-idea font-theme"></span>玩法提示</p>
            <p v-if="currentRule"
               class="example-content">
              {{currentRule.tips}}
            </p>
          </div>
          <div class="example-tips">
            <p class="example-title">
              <span class="iconfont icon-jihua font-theme"></span>中奖说明</p>
            <p v-if="currentRule"
               class="example-content">
              {{currentRule.rule}}
            </p>
          </div>
          <div class="example-tips">
            <p class="example-title">
              <span class="iconfont icon-case font-theme"></span>范例</p>
            <p v-if="currentRule"
               class="example-content">
              {{currentRule.case}}
            </p>
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>

<script>
export default {
  props:{
    /* 数据 */
    ruleKey:String, //玩法规则的名字
    currentOdds:String, //当前玩法
    /* 状态 */
    value:Boolean
  },
  data(){
    return {
      ruleObj:{}
    }
  },
  computed:{
    currentRule(){
      let {currentOdds, ruleObj} = this;
      return ruleObj[currentOdds];
    }
  },
  methods:{
    closeModal(){
      this.$emit('input',false)
    }
  },
  mounted(){
    // 第一次显示规则弹窗的时候 读取规则缓存，之后注销watch
    let unwatch = this.$watch(
      function(){
        return this.value;
      },
      function(newVal, oldVal){
        if (newVal === true){
          this.ruleObj = this.$getLocalCache(this.ruleKey);
          unwatch();
        }
      }
    )
  }
}
</script>

<style scoped>
/* 玩法规则弹窗 */
.fadeIn-enter-active,
.fadeIn-leave-active {
  will-change: opacity;
  transition: opacity 500ms;
}
.fadeIn-enter,
.fadeIn-leave-to {
  opacity: 0;
}
.modal {
  transition: 0.25s;
}
.fadeIn-enter .modal {
  transform: translate3d(100%, 0, 0);
}
.fadeIn-leave-active .modal {
  transform: translate3d(100%, 0, 0);
}
.example-modal .modal-title {
  background: #e64600;
  color: #fff;
  line-height: 10.6vw;
}
.example-modal .modal-body {
  padding: 2.6vw;
  padding-bottom: 8vw;
}
.example-title {
  line-height: 2;
  font-weight: bold;
}
.example-title .iconfont {
  margin-right: 1.3vw;
}
.example-content {
  padding: 0 1.5em 0;
  font-size: 3.46vw;
}
</style>

