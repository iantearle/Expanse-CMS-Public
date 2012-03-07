/********* Expanse ***********/
/*
Language get function
*/
function _l(constant){
	var lconstant = $('L_JS_'+constant.toUpperCase());
	return lconstant ? lconstant.value : 'Sorry, but you\'re missing a language setting.';
	}
/*
EULA checking
*/
function checkEula(e){
		var eulaAgreed = $('eula_read');
		if(eulaAgreed){
				if(!eulaAgreed.checked){
					alert(_l('EULA'));
					Event.stop(e);
						return false;
					}
				}
				return true;		
		}
//----- Browser check. For use when object detection wont work
function checkIt(string)
{
	var detect = navigator.userAgent.toLowerCase();
	var place = detect.indexOf(string) + 1;
	var thestring = string;
	return place;
}
function toggleClassName(id, class1, class2) {
  if (document.getElementById) {
    var e = $(id);
    if (e) {
      if (e.className !== class1){
			e.className = class1;
		}else{
			e.className = class2; 
		}
    }  
  }
}
function toggleBoxes(theElement) {
	var theForm = theElement.form, z = 0;
	theForm = $A(theForm);
	theForm.each(function(i) {
		if(i.type == 'checkbox' && i.name != 'checkall' && !i.disabled){
		   i.checked = theElement.checked;
		}
	});
}
Element.addMethods({
      insertionBefore: function(element, content) { new Insertion.Before(element, content); },
      insertionAfter:  function(element, content) { new Insertion.After(element, content); },
      insertionTop:    function(element, content) { new Insertion.Top(element, content);  },
      insertionBottom: function(element, content) { new Insertion.Bottom(element, content); }
    });
var toggleBox = {
	init : function(){
		this.itemList = $('itemList');
		if(!this.itemList){return;}
		this.createBox();
		},
	createBox : function(){
		var itemList = this.itemList;
		var clear = document.createElement('br');
		var divGroup = document.createElement('div');
		var label = document.createElement('label');
		var labelT = document.createTextNode(_l('CHECK_BOXES'));
		label.setAttribute('for', 'checkThemAll');
		label.appendChild(labelT);
		
		divGroup.id = 'checkGroup';
		divGroup.setAttribute('class', 'input');
		
		var field = document.createElement('input');
		field.setAttribute('id', 'checkThemAll');
		field.setAttribute('name', 'checkThemAll');
		field.setAttribute('type', 'checkbox');
		field.className = 'checkbox';

		divGroup.appendChild(label);
		label.appendChild(field);

		itemList.parentNode.insertBefore(divGroup,itemList);
		this.assign();
		Event.observe(field,'click', this.run.bindAsEventListener(field));
		},
	run : function(){
		var inputs = document.getElementsByTagName('input');
		var thisBox = this;
		$A(inputs).each(function(i){
			if(i.type == 'checkbox' && i.id != 'checkThemAll' && !i.disabled){
				if(!$('mark_'+i.id)){
					domEl('img','',{src:'images/markedfordeletion.gif','class':'marked', id:'mark_'+i.id}, i.parentNode.parentNode);
				}
				i.checked = thisBox.checked;
				i.parentNode.parentNode.className = (i.checked) ? 'deleting' : '';
			}
		});
	},
	assign : function(){
		var inputs = this.itemList.getElementsByTagName('input');
		$A(inputs).each(function(i){
		if(i.type != 'checkbox' || i.id == 'checkThemAll' || i.disabled){throw $continue;}
		var fade = new fx.Opacity(i.parentNode.parentNode);
		Event.observe(i,'click', function(){
			if(!$('mark_'+i.id)){
			domEl('img','',{src:'images/markedfordeletion.gif','class':'marked', id:'mark_'+i.id}, this.parentNode.parentNode);
			}
			this.parentNode.parentNode.className = this.checked ? 'deleting' : '';
											  }.bindAsEventListener(i));
								 });
		}
	}
var confirmUninstall = {
	init : function (){	
		if($('uninstall')){
			addEvent($('uninstall'), 'click', this.confirmIt);
		}
		if($('delete_uploads')){
			Event.observe($('delete_uploads'), 'click', function(e){
				var deleteUploads = confirm(_l('DELETE_UPLOADS'));
				if(!deleteUploads){
					Event.stop(e);
				}
			});
		}
		if($('delete_db')){
			Event.observe($('delete_db'), 'click', function(e){
				var deleteDB = confirm(_l('DELETE_DB'));
				if(!deleteDB){
					Event.stop(e);
				}
			});
		}
		if($('delete_config')){
			Event.observe($('delete_config'), 'click', function(e) {
				var deleteConfig = confirm(_l('DELETE_CONFIG'));
				if(!deleteConfig){
					Event.stop(e);
				}
			});
		}
	},
	confirmIt : function(e){
			if(!e){var e = window.event;}
			var uninstall = confirm(_l('UNINSTALL'));
			if(!uninstall) {
				return (e.preventDefault) ? e.preventDefault() : e.returnValue = false;
			}
	}
}
function checkAll(obj) {
	if(obj) {
		if(obj.checked) {
			if(!$('noteText')) {
				var pEl = obj.parentNode.parentNode.parentNode;
				var inputs = pEl.getElementsByTagName('input');
				var txt = document.createTextNode(_l('ADMIN_RIGHTS'));
				var sptxt = document.createElement('span');
				sptxt.id = 'noteText';
				sptxt.className = 'formNote';
				sptxt.appendChild(txt);
				alert(obj.nextSibling.id);
				insertAfter(pEl, sptxt, pEL);
				for(var i=0; i<inputs.length;i++) {
					if(inputs[i].type=='checkbox' && inputs[i].id != 'adminCheck') {
						inputs[i].checked = true;
						addEvent(inputs[i], 'click', function(e) {
							if($('adminCheck').checked) {
								var input = (window.event) ? window.event.srcElement : this;
								input.checked = true;
							}						
						});
					}				
				}
			}
		}
		addEvent(obj, 'click', function(){
			if($('disabled').checked == false) {
				var pEl = obj.parentNode.parentNode.parentNode;
				var inputs = pEl.getElementsByTagName('input');
				if(obj.checked){
					if(!$('noteText')){
						var txt = document.createTextNode(_l('ADMIN_RIGHTS'));
						var sptxt = document.createElement('span');
						sptxt.id = 'noteText';
						sptxt.className = 'formNote';
						sptxt.appendChild(txt);
						insertAfter(pEl, sptxt, obj.nextSibling);
					}
					var noteFade = new fx.Opacity($('noteText'), {
						duration: 400
					});
					noteFade.hide();
					noteFade.toggle();
					for(var i=0; i<inputs.length;i++) {
						if(inputs[i].type === 'checkbox' && inputs[i].id != 'adminCheck') {
							inputs[i].checked = true;
							addEvent(inputs[i], 'click', function(e) {
								if($('adminCheck').checked){
									var input = (window.event) ? window.event.srcElement : this;
									input.checked = true;
								}							
							});
						}
						
					}
				} else {
					if(!$('noteText')){
					for(var i=0; i<inputs.length;i++){
					if(inputs[i].type=='checkbox' && inputs[i].id != 'adminCheck'){
					inputs[i].checked = false;
					}
					}
					}
					
					if($('noteText')){
					var noteFade = new fx.Opacity($('noteText'), {duration: 400});
					noteFade.toggle();
					for(var i=0; i<inputs.length;i++){
					if(inputs[i].type=='checkbox' && inputs[i].id != 'adminCheck'){
					inputs[i].checked = false;
					}
					}
					}
				}
			}		
		});
	}
}
function disableBoxes(){
	if($('disabled') && $('categoryBoxes')){
		addEvent($('disabled'), 'click', function(){
			if($('disabled').checked){
				$('categoryBoxes').style.MozOpacity = '0.5';
				$('categoryBoxes').style.opacity = '0.5';
				$('categoryBoxes').style.filter = 'alpha(opacity=50)';
				$('categoryBoxes').style.backgroundColor = '#fff';
			} else {
				$('categoryBoxes').style.MozOpacity = '1.0';
				$('categoryBoxes').style.opacity = '1.0';
				$('categoryBoxes').style.filter = 'alpha(opacity=100)';
				$('categoryBoxes').style.backgroundColor = (document.all) ? '#fff' : 'none';
			}					 
		});
	}
}
function debug(obj, op) {
 	var output = '';
    for (var i in obj)
        output += i+ ' | \n';
    if(!op){
		alert(output);
		} else{
		document.write(output);		
		}
	
}
function insertAfter(parent, node, referenceNode) {
	parent.insertBefore(node, referenceNode.nextSibling);
}

