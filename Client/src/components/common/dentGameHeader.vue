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
      <div @click="modalShow=true"
           dentHoverclass="title-hoverclass"
           class="dent-dropdown flex">
        <span v-if="subType==='normal'">{{mixin_KeyToCharacter(select)}}</span>
        <span v-if="subType==='multipleSub'">{{selectConcatCharacter(selectConcat)}}</span>
        <span :class="modalShow?'modalShow':''"
              class="iconfont icon-arrow-down down-icon"></span>
      </div>
    </div>

    <!-- 右边图标 -->
    <div class="flex">
      <!-- rightOrder购物车按钮 -->
      <span v-if="rightOrder"
            @click="$emit('onClickRightOrder')"
            dentHoverclass="title-hoverclass"
            style="font-size: 6vw;"
            class="iconfont icon-storeCar1">
      </span>
      <!-- rightNormal普通的菜单按钮 -->
      <span v-if="rightNormal"
            @click.self="basicModal=!basicModal"
            dentHoverclass="title-hoverclass"
            class="iconfont icon-caidan click-icon">
      </span>
      <!-- rightNormal走势图的齿轮按钮 -->
      <span v-if="rightCharts"
            @click="$emit('onClickRightIcon')"
            dentHoverclass="title-hoverclass"
            class="iconfont icon-chilun click-icon">
      </span>
    </div>

    <!-- 普通玩法切换 弹窗 （参考6合彩页面的切换菜单）-->
    <template v-if="obj && subType==='normal'">
      <transition name="modalShow">
        <div @click="modalShow=false"
             v-show="modalShow"
             class="modal-mask">
          <div class="dent-head-modal dent-head-modal-normal flex">
            <label v-for="(value,key,index) in obj"
                   @click="modalShow=false"
                   :key="index"
                   class="dent-head-modal-item">
              <input type="radio"
                     class="hidden"
                     :value="key"
                     v-model="select">
              <!-- iconfont类名 配合after伪元素生成 对勾 字体图标 -->
              <span dentHoverclass="span-hoverclass"
                    class="duigou iconfont">{{mixin_KeyToCharacter(key)}}</span>
            </label>
          </div>
        </div>
      </transition>
    </template>
    <!-- 有多个2级的玩法切换 弹窗 （参考江苏快三页面的切换菜单）-->
    <template v-if="obj && subType==='multipleSub'">
      <transition name="modalShow">
        <div v-show="modalShow"
             @click.self="modalShow=false"
             class="modal-mask">
          <div @click.self="modalShow=false"
               class="dent-head-modal sub-switch-modal flex flow-col">
            <!-- 总玩法 -->
            <div class="switch-wrap flex">
              <label v-for="(value,key,index) in obj"
                     :key="index"
                     class="dent-head-modal-item multipleSub-item bgc-fff">
                <input type="radio"
                       class="hidden"
                       :value="key"
                       v-model="select">
                <!-- iconfont类名 配合after伪元素生成 对勾 字体图标 -->
                <span dentHoverclass="span-hoverclass"
                      class="full iconfont">{{mixin_KeyToCharacter(key)}}</span>
              </label>
            </div>
            <!-- 子玩法 -->
            <div class="sub-switch-wrap">
              <div v-for="(value,key) in obj"
                   class="srcollshadow"
                   v-show="select === key">
                <template v-for="(subvalue,subkey) in value">
                  <p class="sub-switch-title fontc-3">{{mixin_KeyToCharacter(subkey)}}</p>
                  <div class="flex sub-switch">
                    <label v-for="(objvalue,objkey) in subvalue"
                           @click="modalShow = false;"
                           class="dent-head-modal-item">
                      <input type="radio"
                             :value="subkey+'-'+objkey"
                             v-model="finallySelect"
                             class="hidden">
                      <!-- iconfont类名 配合after伪元素生成 对勾 字体图标 -->
                      <span dentHoverclass="span-hoverclass"
                            class="border iconfont">{{mixin_KeyToCharacter(objkey)}}</span>
                    </label>
                  </div>
                </template>
              </div>
            </div>
          </div>
        </div>
      </transition>
    </template>

    <!-- 右上角常用操作 弹窗 -->
    <transition name="modalShow">
      <div @click.self="basicModal=false"
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
import { filterMethods } from '@/mixins/mixins'

