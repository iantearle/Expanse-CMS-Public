/**************************************************
 * dom-drag.js
 * 09.25.2001
 * www.youngpup.net
 **************************************************
 * 10.28.2001 - fixed minor bug where events
 * sometimes fired off the handle, not the root.
 **************************************************/

var Drag = {

    obj : null,

    init : function(o, oRoot, minX, maxX, minY, maxY, bSwapHorzRef, bSwapVertRef, fXMapper, fYMapper)
    {
        o.onmousedown    = Drag.start;
		//addEvent(o, 'mousedown', Drag.start);

        o.hmode            = bSwapHorzRef ? false : true ;
        o.vmode            = bSwapVertRef ? false : true ;

        o.root = oRoot && oRoot != null ? oRoot : o ;

        if (o.hmode  && isNaN(parseInt(o.root.style.left  ))) o.root.style.left   = "0px";
        if (o.vmode  && isNaN(parseInt(o.root.style.top   ))) o.root.style.top    = "0px";
        if (!o.hmode && isNaN(parseInt(o.root.style.right ))) o.root.style.right  = "0px";
        if (!o.vmode && isNaN(parseInt(o.root.style.bottom))) o.root.style.bottom = "0px";

        o.minX    = typeof minX != 'undefined' ? minX : null;
        o.minY    = typeof minY != 'undefined' ? minY : null;
        o.maxX    = typeof maxX != 'undefined' ? maxX : null;
        o.maxY    = typeof maxY != 'undefined' ? maxY : null;

        o.xMapper = fXMapper ? fXMapper : null;
        o.yMapper = fYMapper ? fYMapper : null;

        o.root.onDragStart    = new Function();
        o.root.onDragEnd    = new Function();
        o.root.onDrag        = new Function();
    },

    start : function(e)
    {
        var o = Drag.obj = this;
        e = Drag.fixE(e);
        var y = parseInt(o.vmode ? o.root.style.top  : o.root.style.bottom);
        var x = parseInt(o.hmode ? o.root.style.left : o.root.style.right );
        o.root.onDragStart(x, y);

        o.lastMouseX    = e.clientX;
        o.lastMouseY    = e.clientY;

        if (o.hmode) {
            if (o.minX != null)    o.minMouseX    = e.clientX - x + o.minX;
            if (o.maxX != null)    o.maxMouseX    = o.minMouseX + o.maxX - o.minX;
        } else {
            if (o.minX != null) o.maxMouseX = -o.minX + e.clientX + x;
            if (o.maxX != null) o.minMouseX = -o.maxX + e.clientX + x;
        }

        if (o.vmode) {
            if (o.minY != null)    o.minMouseY    = e.clientY - y + o.minY;
            if (o.maxY != null)    o.maxMouseY    = o.minMouseY + o.maxY - o.minY;
        } else {
            if (o.minY != null) o.maxMouseY = -o.minY + e.clientY + y;
            if (o.maxY != null) o.minMouseY = -o.maxY + e.clientY + y;
        }

        document.onmousemove    = Drag.drag;
        document.onmouseup        = Drag.end;

        return false;
    },

    drag : function(e)
    {
        e = Drag.fixE(e);
        var o = Drag.obj;

        var ey    = e.clientY;
        var ex    = e.clientX;
        var y = parseInt(o.vmode ? o.root.style.top  : o.root.style.bottom);
        var x = parseInt(o.hmode ? o.root.style.left : o.root.style.right );
        var nx, ny;

        if (o.minX != null) ex = o.hmode ? Math.max(ex, o.minMouseX) : Math.min(ex, o.maxMouseX);
        if (o.maxX != null) ex = o.hmode ? Math.min(ex, o.maxMouseX) : Math.max(ex, o.minMouseX);
        if (o.minY != null) ey = o.vmode ? Math.max(ey, o.minMouseY) : Math.min(ey, o.maxMouseY);
        if (o.maxY != null) ey = o.vmode ? Math.min(ey, o.maxMouseY) : Math.max(ey, o.minMouseY);

        nx = x + ((ex - o.lastMouseX) * (o.hmode ? 1 : -1));
        ny = y + ((ey - o.lastMouseY) * (o.vmode ? 1 : -1));

        if (o.xMapper)        nx = o.xMapper(y);
        else if (o.yMapper)    ny = o.yMapper(x);

        Drag.obj.root.style[o.hmode ? "left" : "right"] = nx + "px";
        Drag.obj.root.style[o.vmode ? "top" : "bottom"] = ny + "px";
        Drag.obj.lastMouseX    = ex;
        Drag.obj.lastMouseY    = ey;

        Drag.obj.root.onDrag(nx, ny);
        return false;
    },

    end : function()
    {
        document.onmousemove = null;
        document.onmouseup   = null;
        Drag.obj.root.onDragEnd(    parseInt(Drag.obj.root.style[Drag.obj.hmode ? "left" : "right"]),
                                    parseInt(Drag.obj.root.style[Drag.obj.vmode ? "top" : "bottom"]));
        Drag.obj = null;
    },

    fixE : function(e)
    {
        if (typeof e == 'undefined') e = window.event;
        if (typeof e.layerX == 'undefined') e.layerX = e.offsetX;
        if (typeof e.layerY == 'undefined') e.layerY = e.offsetY;
        return e;
    }
};
////////////////////////////////////////////////////////////////////
var isIE = (document.all)? true:false;
var minHeight=40, minWidth=40;

