/*
    Rewritten to work with Expanse.
	javascript - a simple web-based text editor
    Copyright (C) 2006  Oliver Moran

    This library is free software; you can redistribute it and/or
    modify it under the terms of the GNU Lesser General Public
    License as published by the Free Software Foundation; either
    version 2.1 of the License, or (at your option) any later version.

    This library is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
    Lesser General Public License for more details.

    You should have received a copy of the GNU Lesser General Public
    License along with this library; if not, write to the Free Software
    Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
*/
function insertAfter(referenceNode, node) {
	referenceNode = $(referenceNode);
	referenceNode.parentNode.insertBefore(node, referenceNode.nextSibling);
}
//---------------------------------------------------------------------------
function insertEditabljavascript(editabljavascriptName, editabljavascriptWidth, editabljavascriptHeight, editabljavascriptLayout, editabljavascriptStyle) {
	var boldButton = '<input type="image" src="javascript/icons/stock_text_bold.png" value="Bold" alt="Bold" name="' + editabljavascriptName + '" class="editabljavascriptButton" id="bold" style="width:24px; height:24px;">';
	var italicButton = '<input type="image" src="javascript/icons/stock_text_italic.png" value="Italic" alt="Italic" name="' + editabljavascriptName + '" class="editabljavascriptButton" id="italic" style="width:24px; height:24px;">';
	var underlinedButton = '<input type="image" src="javascript/icons/stock_text_underlined.png" value="Underlined" alt="Underlined" name="' + editabljavascriptName + '" class="editabljavascriptButton" id="underline" style="width:24px; height:24px;">';
	var alignLeftButton = '<input type="image" src="javascript/icons/stock_text_left.png" value="Align Left" alt="Align Left" name="' + editabljavascriptName + '" class="editabljavascriptButton" id="justifyleft" style="width:24px; height:24px;">';
	var justifyButton = '<input type="image" src="javascript/icons/stock_text_justify.png" value="Justify" alt="Justify" name="' + editabljavascriptName + '" class="editabljavascriptButton" id="justifyfull" style="width:24px; height:24px;">';
	var alignCenterButton = '<input type="image" src="javascript/icons/stock_text_center.png" value="Align Center" alt="Align Center" name="' + editabljavascriptName + '" class="editabljavascriptButton" id="justifycenter" style="width:24px; height:24px;">';
	var alignRightButton = '<input type="image" src="javascript/icons/stock_text_right.png" value="Align Right" alt="Align Right" name="' + editabljavascriptName + '" class="editabljavascriptButton" id="justifyright" style="width:24px; height:24px;">';
	var editabljavascript = '<iframe src="javascript/blank.html?style=' + editabljavascriptStyle + '" id="' + editabljavascriptName + '" \n style="width:' + editabljavascriptWidth + 'px; height:' + editabljavascriptHeight + 'px; border-style:inset; border-width:thin;" frameborder="0px"></iframe>'

	var editabljavascriptHTML = editabljavascriptLayout;
	editabljavascriptHTML = editabljavascriptHTML.replace("[bold]", boldButton);
	editabljavascriptHTML = editabljavascriptHTML.replace("[italic]", italicButton);
	editabljavascriptHTML = editabljavascriptHTML.replace("[underlined]", underlinedButton);
	editabljavascriptHTML = editabljavascriptHTML.replace("[align-left]", alignLeftButton);
	editabljavascriptHTML = editabljavascriptHTML.replace("[justify]", justifyButton);
	editabljavascriptHTML = editabljavascriptHTML.replace("[align-center]", alignCenterButton);
	editabljavascriptHTML = editabljavascriptHTML.replace("[align-right]", alignRightButton);
	editabljavascriptHTML = editabljavascriptHTML.replace("[edit-area]", editabljavascript);
	
	if (document.designMode) {
		
		var editDiv = domEl('div', '', {id:'safariTextarea'});
		insertAfter($('descr'), editDiv);
		
		$('safariTextarea').innerHTML = editabljavascriptHTML;
		/*
		Safari seems to *remove* display:none elements from the DOM.
		*/
		$('descr').style.position = 'absolute';
		$('descr').style.top = '-1000000px';
		$('descr').style.left = '-1000000px';
		ititButtons(editabljavascriptName);
		Event.observe($('post'), 'submit', function(e){$('descr').value = editabljavascriptContents('descr_safari');});
	}
}
function editabljavascriptContents(editabljavascriptName) {
	if (document.designMode) {
		// Explorer reformats HTML during document.write() removing quotes on element ID names
		// so we need to address Explorer elements as window[elementID]
		if (window[editabljavascriptName]) return window[editabljavascriptName].document.body.innerHTML;
		return document.getElementById(editabljavascriptName).contentWindow.document.body.innerHTML;
	} else {
		// return the value from the <textarea> if document.designMode does not exist
		return document.getElementById(editabljavascriptName).value;
	}
}

function ititButtons(editabljavascriptName) {
	var kids = document.getElementsByTagName('input');

	for (var i=0; i < kids.length; i++) {
		if (kids[i].className == "editabljavascriptButton" && kids[i].name == editabljavascriptName) {
			kids[i].onmouseover = buttonMouseOver;
			kids[i].onmouseout = buttonMouseOut;
			kids[i].onmouseup = buttonMouseUp;
			kids[i].onmousedown = buttonMouseDown;
			kids[i].onclick = buttonOnClick;
		}
	}
}

function buttonMouseOver() {
	// events for mouseOver on buttons
	// e.g. this.style.xxx = xxx
}

function buttonMouseOut() {
	// events for mouseOut on buttons
	// e.g. this.style.xxx = xxx
}


function buttonMouseUp() {
	// events for mouseUp on buttons
	// e.g. this.style.xxx = xxx
}

function buttonMouseDown(e) {
	// events for mouseDown on buttons
	// e.g. this.style.xxx = xxx

	// prevent default event (i.e. don't remove focus from text area)
	Event.stop(e);
	return;
	var evt = e ? e : window.event; 

	if (evt.returnValue) {
		evt.returnValue = false;
	} else if (evt.preventDefault) {
		evt.preventDefault( );
	} else {
		return false;
	}
}

function buttonOnClick(e) {
	Event.stop(e);
	// Explorer reformats HTML during document.write() removing quotes on element ID names
	// so we need to address Explorer elements as window[elementID]
	if (window[this.name]) { window[this.name].document.execCommand(this.id, false, null); }
	else { document.getElementById(this.name).contentWindow.document.execCommand(this.id, false, null); }
}
var safariEditor = {
	init : function(){
		insertEditabljavascript(
		"descr_safari",
		300,
		200,
		"[bold][italic][underlined][align-left][justify][align-center][align-right]<br />[edit-area]",
		"font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; color:Black;"
	);
		}
	}
Event.onDOMReady(safariEditor.init);