/*
	domEl() function - painless DOM manipulation
	written by Pawel Knapik  //  pawel.saikko.com
*/

var domEl = function(e,c,a,p,x) {
if(e||c) {
	c=(typeof c=='string'||(typeof c=='object'&&!c.length))?[c]:c;	
	e=(!e&&c.length==1)?document.createTextNode(c[0]):e;	
	var n = (typeof e=='string')?document.createElement(e) : !(e&&e===c[0])?e.cloneNode(false):e.cloneNode(true);	
	if(e.nodeType!=3) {
		c[0]===e?c[0]='':'';
		for(var i=0,j=c.length;i<j;i++) typeof c[i]=='string'? ((c[i] =='') ? '': n.appendChild(document.createTextNode(c[i]))):n.appendChild(c[i].cloneNode(true));
		if(a) {
			for (var i in a) i=='class'?n.className=a[i]:(i == 'style')?n.style.cssText=a[i]:n.setAttribute(i,a[i]);}
	}
}
	if(!p)return n;
	p=(typeof p=='object'&&!p.length)?[p]:p;
	for(var i=(p.length-1);i>=0;i--) {
		if(x){while(p[i].firstChild)p[i].removeChild(p[i].firstChild);
			if(!e&&!c&&p[i].parentNode)p[i].parentNode.removeChild(p[i]);}
		if(n){
			if(!document.all){
				p[i].appendChild(n.cloneNode(true));
				} else {
				if(n.canHaveChildren){
					p[i].appendChild(n.cloneNode(true));
				} else if(p[i].canHaveChildren) {
				p[i].appendChild(n.cloneNode(false));			
				} else {
					p[i].parentNode.appendChild(n);
					}
				}
			} 
	}	
}

function setDateFormat(){
	if($('dateformat') && $('timeformat')){
		var dformat = $('dateformat').value;
		var offset = ($F('timeoffset') == '') ? theZone() : $('timeoffset').value;
		xajax_returnDate(dformat,offset);
		var tformat = document.getElementById('timeformat').value;
		xajax_returnTime(tformat,offset);
	}
}
function ToggleAll(e) {
	if (e.checked) {
	    CheckAll();
	}
	else {
	    ClearAll();
	}
}
function CheckAll() {
	var ml = document.edit;
	var len = ml.elements.length;
	for (var i = 0; i < len; i++) {
	    var e = ml.elements[i];
	    eval(e.checked = true);
	}
	ml.toggleAll.checked = true;
}
function ClearAll() {
	var ml = document.edit;
	var len = ml.elements.length;
	for (var i = 0; i < len; i++) {
		var e = ml.elements[i];
		eval(e.checked = false);
	}
	ml.toggleAll.checked = false;
}
function appendInputTypeClasses() {
	if ( !document.getElementsByTagName )
	return;
var inputs = document.getElementsByTagName('input');
var inputLen = inputs.length;
	for ( i=0;i<inputLen;i++ ) {
		if ( inputs[i].getAttribute('type') )
		inputs[i].className += ' '+inputs[i].getAttribute('type');
	}
}
/*
Cookie functions. Get, Set, and Delete
*/
function getCookie( name ) {
	var start = document.cookie.indexOf( name + "=" );
	var len = start + name.length + 1;
	if ( ( !start ) && ( name != document.cookie.substring( 0, name.length ) ) ) {
		return null;
	}
	if ( start == -1 ) return null;
	var end = document.cookie.indexOf( ";", len );
	if ( end == -1 ) end = document.cookie.length;
	return unescape( document.cookie.substring( len, end ) );
}
	
function setCookie( name, value, expires, path, domain, secure ) {
	var today = new Date();
	today.setTime( today.getTime() );
	if ( expires ) {
		expires = expires * 1000 * 60 * 60 * 24;
	}
	var expires_date = new Date( today.getTime() + (expires) );
	document.cookie = name+"="+escape( value ) +
		( ( expires ) ? ";expires="+expires_date.toGMTString() : "" ) + //expires.toGMTString()
		( ( path ) ? ";path=" + path : "" ) +
		( ( domain ) ? ";domain=" + domain : "" ) +
		( ( secure ) ? ";secure" : "" );
}
	