var canvasEl, imageEl, dragBox;
var oldX, oldY;
var mouseMoved;
var canvasImgId = 'canvasImg';
function setMessages(){
saveMessage = _l('CROP_SAVE');
discardMessage = _l('CROP_DISCARD');
resetMessage = _l('CROP_RESET');
	}


function statMess(txt){
	window.status = txt;
	return true;
	}
function rememberCrop(){

	var crop_x = $('crop_x').value;
	var crop_y = $('crop_y').value;
	var thumb_w =  $('thumb_w').value;
	var thumb_h =  $('thumb_h').value;
	var dragBox = $("dragBox");

	var x = crop_x;
	var y = crop_y;
	var w = thumb_w;
	var h = thumb_h;

	dragBox.style.display = "block";
	dragBox.style.width = w + "px";
	dragBox.style.height = h + "px";
	dragBox.style.left = x + "px";
	dragBox.style.top =  y + "px";

	}
function rememberScale(){

	var thumb_max =  $('thumb_max').value;
	var sHandle = $("handle");
	var scaleSize = parseInt(($('thumb_max').value/$('thumb_w').value)*194);

	sHandle.style.position = "relative";
	sHandle.style.left = scaleSize+"px";

	}
function mouseDown(e) {
	if (!e) var e = window.event;
			if (e.pageX || e.pageY)
			{
				mousePosX = e.pageX;
				mousePosY = e.pageY;
			}
			else if (e.clientX || e.clientY)
			{

mousePosX = (document.documentElement && document.documentElement.scrollLeft) ? e.clientX + document.documentElement.scrollLeft : e.clientX + document.body.scrollLeft;
mousePosY = (document.documentElement && document.documentElement.scrollTop) ? e.clientY + document.documentElement.scrollTop : e.clientY + document.body.scrollTop;
			}
	var targetEl = (isIE)? e.srcElement : e.target;

	while (targetEl.id != canvasImgId) {
		targetEl = targetEl.parentNode;
		if (targetEl == null) { return }
	}

	if (targetEl.id==canvasImgId) {
		canvasEl = $("canvas");
		imageEl = $(canvasImgId);
		dragBox = $("dragBox");
		oldX = mousePosX;
		oldY = mousePosY;

		mouseMoved=false;
	}
	else
		imageEl == null;

	if (isIE)
		e.returnValue = false;
	else
		e.preventDefault();
}

