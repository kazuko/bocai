

function qingkong(){ //清空提示函数
    updateinfo();
}

var wait = 90; //停留时间
function updateinfo(){

    if(wait == 0){
        document.getElementById('Submitbfs').value = "获取验证码";
        document.getElementById('Submitbfs').disabled="";
        document.getElementById("show_statu").style.display='none';
        //document.getElementById("show_statu").innerHTML="<span style=color:blue>如果您没有收到短信校验码，您现在可以重新获取！</span>";
        wait=90 //还原重发时的初始值
    }
    else{
        document.getElementById('Submitbfs').disabled="disabled"; //防止关闭层后，又激活了
        document.getElementById('Submitbfs').value = "等待 "+wait+" 秒";
        wait--;
        setTimeout("updateinfo()",1000);
    }

}

//------------------------------------------------------------


function giveyz(scms_yzcode)
{
    var chk=true;
    var divid=document.getElementById("show_statu");
    var regex=/[0-9]$/ 

    divid.style.display='block';

    if (scms_yzcode=="" ){
        divid.innerHTML="<span style='color:red'>请填写您收到的短信校验码！</span>";
    }else if ( document.getElementById('right_yzcode').value!=scms_yzcode ){
        divid.innerHTML="<span style='color:red'>您填写的短信校验码不正确！</span>";
    }else if ( document.getElementById('right_yzcode').value==scms_yzcode ){
        divid.innerHTML="<span style='color:green'>验证成功！</span>";
        //divid.style.display='none';
    }
}

var xmlHttp

function giveduanxin(tel)
{
    var chk=true;
    var divid=document.getElementById("show_statu");
    var regex=/^(?:13\d|15\d|18[123456789])-?\d{5}(\d{3}|\*{3})$/;

    divid.style.display='';

    if (tel=="" ){
        divid.innerHTML="<span style='color:red'>请填写手机号码！</span>";
    }else if ( !regex.exec(tel) ){
        divid.innerHTML="<span style='color:red'>手机号码格式不正确！</span>";
    }else{

        xmlHttp=GetXmlHttpObject()
        if (xmlHttp==null)
        {
            alert ("抱歉，浏览器不支持")
            return
        } 

        var url="http://localhost/tp5/extend/SMS.cn/sms.php"
        url=url+"?action=chk&tel="+tel
        url=url+"&sid="+Math.random()
        xmlHttp.onreadystatechange=stateChanged
        xmlHttp.open("GET",url,true)
        xmlHttp.send(null)
    } 

    function stateChanged() 
    { 
         if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
         {

             var give_strs= new Array(); //定义一数组
             give_strs=xmlHttp.responseText.split("|"); //字符分割  

             if (give_strs[0]=="right"){
                //document.getElementById('Submitbfs').style.visibility='hidden';
                //closeWindow();
                divid.innerHTML="<span style='color:green'>验证码已发送,请查收！</span>";
                //document.getElementById('codeshows').innerHTML=yzm;
                document.getElementById('Submitbfs').disabled="disabled";//立即失效，并开始提示下面是2秒后换提示内容并开始倒数
                document.getElementById('right_yzcode').value=give_strs[1];//回传发到短信的校验码
                setTimeout("qingkong()",2000);//1秒后提示，重新发送
             }else{
                divid.innerHTML=xmlHttp.responseText;
             }
         }
    }

    //document.getElementById('Submitbfs').disabled="disabled";

}

function GetXmlHttpObject(){
    var xmlHttp=null;
    try
     {
     // Firefox, Opera 8.0+, Safari
     xmlHttp=new XMLHttpRequest();
     }
    catch (e)
     {
     // Internet Explorer
     try
      {
      xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
      }
     catch (e)
      {
      xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
      }
     }
    return xmlHttp;

}