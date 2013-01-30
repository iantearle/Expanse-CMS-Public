/********* Expanse ***********/

jQuery.fn.exists = function(){return this.length>0;}

/*
Language get function
*/
function _l(constant){
	var lconstant = $('#L_JS_'+constant.toUpperCase()).val();
	return lconstant ? lconstant : 'Sorry, but you\'re missing a language setting.';
}

function A(obj) {
	var array = [];
	// iterate backwards ensuring that length is an UInt32
	for (var i = obj.length >>> 0; i--;) {
		array[i] = obj[i];
	}
	return array;
}

/*
EULA checking
*/
function checkEula(e) {
	var eulaAgreed = $('#eula_read').exists();
	if(eulaAgreed) {
		if(!$('#eula_read').is(':checked')) {
			alert(_l('EULA'));
			e.preventDefault();
			return false;
		}
	}
	return true;
}

function confirmEula(){
	if($('#eula_read')){
		$('#post').submit(function() {
			if(!checkEula) {
				return false;
			}
			return true;
		});
	}
}

/*
Browser check. For use when object detection wont work
*/
function checkIt(string) {

	var detect = navigator.userAgent.toLowerCase();
	var place = detect.indexOf(string) + 1;
	var thestring = string;
	return place;
}

/*
Toggle Class name based on ID
*/
function toggleClassName(id, class1, class2) {
	if(document.getElementById) {
		var e = $('#'+id);
		if(e) {
			if(e.attr('class') !== class1){
				e.attr('class', class1);
			}else{
				e.attr('class', class2);
			}
		}
	}
}


window.expanse = window.expanse || {};
expanse = expanse || {};

/*
Toggle all checkboxes
*/
expanse.toggleBox = Backbone.View.extend({
	initialize: function () {
		this.itemList = $('#itemList');
		if(!this.itemList.exists()) {
			return;
		}
		this.createBox();
	},
	createBox: function() {
		var itemList = this.itemList;
		itemList.before('<div id="checkGroup"><label for="checkThemAll" class="checkbox"><input type="checkbox" class="checkbox" id="checkThemAll" name="checkThemAll" />'+_l('CHECK_BOXES')+'</label></div>');
		this.assign();
		$('#checkThemAll').click(_.bind(this.run, this));
	},
	run: function() {
		var inputs = $(':input');
		$.each(inputs, function(i) {
			if($(this).is(':checkbox') && !$(this).is('#checkThemAll') && !$(this).is(':disabled')) {
				if($(this).not('#mark_'+i)) {
					$(this).parent().after('<img src="images/markedfordeletion.gif" class="marked" id="mark_item_delete_'+ i +'">');
					$(this).parent().parent().toggleClass('deleting');
					$(this).attr('checked', function(idx, oldAttr) {
			            return !oldAttr;
			        });
				}
			}
		});
	},
	assign: function() {
		var inputs = $(':input');
		$.each(inputs, function(i) {
			if(!$(this).is(':checkbox') || $(this).is('#checkThemAll') || $(this).is(':disabled')) { return; }
			$(this).click(function() {
				$(this).parent().after('<img src="images/markedfordeletion.gif" class="marked" id="mark_item_delete_'+ i +'">');
				$(this).parent().parent().toggleClass('deleting');
			});
		});
	}
});

/*
Confirm Uninstallation of Expanse
*/
expanse.confirmUninstall = Backbone.View.extend({
	initialize: function () {
		if($('#uninstall').exists()) {
			$('#uninstall').click(function(e) {
				var uninstall = confirm(_l('UNINSTALL'));
				if(!uninstall) {
					return (e.preventDefault) ? e.preventDefault() : e.returnValue = false;
				}
			});
		}
		if($('#delete_uploads').exists()) {
			$('delete_uploads').click(function(e) {
				var deleteUploads = confirm(_l('DELETE_UPLOADS'));
				if(!deleteUploads) {
					e.preventDefault();
				}
			});
		}
		if($('#delete_db').exists()) {
			$('#delete_db').click(function(e) {
				var deleteDB = confirm(_l('DELETE_DB'));
				if(!deleteDB) {
					e.preventDefault();
				}
			});
		}
		if($('#delete_config').exists()) {
			$('#delete_config').click(function(e) {
				var deleteConfig = confirm(_l('DELETE_CONFIG'));
				if(!deleteConfig) {
					e.preventDefault();
				}
			});
		}
	}
});