function mouseMove(e) {
	if (!e) var e = window.event;
	if (imageEl == null) { return };
			if (e.pageX || e.pageY)
			{
				mousePosX = e.pageX;
				mousePosY = e.pageY;
			}
			else if (e.clientX || e.clientY)
			{

mousePosX = (document.documentElement && document.documentElement.scrollLeft) ? e.clientX + document.documentElement.scrollLeft : e.clientX + document.body.scrollLeft;
mousePosY = (document.documentElement && document.documentElement.scrollTop) ? e.clientY + document.documentElement.scrollTop : e.clientY + document.body.scrollTop;
			}
	mouseMoved = true;
	var imageW = parseInt(imageEl.width, 10 );
	var imageH = parseInt(imageEl.height, 10 );

	var newX = mousePosX;
	var newY = mousePosY;
	if(e.shiftKey){
			//newY = newX;
			}
	var x = oldX+canvasEl.scrollLeft-parseInt(canvasEl.style.left,10);
	var y = oldY+canvasEl.scrollTop-parseInt(canvasEl.style.top,10);
	var w = (newX - oldX);
	var h = (newY - oldY);

	w = Math.min( w, (imageW-x));
	h = Math.min( h, (imageH-y));
	w = Math.max( w, 1);
	h = Math.max( h, 1);

	if(e.shiftKey){
		if(h <= imageW){
			w = h;
			}
			}

	dragBox.style.display = "block";
	dragBox.style.width = w + "px";
	dragBox.style.height = h + "px";
	dragBox.style.left = x + "px";
	dragBox.style.top =  y + "px";

	$('crop_x').value = x;
	$('crop_y').value = y;
	$('thumb_w').value = w;
	$('thumb_h').value = h;

	if (isIE)
		e.returnValue = false;
	else
		e.preventDefault();

}

function mouseUp(e) {
	imageEl = null;
	if (mouseMoved==false) {
		dragBox.style.display = "none";
	}
}
function loadCrop(){
	createCropDivs();
	rememberCrop();
	hideSelects();
	}
function loadScale(){
	createScaleDivs();
	//rememberCrop();
	hideSelects();
	}
function saveChanges(){
	$('canvas').style.display = 'none';
	showSelects();
	xajax_saveCrop(xajax.getFormValues('post'));
	$('thumb_max').value = $('thumb_w').value;
	if($('handle')){
		$('scaleImg').style.width = document.images['scaleImg'].width = $('thumb_max').value;
		$('handle').style.left = '194px';
		scaleIt(194, $('thumb_w').value);
		if($('scaleCanvas') && $('scaleCanvas').style.display == 'block'){
			createScaleDivs();
		}
		else {
			createScaleDivs();
			$('scaleCanvas').style.display = 'none';
			showSelects();
		}
	}
	hideOverlay();
	mvThumbOR('thumbInfoContents');
}
function discardChanges(x, y, w, h, utd){
	$('crop_x').value = x;
	$('crop_y').value = y;
	$('thumb_w').value = w;
	$('thumb_h').value = h;
	$('use_default_thumbsize').checked = utd;
	rememberCrop();
	$('canvas').style.display = 'none';
	showSelects();
	hideOverlay();
	mvThumbOR('thumbInfoContents');
	toggleScaleLink();
	}
function resetChanges(x, y, w, h, utd){
	$('crop_x').value = x;
	$('crop_y').value = y;
	$('thumb_w').value = w;
	$('thumb_h').value = h;
	$('use_default_thumbsize').checked = utd;
	rememberCrop();
	toggleScaleLink();
	}
function saveScale(mw){
	$('scaleCanvas').style.display = 'none';
	showSelects();
	xajax_saveCrop(xajax.getFormValues('post'));
	hideOverlay();
	}
function discardScale(mw){
	$('thumb_max').value = mw;
	rememberScale();
	$('scaleCanvas').style.display = 'none';
	document.images['scaleImg'].width = mw;
	$('scaleImg').style.width = mw;
	showSelects();
	hideOverlay();
	}
function resetScale(mw){
	$('thumb_max').value = mw;
	document.images['scaleImg'].width = mw;
	$('scaleImg').style.width = mw;
	rememberScale();
	}
function hideOverlay(){
	if($('scaleCanvas') && $('canvas')){
			if($('scaleCanvas').style.display != 'block' && $('canvas').style.display != 'block'){
				$('overlay').style.display = 'none';
			}
		} else {
				$('overlay').style.display = 'none';
		}
		if($('cropLinksContainer')){
						$('cropLinksContainer').style.background = '';
						$('cropLinksContainer').style.position = '';
						$('cropLinksContainer').style.zIndex = '';
						$('cropLinksContainer').style.padding = '';

		}

	}

function mvThumbOR(where){
	var box = $('keepThumbSize');
	var checked = $('use_default_thumbsize').checked;
	$(where).appendChild(box);
	$('use_default_thumbsize').checked = checked;
	}
