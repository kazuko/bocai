<template>
  <!-- 参考 菠菜app6合彩 页面做的 头部导航栏 -->
  <header id='dent-head'
          class="flex justify-sb">

    <!-- 返回上一级 -->
    <span dentHoverclass="title-hoverclass"
          @click="$router.go(-1)"
          class="iconfont icon-left click-icon"></span>

    <!-- 中间部分 -->
    <div class="flex">
      <div class="tips">
        <slot name="tips"></slot>
      </div>
      <div @click="modalShow=!modalShow"
           dentHoverclass="title-hoverclass"
           class="dent-dropdown flex">
        <span>{{select | keyToCharacter}}</span>
        <span :class="modalShow?'modalShow':''"
              class="iconfont icon-arrow-down down-icon"></span>
      </div>
    </div>

    <!-- 右边图标 -->
    <div class="flex">
      <!-- rightOrder购物车按钮 -->
      <router-link v-if="rightOrder"
                   tag="span"
                   to="/6hecai/6hecai-waiForPay"
                   dentHoverclass="title-hoverclass"
                   style="font-size: 6vw;"
                   class="iconfont icon-storeCar1">
      </router-link>
      <!-- rightNormal普通的菜单按钮 -->
      <span v-if="rightNormal"
            @click.self="basicModal=!basicModal"
            dentHoverclass="title-hoverclass"
            class="iconfont icon-caidan click-icon">
      </span>
      <!-- rightNormal走势图的齿轮按钮 -->
      <span v-if="rightCharts"
            @click="onClickRightIcon"
            dentHoverclass="title-hoverclass"
            class="iconfont icon-chilun click-icon">
      </span>
    </div>
    <!-- 玩法切换 弹窗 -->
    <transition name="modalShow">
      <div @click="modalShow=!modalShow"
           v-show="modalShow"
           class="modal-mask">
        <div class="dent-head-modal flex">
          <label v-for="(value,key,index) in obj"
                 :key="index"
                 class="dent-head-modal-item">
            <input type="radio"
                   class="hidden"
                   :value="key"
                   v-model="select">
            <!-- iconfont类名 配合after伪元素生成 对勾 字体图标 -->
            <span dentHoverclass="span-hoverclass"
                  class="iconfont">{{key | keyToCharacter}}</span>
          </label>
        </div>
      </div>
    </transition>

    <!-- 右上角常用操作 弹窗 -->
    <transition name="modalShow">
      <div @click.self="basicModal=!basicModal"
           v-show="basicModal"
           class="modal-mask">
        <div class="basic-opr fontc-3">
          <div v-for="(item,index) in [{icon:'icon-stock',text:'走势图'},{icon:'icon-jiangbei',text:'近期开奖'},{icon:'icon-jilu',text:'购彩记录'},{icon:'icon-shuoming',text:'玩法说明'}]"
               class="basic-opr-item"
               @click="onClickOpr(index)"
               dentHoverclass="hoverclass">
            <span :class="item.icon"
                  class="fontc-6 iconfont"></span> {{item.text}}
          </div>
        </div>
      </div>
    </transition>

  </header>
</template>

<script>
import { keyToCharacter } from '@/js/6hecai-odds';

export default {
  data(){
        return{
          modalShow: false, //玩法切换弹窗显隐
          basicModal: false,  //常用操作弹窗显隐
          select: Object.keys(this.obj)[0] //默认选择第一项
        }
    },
    filters: {
      // 键值转成中文
      keyToCharacter: function (value) {
        let character = '';
        for( let key in keyToCharacter){
          if( key === value ) {
            character = keyToCharacter[key]
            break;
          }
        }
        if(!character)character = value;

        return character;
      }
    },
    props: {
      rightCharts: Boolean, //显示齿轮按钮
      rightNormal: Boolean, //显示常用操作按钮
      rightOrder:  Boolean, //显示订单按钮
      obj: Object
    },
    computed: {
    },
    methods: {
      //点击 走势图的齿轮按钮
      onClickRightIcon(){
        this.$emit('onClickRightIcon');
      },
      //点击常用操作弹窗
      onClickOpr(index){
        this.$emit('onClickOpr',index);
      }
    },
    watch: {
      // watch用户的更改，向事件派发事件
      select(val) {
        this.modalShow = false;
        this.$emit('onselect',val);
      },
    }
}
</script>

