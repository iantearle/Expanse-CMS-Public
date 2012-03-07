Object.extend(Event, {
  _domReady : function() {
    if (arguments.callee.done) return;
    arguments.callee.done = true;

    if (this._timer)  clearInterval(this._timer);
    
    this._readyCallbacks.each(function(f) { f() });
    this._readyCallbacks = null;
},
  onDOMReady : function(f) {
    if (!this._readyCallbacks) {
      var domReady = this._domReady.bind(this);
      
      if (document.addEventListener)
        document.addEventListener("DOMContentLoaded", domReady, false);
        
        /*@cc_on @*/
        /*@if (@_win32)
            document.write("<script id=__ie_onload defer src=javascript:void(0)><\/script>");
            document.getElementById("__ie_onload").onreadystatechange = function() {
                if (this.readyState == "complete") domReady(); 
            };
        /*@end @*/
        
        if (/WebKit/i.test(navigator.userAgent)) { 
          this._timer = setInterval(function() {
            if (/loaded|complete/.test(document.readyState)) domReady(); 
          }, 10);
        }
        
        Event.observe(window, 'load', domReady);
        Event._readyCallbacks =  [];
    }
    Event._readyCallbacks.push(f);
  }
});
/*
	Base, version 1.0.2
	Copyright 2006, Dean Edwards
	License: http://creativecommons.org/licenses/LGPL/2.1/
*/

function Base(){};Base.version="1.0.2";Base.prototype={extend:function(s,v){var e=Base.prototype.extend;if(arguments.length==2){var a=this[s];if((a instanceof Function)&&(v instanceof Function)&&a.valueOf()!=v.valueOf()&&/\bbase\b/.test(v)){var m=v;v=function(){var p=this.base;this.base=a;var r=m.apply(this,arguments);this.base=p;return r};v.valueOf=function(){return m};v.toString=function(){return String(m)}}return this[s]=v}else if(s){var p={toSource:null};var x=["toString","valueOf"];if(Base._)x[2]="constructor";for(var i=0;(n=x[i]);i++){if(s[n]!=p[n]){e.call(this,n,s[n])}}for(var n in s){if(!p[n]){e.call(this,n,s[n])}}}return this},base:function(){}};Base.extend=function(i,s){var e=Base.prototype.extend;if(!i)i={};Base._=1;var p=new this;e.call(p,i);var constructor=p.constructor;p.constructor=this;delete Base._;var k=function(){if(!Base._)constructor.apply(this,arguments);this.constructor=k};k.prototype=p;k.extend=this.extend;k.toString=function(){return String(constructor)};e.call(k,s);var o=constructor?k:p;if(o.init instanceof Function)o.init();return o};