function createCropDivs(){

	if($('canvas')){

		$('canvas').style.display = 'block';

		} else {

	var canvasDiv = document.createElement("div");
	var dragBoxDiv = document.createElement("div");
	var controlLinks = document.createElement("p");
	var handle = document.createElement("p");
	var savDimsL = document.createElement("a");
	var discardDimsL = document.createElement("a");
	var resetDimsL = document.createElement("a");

	var origX = $('crop_x').value;
	var origY = $('crop_y').value;
	var origW = $('thumb_w').value;
	var origH = $('thumb_h').value;
	var origUTD = $('use_default_thumbsize').checked;

	canvasDiv.id = 'canvas';
	canvasDiv.style.display = 'block';
	canvasDiv.style.position = 'absolute';
	canvasDiv.style.top = '105px';
	canvasDiv.style.left = '100px';
	canvasDiv.style.zIndex = '100';
	$('canvasImg').style.display = 'block';
	canvasDiv.style.height = $('canvasImg').height;
	$('canvasImg').parentNode.insertBefore(canvasDiv,$('canvasImg'));
	canvasDiv.appendChild($('canvasImg'));
	dragBoxDiv.id = 'dragBox';

	controlLinks.className = 'controllinks';
	controlLinks.id = 'controlLinksCrop';
	savDimsL.id = 'saveDims';
	savDimsL.className = 'saveDims';
	savDimsL.href = 'javascript:;';
	discardDimsL.id = 'discardDims';
	discardDimsL.href = 'javascript:;';
	discardDimsL.className = 'discardDims';
	resetDimsL.id = 'resetDims';
	resetDimsL.href = 'javascript:;';
	resetDimsL.className = 'resetDims';
	savDimsT = document.createTextNode(saveMessage);
	discardDimsT = document.createTextNode(discardMessage);
	resetDimsT = document.createTextNode(resetMessage);
	savDimsL.appendChild(savDimsT);
	discardDimsL.appendChild(discardDimsT);
	resetDimsL.appendChild(resetDimsT);


	//tHandle = document.createTextNode('+');
	//handle.appendChild(tHandle);
	domEl('span', '+', {id:'thandlespan'}, handle);
	handle.id = 'theHandle';

	canvasDiv.appendChild(handle);
	canvasDiv.appendChild(dragBoxDiv);
	canvasDiv.appendChild(controlLinks);
	canvasDiv.appendChild($('canvasImg'));
	controlLinks.appendChild(savDimsL);
	controlLinks.appendChild(discardDimsL);
	controlLinks.appendChild(resetDimsL);

	//canvasDiv.parentNode.replaceChild(domEl('div', [handle,controlLinks,canvasDiv], {id:'canvasDivContainer'}), canvasDiv);
		Event.observe(savDimsL, 'mouseover', function(){window.status=saveMessage; return true;});
		Event.observe(savDimsL, 'mouseout', function(){window.status='';return true;});
		Event.observe(discardDimsL, 'mouseover', function(){window.status=discardMessage; return true;});
		Event.observe(discardDimsL, 'mouseout', function(){window.status='';return true;});
		Event.observe(resetDimsL, 'mouseover', function(){window.status=resetMessage; return true;});
		Event.observe(resetDimsL, 'mouseout', function(){window.status='';return true;});
	}
	createOverlay();
	mvThumbOR('controlLinksCrop');
	addEvent($('canvas'),'mousedown',mouseDown);
	addEvent($('canvas'),'mousemove',mouseMove);
	addEvent($('canvas'),'mouseup',mouseUp);

	var mwidth = $('canvasImg').width - parseInt($('thumb_w').value);
	var mheight = $('canvasImg').height -  parseInt($('thumb_h').value);
	var positionX = (!isNaN(parseInt($('dragBox').style.left))) ? parseInt($('dragBox').style.left) : parseInt($('crop_x').value);
	var positionY = (!isNaN(parseInt($('dragBox').style.top))) ? parseInt($('dragBox').style.top) : parseInt($('crop_y').value);
	var dragBoxWidth;
	var dragBoxHeight;



	addEvent($('dragBox'), 'mousedown', function(){
		dragBoxWidth = (!isNaN(parseInt($('dragBox').style.width))) ? parseInt($('dragBox').style.width) : parseInt($('thumb_w').value);
		dragBoxHeight = (!isNaN(parseInt($('dragBox').style.height))) ? parseInt($('dragBox').style.height) : parseInt($('thumb_h').value);

		mwidth = $('canvasImg').width - dragBoxWidth;
		mheight = $('canvasImg').height - dragBoxHeight;

		positionX = parseInt($('dragBox').style.left);
		positionY = parseInt($('dragBox').style.top);

		Drag.init($('dragBox'), null, 0, mwidth, 0, mheight);
		$('dragBox').onDrag = function(x,y){
			$('crop_x').value = x;
			$('crop_y').value = y;
		}
		$('dragBox').onDrag(positionX,positionY);/**/

	});

	Drag.init($('dragBox'), null, 0, mwidth, 0, mheight);
	Drag.init($('theHandle'),$('canvas'));

	addEvent(savDimsL, 'click', function(){
				saveChanges();
				origX = $('crop_x').value;
				origY = $('crop_y').value;
				origW = $('thumb_w').value;
				origH = $('thumb_h').value;
				origUTD = $('use_default_thumbsize').checked;
	});
	addEvent(discardDimsL, 'click', function(){
		discardChanges(origX, origY, origW, origH, origUTD);
	});
	addEvent(resetDimsL, 'click', function(){
		resetChanges(origX, origY, origW, origH, origUTD);
	});
	addEvent($('canvas'), 'mousedown', function(){
		winFocus($('canvas'), $('scaleCanvas'));
		winFocus($('dragBox'), $('canvas'));
											 });
	}