/*
Reorder Main Menu
*/
expanse.reorderMenu = Backbone.View.extend({
	sortContainer: Object,
	beforeContainer: Object,
	draggables: Array,
	initialize: function() {
		if($('#keepMenu').exists()) {
			$('#keepMenu').addClass('connectedSortable');
			$('#excludeMenu').addClass('connectedSortable');
			this.sortContainer = $('#keepMenuContainer');
			this.beforeContainer = $('#beforeMenuContainer');
			var itemsCount = this.sortContainer.children().size();
			var reorderL = '<a href="javascript:;" id="reorderLink" class="btn btn-primary">' + _l('REORDER_MENU') + '</a>';
			var response = '<div id="responseText"></div>';

			this.beforeContainer.before(reorderL);
			this.beforeContainer.before(response);

			$('#reorderLink').click(_.bind(this.create, this));
			$('#reorderLink').mouseover(function() {
				window.status=_l('REORDER_MENU');
				return true;
			});
			$('#reorderLink').mouseover(function() {
				window.status='';
				return true;
			});

			var check_or_no = $('#cb_subcats');
			var includeSubcats = '<div class="control-group"><label for="include_subcats" class="checkbox"><input type="checkbox" id="include_subcats" '+(check_or_no == 'yes' ? 'checked="checked"' : '')+' />'+_l('MB_INCLUDE_SUBCATS')+'</label></div>';
			$('#reorderLink').after(includeSubcats);
			$('#include_subcats').click(_.bind(this.toggleSubcats, this));
			this.primeSubcats();
		}
	},
	create: function() {
		$('#reorderLink').unbind('click');
		$('#reorderLink').click(_.bind(this.destroy, this)).text(_l('REORDER_FINISHED'));
		$('#keepMenu, #excludeMenu').sortable({
	        	connectWith: ".connectedSortable",
	        	items: 'div',
	        	receive: function(event, ui) {
		        	$(ui.item).toggleClass('trashed').toggleClass('kept');
	        	},
	        	opacity: 0.5
	    }).disableSelection().sortable('enable').addClass('reorderingMenu');
	},
	destroy: function() {
		$('#reorderLink').unbind('click');
		$('#reorderLink').click(_.bind(this.create, this)).text(_l('REORDER_MENU'));
		$('#keepMenu, #excludeMenu').sortable('disable').removeClass('reorderingMenu');
		this.updateOrder();
	},
	updateOrder: function() {
		var sorted = this.serialize();
		xajax_updateMenuOrder(sorted.sections, 'sections');
		xajax_updateMenuOrder(sorted.pages, 'items');
	},
	toggleSubcats: function() {
		var subcats = $('.sub_cat');
		if($('#include_subcats').attr('checked') == false) {
			xajax_update_option('mb_include_subcats', 1);
		} else {
			xajax_update_option('mb_include_subcats', 0);
		}
		subcats.each(function(el) {
			$(this).toggle();
		});
	},
	primeSubcats: function() {
		var check_or_no = $('#cb_subcats');
		if(check_or_no == 'yes'){ return; }
		var subcats = $('.sub_cat');
		subcats.each(function() {
			$(this).hide();
		});
	},
	serialize: function() {
		var cats = '', items = '', id, itemsArr = new Array, catsArr = new Array, item_i = 0, cat_i = 0, vis_i = 1;
		var keepMenu = $('#keepMenu > div');
		keepMenu.each(function(el,i) {
			if(!$(this).is(':visible')){ return; }
			id = $(this).attr('id').replace('item_', '');

			if($(this).hasClass('page')) {
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
});

/*
Reorder itemList
*/
expanse.reorder = Backbone.View.extend({
	sortContainer : Object,
	draggables : Array,
	initialize: function() {
		if($('#itemList').exists()) {
			this.sortContainer = $('#itemList');
			var itemsCount = this.sortContainer.children().size();
			if(itemsCount <= 1) {
				return;
			}
			var reorderL = '<a href="javascript:;" id="reorderLink">' + _l('REORDER') + '</a>';
			var response = '<div id="responseText"></div>';
			$('#pageList').before(response);
			this.sortContainer.before(reorderL);
			$('#reorderLink').click(_.bind(this.create, this));
			$('#reorderLink').mouseover(function() {
				window.status = _l('REORDER');
				return true;
			});
			$('#reorderLink').mouseover(function() {
				window.status = '';
				return true;
			});
		}
	},
	create: function() {
		if(($('#order_by').exists() && $('#order_by').val() !== 'order_rank')) {
			return alert(_l('REORDER_NOTICE'));
		}
		$('#reorderLink').unbind('click');
		$('#reorderLink').click(_.bind(this.destroy, this)).text(_l('REORDER_FINISHED'));
		this.sortContainer.sortable({
	        	items: 'div',
	        	opacity: 0.5
	    }).disableSelection().sortable('enable').addClass('reordering');
	},
	destroy: function() {
		$('#reorderLink').unbind('click');
		$('#reorderLink').click(_.bind(this.create, this)).text(_l('REORDER'));
		this.updateOrder();
		this.sortContainer.sortable('disable').removeClass('reordering');
	},
	updateOrder: function() {
		xajax_updateOrder(this.sortContainer.sortable('enable').sortable('serialize'));
	}
});

/*
Resize Editor frame
*/
expanse.resizeEditor = Backbone.View.extend({
	initialize: function() {
		if($('.wysihtml5-editor')) {
			this.createText();
		}
	},
	createText: function() {
		var sizerCont = '<span id="sizerContainer"><a href="javascript:;" id="increaseBox">+</a> <a href="javascript:;" id="decreaseBox">-</a></span>';
		$('.descr').parent().after(sizerCont);
		$('#increaseBox').mouseover(function() {window.status=_l('INCREASE_EDITOR'); return true;});
		$('#increaseBox').mouseout(function() {window.status=''; return true;});
		$('#decreaseBox').mouseover(function() {window.status=_l('DECREASE_EDITOR'); return true;});
		$('#decreaseBox').mouseout(function() {window.status=''; return true;});
		this.addActions();
	},
	addActions: function() {
		var edObj = ($('.wysihtml5-sandbox').exists()) ? $('.wysihtml5-sandbox') : $('.descr');
		$('#increaseBox').click(function() {
			$(edObj).css("height","+=100");
		});
		$('#decreaseBox').click(function() {
			$(edObj).css("height","-=100");
		});
	}
});

/*
Highlight Inputs
*/
expanse.hiliteInput = Backbone.View.extend({
	initialize: function() {
		var fields = $('.shareField');
		fields.each(function(i) {
			$(this).click(function(e){
				$(this).select();
				e.preventDefault();
			});
		});
	}
});

/*
Set Checks for ever - keep this checked...
*/
expanse.setChecks = Backbone.View.extend({
	checkBoxes: Array,
	initialize: function() {
		this.checkBoxes = ['#online', '#autothumb', '#comments', '#smilies', '#for_sale'];
		this.doCheckBoxes();
	},
	assignRemember: function() {
		var that = this;
		var boxes = this.checkBoxes;
		$.each(boxes, function(index, value) {
			$(value).click(function() {
				that.rememberChecks(this);
			});
		});
	},
	rememberChecks: function(obj) {
		var that = this;
		if(obj) {
			var keepStateID = '#keepStateID'+obj.id;
			var keepMessage;
			if(obj.checked == true){
				keepMessage = _l('KEEP_CHECKED');
			} else {
				keepMessage = _l('KEEP_UNCHECKED');
			}
			if($(keepStateID).exists()) {
				$('#keepStateContainer').remove();
			}
			$('#'+obj.id).parent().append('<span id="keepStateContainer"><a href="javascript:;" id="keepStateID'+obj.id+'" class="keepState">'+keepMessage+'</a></span>').fadeIn();
			$(keepStateID).mouseover(function() {window.status=keepMessage; return true;});
			$(keepStateID).mouseover(function() {window.status=''; return true;});

			setTimeout( function(){
		    	$(keepStateID).fadeOut();
		    }, 2000 );

			$(keepStateID).click(function() {
				that.setCheckBox(obj);
			});
		}
	},
	setCheckBox: function(obj) {
		if(obj.checked == true) {
			$.cookie(obj.id, 'checked');
		} else {
			$.cookie(obj.id, 'unchecked');
		}
	},
	doCheckBoxes: function() {
		var docloc = document.URL;
		var add = /type=add/;
		var edit = /type=edit/;
		if((docloc.match(add) && !docloc.match(edit))) {
			this.assignRemember();
			this.assignGets();
		}

	},
	getCheck: function(obj) {
		if(obj) {
			var cookie = $.cookie(obj.substring(1, obj.length));
			if(cookie !== null) {
				if(cookie == 'checked'){
					$(obj).attr('checked', true);
				} else {
					$(obj).attr('checked', false);
				}
			}
		}
	},
	assignGets: function() {
		var that = this;
		$.each(this.checkBoxes, function(index, value) {
			that.getCheck(value);
		});
	}
});

/*
Action the marking of extra images for delete
*/
expanse.markDelete = Backbone.View.extend({
	boxes: Array,
	initialize: function() {
		var that = this;
		this.boxes = $('.xtraImgDelete');
		$.each(this.boxes, function(index, value) {
			$('#'+value.id).click(function() {
				var fade = $(this).parent();
				that.mark(value, fade);
			});
		});
	},
	mark: function(value, fade) {
		if($('#'+value.id).attr('checked')) {
			fade.closest('.imgBox').css({ opacity: 0.5 });
		} else {
			fade.closest('.imgBox').css({ opacity: 1 });
		}
	}
});

function loadAlert() {
	$('#post').submit(function() {
		$('body').append('<div style="display:block;position:absolute;top:0;left:0;z-index:90;width:'+ $(document).width() +'px;height:'+ $(document).height() +'px;" id="overlay"></div>');
		$('body').append('<blockquote class="overlayHelp" style="position:fixed;top:50%;left:50%;"><h4>'+_l('WAIT_NOTICE')+'</h4></blockquote>');
		$('#submit').hide();
		$('#submit').after(_l('WAIT_NOTICE'));
		return new expanse.validate;
	});
}

/*
Validae form posts
*/
expanse.validate = Backbone.View.extend({
	initialize: function() {
		$('#post').submit(function() {
			this.validateFields;
		});
	},
	validateFields: function(e) {
		if($('#confirmpassword') && !$('#edit_user')){
 			if($('#username') == '' || $('#password') == '') {
				alert(_l('ENTER_USER_DETAILS'));
				return false;
			}
		}
	}
});

/*
Magic Fields, append buttons to allow for additional adds
*/
var magicFields = Backbone.View.extend({
	initialize: function(options) {
		if(!options.firstField){return;}
		this.optField = $(options.firstField);
		this.fieldName = options.fieldName;

		this.incrementLabel = options.incrementLabel || false;
		this.addText = options.addText || _l('ADD_CUSTOM_FIELD');
		this.removeText = options.removeText || _l('REMOVE_CUSTOM_FIELD');
		this.clearText = options.clearText || _l('CLEAR_CUSTOM_FIELD');
		this.labelText = options.labelText || _l('CUSTOM_FIELD');
		this.confirmDelete = options.confirmDelete || _l('CLEAR_CONFIRM_CUSTOM_FIELD');

		this.optGroup = this.optField.parent().parent().parent().parent().parent();
		this.fieldCount = this.optGroup.find('input').length;
		this.addLinkID = 'addLink_'+this.fieldName;
		this.removeLinkID = 'removeLink_'+this.fieldName;
		this.resetLinkID = 'resetLink_'+this.fieldName;
		this.run();
	},
	run: function() {
		this.optGroup.append('<a href="javascript:;" class="btn" id="'+this.addLinkID+'">'+this.addText+'</a> ');
		this.optGroup.append('<a href="javascript:;" class="btn btn-info" id="'+this.removeLinkID+'">'+this.removeText+'</a> ');
		this.optGroup.append('<a href="javascript:;" class="btn btn-danger" id="'+this.resetLinkID+'">'+this.clearText+'</a> ');

		$('#'+this.addLinkID).click(_.bind(this.addField, this));
		$('#'+this.removeLinkID).click(_.bind(this.removeFields, this));
		$('#'+this.resetLinkID).click(_.bind(this.resetFields, this));
	},
	addField: function() {
		var fieldCount = this.countFields()+1;
		var optID = 'new_cat'+fieldCount;
		var incrementor = this.incrementLabel == true ? ' '+fieldCount : '';
		var divHTML = '<div id="'+optID+'Group" class="row">'
			+'<div class="span6">'
			+'<div class="control-group">'
			+'<label for="'+optID+'">'+this.labelText+incrementor+'</label>'
			+'<div class="controls">'
			+'<input type="text" value="" name="'+this.fieldName+'[]" id="'+optID+'" class="text" />'
			+'</div>'
			+'</div>'
			+'</div>'
			+'</div>';
		$('#'+this.addLinkID).before(divHTML);
	},
	removeFields: function() {
		this.fieldCount = this.countFields();
		if(this.fieldCount === 1) {return}
		var fieldset = this.optGroup;
		var olddiv = this.optGroup.attr('id')+this.fieldCount+'Group';
		$('#'+olddiv).remove();
	},
	resetFields: function() {
		this.fieldCount = this.countFields();
		if(this.fieldCount === 1) {return;}
		var resetConf = confirm(this.confirmDelete);
		if(resetConf) {
			var fieldset = this.optGroup;
			fieldset.find('div').remove();
			this.addField();
		}
	},
	countFields: function() {
		this.Fields = this.optGroup.find('.row');
		return this.fieldCount = this.Fields.length;
	}
});

/*
Magic Uploads, create more upload fields.
*/
var magicUploads = magicFields.extend({
	addField: function() {
		var fieldCount = this.countFields()+1;
		var optID = this.fieldName+fieldCount;
		var incrementor = this.incrementLabel == true ? ' '+fieldCount : '';
		var divHTML = '<div id="'+optID+'Group" class="row">'
					+'<div class="span8">'
					+'<div class="control-group">'
					+'<label for="'+optID+'" class="control-label">'+this.labelText+incrementor+'</label>'
					+'<div class="controls">'
					+'<input type="file" name="'+this.fieldName+fieldCount+'"  id="'+optID+'"  class="formfields file" />'
					+'</div>'
					+'</div>'
					+'<div class="control-group">'
					+'<label for="'+optID+'" class="control-label">Caption'+incrementor+'</label>'
					+'<div class="controls">'
					+'<textarea name="caption['+optID+']" class="caption" id="'+optID+'"></textarea>'
					+'</div>'
					+'</div>'
					+'</div>'
					+'</div>';
		$('#'+this.addLinkID).before(divHTML);
	}
});

/*
Magic custom fields
*/
var magicCustom = magicFields.extend({
	initialize: function() {
		magicFields.prototype.initialize.apply(this, arguments);
		var label,
			field,
			customVar,
			num = 0;
		$.each($('#customGroup > div'), function(index, value) {
			num++;
			label = $('#'+value.id).find('.fieldLabel');
			field = $('#'+value.id).find('.fieldValue');
			customVar = $('#'+value.id).find('.variableField');
			this.makeCustomVar(customVar, label);
			this.insertSizers(field, num);
			/*
			new AutoSuggest(label,this.customLabels);
			*/
		}.bind(this));
	},
	addField: function() {
		var fieldCount = this.countFields()+1;
		var optID = this.fieldName+fieldCount;
		var incrementor = this.incrementLabel == true ? ' '+fieldCount : '';
		var customLabelTxt = this.labelInnerText;
		var customLabelID = 'customLabel'+fieldCount;
		var customValueID = 'customValue'+fieldCount;
		var customVarID = 'customVar'+fieldCount;
		var customValueTxt = this.fieldInnerText;
		var divHTML = '<div id="'+optID+'Group" class="row customFieldGroup">'
					+'<div class="span6">'
					+'<div class="control-group">'
					+'<div class="controls">'
					+'<input type="text" value="" class="span6 fieldLabel text" id="'+customLabelID+'" name="custom['+fieldCount+'][label]" autocomplete="off" value="" placeholder="Label" />'
					+'</div>'
					+'</div>'
					+'<div class="control-group">'
					+'<div class="controls">'
					+'<textarea id="'+customValueID+'" name="custom['+fieldCount+'][value]" class="span6 fieldValue" placeholder="Value">'
					+'</textarea>'
					+'</div>'
					+'</div>'
					+'<div class="control-group">'
					+'<label for="'+customVarID+'">'+_l('CUSTOM_VARIABLE_TEXT')+'</label>'
					+'<div class="controls">'
					+'<input type="text" class="shareField variableField uneditable-input" readonly="readonly" id="'+customVarID+'">'
					+'</div>'
					+'</div>'
					+'</div>'
					+'</div>';

		$('#'+this.addLinkID).before(divHTML);
		var label = $('#'+customLabelID);
		var field = $('#'+customValueID);
		var customVar = $('#'+customVarID);
		this.makeCustomVar(customVar,label);
		this.insertSizers(field, fieldCount);
		/*
		new AutoSuggest(label,this.customLabels);
		*/
	},
	insertSizers: function(obj, num) {
		var that = this;
		var sizerCont = '<span class="sizerContainer" id="customValue'+obj.attr('id')+num+'">'
			+'<a href="javascript:;" id="increaseBox'+num+'">+</a>'
			+'<a href="javascript:;" id="decreaseBox'+num+'">-</a>'
			+'<a href="javascript:;" title="'+_l('CUSTOM_DELETE_FIELD')+'" id="deleteLink_'+num+'" class="deleteLink">'+_l('CUSTOM_DELETE_FIELD')+'</a>'
			+'</span>';
		obj.after(sizerCont);
		var increaseBox = $('#increaseBox'+num);
		var decreaseBox = $('#decreaseBox'+num);
		increaseBox.mouseover(function() { window.status=_l('INCREASE_FIELD_SIZE'); return true; })
		increaseBox.mouseout(function() { window.status='';return true; })
		decreaseBox.mouseover(function() { window.status=_l('DECREASE_FIELD_SIZE'); return true; })
		decreaseBox.mouseout(function() { window.status='';return true; })

		$('#increaseBox'+num).click(function() {
			$(obj).css("height","+=100");
		});
		$('#decreaseBox'+num).click(function() {
			$(obj).css("height","-=100");
		});

		var deleteLink = $('#deleteLink_'+num);
		var optID = this.fieldName+num;
		deleteLink.click((function() {
			$('#'+optID).parent().parent().parent().parent().remove();
			if(this.countFields() === 0) {
				this.addField();
			}
		}).bind(this));
	},
	makeCustomVar: function(customVar, label) {
		var that = this;
		label.keyup(function(){
			that.assembleVar(customVar, label);
		});
		label.blur(function() {
			that.assembleVar(customVar, label);
		});
		label.focus(function() {
			that.assembleVar(customVar, label);
		});
	},
	assembleVar: function(customVar, label) {
		customVar.val('{'+'custom_var'+label.attr('id').replace(/[^0-9]/gi,'')+'}');
	},
});

/*
Magic custom Subcats
*/
var magicSubs = magicFields.extend({
	addField: function() {
		var fieldCount = this.countFields()+1;
		var optID = 'addSubcats'+fieldCount;
		var incrementor = this.incrementLabel == true ? ' '+fieldCount : '';
		this.labelDescrText = _l('SUBCAT_DESCR');
		var divHTML = '<div id="'+optID+'Group" class="row">'
			+'<div class="span6">'
			+'<div class="control-group">'
			+'<label for="'+optID+'" class="control-label">'+this.labelText+incrementor+'</label>'
			+'<div class="controls">'
			+'<input type="text" value="" name="'+this.fieldName+'[]" id="'+optID+'" class="text" />'
			+'</div>'
			+'</div>'
			+'<div class="control-group">'
			+'<label for="'+optID+'" class="control-label">'+this.labelDescrText+incrementor+'</label>'
			+'<div class="controls">'
			+'<textarea name="'+this.fieldName+'_descr[]"></textarea>'
			+'</div>'
			+'</div>'
			+'</div>';
		$('#'+this.addLinkID).before(divHTML);
	}
});

/*
Chack all boxes if an admin user
*/
function checkAll(obj) {
	if(obj) {
		if(!$('#noteText').exists()) {
			var sptxt = '<span id="noteText" class="alert alert-info formNote" style="visibility: visible; display: none;">'
				+_l('ADMIN_RIGHTS')
				+'</span>';
			obj.parent().parent().after(sptxt);
		}
		obj.click(function() {
			$('#noteText').fadeToggle();
			if($('#disabled').attr('checked', false)) {
				var checkBoxes = $("input[name=permissions\\[\\]]");
				checkBoxes.attr("checked", !checkBoxes.attr("checked"));
			}
		});
	}
};

expanse.sortCats = Backbone.View.extend({
	initialize: function() {
		var sort_submit = $('#sort_submit');
		if(!sort_submit.exists()){ return; }
		sort_submit.click(_.bind(this.changeMethod, this));
	},
	changeMethod: function() {
		form = $("#post");
        form.attr("method", "get");
        form.submit();
	}
});

$(function() {
	if($('#file_contents').exists()) {
		var textarea = $('textarea[name="file_contents"]').hide();
		editor.getSession().setValue(textarea.val());
		$('#theme_editor_form').submit(function() {
			var editor = ace.edit("file_contents");
			textarea.val(editor.getSession().getValue());
		});
	}
	new expanse.toggleBox;
	new expanse.sortCats;
	new expanse.confirmUninstall;
	new expanse.reorderMenu;
	new expanse.reorder;
	new expanse.hiliteInput;
	new expanse.setChecks;
	new expanse.markDelete;
	if($('.descr').exists()) {
		//FCKeditor.ReplaceAllTextareas('descr');
		$('.descr').wysihtml5({
			imagesUrl: './javascript/images.json.php',
			imageUpload: function(el) {
				var checkComplete, form;
				form = $(el).find('.image-upload-form');
				checkComplete = function() {
					var iframeContents, response, url;
					iframeContents = $("#uploadiframe")[0].contentWindow.document.body.innerHTML; //el.find('iframe').contents().find('body').text();
					if(iframeContents === "") {
						console.log('Result: '+iframeContents);
						return setTimeout(checkComplete, 2000);
					} else {
						response = $.parseJSON(iframeContents);
						console.log('Result: '+response);
						url = response[0].url;
						self.editor.currentView.element.focus();
						self.editor.composer.commands.exec("insertImage", {
		                    src: url
	                    });
						$('div.progress.upload').remove();
						$('.bootstrap-wysihtml5-insert-image-modal').modal('hide');
						return form.find('.progress').hide();
					}
				};
				return form.on('change', function() {
					form.attr('target', 'upload-iframe').attr('action', './javascript/bootstrap-upload.php');
					form.find('.progress').show();
					form.submit();
					return checkComplete();
				});
			}
		});
	}
	loadAlert();
//	new expanse.resizeEditor;
	new magicFields({
		firstField: '#option1',
		fieldName : 'extraoptions',
		addText:_l('ADD_OPTION'),
		clearText:_l('CLEAR_OPTION'),
		removeText:_l('REMOVE_OPTION'),
		labelText: _l('OPTION_LABEL'),
		confirmDelete : _l('OPTION_CLEAR_CONFIRM'),
		incrementLabel : true
	});
	new magicUploads({
		firstField: '#additional_images1',
		fieldName : 'additional_images',
		addText:_l('ADD_IMAGE'),
		clearText:_l('CLEAR_IMAGE'),
		removeText:_l('REMOVE_IMAGE'),
		labelText: ($('additionalImages') && $('additionalImages').className == 'addFiles') ? _l('IMAGE_LABEL_FILE') : _l('IMAGE_LABEL'),
		confirmDelete : _l('IMAGE_CLEAR_CONFIRM'),
		incrementLabel : true
	});
	new magicCustom({
		firstField: '#customLabel1',
		fieldName: 'customLabel',
		incrementLabel : true
	});
	new magicSubs({
		firstField: '#new_cat1',
		fieldName : 'addSubcats',
		addText:_l('ADD_SUBCAT'),
		clearText:_l('CLEAR_SUBCAT'),
		removeText:_l('REMOVE_SUBCAT'),
		labelText: _l('SUBCAT_LABEL'),
		confirmDelete : _l('SUBCAT_CLEAR_CONFIRM')
	});

	checkAll($('#adminCheck'));

	confirmEula();
	if(typeof window.Expanse == 'function') {
		// function exists, so we can now call it
		Expanse();
	}
	location.hash && $(location.hash + '.collapse').collapse('show');
	$("[rel=popover]").popover();

	/* Fix for dropdown menus, until Bootstrap manage to merge a change to fix officially */
	$('body').on('touchstart.dropdown', '.dropdown-menu', function (e) { e.stopPropagation(); }).on('touchstart.dropdown', '.dropdown-submenu', function (e) { e.preventDefault(); });

	Modernizr.load({
		test: Modernizr.ipad || Modernizr.ipod || Modernizr.iphone || Modernizr.fontface,
		yep : 'javascript/ios.js'
	});
});

/*
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
*/