function deleteCookie( name, path, domain ) {
	if ( getCookie( name ) ) document.cookie = name + "=" +
			( ( path ) ? ";path=" + path : "") +
			( ( domain ) ? ";domain=" + domain : "" ) +
			";expires=Thu, 01-Jan-1970 00:00:01 GMT";
}	
//ToolTip code courtesy of http://livsey.org/2005/03/17/help-tips-experiment/ & http://www.squidfingers.com/code/dhtml/
document.getElementsByClassName = function (needle)
{
  var         my_array = document.getElementsByTagName("*");
  var         retvalue = new Array();
  var        i;
  var        j;
  for (i = 0, j = 0; i < my_array.length; i++)
  {
    var c = " " + my_array[i].className + " ";
    if (c.indexOf(" " + needle + " ") != -1)
      retvalue[j++] = my_array[i];
  }
  return retvalue;
}
function addEvent( obj, type, fn ) {
	if(obj){
		if (obj.addEventListener) {
		obj.addEventListener( type, fn, false );
		EventCache.add(obj, type, fn);
	}
	else if (obj.attachEvent) {
		obj["e"+type+fn] = fn;
		obj[type+fn] = function() { obj["e"+type+fn]( window.event ); }
		obj.attachEvent( "on"+type, obj[type+fn] );
		EventCache.add(obj, type, fn);
	}
	else {
		obj["on"+type] = obj["e"+type+fn];
	}
		}
	
}
function removeEvent(obj, evType, fn, useCapture){
  if (obj.removeEventListener){
    obj.removeEventListener(evType, fn, useCapture);
    return true;
  } else if (obj.detachEvent){
    var r = obj.detachEvent("on"+evType, fn);
    return r;
  } else {
    alert("Handler could not be removed");
  }
}	
var EventCache = function(){
	var listEvents = [];
	return {
		listEvents : listEvents,
		add : function(node, sEventName, fHandler){
			listEvents.push(arguments);
		},
		flush : function(){
			var i, item;
			for(i = listEvents.length - 1; i >= 0; i = i - 1){
				item = listEvents[i];
				if(item[0].removeEventListener){
					item[0].removeEventListener(item[1], item[2], item[3]);
				};
				if(item[1].substring(0, 2) != "on"){
					item[1] = "on" + item[1];
				};
				if(item[0].detachEvent){
					item[0].detachEvent(item[1], item[2]);
				};
				item[0][item[1]] = null;
			};
		}
	};
}();
addEvent(window,'unload',EventCache.flush);
function HelpHover()
{
	this._mousePosX = 0;
	this._mousePosY = 0;
	this._hoverItem = null;
	this._hoverContents = null;
}
HelpHover.prototype.init = function()
{
	var hh = this;
	var helpItems = document.getElementsByClassName('hasHelp');
	for (var i=0; i<helpItems.length; i++)
	{
		helpItems[i].onmouseover = function(e)
		{
			if (!e) var e = window.event;
			if (e.pageX || e.pageY)
			{
				hh.mousePosX = e.pageX;
				hh.mousePosY = e.pageY;
			}
			else if (e.clientX || e.clientY)
			{
				
hh.mousePosX = (document.documentElement && document.documentElement.scrollLeft) ? e.clientX + document.documentElement.scrollLeft : e.clientX + document.body.scrollLeft;
hh.mousePosY = (document.documentElement && document.documentElement.scrollTop) ? e.clientY + document.documentElement.scrollTop : e.clientY + document.body.scrollTop;
			}
			hh._hoverItem = this;
			hh._hoverContents = document.getElementById(this.id+'Help');
			hh.move();
		}
		helpItems[i].onmouseout = function (e)
		{
			hh.out();
		}
	}
}
HelpHover.prototype.out = function()
{
	this._hoverContents.style.top = -10000+'px';
	this._hoverContents.style.left = -10000+'px';
	this._hoverItem = null;
	this._hoverContents = null;
	// IE hack because of floating elements over SELECT drop boxes
	if(document.all){
		selects = document.getElementsByTagName('select');
		selects.length.times(function(n){
							selects[n].style.visibility = 'visible';
									  });
		toggleFrame('showSelects');
		}
	//End IE HACK
}
HelpHover.prototype.move = function()
{
	this._hoverContents.style.top = this.mousePosY-70+'px';
	this._hoverContents.style.left = this.mousePosX-278+'px';
	// IE hack because of floating elements over SELECT drop boxes
	if(document.all){
		selects = document.getElementsByTagName('select');
		selects.length.times(function(n){
			selects[n].style.visibility = 'hidden';
									  });
		}
	//End IE HACK
}

function toggleFrame(type){
	if(type == 'hideSelects'){
		if(!$('hideFrame')){
			var objBody = document.getElementsByTagName("body").item(0);
			var arrayPageSize = getPageSize();
	// create overlay div and hardcode some functional styles (aesthetic styles are in CSS file)
			var objOverlay = document.createElement("iframe");
			objOverlay.setAttribute('id','hideFrame');
			objOverlay.style.display = 'block';
			objOverlay.style.height = (arrayPageSize[1] + 'px');
			objOverlay.style.position = 'absolute';
			objOverlay.style.top = '0';
			objOverlay.style.left = '0';
			objOverlay.style.zIndex = '90';
			objOverlay.style.width = (arrayPageSize[0] + 'px');
			objOverlay.style.filter='progid:DXImageTransform.Microsoft.Alpha(style=0,opacity=0)';
			objBody.insertBefore(objOverlay, objBody.firstChild);
			} else {
				$('hideFrame').style.display = 'block';
				}
		} else if(type == 'showSelects'){
			if($('hideFrame')){
				$('hideFrame').style.display = 'none';
				}
			}
	
	}

function theZone(){
   var d, tz, s = "";
   d = new Date();
   tz = d.getTimezoneOffset();
   if (tz < 0)
      s += Math.abs(tz) / 60;
   else if (tz == 0)
      s += "";
   else
      s += '-'+tz / 60;
   return(s);
}
function confirmEula(){
	if($('eula_read')){
		Event.observe(document.forms[0], 'submit', checkEula);
		}
	
	}
function dInit(){
	if($('dateformat') && $('timeformat')){
		addEvent($('dateformat'), 'blur', function(){
			var offset = ($F('timeoffset') == '') ? theZone() : $F('timeoffset');
			var tformat = $F('timeformat');
			var dformat = $F('dateformat');
			xajax_returnDate(dformat,offset);
		});
		addEvent($('timeformat'), 'blur', function(){
			var offset = ($F('timeoffset') == '') ? theZone() : $F('timeoffset');
			var tformat = $F('timeformat');
			var dformat = $F('dateformat');
			xajax_returnTime(tformat,offset);
		});
		addEvent($('timeoffset'), 'blur', function(){
			var offset = ($F('timeoffset') == '') ? theZone() : $F('timeoffset');
			var tformat = $F('timeformat');
			var dformat = $F('dateformat');
			xajax_returnDate(dformat,offset);
			xajax_returnTime(tformat,offset);
		});
		addEvent($('dateformat'), 'keyup', function(){
			var offset = ($F('timeoffset') == '') ? theZone() : $F('timeoffset');
			var tformat = $F('timeformat');
			var dformat = $F('dateformat');
			xajax_returnDate(dformat,offset);
		});
		addEvent($('timeformat'), 'keyup', function(){
			var offset = ($F('timeoffset') == '') ? theZone() : $F('timeoffset');
			var tformat = $F('timeformat');
			var dformat = $F('dateformat');
			xajax_returnTime(tformat,offset);
		});
		addEvent($('timeoffset'), 'keyup', function(){
			var offset = ($F('timeoffset') == '') ? theZone() : $F('timeoffset');
			var tformat = $F('timeformat');
			var dformat = $F('dateformat');
			xajax_returnDate(dformat,offset);
			xajax_returnTime(tformat,offset);
		});
			
		}
	}