function toggleMaxThumbSize(){
	if($('overRideSize').checked == true){
		$('thumb_max').value = 0;
		$('scaleLink').style.visibility = 'hidden';
		} else {

		$('thumb_max').value = $F('thumb_w');
		$('scaleLink').style.visibility = 'visible';
		}
	}
function createScaleDivs(){
	if($('scaleCanvas')){
		$('scaleCanvas').style.display = 'block';

		} else {

	var rootMax = $('thumb_w').value;
	var origMax = $('thumb_max').value;
	var scaleSize = parseInt(($('thumb_max').value/$('thumb_w').value)*194);

	var canvasDiv = document.createElement("div");
	var imgContainer = document.createElement("div");
	var output = document.createElement("div");
	var scaleTrack = document.createElement("div");
	var handle = document.createElement("img");
	var mHandle = document.createElement('p');
	var controlLinks = document.createElement("p");
	var savDimsL = document.createElement("a");
	var discardDimsL = document.createElement("a");
	var resetDimsL = document.createElement("a");

	canvasDiv.id = 'scaleCanvas';
	canvasDiv.style.display = 'block';
	canvasDiv.style.position = 'absolute';
	canvasDiv.style.top = '50px';
	canvasDiv.style.left = '50px';
	canvasDiv.style.zIndex = '100';
	$('scaleImg').style.display = 'block';
	canvasDiv.style.height = $('scaleImg').height;
	//$('scaleImg').parentNode.insertBefore(canvasDiv,$('scaleImg'));
	$('post').appendChild(canvasDiv);

	controlLinks.className = 'controllinks';
	savDimsL.id = 'saveScaleDims';
	savDimsL.href = 'javascript:;';
	savDimsL.className = 'saveDims';
	discardDimsL.id = 'discardScaleDims';
	discardDimsL.href = 'javascript:;';
	discardDimsL.className = 'discardDims';
	resetDimsL.id = 'resetScaleDims';
	resetDimsL.href = 'javascript:;';
	resetDimsL.className = 'resetDims';


	var savDimsT = document.createTextNode(saveMessage);
	var discardDimsT = document.createTextNode(discardMessage);
	var resetDimsT = document.createTextNode(resetMessage);
	savDimsL.appendChild(savDimsT);
	discardDimsL.appendChild(discardDimsT);
	resetDimsL.appendChild(resetDimsT);
	controlLinks.appendChild(savDimsL);
	controlLinks.appendChild(discardDimsL);
	controlLinks.appendChild(resetDimsL);

	Event.observe(savDimsL, 'mouseover', function(){window.status=saveMessage; return true;});
	Event.observe(savDimsL, 'mouseout', function(){window.status='';return true;});
	Event.observe(discardDimsL, 'mouseover', function(){window.status=discardMessage; return true;});
	Event.observe(discardDimsL, 'mouseout', function(){window.status='';return true;});
	Event.observe(resetDimsL, 'mouseover', function(){window.status=resetMessage; return true;});
	Event.observe(resetDimsL, 'mouseout', function(){window.status='';return true;});

	mHandle.id = 'theScaleHandle';
	domEl('span', '+', {id:'thandlespan'}, mHandle);

	imgContainer.id = 'imgContainer';
	output.id = 'scaleOutput';
	scaleTrack.id = 'scaleTrack';
	scaleTrack.style.width = '200px';
	scaleTrack.style.height = '28px';
	scaleTrack.style.position = 'relative';
	scaleTrack.style.backgroundImage = 'url(images/scale_track.gif)';
	scaleTrack.style.backgroundRepeat = 'no-repeat';

	handle.id = 'handle';
	handle.src = 'images/scale_slider.gif';
	handle.name = '';
	handle.alt = handle.title = 'Slide the bar to resize the image.';
	handle.style.position = 'relative';
	handle.style.left = (!isNaN(scaleSize)) ? scaleSize+'px' : '194px';
	handle.style.backgroundImage = 'url(images/scale_slider.gif)';
	handle.style.backgroundRepeat = 'no-repeat';

	canvasDiv.appendChild(mHandle);
	canvasDiv.appendChild(controlLinks);

	canvasDiv.appendChild(imgContainer);
	canvasDiv.appendChild(scaleTrack);
	canvasDiv.appendChild(output);

	imgContainer.appendChild($('scaleImg'));
	scaleTrack.appendChild(handle);


		}
	createOverlay();
	addEvent(savDimsL, 'click', function(){
		saveScale();
		origMax = $('thumb_max').value;
	});
	addEvent(discardDimsL, 'click', function(){
		discardScale(origMax);
	});
	addEvent(resetDimsL, 'click', function(){
		resetScale(origMax);
	});

	addEvent($('scaleCanvas'), 'mousedown', function(){
		winFocus($('scaleCanvas'), $('canvas'));
											 });
Drag.init($('handle'), null, -6, 194, 0, 0);
Drag.init($('theScaleHandle'),$('scaleCanvas'));
addEvent($('scaleCanvas'), 'mousedown', function(){
	winFocus($('scaleCanvas'), $('canvas'));
										 });
document.images['scaleImg'].width = $('thumb_max').value;
var imgWidth = document.images['scaleImg'].width;
var imgHeight = document.images['scaleImg'].height;
$('imgContainer').style.width = imgWidth+'px';
$('imgContainer').style.minWidth = $('thumb_w').value+'px';
$('imgContainer').style.height = $('thumb_h').value+'px';
$('imgContainer').style.minHeight = $('thumb_h').value+'px';
$('handle').onDrag = function(x,y){
scaleIt(x, $('thumb_w').value);

	}


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
				if($('cropLinksContainer')){
						$('cropLinksContainer').style.background = '#fff';
						$('cropLinksContainer').style.position = 'absolute';
						$('cropLinksContainer').style.zIndex = '95';
						$('cropLinksContainer').style.padding = '0.5em';

					}
	}

