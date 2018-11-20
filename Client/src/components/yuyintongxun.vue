<script>
// import {Recorder} from "../js/recorder.js";

var intervalHandle = null;
var audio_context = null;
var myRecorder = null;
/**
 *
 */
const initMedia = function(that, elementId , audioCallback) {
  // alert("初始化Media");
  try {
    // 老的浏览器可能根本没有实现 mediaDevices，所以我们可以先设置一个空的对象
    window.AudioContext = window.AudioContext || window.webkitAudioContext;

    if (navigator.mediaDevices === undefined) {
      navigator.mediaDevices = {};
    }

    // 一些浏览器部分支持 mediaDevices。我们不能直接给对象设置 getUserMedia
    // 因为这样可能会覆盖已有的属性。这里我们只会在没有getUserMedia属性的时候添加它。
    if (navigator.mediaDevices.getUserMedia === undefined) {
      navigator.mediaDevices.getUserMedia = function(constraints) {
        // 首先，如果有getUserMedia的话，就获得它
        var getUserMedia =
          navigator.webkitGetUserMedia ||
          navigator.mozGetUserMedia ||
          navigator.getUserMedia;

        // 一些浏览器根本没实现它 -
        // 那么就返回一个error到promise的reject来保持一个统一的接口;
        if (!getUserMedia) {
          return Promise.reject(
            new Error("getUserMedia is not implemented in this browser")
          );
        }

        // 否则，为老的navigator.getUserMedia方法包裹一个Promise
        return new Promise(function(resolve, reject) {
          getUserMedia.call(navigator, constraints, resolve, reject);
        });
      };
    }

    // navigator.getUserMedia =
    // navigator.getUserMedia || navigator.webkitGetUserMedia;

    window.URL = window.URL || window.webkitURL;
    audio_context = new AudioContext();
  } catch (e) {
    that.$messagebox.alert(
      "当前设备不支持语音输入！(" + e.name + ":" + e.message + ")"
    );
  }

  // navigator.getUserMedia(
  //   { audio: true },
  //   function(stream) {
  //     var input = audio_context.createMediaStreamSource(stream);
  //     myRecorder = new Recorder(input);
  //   },
  //   function(e) {
  //     that.$messagebox.alert(e);
  //   }
  // );
  // let that = this;
  // console.log("audioFlag:" + audioFlag);
  // console.log("videoFlag:" + videoFlag);
  navigator.mediaDevices
    .getUserMedia({ audio: true })
    .then(function(stream) {
      var input = audio_context.createMediaStreamSource(stream);
      myRecorder = new Recorder(input);
    })
    .catch(function(err) {
      // alert(err.name + ": " + err.message);
      // that.$messagebox.alert("当前设备不支持语音输入！");
      if(audioCallback){
        audioCallback(err);
      }
    });
};
/**s
 * 准备录音
 */
const readyRecord = function(
  paramObj = {
    that: "",
    elementId: "",
    showId: "",
    url: "",
    type: "POST",
    success: "",
    fail: "",
    audioFail: ""
  }
) {
  document.oncontextmenu = function(e) {
    e.preventDefault();
  };
  // alert('sss');
  initMedia(paramObj.that, paramObj.audioId, paramObj.audioFail);
  var endLocation = 0;
  var startLocation = 0;
  let elementObj = document.getElementById(paramObj.elementId);
  elementObj.addEventListener("touchstart", function(even) {
    var oEvent = even || event;
    //js阻止事件冒泡
    oEvent.cancelBubble = true;
    oEvent.stopPropagation();

    myRecorder && myRecorder.record();
    showBox(paramObj.that, paramObj.showId);
    //startLocation = e
  });

  elementObj.addEventListener("touchmove", function(even) {});

  //离开发送语音
  elementObj.addEventListener("touchend", function(even) {
    // var oEvent = even || event;
    // //js阻止事件冒泡
    // oEvent.cancelBubble = true;
    // oEvent.stopPropagation();
    hideBox(paramObj.that, paramObj.showId);
    myRecorder && myRecorder.stop();

    myRecorder &&
      myRecorder.exportWAV(function(blob) {
        // console.log("blob=>{");
        // console.log(blob);
        // console.log("}");
        var formdata = new FormData();
        formdata.append("videofile", blob);
        formdata.append("type", "sendVoice");
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function(respone) {
          if (xhr.readyState == 4 && xhr.status == 200) {
            // alert("上传成功");
            //console.log(respone.currentTarget.response);
            if (paramObj.success) {
              paramObj.success(respone.currentTarget.response);
            }
          } else if (xhr.readyState == 4 && xhr.status != 200) {
            if (paramObj.fail) {
              paramObj.fail({ readyState: xhr.readyState, status: xhr.status });
            }
          }
        };
        xhr.open("POST", paramObj.url || this.GLOBAL.uploadHref);
        xhr.send(formdata);
      });
    myRecorder.clear();
  });
};
const hideBox = function(that, showId) {
  let obj = document.getElementById(showId);
  obj.innerHTML = "";
  if (intervalHandle) {
    clearInterval(intervalHandle);
    intervalHandle = null;
  }
};
/**
 * 展示录音状态框框
 */
const showBox = function(that, showId) {
  var totalN = 5;
  let obj = document.getElementById(showId);
  let htmlStr =
    "<div id='wenwenMediaBox' style='width:30vw;height:30vw;border-radius: 30vw;position:absolute;top:40vh;left:35vw;z-index:100000;overflow:hidden;'>";
  htmlStr +=
    "<div style='width:100%;height:100%;background:black;opacity:0.5;'></div>";
  // justify-content: center;justify-items: center;align-content: center;align-items: center;'>
  htmlStr +=
    "<div style='position:absolute;top:5%;left:5%;width:90%;height:90%;border:2px solid red;border-radius:100%;";
  for (var i = 0; i < totalN; i++) {
    htmlStr +=
      "<div style='text-align:center;width:100%;margin-top:1%;'><span id='wenwenMedia" +
      i +
      "' style='display:inline-block;background:white;height:8%;width:0px;opacity:.8;border-radius:5vw;'>" +
      i +
      "</span></div>";
  }
  htmlStr += "</div>";
  htmlStr += "</div>";
  // alert(htmlStr);
  obj.innerHTML = htmlStr;
  let maxWidth = document.getElementById("wenwenMediaBox").offsetWidth;

  // alert(maxWidth);
  if (!intervalHandle) {
    intervalHandle = setInterval(function() {
      for (var i = 0; i < totalN; i++) {
        let width = randomNum(0, maxWidth);
        document.getElementById("wenwenMedia" + i).style.width = width + "px";
      }
    }, 100);
  }
};
/**
 * 生成随机数
 */
function randomNum(minNum, maxNum) {
  switch (arguments.length) {
    case 1:
      return parseInt(Math.random() * minNum + 1, 10);
      break;
    case 2:
      return parseInt(Math.random() * (maxNum - minNum + 1) + minNum, 10);
      break;
    default:
      return 0;
      break;
  }
}

export default {
  initMedia,
  readyRecord,
  showBox,
  hideBox,
  intervalHandle
};
</script>