<style scoped>
/* 常用操作弹窗 */
.basic-opr {
  position: absolute;
  top: calc(10vw + 14px);
  right: 10px;
  background: #fff;
  will-change: transform;
  transform: scale(1);
  transform-origin: top right;
  transition: 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
.modalShow-enter .basic-opr,
.modalShow-leave-to .basic-opr {
  transform: scale(0.5);
}
.basic-opr::after {
  content: "";
  position: absolute;
  top: -16px;
  right: 8px;
  border: 8px solid transparent;
  border-bottom-color: #fff;
}
.basic-opr-item {
  width: 118px;
  height: 10vw;
  line-height: 10vw;
  font-size: 14px;
  text-align: left;
  text-indent: 1em;
}
.basic-opr .iconfont {
  font-size: 22px;
  vertical-align: middle;
}
.basic-opr-item + .basic-opr-item {
  border-top: 1px solid #e8e8e8;
}

#dent-head {
  position: fixed;
  z-index: 10;
  top: 0;
  left: 0;
  right: 0;
  height: 13.8vw;
  line-height: 13.8vw;
  background: #57d6dd;
  color: #fff;
}
.hidden {
  display: none;
}
.title-hoverclass {
  background: rgba(0, 0, 0, 0.1);
}
.span-hoverclass {
  background: #f7f9fa;
}
.modalShow-enter-active,
.modalShow-leave-active {
  will-change: opacity;
  transition: 0.35s;
}
.modalShow-enter,
.modalShow-leave-to {
  opacity: 0;
}
.modal-mask {
  bottom: -100vh;
}
/* 玩法 切换弹窗 */
.dent-head-modal {
  flex-wrap: wrap;
  position: absolute;
  z-index: 2;
  top: 13.8vw;
  left: 0;
  right: 0;
  padding-right: 4vw;
  padding-bottom: 4vw;
  background: #fff;
  transform: translate3d(0, 0, 0);
  transition: 0.35s;
  will-change: transform, opacity;
}
.modalShow-enter .dent-head-modal,
.modalShow-leave-to .dent-head-modal {
  transform: translate3d(0, 100%, 0);
  opacity: 0;
}
.dent-head-modal-item {
  box-sizing: border-box;
  margin-left: 4vw;
  margin-top: 4vw;
  width: calc(100% / 3 - 4vw);
  color: #545454;
  font-size: 14px;
  line-height: 2;
}
.dent-head-modal-item span {
  position: relative;
  display: block;
  border: 1px solid #ddd;
  font-size: 14px;
  border-radius: 4px;
}
input:checked + span {
  border: 1px solid #e64600;
}
input:checked + span::before {
  content: "";
  border: 4vw solid transparent;
  border-top-color: #e64600;
  position: absolute;
  bottom: -4vw;
  right: -4vw;
  transform: rotate(-45deg);
}
input:checked + span::after {
  content: "\E65A";
  position: absolute;
  right: 0;
  bottom: 0;
  color: #fff;
  font-size: 12px;
  line-height: 1;
}
.click-icon {
  padding: 0 3vw;
  font-size: 6.5vw;
}
.down-icon {
  display: block;
  margin-top: 2px;
  margin-left: 4px;
  font-size: 12px;
  line-height: 1;
  transition: 0.5s;
}
.modalShow.down-icon {
  transform: rotate(180deg);
}
.tips {
  writing-mode: vertical-rl;
  float: left;
  font-size: 12px;
  line-height: 2;
}
.dent-dropdown {
  padding: 0 2vw;
  height: 8vw;
  border: 1px solid #fff;
  line-height: 8vw;
  font-size: 14px;
  letter-spacing: 0.1em;
  border-radius: 2px;
}
</style>