export default {
  mixins: [ filterMethods ],
  props: {
    rightCharts: Boolean, //显示齿轮按钮
    rightNormal: Boolean, //显示常用操作按钮
    rightOrder:  Boolean, //显示订单按钮
    obj: Object,  //需要渲染的对象
    /*subType 子玩法的类型
     *subType 值为 normal时，渲染成单个子玩法切换的 参考 6合彩页面
     *subType 值为 multipleSub时，渲染成有多个子玩法的 ，参考 江苏快三页面
     */
    subType: String,
    gameKey:String //游戏的名字
  },
  data(){
      return{
        modalShow: false, //玩法切换弹窗显隐
        basicModal: false,  //常用操作弹窗显隐
        select: '', //默认选择第一项
        finallySelect:'', //子玩法第3级,
      }
  },
  computed: {
    selectConcat() {
      let {select, finallySelect} = this;
      return `${select}-${finallySelect}`;
    }
  },
  methods: {
    //自动选择子玩法的第一项
    autoSelectSub(val){
      //TODO 不传入times
      function getFirstKey(obj,time){
        if(time) time--;
        let value = Object.values( obj )[0];
        let ifObject = value instanceof Object;
        if(ifObject && time){
          return getFirstKey(value,time);
        }else{
          return Object.keys( obj )[0];
        }
      }

      let currentObj  = this.obj[val];
      this.finallySelect = getFirstKey(currentObj,1)+'-'+getFirstKey(currentObj,2);
    },

    //点击常用操作弹窗
    onClickOpr(index){
      this.basicModal = !this.basicModal
      this.$emit('onClickOpr',index);
    },

    selectConcatCharacter(val){
      let [ type, leixin, name ] = val.split('-');
      return this.mixin_KeyToCharacter(type) + '-' + this.mixin_KeyToCharacter(name);
    }
  },
  watch: {
    //用户的切换了总玩法，向外部派发事件
    select(val) {
      this.autoSelectSub(val);
      this.$emit('onselect',val);
    },
    //用户的切换了子玩法，向外部派发事件
    selectConcat(val){
      this.$emit('onSubSelect',val);
    },
    //外部传递渲染对象进来，默认选择第一个key值 展示出来
    obj:{
      handler(){
        if(!this.obj)return;
        //外部传递渲染对象进来，默认选择第一个key值 展示出来
        let defaultSelect = Object.keys(this.obj)[0];
        let checkRes = this.obj instanceof Object || this.obj instanceof Array;

        if(checkRes && defaultSelect !== undefined){
          this.select = defaultSelect;
        }
      },
      immediate:true
    }
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
  width: 31.4vw;
  height: 10vw;
  line-height: 10vw;
  font-size: 3.74vw;
  text-align: left;
  text-indent: 1em;
}
.basic-opr .iconfont {
  font-size: 5.86vw;
  vertical-align: middle;
}
.basic-opr-item + .basic-opr-item {
  border-top: 1px solid #e8e8e8;
}

#dent-head {
  position: absolute;
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
.hoverclass {
  background: rgba(230, 43, 0, 0.1);
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
/* 玩法 切换弹窗 */
.dent-head-modal {
  align-items: stretch;
  align-content: flex-start;
  will-change: transform, opacity;
  position: absolute;
  z-index: 2;
  top: 13.8vw;
  left: 0;
  right: 0;
  bottom: 13.8vw;
  padding-right: 4vw;
  padding-bottom: 4vw;
  transform: translate3d(0, 0, 0);
  transition: 0.35s;
}
.dent-head-modal-normal {
  bottom: auto;
  background: #fff;
  flex-wrap: wrap;
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
  font-size: 3.74vw;
  line-height: 2;
}
.multipleSub-item {
  width: calc(100% / 4 - 4vw);
}
.dent-head-modal-item span {
  position: relative;
  display: block;
  border: 1px solid #ddd;
  font-size: 3.74vw;
  border-radius: 4px;
}
input:checked + .duigou {
  border: 1px solid #e64600;
}
input:checked + .duigou::before {
  content: "";
  border: 4vw solid transparent;
  border-top-color: #e64600;
  position: absolute;
  bottom: -4vw;
  right: -4vw;
  transform: rotate(-45deg);
}
input:checked + .duigou::after {
  content: "\E65A";
  position: absolute;
  right: 0;
  bottom: 0;
  color: #fff;
  font-size: 3.2vw;
  line-height: 1;
}
input:checked + .full {
  background: #e64600;
  border-color: #e64600;
  color: #fff;
}
input:checked + .border {
  border-color: currentColor;
  color: #e64600;
}
.click-icon {
  padding: 0 3vw;
  font-size: 6.5vw;
}
.down-icon {
  display: block;
  margin-top: 2px;
  margin-left: 4px;
  font-size: 3.2vw;
  line-height: 1;
  transition: 0.5s;
}
.modalShow.down-icon {
  transform: rotate(180deg);
}
.tips {
  writing-mode: vertical-rl;
  float: left;
  font-size: 3.2vw;
  line-height: 2;
}
.dent-dropdown {
  padding: 0 2vw;
  height: 8vw;
  border: 1px solid #fff;
  line-height: 8vw;
  font-size: 3.74vw;
  letter-spacing: 0.1em;
  border-radius: 2px;
}
/* 玩法切换弹窗 */
.sub-switch-modal {
  padding: 0;
}
.switch-wrap,
.sub-switch {
  flex-wrap: wrap;
  padding-right: 4vw;
  padding-bottom: 4vw;
}
.switch-wrap {
  flex: none;
  background: #f7f7f7;
}
.sub-switch-wrap {
  background: #fff;
  overflow: auto;

  box-shadow: inset 0 -2.133vw 2.133vw -2.133vw rgba(114, 114, 114, 0.4),
    inset 0 2.133vw 2.133vw -2.133vw rgba(114, 114, 114, 0.4);
}
.srcollshadow {
  background: linear-gradient(white 5px, hsla(0, 0%, 100%, 0)) 0 0 / 100% 10px,
    linear-gradient(to top, white 5px, hsla(0, 0%, 100%, 0)) bottom / 100% 10px;
  background-repeat: no-repeat;
}
.sub-switch {
  margin-top: -4vw;
}
.sub-switch-title {
  text-indent: 4vw;
  line-height: 10vw;
  text-align: left;
  font-weight: bold;
}
</style>