function createCropLinks(){
	if($('thumbNails')){
	var cropLinksContainer = domEl('p', [domEl('a', _l('CROP_THUMB'), {id:'cropImg',href:'javascript:;'}),domEl('a', _l('RESIZE_THUMB'), {id:'scaleLink',href:'javascript:;'})], {id:'cropLinksContainer'});
	var br = domEl('br', '');
	if(document.all){
		insertAfter($('scaleImg').parentNode, cropLinksContainer, $('scaleImg').nextSibling);
	} else {
		insertAfter($('scaleImg').parentNode, cropLinksContainer, $('scaleImg'));
	}
	insertAfter($('scaleImg').parentNode, br, cropLinksContainer);
	addEvent($('cropImg'), 'click', loadCrop);
	addEvent($('thumbNailThumb'), 'click', loadCrop);
	addEvent($('scaleLink'), 'click', loadScale);
	Event.observe($('cropImg'), 'mouseover', function(){window.status=_l('CROP_THUMB'); return true;});
	Event.observe($('cropImg'), 'mouseout', function(){window.status='';return true;});
	Event.observe($('scaleLink'), 'mouseover', function(){window.status=_l('RESIZE_THUMB'); return true;});
	Event.observe($('scaleLink'), 'mouseout', function(){window.status='';return true;});
	toggleScaleLink();
		}
	}

