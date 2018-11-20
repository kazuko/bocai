/*
** function_name ajax_submit_form
** description ajax提交表单
** url 接受路径
** formid 表单id
** appenddata 追加数据，二维数组，格式：array(array('name'=>索引名称,'value'=>值),array('name'=>索引名称,'value'=>值))
** method 发送方式
** dataType 数据格式
...
** return 
*/
var fromList = new Array();
function ajax_submit_form(url,formid='',appenddata='',success_callback='',fail_callback='',method='POST',dataType='json',processData=false,contentType=false,tips_content='添加成功！',tips_title='提示',deal_title='正在处理...'){
	if(!url){
		show_tips_box('参数错误！','错误提示');
		return;
	}
	if(fromList[formid]){
		return;
	}
	fromList[formid] = true;
	show_tips_box(deal_title,'提示',1,300,0);
	if(formid){
		var formobj = document.getElementById(formid);
		var formdata = new FormData(formobj);
	}else{
		var formdata = new FormData();
	}
	// console.log(formdata);
	if(appenddata){
		for(var i=0; i<appenddata.length; i++){
			formdata.append(appenddata[i]['name'],appenddata[i]['value']);
		}
	}
	$.ajax({
		url: url,
		type: method,
		processData: processData,
		contentType: contentType,
		dataType: dataType,
		data: formdata,
		success: function(res){
			// console.log(res);
			// console.log(dataType);
			tips_hidden(1,10,function(){
				fromList[formid] = false;
				if(success_callback){
					success_callback(res);
				}else{
					show_tips_box(res,'结果提示');
				}
			});		
		},
		fail: function(res){
			if(fail_callback){
				fail_callback(res);
			}else{
				show_tips_box(res,'错误提示');
			}
		}
	});
}

/*
** function_name show_tips_box
** description 显示提示
** @param content 提示内容
** @param title 提示标题
** @param way 提示消失方式
** @param slides 提示下滑时间
** @param removes 提示停留时间
** @param n 提示下滑时间/n = 提示消失过程时间
** @param sureBtn 确定按钮是否显示
** @param cancelBtn 取消按钮是否显示
** @param sureCallback 确定按钮回调函数
** @param cancelCallback 却笑按钮回调函数
*/ 
var firstTime = true;
function show_tips_box(content='添加成功！',title='提示',way=1,slides=500,removes=1000,n=3,sureBtn=false,cancelBtn=false,sureCallback='',cancelCallback=''){
	if(removes==0){
		removes = 10000000;
	}
	var str = '';
	str += '<section id="show_tips_box_mytips" style="position: fixed;top: -100vh; left: 20vw;width: 60vw;background: white;border-radius: 10px;border: 1px solid #ccc;box-shadow: 0px 0px 15px #888888;">';
	str += '<p style="text-align: center;font-size: 6vw;font-weight: bold;padding: 5px;border: 1px solid #ccc;background: #ccc;">'+title+'</p>';
	str += '<p style="text-align: center;padding: 10vw 6px;font-size: 5vw;">';
	str += '<span style="font-size: 5vw;">'+content+'</span>';
	if(sureBtn||cancelBtn){
		str += '<span style="display:inline-block;width:100%;padding-top:10px;">';
		if(sureBtn){
			str += '<button id="myproject_show_tips_sure_button" type="button" style="font-size: 5vw;min-width:60px;width:30%;">确定</button>';
		}
		if(sureBtn&&cancelBtn){
			str += '<span style="display:inline-block;width:20%;"></span>'
		}
		if(cancelBtn){
			str += '<button id="myproject_show_tips_cancel_button" type="button" style="font-size: 5vw;min-width:60px;width:30%;">取消</button>';
		}
		str += '</span>';
	}
	str += '</p></section>';
	$('body').append(str);
	$('body').find('#show_tips_box_mytips').animate({'top':'30vh'},slides,function(e){
		if((sureBtn&&sureCallback)||(cancelBtn&&cancelCallback)){
			if(sureCallback&&firstTime){
				$('body').on('click','#myproject_show_tips_sure_button',function(){
					tips_hidden(way,(slides/n));
					sureCallback();
				});
			}else if(sureBtn){
				$('body').on('click','#myproject_show_tips_sure_button',function(){
					tips_hidden(way,(slides/n));
				});
			}
			if(cancelCallback&&firstTime){
				$('body').on('click','#myproject_show_tips_cancel_button',function(){
					tips_hidden(way,(slides/n));
					cancelCallback();
				});
			}else if(cancelBtn){
				$('body').on('click','#myproject_show_tips_cancel_button',function(){
					tips_hidden(way,(slides/n));
				});
			}
			if(firstTime){
				firstTime =false
			}
		}else{
			setTimeout(function(){
				if($('body').find('#show_tips_box_mytips')){
					tips_hidden(way,(slides/n));
				}
			},removes);
		}
	});
}
/*
** function_name tips_hidden
** description 隐藏提示，与show_tips_box搭配使用
** @param way 使用的方式，可选择为[0,1,2,3,4]，默认为1
** @slides 消失的数独
*/ 
function tips_hidden(way,slides,callback=''){
	switch(way){
		case 0:
		$("body").find("#show_tips_box_mytips").animate({'top': '100vh'},slides,function(e){
			$("body").find("#show_tips_box_mytips").remove();
			if(callback){
				callback();
			}
		});
		break;
		case 1:
		$("body").find("#show_tips_box_mytips").animate({'display': 'none'},slides,function(e){
			$("body").find("#show_tips_box_mytips").remove();
			if(callback){
				callback();
			}
		});
		break;
		case 2:
		$("body").find("#show_tips_box_mytips").animate({'left': '-100vw'},slides,function(e){
			$("body").find("#show_tips_box_mytips").remove();
			if(callback){
				callback();
			}
		});
		break;
		case 3:
		$("body").find("#show_tips_box_mytips").animate({'left': '100vw'},slides,function(e){
			$("body").find("#show_tips_box_mytips").remove();
			if(callback){
				callback();
			}
		});
		break;
		case 4:
		$("body").find("#show_tips_box_mytips").animate({'opacity': '0'},slides,function(e){
			$("body").find("#show_tips_box_mytips").remove();
			if(callback){
				callback();
			}
		});
		break;
		default:
		alert('参数不对！');
	}
	
}