function collapseIt(){
var fadeTheBox = new Array;
var collBoxId;
var collSwitch = document.getElementsByClassName("collSwitch");
for(i=0;i<collSwitch.length;i++){
			collBoxId = collSwitch[i].id+'Contents';
			if($(collBoxId)){
			fadeTheBox[collBoxId] = new fx.Combo($(collBoxId), {duration: 400});
			fadeTheBox[collBoxId].hide($(collBoxId), 'height');		
				}
			addEvent(collSwitch[i], 'click', function(e){
				var divId = this.id+'Contents';
				if($(divId)){
					fadeTheBox[divId].toggle('height');
				}
				});
	}
}	
function print_r(theObj){
  if(theObj.constructor == Array ||
     theObj.constructor == Object){
    document.write("<ul>")
    for(var p in theObj){
      if(theObj[p].constructor == Array||
         theObj[p].constructor == Object){
document.write("<li>["+p+"] => "+typeof(theObj)+"</li>");
        document.write("<ul>")
        print_r(theObj[p]);
        document.write("</ul>")
      } else {
document.write("<li>["+p+"] => "+theObj[p]+"</li>");
      }
    }
    document.write("</ul>")
  }
}
var setChecks = {
	checkBoxes : Array,
	init : function(){
		this.checkBoxes = ['online', 'autothumb', 'comments', 'smilies', 'for_sale'];
		this.doCheckBoxes();
		},
	assignRemember : function(){
		var boxes = this.checkBoxes;
		boxes.each(function(box){
			addEvent($(box), 'click', function(){
				setChecks.rememberChecks(this);
			});	
		});
		},
	rememberChecks :  function(obj){
if(obj){
	
		var keepStateID = 'keepState'+obj.id;
		var keepMessage;
		if(obj.checked == true){
			keepMessage = _l('KEEP_CHECKED');
		} else {
			keepMessage = _l('KEEP_UNCHECKED');
		}
		if($(keepStateID)){
			$(keepStateID).parentNode.removeChild($(keepStateID));
			}
		insertAfter(obj.parentNode, domEl('span', domEl('a', keepMessage, {id : keepStateID, href : 'javascript:;', 'class' : 'keepState'}), {id : 'keepStateContainer'+obj.id}), obj);
		Event.observe($(keepStateID), 'mouseover', function(){window.status=keepMessage; return true;});
		Event.observe($(keepStateID), 'mouseout', function(){window.status='';return true;});
		
		
		var keepState = $(keepStateID);
		var opayk = new fx.Opacity(keepState, {duration: 4500});
		opayk.toggle();
		addEvent(keepState, 'click', function(){
				setChecks.setCheckBox(obj);
				opayk.duration = 1600;
				opayk.toggle();
										  });
			}
		},
	setCheckBox : function(obj){
		if(obj.checked == true){
			setCookie(obj.id+'Checked', 'checked');
			} else {
			setCookie(obj.id+'Checked', 'unchecked');
		}
	},
	doCheckBoxes : function(){
		var docloc = document.URL;
		var add = /type=add/;
		var edit = /type=edit/;
		if((docloc.match(add) && !docloc.match(edit))){
			this.assignRemember();
			this.assignGets();
		}	
	
		},
	assignGets : function(){
		this.checkBoxes.each(function(box){
				setChecks.getCheck($(box));
			});
		},
	getCheck : function(obj){
		if(obj){
			var cookie = getCookie(obj.id+'Checked');
			if(cookie !== null){
				if(cookie == 'checked'){
					obj.checked = true;
					} else {
					obj.checked = false;
					}
				}	
		}
		
		}
	}
var reorderMenu = {
	sortContainer : Object,
	beforeContainer : Object,
	draggables : Array,
	init:function(){
		
		if($('keepMenu')){
			this.sortContainer = $('keepMenuContainer');
			this.beforeContainer = $('beforeMenuContainer');
			var itemsCount = this.sortContainer.getElementsByTagName('div').length;
			var reorderL = domEl('a', _l('REORDER_MENU'), {href:'javascript:;',id:'reorderLink',class:'btn primary'});
			var response = domEl('div', '', {id:'responseText'});
			Event.observe(reorderL, 'click',reorderMenu.create);
			Event.observe(reorderL, 'mouseover', function(){window.status=_l('REORDER_MENU'); return true;});
			Event.observe(reorderL, 'mouseout', function(){window.status='';return true;});
			
			this.beforeContainer.parentNode.insertBefore(response, this.beforeContainer);
			this.beforeContainer.parentNode.insertBefore(reorderL, this.beforeContainer);
			var check_or_no = $F('cb_subcats');
			var includeSubcats = '<div class="clearfix"><div class="inputs-list"><label for="include_subcats"><input type="checkbox" id="include_subcats" '+(check_or_no == 'yes' ? 'checked="checked"' : '')+' /><span>'+_l('MB_INCLUDE_SUBCATS')+'</span></label></div></div>';
			$('reorderLink').insertionAfter(includeSubcats);
			var inc_cb = $('include_subcats');
			Event.observe(inc_cb, 'click', reorderMenu.toggleSubcats.bindAsEventListener(inc_cb));
			reorderMenu.primeSubcats();
			}
		},
	create:function(){
		$('reorderLink').childNodes[0].nodeValue = _l('REORDER_FINISHED');
			Event.stopObserving($('reorderLink'), 'click', reorderMenu.create);
			Event.observe($('reorderLink'), 'click', reorderMenu.destroy);
			$('keepMenu', 'excludeMenu').each(function(el){
			 	Element.addClassName(el, 'reorderingMenu');						   
													   });
		Sortable.create('keepMenu',{tag:'div',ghosting:false,constraint:false,hoverclass:'over'});
		Sortable.create('excludeMenu',{tag:'div',ghosting:false,constraint:false,hoverclass:'over'});
		var keepMenu = $A($('keepMenu').getElementsByTagName('div'));
		var excludeMenu = $A($('excludeMenu').getElementsByTagName('div'));
		keepMenu = keepMenu.concat(excludeMenu);
		keepMenu.each(function(el){
		new Draggable(el, {revert:true});						
		});
		Droppables.add('excludeMenu',{accept:'kept',onDrop:reorderMenu.drop,onHover:reorderMenu.hover, hoverclass:'menuHoverTrash'});
		Droppables.add('keepMenu',{accept:'trashed',onDrop:reorderMenu.drop,onHover:reorderMenu.hover, hoverclass:'menuHover'});
		},
	drop : function(el, dropEl){
		if(dropEl.id == 'keepMenu'){
			el.className = el.className.replace('trashed', 'kept');
			}
		else {
			el.className = el.className.replace('kept', 'trashed');
			}
		dropEl.appendChild(el);
		
		},
	hover:function(el, dropEl){return;
		},
	destroy:function(){
		Event.stopObserving($('reorderLink'), 'click', reorderMenu.destroy);
		Event.observe($('reorderLink'), 'click', reorderMenu.create);
		$('reorderLink').childNodes[0].nodeValue = _l('REORDER_MENU');
		reorderMenu.updateOrder();
		Sortable.destroy('keepMenu');
		Sortable.destroy('excludeMenu');
		Droppables.remove('keepMenu');
		Droppables.remove('excludeMenu');
		$('keepMenu', 'excludeMenu').each(function(el){
			 	Element.removeClassName(el, 'reorderingMenu');						   
													   });
		},
	updateOrder:function(){
		var sorted = reorderMenu.serialize();
	 xajax_updateMenuOrder(sorted.sections, 'sections');
	 xajax_updateMenuOrder(sorted.pages, 'items');
		},
	toggleSubcats : function(){
		var subcats = $A(document.getElementsByClassName('sub_cat'));
		if(this.checked){
			xajax_update_option('mb_include_subcats', 1);
			} else {
			xajax_update_option('mb_include_subcats', 0);
			}
		subcats.each(function(el){
				el.toggle();
				});
		},
	primeSubcats : function(){
		var check_or_no = $F('cb_subcats');
		if(check_or_no == 'yes'){return;}
		var subcats = $A(document.getElementsByClassName('sub_cat'));
		subcats.each(function(el){
				Element.hide(el);
				});
		},
	serialize : function(){
		var cats = '', items = '', id, itemsArr = new Array, catsArr = new Array, item_i = 0, cat_i = 0, vis_i = 1;
		var keepMenu = $A($('keepMenu').getElementsByTagName('div'));
		keepMenu.each(function(el,i){
			if(!Element.visible(el)){throw $continue;}
				id = el.id.replace('item_', '');
				if(el.className.indexOf('page') != -1){
					itemsArr[item_i] = 'keepMenu['+vis_i+']='+id;
					item_i++;
					
				} else {
					catsArr[cat_i] = 'keepMenu['+vis_i+']='+id;
					cat_i++;	
			}
			vis_i++;
		});
		items = itemsArr.join('&');
		cats = catsArr.join('&');
		return {sections:cats,pages:items};
		}
	};
