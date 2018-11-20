<template>
  <div :class="type === 'hq'?'hq-switch':''"
       class="switch flex">
    <!-- 合肖特殊处理 -->
    <template v-if="type === 'hq'">
      <label v-for="(tabvalue,tabkey,tabindex) in tabObj"
             @click="onCLickHq(tabvalue,tabkey,tabindex)"
             :key="tabindex"
             class="hq-switch-item switch-item flex flow-col justify-ct">
        <input type="radio"
               :value="tabvalue.zhl"
               v-model="value"
               class="hidden">
        <span dentHoverclass="hqhoverclass"
              class="hq-switch-span">{{tabkey | keyToCharacter}}</span>
      </label>
    </template>
    <template v-else-if="!needShowOdds">
      <div v-for="(tabvalue,tabkey,tabindex) in tabObj"
           @click="onSwitch(tabvalue,tabkey,tabindex)"
           :key="tabindex"
           :class="{'switch-item-flex': flex,'font-theme': value === tabindex}"
           dentHoverclass="hoverclass"
           class="switch-item flex flow-col justify-ct">
        <span class="fz-16">{{tabkey | keyToCharacter}}</span>
      </div>
    </template>
    <template v-else>
      <div v-for="(value,key,index) in tabObj"
           @click="onSwitch(value,key,index)"
           :key="index"
           dentHoverclass="hoverclass"
           class="switch-item flex flow-col justify-ct">
        <span class="fz-16">{{key | keyToCharacter}}</span>
        <span v-if="value instanceof Object"
              class="font-theme fz-12">
          <template v-for="(subvalue) in value">
            {{subvalue}}
          </template>
        </span>
        <span v-else
              class="font-theme fz-12">{{value}}</span>
      </div>
    </template>

    <!-- 滑动边框 -->
    <!-- 合肖特殊处理 -->
    <div v-if="type !== 'hq'"
         class="switch-line"
         v-bind:style="style"></div>

  </div>
</template>

<script>
import { keyToCharacter } from '@/js/6hecai-odds';

export default {
  props: {
    tabObj: Object, ////需要渲染的对象或者数组
    value:[String,Number], //value 用来接受v-model传进来的值
    type: String, //组件的 type ，用来切换不同的子玩法
    flex: Boolean //emmm... 下注页面 的样式和 购彩记录 的页面有点不一样，flex为true的时候 ，是购彩记录页
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
      !character && (character = value)
      return character;
    }
  },

  data () {
    return {
      style: {
        left:0 ,
        width:'21.6vw'
      },  //内联样式
      needShowOdds:false, //是否需要显示赔率
    }
  },
  methods:{
    onCLickHq(value,key,index){
      this.$emit('input',value.zhl);
    },
    onSwitch (value,key,index){
      this.$emit('input', index);
      this.setLineAnimate(index);
    },
    setLineAnimate(index) {
      if(this.flex){
        this.style.left = index / Object.keys(this.tabObj).length * 100 + '%';
      }else {
        //下注页面
        this.style.left = index*21.6 + 'vw'
      }
    },
  },
  mounted(){
      if(this.flex)this.style.width = 1 / Object.keys(this.tabObj).length * 100 + '%';
  },
  watch: {
    // watch currentActive，改变下划线的位置
    value(newVal) {
      this.setLineAnimate(newVal);
    },
    type:{
      handler(type) {
        let needShowOdds = ['zxbzh','lma','zhy'];

        this.needShowOdds = needShowOdds.includes(type);
      },
      immediate: true
    }
  }
}
</script>

<style scoped>
.hoverclass {
  background: rgba(230, 70, 0, 0.1);
}
.hqhoverclass {
  background: #f7f9fa;
}
/* 子玩法切换 */
.fz-12 {
  font-size: 12px;
}
.fz-16 {
  font-size: 16px;
}
.switch {
  position: relative;
  z-index: 1;
  width: 100%;
  line-height: 1.6;
  box-shadow: 0 2px 2px rgba(144, 144, 144, 0.5);
  overflow: auto;
}
.hq-switch {
  flex-wrap: wrap;
  padding-bottom: 3vw;
  border-bottom: 1px solid #e4e6e8;
  box-shadow: none;
}
.switch-item {
  flex: none;
  padding: 2vw 0;
  width: 21.6vw;
}
.switch-item-flex {
  flex: 1;
}
.hq-switch-item {
  padding: 3vw 0 0;
  width: 25%;
}
.hq-switch-span {
  border: 1px solid #e1e1e1;
  width: 16vw;
  border-radius: 4px;
  font-size: 14px;
}
input:checked + .hq-switch-span {
  border-color: currentColor;
  color: #e64600;
}
.switch-line {
  position: absolute;
  left: 0;
  bottom: 0;
  height: 2px;
  background: #e64600;
  transition: 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
</style>