function isRunNian(nianfen){
	if(!(nianfen%400)||(!(nianfen%4)&&nianfen%100)){
		return true;
	}else{
		return false;
	}
}

// if(isRunNian(2000)){
// 	alert('是闰年');
// }else{
// 	alert('不是闰年');
// }
// 日期时分秒
function wenDate(input_name,date_info=0){
	
	// alert(obj);
	var year=null,month=null,date=null,hours=null,munite=null,seconds=null;
	var Mn=0,dn=0,hn=0,mn=0,sn=0;
	var mywenDate = new Date();
	year = mywenDate.getFullYear();
	month = mywenDate.getMonth()+1;
	date = mywenDate.getDate();
	hours = mywenDate.getHours();
	munite = mywenDate.getMinutes();
	seconds = mywenDate.getSeconds();
	var htmlStr = '<section id="wenDateObject"><div id="wenDateBG" style="position: fixed;top: 0px;left: 0px;z-index:99;background: black;opacity: 0.5;filter: opacity(5);width: 100vw;height: 100vh;"></div><table style="position: fixed;z-index: 130;top: 35vw;left: 5vw;width: 90vw;height: 60vw;background: rgb(255,255,255);border-spacing: 2px;"><tr style="height: 5vw;"><td style="border: 1px solid #ccc;text-align:center;">年</td><td style="border: 1px solid #ccc;text-align:center;">月</td><td style="border: 1px solid #ccc;text-align:center;">日</td><td style="border: 1px solid #ccc;text-align:center;"></td><td style="border: 1px solid #ccc;text-align:center;">时</td><td style="border: 1px solid #ccc;text-align:center;">分</td><td style="border: 1px solid #ccc;text-align:center;">秒</td></tr>';

	htmlStr += '<tr><td style="border: 1px solid #ccc;text-align:center;width:"><ul id="wenYear" style="height: 55vw;overflow: auto;list-style: none;padding: 0px;margin: 0px;">';
	htmlStr += '<li style="border: 1px solid #ccc;height: 19%;"></li><li style="border: 1px solid #ccc;height: 19%;"></li>';
	for(var i=year; i<(year+6); i++){
		htmlStr += '<li style="border: 1px solid #ccc;height: 19%;box-sizing:border-box;padding-top:2vw;';
		if(i==year){
			htmlStr += 'color:red;';
		}
		htmlStr += '">'+i+'</li>';
	}
	htmlStr += '<li style="border: 1px solid #ccc;height: 19%;"></li><li style="border: 1px solid #ccc;height: 19%;"></li>';
	htmlStr += '</ul></td><td style="border: 1px solid #ccc;text-align:center;"><ul id="wenMonth" style="height: 55vw;overflow: auto;list-style: none;padding: 0px;margin: 0px;">';
	htmlStr += '<li style="border: 1px solid #ccc;height: 19%;"></li><li style="border: 1px solid #ccc;height: 19%;"></li>';
	for(var i=1;i<=12;i++){
		htmlStr += '<li style="border: 1px solid #ccc;height: 19%;box-sizing:border-box;padding-top:2vw;';
		if(i==month){
			htmlStr += 'color:red;';
			Mn = i;
		}
		htmlStr += '">'+i+'</li>';
	}
	htmlStr += '<li style="border: 1px solid #ccc;height: 19%;"></li><li style="border: 1px solid #ccc;height: 19%;"></li>';
	htmlStr += '</ul></td><td style="border: 1px solid #ccc;text-align:center;"><ul id="wenDate" style="height: 55vw;overflow: auto;margin: 0px;padding: 0px;">';
	htmlStr += '<li style="border: 1px solid #ccc;height: 19%;"></li><li style="border: 1px solid #ccc;height: 19%;"></li>';
	if(month==2){
		if(isRunNian(year)){
			var day = 29;
		}else{
			var day = 28;
		}
	}else if(month%2||month==8){
		var day = 31;
	}else{
		var day = 30;
	}
	for(var i=1; i<=day; i++){
		htmlStr += '<li style="border: 1px solid #ccc;height: 19%;box-sizing:border-box;padding-top:2vw;';
		if(i==date){
			htmlStr += 'color:red;';
			dn = i;
		}
		htmlStr += '">'+i+'</li>';
	}
	htmlStr += '<li style="border: 1px solid #ccc;height: 19%;"></li><li style="border: 1px solid #ccc;height: 19%;"></li>';
	htmlStr += '</ul></td><td style="border: 1px solid #ccc;text-align:center;width: 2vw;"></td><td style="border: 1px solid #ccc;text-align:center;"><ul id="wenHours" style="height: 55vw;overflow: auto;margin: 0px;padding: 0px;">';
	htmlStr += '<li style="border: 1px solid #ccc;height: 19%;"></li><li style="border: 1px solid #ccc;height: 19%;"></li>';
	for(var i=0; i<24; i++){
		htmlStr += '<li style="border: 1px solid #ccc;height: 19%;box-sizing:border-box;padding-top:2vw;';
		if(i==hours){
			htmlStr += 'color:red;';
			hn = i;
		}
		htmlStr += '">'+i+'</li>';
	}
	htmlStr += '<li style="border: 1px solid #ccc;height: 19%;"></li><li style="border: 1px solid #ccc;height: 19%;"></li>';
	htmlStr += '</ul></td><td style="border: 1px solid #ccc;text-align:center;"><ul id="wenMunite" style="height: 55vw;overflow: auto;margin: 0px;padding: 0px;">';
	htmlStr += '<li style="border: 1px solid #ccc;height: 19%;"></li><li style="border: 1px solid #ccc;height: 19%;"></li>';
	for(var i=0; i<60; i++){
		htmlStr += '<li style="border: 1px solid #ccc;height: 19%;box-sizing:border-box;padding-top:2vw;';
		if(i==munite){
			htmlStr += 'color:red;';
			mn = i;
		}
		htmlStr += '">'+i+'</li>';
	}
	htmlStr += '<li style="border: 1px solid #ccc;height: 19%;"></li><li style="border: 1px solid #ccc;height: 19%;"></li>';
	htmlStr += '</ul></td><td style="border: 1px solid #ccc;text-align:center;"><ul id="wenSeconds" style="height: 55vw;overflow: auto;margin: 0px;padding: 0px;">';
	htmlStr += '<li style="border: 1px solid #ccc;height: 19%;"></li><li style="border: 1px solid #ccc;height: 19%;"></li>';
	for(var i=0; i<60; i++){
		htmlStr += '<li style="border: 1px solid #ccc;height: 19%;box-sizing:border-box;padding-top:2vw;';
		if(i==seconds){
			htmlStr += 'color:red;';
			sn = i;
		}
		htmlStr += '">'+i+'</li>';
	}
	htmlStr += '<li style="border: 1px solid #ccc;height: 19%;"></li><li style="border: 1px solid #ccc;height: 19%;"></li>';
	htmlStr += '</ul></td></tr>';
	htmlStr += '<tr><td id="wenDateBtn" colspan="10" style="height:8vw;border:1px solid #ccc;background:#cce;text-align:center;">确定<td></tr>'
	htmlStr += '</table></section>';
	$('body').append(htmlStr);
	var bgObj = document.getElementById('wenDateBG');
	var wenYearObj = document.getElementById('wenYear');
	var wenMonthObj = document.getElementById('wenMonth');
	var wenDateObj = document.getElementById('wenDate');
	var wenHoursObj = document.getElementById('wenHours');
	var wenMuniteObj = document.getElementById('wenMunite');
	var wenSecondsObj = document.getElementById('wenSeconds');
	var buttonOBJ = document.getElementById('wenDateBtn');
	wenYearObj.addEventListener('touchstart',touch, false);
	wenMonthObj.addEventListener('touchstart',touch, false);
	wenDateObj.addEventListener('touchstart',touch, false);
	wenHoursObj.addEventListener('touchstart',touch, false);
	wenMuniteObj.addEventListener('touchstart',touch, false);
	wenSecondsObj.addEventListener('touchstart',touch, false);
	bgObj.addEventListener('click',function(e){
		$('#wenDateObject').remove();
	},false);
	buttonOBJ.addEventListener('click',function(e){
		var dateInfo = '';
		switch(date_info){
			case 0:
			dateInfo = year;
			break;
			case 1:
			if(Number(month)<10){
				month = '0'+month;
			}
			dateInfo = year+'-'+month;
			break;
			case 2:
			if(Number(month)<10){
				month = '0'+month;
			}
			if(Number(date)<10){
				date = '0'+date;
			}
			dateInfo = year+'-'+month+'-'+date;
			break;
			case 3:
			if(Number(month)<10){
				month = '0'+month;
			}
			if(Number(date)<10){
				date = '0'+date;
			}
			if(Number(hours)<10){
				hours = '0'+hours;
			}
			dateInfo = year+'-'+month+'-'+date+' '+hours;
			break;
			case 4:
			if(Number(month)<10){
				month = '0'+month;
			}
			if(Number(date)<10){
				date = '0'+date;
			}
			if(Number(hours)<10){
				hours = '0'+hours;
			}
			if(Number(munite)<10){
				munite = '0'+munite;
			}
			dateInfo = year+'-'+month+'-'+date+' '+hours+':'+munite;
			break;
			case 5:
			if(Number(month)<10){
				month = '0'+month;
			}
			if(Number(date)<10){
				date = '0'+date;
			}
			if(Number(hours)<10){
				hours = '0'+hours;
			}
			if(Number(munite)<10){
				munite = '0'+munite;
			}
			if(Number(seconds)<10){
				seconds = '0'+seconds;
			}
			dateInfo = year+'-'+month+'-'+date+' '+hours+':'+munite+':'+seconds;
			break;
		}

		$('input[name="'+input_name+'"]').val(dateInfo);
		$('#wenDateObject').remove();
	},false);
	var topValue = 0;//滚动条到顶部的距离
	var interval = null;//定时器
	var oneLiHeight = $('#wenYear li').height();
	wenMonthObj.scrollTop = (Mn-1)*oneLiHeight;
	wenDateObj.scrollTop = (dn-1)*oneLiHeight;
	wenHoursObj.scrollTop = hn*oneLiHeight;
	wenMuniteObj.scrollTop = mn*oneLiHeight;
	wenSecondsObj.scrollTop = sn*oneLiHeight;
	function touch (event){ 
		var event = event || window.event;
		switch(event.type){ 
			case "touchstart": 
			if(event.target.localName=='li'){
				var id = event.path[1].id;
			}else{
				var id = event.path[0].id;
			}
			switch(id){
				case 'wenYear':
				wendateli(wenYearObj,id,oneLiHeight);
				break;
				case 'wenMonth':
				wendateli(wenMonthObj,id,oneLiHeight);
				break;
				case 'wenDate':
				wendateli(wenDateObj,id,oneLiHeight);
				break;
				case 'wenHours':
				wendateli(wenHoursObj,id,oneLiHeight);
				break;
				case 'wenMunite':
				wendateli(wenMuniteObj,id,oneLiHeight);
				break;
				case 'wenSeconds':
				wendateli(wenSecondsObj,id,oneLiHeight);
				break;
			}
			break; 
			case "touchend": 
			
			break; 
			case "touchmove": 
			break; 
		} 
		function wendateli(obj,id,oneLiHeight){
			topValue = 0;
			obj.addEventListener('scroll',function(){
				if(interval==null){
					interval = setInterval(function(){
						if(obj.scrollTop == topValue){
							// var oneLiHeight = $("#"+id+" li").height();
							// console.log('height='+oneLiHeight);
							var n = Math.round(obj.scrollTop/oneLiHeight);
							// console.log(id+'='+obj.scrollTop+"; n="+n);
							obj.scrollTop = n*oneLiHeight;
							switch(id){
								case 'wenYear':
								year = $('#'+id+' li').eq(n+2).css('color','red').siblings().css('color','black');
								year = $('#'+id+' li').eq(n+2).html();
								break;
								case 'wenMonth':
								month = $('#'+id+' li').eq(n+2).css('color','red').siblings().css('color','black');
								month = $('#'+id+' li').eq(n+2).html();
								break;
								case 'wenDate':
								date = $('#'+id+' li').eq(n+2).css('color','red').siblings().css('color','black');
								date = $('#'+id+' li').eq(n+2).html();
								break;
								case 'wenHours':
								hours = $('#'+id+' li').eq(n+2).css('color','red').siblings().css('color','black');
								hours = $('#'+id+' li').eq(n+2).html();
								break;
								case 'wenMunite':
								munite = $('#'+id+' li').eq(n+2).css('color','red').siblings().css('color','black');
								munite = $('#'+id+' li').eq(n+2).html();
								break;
								case 'wenSeconds':
								seconds = $('#'+id+' li').eq(n+2).css('color','red').siblings().css('color','black');
								seconds = $('#'+id+' li').eq(n+2).html();
								break;
							}
							
							console.log("Y="+year+'; M='+month+'; D='+date+'; H='+hours+'; m='+munite+'; s='+seconds);
							clearInterval(interval);
							interval = null;
						}
					},100);
				}
				topValue = obj.scrollTop;
			},false);
		}
	}
}
// 提交form表单
function submitForm(formid,url='',callback='',deal_title='正在处理...'){
	if(url){
		ajax_submit_form(url,formid,'',function(res){
			if(typeof(res)=='json'){
				res = JSON.parse(res);
			}
			if(callback){
				callback(res);
			}else{
				show_tips_box(res['msg']);
			}
		},function(res){
			show_tips_box('请检查网络是否正常！');
			if(callback){
				callback(res);
			}
		},'POST','json',false,false,'添加成功！','提示',deal_title);
	}else{
		$('#'+formid).submit();
	}
}

