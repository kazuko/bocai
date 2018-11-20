<template>
  <div class="balls flex">
    <div class="balls-main flex">
      <template v-if="newArr">
        <div v-for="n in newArr"
             class="flex flow-col">
          <span :class="getColorFunc(n.bs)"
                class="balls-item">{{n.shuZ.toString().length === 1?'0'+n.shuZ:n.shuZ}}</span>
          <span v-if="showAnimals"
                class="balls-text"> {{n.shX | keyToCharacter}} </span>
        </div>
      </template>
    </div>
    <span class="fontc-9 balls-link">+</span>
    <div class="balls-spec flex">
      <div class="flex flow-col">
        <template v-if="newArrShift">
          <span :class="getColorFunc(newArrShift.bs)"
                class="balls-spec-item">{{newArrShift.shuZ.toString().length === 1?'0'+newArrShift.shuZ:newArrShift.shuZ}}</span>
          <span v-if="showAnimals"
                class="balls-text">{{newArrShift.shX | keyToCharacter}}</span>
        </template>
      </div>
    </div>
  </div>
</template>

<script>
import { six as keyToCharacter } from '@/js/keyToCharacter';

export default {
  props: {
    openNumObj:Object, //要渲染的对象
    showAnimals: Boolean, //是否需要显示生肖
    // haoma:Object  //生肖的号码
  },
  data(){
    return{
      openArr:[]
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
    },
  },
  computed:{
    newArr(){
      let newArr = [...this.openArr];
      newArr.pop();
      return newArr;
    },
    newArrShift(){
      let newArr = [...this.openArr];
      return newArr.pop();
    }
  },
  methods:{
    getColorFunc(objKey) {
      const color = {
        hong:'red',
        lan:'blue',
        lv:'green'
      }

      return color[objKey];
    }
  },
  watch: {
    openNumObj:{
      handler(objVal){
        let temp = [];
        Object.keys(objVal).forEach(key => {
          let index = key.match(/\d+/) && key.match(/\d+/)[0];
          if(index) temp[index - 1] = objVal[key];
        })
        temp.push(objVal['tm']);

        this.openArr = temp;
      },
      immediate:true
    }
  }
}
</script>

<style scoped>
.balls-item,
.balls-spec-item {
  margin: 0 0.5vw;
  width: 6vw;
  height: 6vw;
  background: #e64600;
  font-size: 3.2vw;
  line-height: 6vw;
  text-align: center;
  color: #fff;
  border-radius: 50%;
}
.balls-spec-item {
  background: #008000;
}
.balls-text {
  color: #535353;
}
.balls-link {
  position: relative;
  top: -0.5em;
  margin: 1vw;
}
.red {
  background: #cc0000;
}
.blue {
  background: #0e86e3;
}
.green {
  background: #38be4f;
}
</style>