var reorder = {
	sortContainer : Object,
	draggables : Array,
	init:function(){
		
		if($('itemList')){
			this.sortContainer = $('itemList');
			var itemsCount = this.sortContainer.getElementsByTagName('div').length;
			if(itemsCount <= 1){
				return;
				}
			var reorderL = domEl('a', _l('REORDER'), {href:'javascript:;',id:'reorderLink'});
			var response = domEl('div', '', {id:'responseText'});
			Event.observe(reorderL, 'click',reorder.create);
			Event.observe(reorderL, 'mouseover', function(){window.status=_l('REORDER'); return true;});
			Event.observe(reorderL, 'mouseout', function(){window.status='';return true;});
			
			this.sortContainer.parentNode.insertBefore(response, this.sortContainer);
			this.sortContainer.parentNode.insertBefore(reorderL, this.sortContainer);
			}
		},
	create:function(){
		if(($('order_by') && $F('order_by') !== 'order_rank')){
			return alert(_l('REORDER_NOTICE'));
			}
		$('reorderLink').childNodes[0].nodeValue = _l('REORDER_FINISHED');
			Event.stopObserving($('reorderLink'), 'click', reorder.create);
			Event.observe($('reorderLink'), 'click', reorder.destroy);
		Element.addClassName(reorder.sortContainer, 'reordering');
		Sortable.create('itemList',{tag:'div',ghosting:false,constraint:false,hoverclass:'over'});
		},
	destroy:function(){
		Event.stopObserving($('reorderLink'), 'click', reorder.destroy);
		Event.observe($('reorderLink'), 'click', reorder.create);
		$('reorderLink').childNodes[0].nodeValue = 'Reorder your items';
		reorder.updateOrder();
		Sortable.destroy('itemList');
		Element.removeClassName(reorder.sortContainer, 'reordering');
		},
	updateOrder:function(){
	 xajax_updateOrder(Sortable.serialize('itemList'));
		}
	};
//
// getPageSize()
// Returns array with page width, height and window width, height
// Core code from - quirksmode.org
// Edit for Firefox by pHaez
//
function getPageSize(){
	
	var xScroll, yScroll;
	
	if (window.innerHeight && window.scrollMaxY) {	
		xScroll = document.body.scrollWidth;
		yScroll = window.innerHeight + window.scrollMaxY;
	} else if (document.body.scrollHeight > document.body.offsetHeight){ // all but Explorer Mac
		xScroll = document.body.scrollWidth;
		yScroll = document.body.scrollHeight;
	} else { // Explorer Mac...would also work in Explorer 6 Strict, Mozilla and Safari
		xScroll = document.body.offsetWidth;
		yScroll = document.body.offsetHeight;
	}
	
	var windowWidth, windowHeight;
	if (self.innerHeight) {	// all except Explorer
		windowWidth = self.innerWidth;
		windowHeight = self.innerHeight;
	} else if (document.documentElement && document.documentElement.clientHeight) { // Explorer 6 Strict Mode
		windowWidth = document.documentElement.clientWidth;
		windowHeight = document.documentElement.clientHeight;
	} else if (document.body) { // other Explorers
		windowWidth = document.body.clientWidth;
		windowHeight = document.body.clientHeight;
	}	
	
	// for small pages with total height less then height of the viewport
	if(yScroll < windowHeight){
		pageHeight = windowHeight;
	} else { 
		pageHeight = yScroll;
	}

	// for small pages with total width less then width of the viewport
	if(xScroll < windowWidth){	
		pageWidth = windowWidth;
	} else {
		pageWidth = xScroll;
	}


	arrayPageSize = new Array(pageWidth,pageHeight,windowWidth,windowHeight) 
	return arrayPageSize;
}
function getPageScroll(){

	var yScroll;

	if (self.pageYOffset) {
		yScroll = self.pageYOffset;
	} else if (document.documentElement && document.documentElement.scrollTop){	 // Explorer 6 Strict
		yScroll = document.documentElement.scrollTop;
	} else if (document.body) {// all other Explorers
		yScroll = document.body.scrollTop;
	}

	arrayPageScroll = new Array('',yScroll) 
	return arrayPageScroll;
}
function createOverlay(){
	if(!$('overlay')){
			var objBody = document.getElementsByTagName("body").item(0);
			var arrayPageSize = getPageSize();
			var objOverlay = domEl('div', ' ', {id:'overlay',style:'display:block;position:absolute;top:0;left:0;z-index:90;width:'+arrayPageSize[0] + 'px;height:'+arrayPageSize[1] + 'px'});
			objBody.insertBefore(objOverlay, objBody.firstChild);
	} else {
	$('overlay').style.display = 'block';
	}
}
function hideSelects(){
	if(document.all){
		var selects = document.getElementsByTagName('select');
		for(i=0;i<selects.length;i++){
				selects[i].className = 'hidden';
			}
		}
	}
function showSelects(){
	if(document.all){
		var selects = document.getElementsByTagName('select');
		for(i=0;i<selects.length;i++){
				selects[i].className = 'formfields';
			}
		}
	}
function makeAlert(){
	button = $('submit');
	if(button){
		var dims = getPageSize();
		var middlePos = getPageScroll()[1] + (getPageSize()[3] / 3)+'px';
		var note = domEl('blockquote', domEl('h4', _l('WAIT_NOTICE')),{id:'overlay','class':'helpContents','style':'position:absolute;top:'+middlePos+';left:40%;'});	
		createOverlay();
		hideSelects();
		$('overlay').className += ' submit';
		button.style.visibility='hidden';
		$('post').appendChild(note);
		button.parentNode.insertBefore(document.createTextNode(_l('WAIT_NOTICE')), button);
	}
}
function loadAlert(){
	if($('submit')){
		Event.observe($('post'), 'submit', makeAlert);
		validate.init();
		}
	}
