<template>
  <div class="radio-out-box">
    <div v-for="(item, index) in content" :key="index">
      <transition :name="index" v-on:after-enter="afterEnter(index)">
        <div class="radio-text-box" :class="item.status?index+' showbox':index+' hidebox'" v-if="item.status">{{item.text}}</div>
      </transition>
    </div>
  </div>
</template>
<script>
export default {
  props: {
    list: [Object, Array]
  },
  data() {
    return {
      maxWidth: 0,
      n: 0,
      total: 3,
      listchange: false,
      op: false,
      content: {
        content1: {
          status: false,
          text: ""
        },
        content2: {
          status: false,
          text: ""
        },
        content3: {
          status: false,
          text: ""
        }
        // content4: "",
        // content5: ""
      }
    };
  },
  watch: {
    list: function() {
      if (this.op) {
        this.listchange = true;
      } else {
        this.op = true;
        this.checkList();
      }
    }
  },
  methods: {
    afterEnter(index) {
      console.log("afterEnter => {");
      console.log("index=" + index);

      console.log("}");
      this.$set(this.content[index], "status", false);
      this.$set(this.content[index], "text", "");
      // this.n = this.n > 0 ? this.n - 1 : 0;
      this.n--;
      this.$forceUpdate();
      this.$nextTick(function() {
        if (this.op) {
          this.listchange = true;
        } else {
          this.op = true;
          this.checkList();
        }
      });
    },
    checkList() {
      let listLen = Object.keys(this.list).length;
      if (listLen && this.n < this.total) {
        let i = 0;
        for (var index in this.content) {
          if (
            !this.content[index].status &&
            !this.content[index].text &&
            i < listLen
          ) {
            this.content[index].text =
              this.list[i].send_user + ":" + this.list[i].text;
            this.content[index].status = true;
            this.n++;
            i++;
          }
          if (i == listLen) {
            break;
          }
        }
        if (i) {
          this.GLOBAL.radioTextList.splice(0, i);
        }
        this.$forceUpdate();
      }
      if (this.listchange) {
        this.listchange = false;
        if (Object.keys(this.list).length) {
          this.checkList();
        }
      } else {
        this.op = false;
      }
    }
  },
  created: function() {
    this.maxWidth = window.screen.availWidth
      ? window.screen.availWidth
      : document.body.offsetWidth
        ? document.body.offsetWidth
        : 0;
  },
  mounted: function() {}
};
</script>
<style>
.showbox {
  z-index: 100;
}
.hidebox {
  z-index: -1;
}
.radio-box {
  height: 10vw;
  font-size: 4vw;
  position: fixed;
  left: 0vw;
  width: 100vw;
}
.content1 {
  top: 1vw;
}
.content2 {
  top: 11vw;
}
.content3 {
  top: 22vw;
}
.content4 {
  top: 33vw;
}
.content5 {
  top: 44vw;
}

.radio-text-box {
  text-align: left;
  white-space: nowrap;
  display: inline-block;
  width: auto;
  background: pink;
  padding: 0vw 2vw;
  border-radius: 2vw;
  height: 7vw;
  line-height: 7vw;
  font-size: 4vw;
  position: fixed;
  left: 0vw;
  transform: translate(-300vw, 0);
}
.content1-enter-active,
.content2-enter-active,
.content3-enter-active,
.content4-enter-active,
.content5-enter-active,
.content6-enter-active {
  will-change: transform;
  position: absolute;
  transition: all 8s linear;
  transform: translate(-100%, 0);
}
.content1-enter,
.content2-enter,
.content3-enter,
.content4-enter,
.content6-enter,
.content5-enter {
  /* transform: translate(200%, 0); */
  transform: translate(100vw, 0);
}

/* .content1-leave-active,
.content2-leave-active,
.content3-leave-active,
.content4-leave-active,
.content5-leave-active,
.content6-leave-active {
  will-change: transform;
  position: absolute;
  transition: all 3s linear;
  transform: translate(-100%, 0);
}
.content1-leave,
.content2-leave,
.content3-leave,
.content4-leave,
.content6-leave,
.content5-leave {
  transform: translate(50vw, 0);
} */
</style>


