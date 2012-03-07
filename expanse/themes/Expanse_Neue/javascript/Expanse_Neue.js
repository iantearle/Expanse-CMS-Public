// default.js Theme specific Javascript

var rememberForm = {
	userInfo : Array,
	
	init : function(){
		this.userInfo = new Array('name', 'email', 'url');
		if($('commentform')){
			addEvent($('commentform'), 'submit', this.recordMe);
			this.rememberMe();
			}
		if($('contactform')){
			addEvent($('contactform'), 'submit', this.recordMe);
			this.rememberMe();
			}
	},
	recordMe : function(){
		if($('rememberinfo').checked){
			for(var i=0; i<rememberForm.userInfo.length; i++){
				setCookie(rememberForm.userInfo[i], $(rememberForm.userInfo[i]).value);
				}
		}
	},
	rememberMe : function(){
			var userinfo;
			for(var i=0; i<this.userInfo.length; i++){
				userinfo = getCookie(this.userInfo[i]);
				if(userinfo != null){
					$(this.userInfo[i]).value = getCookie(this.userInfo[i]);
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

function pageLoaders(){
	rememberForm.init();
	createFooter.init();
	appendInputTypeClasses();
	}	
addEvent(window, 'load', pageLoaders);

// Common Javascript functions

/*
Adds the type of the input to the className.
*/
function appendInputTypeClasses() {
	if ( !document.getElementsByTagName )
	return;
var inputs = document.getElementsByTagName('input');
var inputLen = inputs.length;
var type;
	for ( i=0;i<inputLen;i++ ) {
		type = (inputs[i].getAttribute('type') == null) ? 'text': inputs[i].getAttribute('type');
		inputs[i].className += ' '+type;
	}
}
/*
Typical addEvent function
*/
function addEvent(elm, evType, fn, useCapture) {
	if (elm.addEventListener) {
		elm.addEventListener(evType, fn, useCapture);
		return true;
	}
	else if (elm.attachEvent) {
		var r = elm.attachEvent('on' + evType, fn);
		return r;
	}
	else {
		elm['on' + evType] = fn;
	}
}
/*
Extends DOM core functionality by getting an array of Elements by className
*/
function getElementsByClass(searchClass,node,tag) {
	var classElements = new Array();
	if ( node == null )
		node = document;
	if ( tag == null )
		tag = '*';
	var els = node.getElementsByTagName(tag);
	var elsLen = els.length;
	var pattern = new RegExp("(^|\\s)"+searchClass+"(\\s|$)");
	for (i = 0, j = 0; i < elsLen; i++) {
		if ( pattern.test(els[i].className) ) {
			classElements[j] = els[i];
			j++;
		}
	}
	return classElements;
}
/*
Extends DOM core functionality by inserting after a node
*/
function insertAfter(parent, node, referenceNode) {
	parent.insertBefore(node, referenceNode.nextSibling);
}
Array.prototype.inArray = function (value) {
	var i;
	for (i=0; i < this.length; i++) {
		if (this[i] === value) {
			return true;
		}
	}
	return false;
};
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
/*
	domEl() function - painless DOM manipulation
	written by Pawel Knapik  //  pawel.saikko.com
	
	With contributions and modifications by Nate Cavanaugh // alterform.com
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