var resizeEditor = {
	init : function(){
		if($('descr___Frame') || $('descr')){
			this.createText();
			}
		},
	createText : function(){
		var sizerCont = domEl('span', [domEl('a', '+', {href : 'javascript:;', id : 'increaseBox'}), domEl('a', '-', {href : 'javascript:;', id : 'decreaseBox'})], {id : 'sizerContainer'});
		insertAfter($('descr').parentNode, sizerCont, $('descr'));
		Event.observe($('increaseBox'), 'mouseover', function(){window.status=_l('INCREASE_EDITOR'); return true;});
		Event.observe($('increaseBox'), 'mouseout', function(){window.status='';return true;});
		Event.observe($('decreaseBox'), 'mouseover', function(){window.status=_l('DECREASE_EDITOR'); return true;});
		Event.observe($('decreaseBox'), 'mouseout', function(){window.status='';return true;});
		this.addActions();
	},
	addActions : function() {
		var edObj = ($('descr___Frame')) ? $('descr___Frame') : $('descr');
		if($('safari_descr')) {
			edObj = $('safari_descr');
		}
		remDescr = new fx.RememberHeight(edObj, 365, {
			duration: 400
		});
		addEvent($('increaseBox'), 'click', function(){
			remDescr.resize(100);
		});
		addEvent($('decreaseBox'), 'click', function(){
			remDescr.resize(-100);
		});
	}
};

	
var accordion = {
	divs : Array,
	links: Array,
	toggler: Object,
	init : function() {
		this.divs = document.getElementsByClassName('stretch');
		this.links = document.getElementsByClassName('stretchToggle');
		this.run();
	},
	run : function() {
		this.toggler = new fx.Accordion(this.links, this.divs, {opacity: true});
		this.hideDefault();
		this.toggler.showThisHideOpen = function(toShow){
			this.elements.each(function(el, i) {
				if (el.offsetHeight > 0 && el != toShow) this.clearAndToggle(el);
			}.bind(this));
			setTimeout(function(){this.clearAndToggle(toShow);}.bind(this), this.options.delay);
		};
	},
	hideDefault : function() {
		if(this.checkHash()){return;}
		if(this.links.length > 1 || (this.links.length == 1 && this.links[0].id != 'wedge')) {
			this.toggler.showThisHideOpen(this.divs[0]);
		}
	},
	checkHash : function() {
		var found = false;
		this.links.each(function(h3, i){
			if(window.location.href.indexOf(h3.title) > 0) {
				accordion.toggler.showThisHideOpen(accordion.divs[i]);
				found=true;
			}
		});
		return found;
	}
};

		
var hiliteInput = {
	init : function(){
		var fields = document.getElementsByClassName('shareField');
		fields = $A(fields);
		fields.each(function(i){
			Event.observe(i, 'focus', function(){i.select();});
		});
	}
}

/*   Magic Fields - creates, removes, and clears multiple fields w/label   //-------------------------------*/
var magicFields = Base.extend({
	constructor : function(options){
		if(!$(options.firstField)){return;}
		/*   Required   //-------------------------------*/
		this.optField = $(options.firstField);
		this.fieldName = options.fieldName;
		/*   Optional   //-------------------------------*/
		this.incrementLabel = options.incrementLabel || false;
		this.addText = options.addText || _l('ADD_CUSTOM_FIELD');
		this.removeText = options.removeText || _l('REMOVE_CUSTOM_FIELD');
		this.clearText = options.clearText || _l('CLEAR_CUSTOM_FIELD');
		this.labelText = options.labelText || _l('CUSTOM_FIELD');
		this.confirmDelete = options.confirmDelete || _l('CLEAR_CONFIRM_CUSTOM_FIELD');
		/*----//----*/
		this.optGroup = this.optField.parentNode.parentNode.parentNode.parentNode.parentNode;
		this.fieldCount = this.optGroup.getElementsByTagName('input').length;
		this.addLinkID = 'addLink_'+this.fieldName;
		this.removeLinkID = 'removeLink_'+this.fieldName;
		this.resetLinkID = 'resetLink_'+this.fieldName;
		this.run();
	},
	run : function(){
		domEl('a', this.addText, {href : 'javascript:;', id : this.addLinkID, class: 'linky btn'}, this.optGroup);
		domEl('a', this.removeText, {href : 'javascript:;', id : this.removeLinkID, title: this.optGroup.id, class: 'linky btn info'}, this.optGroup);
		domEl('a', this.clearText, {href : 'javascript:;', id : this.resetLinkID, class: 'linky btn danger'}, this.optGroup);
		var addLink = $(this.addLinkID);
		var removeLink = $(this.removeLinkID);
		var resetLink = $(this.resetLinkID);
		Event.observe(addLink, 'mouseover', function(){window.status=this.addText; return true;});
		Event.observe(addLink, 'mouseout', function(){window.status='';return true;});
		Event.observe(removeLink, 'mouseover', function(){window.status=this.removeText; return true;});
		Event.observe(removeLink, 'mouseout', function(){window.status='';return true;});
		Event.observe(resetLink, 'mouseover', function(){window.status=this.clearText; return true;});
		Event.observe(resetLink, 'mouseout', function(){window.status='';return true;});
		Event.observe(addLink, 'click', this.addField.bindAsEventListener(this));	
		Event.observe(removeLink, 'click', this.removeFields.bindAsEventListener(this));	
		Event.observe(resetLink, 'click', this.resetFields.bindAsEventListener(this));	
	},
	addField : function(){
		var fieldCount = this.countFields()+1;
		var optID = 'new_cat'+fieldCount;
		var incrementor = this.incrementLabel == true ? ' '+fieldCount : '';
		var divHTML = '<div id="'+optID+'Group" class"row"><div class="span6"><div class="clearfix"><label for="'+optID+'">'+this.labelText+incrementor+'</label><div class="input"><input type="text" value="" name="'+this.fieldName+'[]" id="'+optID+'" class="text" /></div></div></div></div>';
		$(this.addLinkID).insertionBefore(divHTML);
	},
	removeFields : function(){
		this.fieldCount = this.countFields();
		var fieldset = this.optGroup;
		var olddiv = document.getElementById(this.optGroup.id+this.fieldCount+'Group');
		fieldset.removeChild(olddiv);
		
	},
	resetFields : function(){
		this.fieldCount = this.countFields();
		if(this.fieldCount < 2){return;}
		var resetConf = confirm(this.confirmDelete);
		if(resetConf){
			var fieldset = this.optGroup;
			var divs = fieldset.getElementsByTagName('div');
			divs.length.times(function(n){
				fieldset.removeChild(divs[0]);
			});
			this.optField.value = '';
		}
	},
	countFields : function(){
		this.Fields = this.optGroup.getElementsByTagName('input');
		return this.fieldCount = this.Fields.length;
	}
});
/*   Multiple Upload Fields   //-------------------------------*/