/*
** 获取光标位置
** selection : 当前激活选中区，即高亮文本块，和文档中用户可执行某些操作的其他元素。
** createRange ： 从当前文本选中区中创建 textRange 对象，或 从控件选中区中创建 controlRange 集合
** obj.compareEndPoints(param1,param2) ： 用于比较两个TextRange对象的位置，obj是一个textRange对象，param2也是一个textRange对象，param1：StartToEnd(比较obj开头和参数param2的末尾)、StartToStart(比较obj开头和参数param2的开头)、EndToStart(比较obj末尾和param2的开头)、EndToEnd(比较obj末尾和param2的末尾)
** obj.moveStart(param1,param2) : 更改范围的开始位置, obj是TextRange对象，param1：character(按字符移动)、word(按单词移动)、sentence(按句子移动)、textedit(启动编辑动作)；param2：移动偏差
** obj.moveToElementText(obj1) : obj:textRange对象，obj1：元素对象
** 
*/
function getCursor(elementId){
	var isIE = !(!document.all);
	var start = 0,end = 0;
	// 获取元素对象
	var oTextarea = document.getElementById(elementId);
	if(isIE){
		// 创建textRange或controlRange对象
		var sTextRange = document.selection.createRange();
		// 判断选择的是否为指定对象
		if(sTextRange.parentElement() == oTextarea){
			// 创建一个textRange 或 controlRange 对象
			// var oTextRange = document.body.createRange();
			var oTextRange = document.body.createTextRange();
			// 移动文本范围，以便范围的开始和结束位置能够完全包含指定元素的文本。
			oTextRange.moveToElementText(oTextarea);
			for(start = 0; oTextRange.compareEndPoints('StartToStart',sTextRange) < 0; start++){
				oTextRange.moveStart('character', 1);
			}
			for(var i = 0; i <= start; i++){
				if(oTextarea.value.charAt(i) == '\n'){
					start++;
				}
			}

			oTextRange.moveToElementText(oTextarea);
			for(end = 0; oTextRange.compareEndPoints('StartToEnd',sTextRange) < 0; end++){
				oTextRange.moveStart('character',1);
			}
			for(var i = 0;i <= end; i++){
				if(oTextarea.value.charAt(i) == '\n'){
					end++;
				}
			}
		}
	}else{
		start = oTextarea.selectionStart;
		end = oTextarea.selectionEnd;
	}
	var result = new Array();
	result['start'] = start;
	result['end'] = end;
	return result; 
}
/*
** 设置光标位置
** elementId : 元素ID
** start : 光标起始位置
** end ： 光标结束位置
*/ 
function setCursor(elementId,start,end){
	console.log(elementId+':'+start+':'+end);
	var oTextarea = document.getElementById(elementId);
	// console.log(oTextarea);
	start = parseInt(start);
	end = parseInt(end);
	if(isNaN(start)||isNaN(end)){
		show_tips_box('位置参数错误！');
	}
	var isIE = !(!document.all);
	if(isIE){
		var oTextRange = oTextarea.createTextRange();
		var LStart = start;
		var LEnd = end;
		start = 0;
		end = 0;
		var value = oTextarea.value;
		for(var i = 0; i < value.length && i< LStart; i++){
			var c = value.charAt(i);
			if(c != '\n'){
				start++;
			}
		}
		for(var i = value.length-1; i >= LEnd && i >= 0; i--){
			var c = value.charAt(i);
			if(c != '\n'){
				end++;
			}
		}
		oTextRange.moveStart('character',start);
		oTextRange.moveEnd('character',-end);
		oTextRange.select();
		oTextarea.focus();
	}else{
		oTextarea.select();  
		oTextarea.selectionStart = start;  
		oTextarea.selectionEnd = end;
	}

}
// ip地址格式化输入
// [123].[123].[123].[132]
// []:表示一个input框
function IP_input(parentId){
	$("#"+parentId+" input").keydown(function(e){
		// console.log('bb');
		if(e.keyCode==8){
			if(!$(this).val()){
				var index = $(this).index();
				$("#"+parentId+" input").eq(index-1).focus();
			}
		}
	});

	$("#"+parentId+" input").bind('input propertychange',function(e){
		var index = $(this).index();
		var allval = $("#"+parentId+" input").eq(index).val();
		allval = allval.match(/[0-9]{1,3}/g);
		if(allval){
			console.log(allval.length);
			for(var i = 0; i<allval.length; i++,index++){
				$("#"+parentId+" input").eq(index).val(allval[i]);
				$("#"+parentId+" input").eq(index).focus();
			}
		}else{
			$("#"+parentId+" input").eq(index).val('');
		}
	});
}