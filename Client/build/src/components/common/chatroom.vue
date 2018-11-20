<template>
    <div>
        <mt-header title="聊天室">
            <router-link to="/" slot="left">
                <mt-button icon="back">返回</mt-button>
            </router-link>
        </mt-header>
        <div id="chatroom">
            <div class="chatContent" id="chatContent">
                <div  v-for="(item,index) in others" :key="index">
                    <div class="msg-list" :class="{myList: item.type}">
                        <div class="username" v-show="!item.type">{{'用户'+item.id}}</div>
                        <div class="username" v-show="item.type">我</div>
                        <div class="msg-text" :class="{myText: item.type}">{{item.acceptMsg}}</div>
                    </div>

                </div>
            </div>
        </div>
        <div class="user">
            <div class="userContent">
                <mt-field id="sendText" type="textarea" rows="1" v-model="writeMsg"></mt-field>
                <mt-button class="send" @click="send">发送</mt-button>
            </div>
        </div>
    </div>
</template>

<script>
console.log("COMMON_CHATROOM_VUE");
export default {
  data() {
    return {
      username: "赵日天",
      writeMsg: "",
      others: [
        {
          id: 1,
          acceptMsg: "我是1好",
          type: 0
        },
        {
          acceptMsg: "你好",
          type: 1
        },
        {
          id: 3,
          acceptMsg: "撒打算打算的撒",
          type: 0
        }
      ],
      user4: {
        id: 4,
        acceptMsg: "asdsadsad",
        type: 0
      }
    };
  },
  methods: {
    send: function() {
      let s = { acceptMsg: this.writeMsg, type: 1 };
      this.others.push(s);
    }
  },
  watch: {
    others: function() {
        this.$nextTick(() => {
            let lastChat = document.getElementById("chatContent").lastChild;
            lastChat.scrollIntoView();
        })
    }
  },
  updated: function() {
      
  },
  created: function() {
    this.others.push(this.user4);
    console.log(this.others);
  },
  mounted: function() {
    let h = window.screen.height;
    document.getElementById("chatroom").style.height = h - 137 + "px";
  }
};
</script>

<style scoped>
#chatroom {
  /* position: relative; */
  overflow: hidden;
  overflow-y: auto;
}
.chatContent {
  width: 100%;
  /* display: flex;
    flex-direction: column-reverse; */
}
.user {
  height: 40px;
  position: fixed;
  bottom: 57px;
  width: 100%;
}
.userContent {
  height: 100%;
  border-top: 1px solid #999;
}
.msg-list {
  text-align: left;
}
.username {
  font-size: 14px;
}
.msg-text {
  padding: 5px;
  border: 1px solid #999;
  background-color: #fff;
  display: inline-block;
  border-radius: 10px;
}
.myList {
  text-align: right;
}
.myText {
  background-color: #57d6dd;
  color: #fff;
}
.userContent {
  display: flex;
  justify-content: space-between;
}
#sendText {
  width: 80%;
}
.send {
  width: 20%;
}
</style>