function winFocus(obj, currObj){
	if(obj && currObj){
		obj.style.zIndex = currObj.style.zIndex + 1;
		}


	}
function scaleIt(v, origImgWidth) {

	var scaleImage = $('scaleImg');
	var trackW = parseInt($('scaleTrack').style.width);
	var maxXOffset = (Drag.obj != null) ? trackW - Drag.obj.maxX : trackW-194;
	var minXOffset = (Drag.obj != null) ? Math.abs(0 + Drag.obj.minX) : 6;
	var offset = v+((maxXOffset+minXOffset)/2);
	var percentage = offset/trackW;
	var size  = Math.round(origImgWidth*percentage);
	$('thumb_max').value = size;

  	$('scaleOutput').innerHTML = 'Thumbnail Width: '+ size+'px<br />';
	$('scaleOutput').innerHTML += 'Thumbnail Height: '+ scaleImage.height+'px<br />';
	scaleImage.style.width = size+'px';
	document.images['scaleImg'].width = size;
}
function toggleScaleLink(){
	if($('use_default_thumbsize').checked){
		if(Element.visible($('scaleLink'))){
			Element.toggle($('scaleLink'));
			}

		if($('scaleCanvas')){
			if(Element.visible($('scaleCanvas'))){
				$('scaleCanvas').style.display = 'none';
				}
			}
		if($('sizeNotice')){
			Element.toggle($('sizeNotice'));
			}
	} else {
		$('scaleLink').style.display = 'inline';
		if($('sizeNotice')){
			if(!Element.visible($('sizeNotice'))){
				Element.toggle($('sizeNotice'));
				}

			} else {
			domEl('span', '* This preview may be a different size than what is shown in your portfolio.', {id:'sizeNotice'}, $('thumbNails'));
			}

	}
}

/*
Why do this? To make sure IE caches the image before the event fires for the scaler.

function preloadImgCache(){
	if($('autothumb')){
	domEl('img', '', {'class': 'hidden', src:'images/scale_track.gif', id:'ntest'}, $('post').parentNode);
	domEl('img', '', {'class': 'hidden', src:'images/scale_slider.gif'}, $('post').parentNode);
	domEl('img', '', {'class': 'hidden', src:'images/overlay.png'}, $('post').parentNode);
	}
}

var autoThumb = {
	autothumb : Object,
	thumbField : Object,
	inputs : Array,
	init : function (){
		this.autothumb = $('autothumb');
		this.thumbField = $('thumbField');
		this.inputs = document.getElementsByTagName('input');
		if(this.autothumb && this.thumbField){
			this.loopInputs();
			}
		},
	loopInputs : function(){
		var inputs = this.inputs;
			inputs = $A(inputs);
			inputs.each(function(i){

			if(i.type == "checkbox" && i.name == "autothumb"){
				addEvent(i, 'click', function(){
						toggleClassName('thumbField', 'visible', 'hidden');
						toggleClassName('cropLinksContainer', 'hidden', 'visible');
						if($('thumbNailThumb')){
							$('thumbNailThumb').style.cursor = (i.checked) ? 'pointer' : '';
						if(!i.checked){
							removeEvent($('thumbNailThumb'), 'click', loadCrop);
							} else {
							addEvent($('thumbNailThumb'), 'click', loadCrop);
							}
							}
						});
				if(i.checked==true){
						$('thumbField').className = 'hidden';
						createCropLinks();
						if($('thumbNailThumb')){
							$('thumbNailThumb').style.cursor = 'pointer';
						}
					} else {
						if($('thumbNailFields')){
							$('thumbNailFields').className = 'hidden';
							}
						if($('thumbField')){$('thumbField').className = 'visible';}
						if($('thumbNailThumb')){$('thumbNailThumb').style.cursor = '';}
					}

				}

								 });



		}
}


	setMessages();
autoThumb.init();
preloadImgCache();
if($('use_default_thumbsize')){
	Event.observe($('use_default_thumbsize'),'click',toggleScaleLink);
	}
}
*/
function expanse() {

}