var magicSubs = magicFields.extend({
	addField : function(){
		var fieldCount = this.countFields()+1;
		var optID = 'addSubcats'+fieldCount;
		var incrementor = this.incrementLabel == true ? ' '+fieldCount : '';
		this.labelDescrText = _l('SUBCAT_DESCR');
		var divHTML = '<div id="'+optID+'Group" class="row"><div class="span6"><div class="clearfix"><label for="'+optID+'">'+this.labelText+incrementor+'</label><div class="input"><input type="text" value="" name="'+this.fieldName+'[]" id="'+optID+'" class="text" /></div></div><div class="clearfix"><label for="'+optID+'">'+this.labelDescrText+incrementor+'</label><div class="input"><textarea name="'+this.fieldName+'_descr[]"></textarea></div></div>';
		$(this.addLinkID).insertionBefore(divHTML);
	}
});
/*   Multiple Upload Fields   //-------------------------------*/
var magicUploads = magicFields.extend({
	addField : function(){
		var fieldCount = this.countFields()+1;
		var optID = this.fieldName+fieldCount;
		var incrementor = this.incrementLabel == true ? ' '+fieldCount : '';
		var divHTML = '<div id="'+optID+'Group" class="row">'
					+'<div class="span8">'
					+'<div class="clearfix">'
					+'<label for="'+optID+'">'+this.labelText+incrementor+'</label>'
					+'<div class="input">'
					+'<input type="file" name="'+this.fieldName+fieldCount+'"  id="'+optID+'"  class="formfields file" />'
					+'</div>'
					+'</div>'
					+'<div class="clearfix">'
					+'<label for="'+optID+'">Caption'+incrementor+'</label>'
					+'<div class="input">'
					+'<textarea name="caption['+optID+']" class="caption" id="'+optID+'"></textarea>'
					+'</div>'
					+'</div>'
					+'</div>'
					+'</div>';
		$(this.addLinkID).insertionBefore(divHTML);	
	}	
});
/*  Magic Custom Fields   //-------------------------------*/
var magicCustom = magicFields.extend({
	constructor : function(options){
		if(!$('customList')){return;}
		this.base(options);
		this.customLabels = eval($F('customList'));
		this.initialize();
	},
	run : function(){
		this.optGroup = this.optField.parentNode.parentNode.parentNode.parentNode.parentNode;
		this.base();
	},
	initialize : function(){
		this.labelInnerText = _l('CUSTOM_LABEL_TEXT');
		this.fieldInnerText = _l('CUSTOM_FIELD_TEXT');
		var label, field, customVar, num = 0;
		$$('#customGroup div').each(function(obj){
			num++;
			Element.cleanWhitespace(obj);
			label = document.getElementById('customLabel'+num);
			if(!label) { return; }
			var labelvalue = jQuery(label).val();
			(labelvalue == '') ? this.labelInnerText : jQuery(label).val();
			label.className += ' fieldLabel';
			field = document.getElementById('customValue'+num);
			customVar = document.getElementById('customVar'+num);
			this.toggleValue(this.labelInnerText, label);
			field.value = (field.value == '') ? this.fieldInnerText : field.value;
			field.className += ' fieldValue';
			this.toggleValue(this.fieldInnerText, field);
			this.makeCustomVar(customVar,label);
			if($('increaseBox'+num)){throw $continue;}
			this.insertSizers(field, num);
			new AutoSuggest(label,this.customLabels);
		}.bind(this));
	},
	addField : function(){
		var fieldCount = this.countFields()+1;
		var optID = this.fieldName+fieldCount;
		var incrementor = this.incrementLabel == true ? ' '+fieldCount : '';
				/*--*/
		var customLabelTxt = this.labelInnerText;
		var customLabelID = 'customLabel'+fieldCount;
		var customValueID = 'customValue'+fieldCount;
		var customVarID = 'customVar'+fieldCount;
		var customValueTxt = this.fieldInnerText;
		var divHTML = 
		'<div id="'+optID+'Group" class="row customLabelGroup">'
		+'<div class="span8">'
		+'<div class="clearfix">'
		+'<div class="input">'
		+'<input type="text" value="'+customLabelTxt+'" class="span6 fieldLabel text" id="'+customLabelID+'" name="custom['+fieldCount+'][label]" autocomplete="off" />'
		+'</div>'
		+'</div>'
		+'<div class="clearfix">'
		+'<div class="input">'
		+'<textarea id="'+customValueID+'" name="custom['+fieldCount+'][value]" class="span6 fieldValue">'+customValueTxt+'</textarea>'
		+'</div>'
		+'</div>'
		+'<div class="clearfix">'
		+'<label for="'+customVarID+'">'+_l('CUSTOM_VARIABLE_TEXT')+'</label>'
		+'<div class="input">'
		+'<input type="text" class="shareField variableField uneditable-input" id="'+customVarID+'">'
		+'</div>'
		+'</div>'
		+'</div>'
		+'</div>'
		/*
		var divHTML = '<div id="'+optID+'Group" class="customFieldGroup overflowhidden">'
					+'<input type="text" value="'+customLabelTxt+'" class="fieldLabel text" id="'+customLabelID+'" name="custom['+fieldCount+'][label]" />'
					+'<textarea id="'+customValueID+'" name="custom['+fieldCount+'][value]" class="fieldValue">'
					+customValueTxt
					+'</textarea><br />'
					+'<label for="'+customVarID+'">'+_l('CUSTOM_VARIABLE_TEXT')+'</label><input type="text" class="text shareField variableField" id="'+customVarID+'"><br /><br />'
					+'</div>';
		*/	
		$(this.addLinkID).insertionBefore(divHTML);
		var label = $(customLabelID);
		var field = $(customValueID);
		var customVar = $(customVarID);
		this.toggleValue(customLabelTxt, label);
		this.toggleValue(customValueTxt, field);
		this.makeCustomVar(customVar,label);
		this.insertSizers(field,fieldCount);
		new AutoSuggest(label,this.customLabels);
	},
	resetFields : function(){
		if(this.fieldCount <= 1){return;}
		this.base();
		this.initialize();
		this.addField();
		},
	removeGroup : function(obj){
		this.parentNode.removeChild(this);
		if(obj.countFields() == 0){obj.addField();}
		},
	toggleValue : function(origValue, obj){
		Event.observe(obj, 'focus', function(){
		this.value = (this.value == origValue) ? '' : this.value;
		}.bindAsEventListener(obj));
		Event.observe(obj, 'blur', function(){
		this.value = (this.value == '') ? origValue : this.value;
		}.bindAsEventListener(obj));
		},
	countFields : function(){
		this.Fields = jQuery('.customLabelGroup').size();
		return this.fieldCount = this.Fields;
	},
	makeCustomVar : function(customVar,label){
		Event.observe(label, 'keyup', this.assembleVar.bind(label,customVar, this));
		Event.observe(label, 'blur', this.assembleVar.bind(label,customVar,this));
		Event.observe(label, 'focus', this.assembleVar.bind(label,customVar, this));
		Event.observe(customVar, 'keypress', this.preventCustomMake);
		},
	assembleVar : function(customVar, obj){
		var nlabel;
		nlabel = (this.value == obj.labelInnerText || this.value == '') ? '' : '{'+'custom_var'+this.id.replace(/[^0-9]/gi,'')+'}';
		customVar.value = nlabel;
		},
	varSelect : function(customVar){
		Event.observe(customVar, 'focus', function(){this.select();}.bind(customVar));
		},
	preventCustomMake : function(e){
		Event.stop(e);
		},
	insertSizers : function(obj, num){
		var sizerCont = '<span class="sizerContainer" id="customValue'+obj.id+'">'
						+'<a href="javascript:;" id="increaseBox'+num+'">+</a>'
						+'<a href="javascript:;" id="decreaseBox'+num+'">-</a>'
						+'<a href="javascript:;" title="'+_l('CUSTOM_DELETE_FIELD')+'" id="deleteLink_'+num+'" class="deleteLink">'+_l('CUSTOM_DELETE_FIELD')+'</a>'
						+'</span>';
		
		obj.insertionAfter(sizerCont);
		var increaseBox = $('increaseBox'+num);
		var decreaseBox = $('decreaseBox'+num);
		Event.observe(increaseBox, 'mouseover', function(){window.status=_l('INCREASE_FIELD_SIZE'); return true;});
		Event.observe(increaseBox, 'mouseout', function(){window.status='';return true;});
		Event.observe(decreaseBox, 'mouseover', function(){window.status=_l('DECREASE_FIELD_SIZE'); return true;});
		Event.observe(decreaseBox, 'mouseout', function(){window.status='';return true;});
		if(typeof(fx) == 'undefined'){fx = false;}
		if(!fx){
			var remHeight = {
				init : function(obj){
					this.el = obj;
					this.height = this.el.offsetHeight; 
					this.el.style.height = this.height+'px';
					return this;
				},
				resize : function(size){
					this.height = this.el.offsetHeight; 
					var calcHeight = (this.height + size);
					if(calcHeight < 60 || calcHeight > 300){return;}
					this.el.style.height = calcHeight+'px';
				}
			}.init(obj);
		} else {
			var remHeight = new fx.RememberHeight(obj, 365, {duration: 400});	
		}
		Event.observe(increaseBox, 'click', function(){
			remHeight.resize(100);
								 });
		Event.observe(decreaseBox, 'click', function(){
			if(remHeight.el.offsetHeight < 60){return;}
			remHeight.resize(-100);
								 });
		var deleteLink = $('deleteLink_'+num);
		var optID = this.fieldName+num;
		Event.observe(deleteLink, 'click', this.removeGroup.bind($(optID+'Group'), this));
	}
});
var validate = {
	init : function(){
		var theform = $('post');
		if(theform){
			Event.observe(theform, 'submit', this.validateFields);
		}
		
	},
	validateFields : function(e){
		if($('confirmpassword') && !$('edit_user')){
			if($F('username') == '' || $F('password') == ''){
			alert(_l('ENTER_USER_DETAILS'));
			Event.stopObserving($('post'), 'submit', makeAlert);
			return window.event ? event.returnValue = false : e.preventDefault();
			}
		}
		
		
		}
	
	};
