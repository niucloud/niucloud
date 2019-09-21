(function($){var
$Special=$.event.special,motionMapMap={},isMoveBound=false,pxPinchZoom=-1,optionKey='ue_bound',doDisableMouse=false,defaultOptMap={bound_ns_map:{},wheel_ratio:15,px_radius:3,ignore_class:':input',tap_time:200,held_tap_time:300},callbackList=[],zoomMouseNum=1,zoomTouchNum=4,boundList,Ue,motionDragId,motionHeldId,motionDzoomId,motion1ZoomId,motion2ZoomId,checkMatchVal,removeListVal,pushUniqVal,makeListPlus,fnHeld,fnMotionStart,fnMotionMove,fnMotionEnd,onMouse,onTouch,onMousewheel;checkMatchVal=function(data){var match_count=0,idx;for(idx=this.length;idx;0){if(this[--idx]===data){match_count++;}}
return match_count;};removeListVal=function(data){var removed_count=0,idx;for(idx=this.length;idx;0){if(this[--idx]===data){this.splice(idx,1);removed_count++;idx++;}}
return removed_count;};pushUniqVal=function(data){if(checkMatchVal.call(this,data)){return false;}
this.push(data);return true;};makeListPlus=function(input_list){if(input_list&&$.isArray(input_list)){if(input_list.remove_val){console.warn('The array appears to already have listPlus capabilities');return input_list;}}
else{input_list=[];}
input_list.remove_val=removeListVal;input_list.match_val=checkMatchVal;input_list.push_uniq=pushUniqVal;return input_list;};boundList=makeListPlus();Ue={setup:function(data,a_names,fn_bind){var
this_el=this,$to_bind=$(this_el),seen_map={},option_map,idx,namespace_key,ue_namespace_code,namespace_list;if($.data(this,optionKey)){return;}
option_map={};$.extend(true,option_map,defaultOptMap);$.data(this_el,optionKey,option_map);namespace_list=makeListPlus(a_names.slice(0));if(!namespace_list.length||namespace_list[0]===""){namespace_list=["000"];}
NSPACE_00:for(idx=0;idx<namespace_list.length;idx++){namespace_key=namespace_list[idx];if(!namespace_key){continue NSPACE_00;}
if(seen_map.hasOwnProperty(namespace_key)){continue NSPACE_00;}
seen_map[namespace_key]=true;ue_namespace_code='.__ue'+namespace_key;$to_bind.bind('mousedown'+ue_namespace_code,onMouse);$to_bind.bind('touchstart'+ue_namespace_code,onTouch);$to_bind.bind('mousewheel'+ue_namespace_code,onMousewheel);}
boundList.push_uniq(this_el);if(!isMoveBound){$(document).bind('mousemove.__ue',onMouse);$(document).bind('touchmove.__ue',onTouch);$(document).bind('mouseup.__ue',onMouse);$(document).bind('touchend.__ue',onTouch);$(document).bind('touchcancel.__ue',onTouch);isMoveBound=true;}},add:function(arg_map){var
this_el=this,option_map=$.data(this_el,optionKey),namespace_str=arg_map.namespace,event_type=arg_map.type,bound_ns_map,namespace_list,idx,namespace_key;if(!option_map){return;}
bound_ns_map=option_map.bound_ns_map;if(!bound_ns_map[event_type]){bound_ns_map[event_type]={};}
if(!namespace_str){return;}
namespace_list=namespace_str.split('.');for(idx=0;idx<namespace_list.length;idx++){namespace_key=namespace_list[idx];bound_ns_map[event_type][namespace_key]=true;}},remove:function(arg_map){var
elem_bound=this,option_map=$.data(elem_bound,optionKey),bound_ns_map=option_map.bound_ns_map,event_type=arg_map.type,namespace_str=arg_map.namespace,namespace_list,idx,namespace_key;if(!bound_ns_map[event_type]){return;}
if(!namespace_str){delete bound_ns_map[event_type];return;}
namespace_list=namespace_str.split('.');for(idx=0;idx<namespace_list.length;idx++){namespace_key=namespace_list[idx];if(bound_ns_map[event_type][namespace_key]){delete bound_ns_map[event_type][namespace_key];}}
if($.isEmptyObject(bound_ns_map[event_type])){delete bound_ns_map[event_type];}},teardown:function(a_names){var
elem_bound=this,$bound=$(elem_bound),option_map=$.data(elem_bound,optionKey),bound_ns_map=option_map.bound_ns_map,idx,namespace_key,ue_namespace_code,namespace_list;if(!$.isEmptyObject(bound_ns_map)){return;}
namespace_list=makeListPlus(a_names);namespace_list.push_uniq('000');NSPACE_01:for(idx=0;idx<namespace_list.length;idx++){namespace_key=namespace_list[idx];if(!namespace_key){continue NSPACE_01;}
ue_namespace_code='.__ue'+namespace_key;$bound.unbind('mousedown'+ue_namespace_code);$bound.unbind('touchstart'+ue_namespace_code);$bound.unbind('mousewheel'+ue_namespace_code);}
$.removeData(elem_bound,optionKey);boundList.remove_val(this);if(boundList.length===0){$(document).unbind('mousemove.__ue');$(document).unbind('touchmove.__ue');$(document).unbind('mouseup.__ue');$(document).unbind('touchend.__ue');$(document).unbind('touchcancel.__ue');isMoveBound=false;}}};fnHeld=function(arg_map){var
timestamp=+new Date(),motion_id=arg_map.motion_id,motion_map=arg_map.motion_map,bound_ns_map=arg_map.bound_ns_map,event_ue;delete motion_map.idto_tapheld;if(!motion_map.do_allow_tap){return;}
motion_map.px_end_x=motion_map.px_start_x;motion_map.px_end_y=motion_map.px_start_y;motion_map.ms_timestop=timestamp;motion_map.ms_elapsed=timestamp-motion_map.ms_timestart;if(bound_ns_map.uheld){event_ue=$.Event('uheld');$.extend(event_ue,motion_map);$(motion_map.elem_bound).trigger(event_ue);}
if(bound_ns_map.uheldstart){event_ue=$.Event('uheldstart');$.extend(event_ue,motion_map);$(motion_map.elem_bound).trigger(event_ue);motionHeldId=motion_id;}
else{delete motionMapMap[motion_id];}};fnMotionStart=function(arg_map){var
motion_id=arg_map.motion_id,event_src=arg_map.event_src,request_dzoom=arg_map.request_dzoom,option_map=$.data(arg_map.elem,optionKey),bound_ns_map=option_map.bound_ns_map,$target=$(event_src.target),do_zoomstart=false,motion_map,cb_map,do_allow_tap,event_ue;if(motionMapMap[motion_id]){return;}
if(request_dzoom&&!bound_ns_map.uzoomstart){return;}
if($target.is(option_map.ignore_class)){return;}
do_allow_tap=bound_ns_map.utap||bound_ns_map.uheld||bound_ns_map.uheldstart?true:false;cb_map=callbackList.pop();while(cb_map){if($target.is(cb_map.selector_str)||$(arg_map.elem).is(cb_map.selector_str)){if(cb_map.callback_match){cb_map.callback_match(arg_map);}}
else{if(cb_map.callback_nomatch){cb_map.callback_nomatch(arg_map);}}
cb_map=callbackList.pop();}
motion_map={do_allow_tap:do_allow_tap,elem_bound:arg_map.elem,elem_target:event_src.target,ms_elapsed:0,ms_timestart:event_src.timeStamp,ms_timestop:undefined,option_map:option_map,orig_target:event_src.target,px_current_x:event_src.clientX,px_current_y:event_src.clientY,px_end_x:undefined,px_end_y:undefined,px_start_x:event_src.clientX,px_start_y:event_src.clientY,timeStamp:event_src.timeStamp};motionMapMap[motion_id]=motion_map;if(bound_ns_map.uzoomstart){if(request_dzoom){motionDzoomId=motion_id;}
else if(!motion1ZoomId){motion1ZoomId=motion_id;}
else if(!motion2ZoomId){motion2ZoomId=motion_id;event_ue=$.Event('uzoomstart');do_zoomstart=true;}
if(do_zoomstart){event_ue=$.Event('uzoomstart');motion_map.px_delta_zoom=0;$.extend(event_ue,motion_map);$(motion_map.elem_bound).trigger(event_ue);return;}}
if(bound_ns_map.uheld||bound_ns_map.uheldstart){motion_map.idto_tapheld=setTimeout(function(){fnHeld({motion_id:motion_id,motion_map:motion_map,bound_ns_map:bound_ns_map});},option_map.held_tap_time);}};fnMotionMove=function(arg_map){var
motion_id=arg_map.motion_id,event_src=arg_map.event_src,do_zoommove=false,motion_map,option_map,bound_ns_map,event_ue,px_pinch_zoom,px_delta_zoom,mzoom1_map,mzoom2_map;if(!motionMapMap[motion_id]){return;}
motion_map=motionMapMap[motion_id];option_map=motion_map.option_map;bound_ns_map=option_map.bound_ns_map;motion_map.timeStamp=event_src.timeStamp;motion_map.elem_target=event_src.target;motion_map.ms_elapsed=event_src.timeStamp-motion_map.ms_timestart;motion_map.px_delta_x=event_src.clientX-motion_map.px_current_x;motion_map.px_delta_y=event_src.clientY-motion_map.px_current_y;motion_map.px_current_x=event_src.clientX;motion_map.px_current_y=event_src.clientY;motion_map.timeStamp=event_src.timeStamp;if(motion_map.do_allow_tap){if(Math.abs(motion_map.px_delta_x)>option_map.px_radius||Math.abs(motion_map.pd_delta_y)>option_map.px_radius||motion_map.ms_elapsed>option_map.tap_time){motion_map.do_allow_tap=false;}}
if(motion1ZoomId&&motion2ZoomId&&(motion_id===motion1ZoomId||motion_id===motion2ZoomId)){motionMapMap[motion_id]=motion_map;mzoom1_map=motionMapMap[motion1ZoomId];mzoom2_map=motionMapMap[motion2ZoomId];px_pinch_zoom=Math.floor(Math.sqrt(Math.pow((mzoom1_map.px_current_x-mzoom2_map.px_current_x),2)
+Math.pow((mzoom1_map.px_current_y-mzoom2_map.px_current_y),2))+0.5);if(pxPinchZoom===-1){px_delta_zoom=0;}
else{px_delta_zoom=(px_pinch_zoom-pxPinchZoom)*zoomTouchNum;}
pxPinchZoom=px_pinch_zoom;do_zoommove=true;}
else if(motionDzoomId===motion_id){if(bound_ns_map.uzoommove){px_delta_zoom=motion_map.px_delta_y*zoomMouseNum;do_zoommove=true;}}
if(do_zoommove){event_ue=$.Event('uzoommove');motion_map.px_delta_zoom=px_delta_zoom;$.extend(event_ue,motion_map);$(motion_map.elem_bound).trigger(event_ue);return;}
if(motionHeldId===motion_id){if(bound_ns_map.uheldmove){event_ue=$.Event('uheldmove');$.extend(event_ue,motion_map);$(motion_map.elem_bound).trigger(event_ue);event_src.preventDefault();}}
else if(motionDragId===motion_id){if(bound_ns_map.udragmove){event_ue=$.Event('udragmove');$.extend(event_ue,motion_map);$(motion_map.elem_bound).trigger(event_ue);event_src.preventDefault();}}
if(!motionDragId&&!motionHeldId&&bound_ns_map.udragstart&&motion_map.do_allow_tap===false){motionDragId=motion_id;event_ue=$.Event('udragstart');$.extend(event_ue,motion_map);$(motion_map.elem_bound).trigger(event_ue);event_src.preventDefault();if(motion_map.idto_tapheld){clearTimeout(motion_map.idto_tapheld);delete motion_map.idto_tapheld;}}};fnMotionEnd=function(arg_map){var
motion_id=arg_map.motion_id,event_src=arg_map.event_src,do_zoomend=false,motion_map,option_map,bound_ns_map,event_ue;doDisableMouse=false;if(!motionMapMap[motion_id]){return;}
motion_map=motionMapMap[motion_id];option_map=motion_map.option_map;bound_ns_map=option_map.bound_ns_map;motion_map.elem_target=event_src.target;motion_map.ms_elapsed=event_src.timeStamp-motion_map.ms_timestart;motion_map.ms_timestop=event_src.timeStamp;if(motion_map.px_current_x){motion_map.px_delta_x=event_src.clientX-motion_map.px_current_x;motion_map.px_delta_y=event_src.clientY-motion_map.px_current_y;}
motion_map.px_current_x=event_src.clientX;motion_map.px_current_y=event_src.clientY;motion_map.px_end_x=event_src.clientX;motion_map.px_end_y=event_src.clientY;motion_map.timeStamp=event_src.timeStamp;if(motion_map.idto_tapheld){clearTimeout(motion_map.idto_tapheld);delete motion_map.idto_tapheld;}
if(bound_ns_map.utap&&motion_map.ms_elapsed<=option_map.tap_time&&motion_map.do_allow_tap){event_ue=$.Event('utap');$.extend(event_ue,motion_map);$(motion_map.elem_bound).trigger(event_ue);}
if(motion_id===motionDragId){if(bound_ns_map.udragend){event_ue=$.Event('udragend');$.extend(event_ue,motion_map);$(motion_map.elem_bound).trigger(event_ue);event_src.preventDefault();}
motionDragId=undefined;}
if(motion_id===motionHeldId){if(bound_ns_map.uheldend){event_ue=$.Event('uheldend');$.extend(event_ue,motion_map);$(motion_map.elem_bound).trigger(event_ue);}
motionHeldId=undefined;}
if(motion_id===motionDzoomId){do_zoomend=true;motionDzoomId=undefined;}
else if(motion_id===motion1ZoomId){if(motion2ZoomId){motion1ZoomId=motion2ZoomId;motion2ZoomId=undefined;do_zoomend=true;}
else{motion1ZoomId=undefined;}
pxPinchZoom=-1;}
if(motion_id===motion2ZoomId){motion2ZoomId=undefined;pxPinchZoom=-1;do_zoomend=true;}
if(do_zoomend&&bound_ns_map.uzoomend){event_ue=$.Event('uzoomend');motion_map.px_delta_zoom=0;$.extend(event_ue,motion_map);$(motion_map.elem_bound).trigger(event_ue);}
delete motionMapMap[motion_id];};onTouch=function(event){var
this_el=this,timestamp=+new Date(),o_event=event.originalEvent,touch_list=o_event?o_event.changedTouches||[]:[],touch_count=touch_list.length,idx,touch_event,motion_id,handler_fn;doDisableMouse=true;event.timeStamp=timestamp;switch(event.type){case 'touchstart':handler_fn=fnMotionStart;event.preventDefault();break;case 'touchmove':handler_fn=fnMotionMove;break;case 'touchend':case 'touchcancel':handler_fn=fnMotionEnd;break;default:handler_fn=null;}
if(!handler_fn){return;}
for(idx=0;idx<touch_count;idx++){touch_event=touch_list[idx];motion_id='touch'+String(touch_event.identifier);event.clientX=touch_event.clientX;event.clientY=touch_event.clientY;handler_fn({elem:this_el,motion_id:motion_id,event_src:event});}};onMouse=function(event){var
this_el=this,motion_id='mouse'+String(event.button),request_dzoom=false,handler_fn;if(doDisableMouse){event.stopImmediatePropagation();return;}
if(event.shiftKey){request_dzoom=true;}
if(event.type!=='mousemove'){if(event.button!==0){return true;}}
switch(event.type){case 'mousedown':handler_fn=fnMotionStart;event.preventDefault();break;case 'mouseup':handler_fn=fnMotionEnd;break;case 'mousemove':handler_fn=fnMotionMove;break;default:handler_fn=null;}
if(!handler_fn){return;}
handler_fn({elem:this_el,event_src:event,request_dzoom:request_dzoom,motion_id:motion_id});};$Special.ue=$Special.utap=$Special.uheld=$Special.uzoomstart=$Special.uzoommove=$Special.uzoomend=$Special.udragstart=$Special.udragmove=$Special.udragend=$Special.uheldstart=$Special.uheldmove=$Special.uheldend=Ue;$.ueSetGlobalCb=function(selector_str,callback_match,callback_nomatch){callbackList.push({selector_str:selector_str||'',callback_match:callback_match||null,callback_nomatch:callback_nomatch||null});};}(jQuery));