var createFooter = {
	init : function(){
		this.mainContainer = $('mainContainer');
		if(this.mainContainer){
			this.createElements();
			}
		},
	createElements : function(){
		domEl('div', '', {id : 'footerBottom'}, this.mainContainer);
		$('footerBottom').appendChild($('footer'))
		}
	};
var markDelete = {
	boxes : Array,
	init : function(){
		this.boxes = $A(document.getElementsByClassName('xtraImgDelete',$('extraImages')));
		this.boxes.each(function(i){
								 
			addEvent(i,'click', function(){
										 var fade = new fx.Opacity(i.parentNode);
										 markDelete.mark(i, fade);
										 });
			});
		},
	mark : function(i, fade){
			if(i.checked){
				fade.custom(1, 0.5);
				} else{
					fade.custom(0.5, 1);
					}
			
		}
	};


var Bookmark = {
	init : function(){
		if(!$('content') || $('content').className != 'login' || !$('forgotLink')) {return;}
		if (!window.external && !window.sidebar) { return;}
		var bookmarkText = (window.sidebar) ? _l('BOOKMARK_FF') : _l('BOOKMARK_IE');
		var bookmarkLink = domEl('a', bookmarkText, {href:'javascript:;', id: 'bookmarkLink'});
		insertAfter($('forgotLink').parentNode, bookmarkLink, $('forgotLink'));
		Event.observe(bookmarkLink, 'mouseover', function(){window.status=bookmarkText; return true;});
		Event.observe(bookmarkLink, 'mouseout', function(){window.status='';return true;});
		Event.observe($('bookmarkLink'), 'click', this.run);
		},
	run : function(){
			var title = document.title;   
			var url = window.location.href;  

			if (window.sidebar) { 
			//Firefox	
				window.sidebar.addPanel(title, url,"");	
			} else if( window.external ) { 
			// IE
				window.external.AddFavorite( url, title); 
			}
		}
	};
var sortCats = {
	init : function(){
		var sort_submit = $('sort_submit');
		if(!sort_submit){return;}
		Event.observe(sort_submit, 'click', this.changeMethod)
		},
	changeMethod : function(){
		$('post').setAttribute('method', 'get');
		}
	}
var page = {
	init:function(){
		var hh = new HelpHover();
		hh.init();
		markDelete.init();
		accordion.init();
		createFooter.init();
		setChecks.init();
		confirmEula();
		hiliteInput.init();
		reorder.init();
		reorderMenu.init();
		appendInputTypeClasses();
		confirmUninstall.init();
		Bookmark.init();
		toggleBox.init();
		sortCats.init();
		/*   Custom fields objects   //-------------------------------*/
		var addSubs = new magicSubs({ 
			firstField: 'new_cat1',
			addText:_l('ADD_SUBCAT'),
			clearText:_l('CLEAR_SUBCAT'),
			removeText:_l('REMOVE_SUBCAT'),
			labelText: _l('SUBCAT_LABEL'), 
			fieldName : 'new_cat', 
			confirmDelete : _l('SUBCAT_CLEAR_CONFIRM')
		});
		var addOptions = new magicFields({ 
			firstField: 'option1',
			fieldName : 'extraoptions',
			addText:_l('ADD_OPTION'),
			clearText:_l('CLEAR_OPTION'),
			removeText:_l('REMOVE_OPTION'),
			labelText: _l('OPTION_LABEL'), 
			confirmDelete : _l('OPTION_CLEAR_CONFIRM'),
			incrementLabel : true
		});
		var addImages = new magicUploads({ 
			firstField: 'additional_images1',
			fieldName : 'additional_images',
			addText:_l('ADD_IMAGE'),
			clearText:_l('CLEAR_IMAGE'),
			removeText:_l('REMOVE_IMAGE'),
			labelText: ($('additionalImages') && $('additionalImages').className == 'addFiles') ? _l('IMAGE_LABEL_FILE') : _l('IMAGE_LABEL'), 
			confirmDelete : _l('IMAGE_CLEAR_CONFIRM'),
			incrementLabel : true
		});
		
		var addCustom = new magicCustom({ 
			firstField: 'customLabel1',
			fieldName : 'customLabel',
			incrementLabel : true
		});
		/*--*/
		
		if($('toggleBox')){
			addEvent($('toggleBox'),'click',function(){
				toggleBoxes($('toggleBox'));
			});
			}
		if($('dateformat')){
			setDateFormat();
			dInit();
		}
		if($('descr')){
			FCKeditor.ReplaceAllTextareas('descr');	
		}
		
		if($('timeoffset') && $F('timeoffset') == ''){
			$('timeoffset').value = theZone();
		}
		resizeEditor.init();
		loadAlert();
		checkAll($('adminCheck'));
		disableBoxes();
		if(window.expanse){
			expanse();
		}
	}
};	

	
	Event.onDOMReady(page.init);
$.noConflict();
jQuery(document).ready(function () {
});