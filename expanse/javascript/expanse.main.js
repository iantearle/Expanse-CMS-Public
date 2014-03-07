/********* Expanse ***********/
jQuery.fn.exists = function() {
	return this.length > 0;
};
/*
Language get function
*/
function _l(constant) {
	var lconstant = $('#L_JS_' + constant.toUpperCase()).val();
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
	if (eulaAgreed) {
		if (!$('#eula_read').is(':checked')) {
			alert(_l('EULA'));
			e.preventDefault();
			return false;
		}
	}
	return true;
}

function confirmEula() {
	if ($('#eula_read')) {
		$('#post').submit(function() {
			if (!checkEula) {
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
	if (document.getElementById) {
		var e = $('#' + id);
		if (e) {
			if (e.attr('class') !== class1) {
				e.attr('class', class1);
			} else {
				e.attr('class', class2);
			}
		}
	}
}

function toggleBoxes(theElement) {
	var checkBoxes = $("input[name=" + theElement + "\\[\\]]");
	checkBoxes.attr("checked", !checkBoxes.attr("checked"));
}
window.expanse = window.expanse || {};
expanse = expanse || {};
/*
Toggle all checkboxes
*/
expanse.toggleBox = Backbone.View.extend({
	initialize: function() {
		this.itemList = $('#itemList');
		if (!this.itemList.exists()) {
			return;
		}
		this.createBox();
	},
	createBox: function() {
		var itemList = this.itemList;
		itemList.before('<div id="checkGroup"><label for="checkThemAll" class="checkbox"><input type="checkbox" class="checkbox" id="checkThemAll" name="checkThemAll" />' + _l('CHECK_BOXES') + '</label></div>');
		this.assign();
		$('#checkThemAll').click(_.bind(this.run, this));
	},
	run: function() {
		var inputs = $(':input');
		$.each(inputs, function(i) {
			if ($(this).is(':checkbox') && !$(this).is('#checkThemAll') && !$(this).is(':disabled')) {
				if ($(this).not('#mark_' + i)) {
					$(this).parent().after('<img src="images/markedfordeletion.gif" class="marked" id="mark_item_delete_' + i + '">');
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
			if (!$(this).is(':checkbox') || $(this).is('#checkThemAll') || $(this).is(':disabled')) {
				return;
			}
			$(this).click(function() {
				$(this).parent().after('<img src="images/markedfordeletion.gif" class="marked" id="mark_item_delete_' + i + '">');
				$(this).parent().parent().toggleClass('deleting');
			});
		});
	}
});
/*
Confirm Uninstallation of Expanse
*/
expanse.confirmUninstall = Backbone.View.extend({
	initialize: function() {
		if ($('#uninstall').exists()) {
			$('#uninstall').click(function(e) {
				var uninstall = confirm(_l('UNINSTALL'));
				if (!uninstall) {
					return (e.preventDefault) ? e.preventDefault() : e.returnValue = false;
				}
			});
		}
		if ($('#delete_uploads').exists()) {
			$('delete_uploads').click(function(e) {
				var deleteUploads = confirm(_l('DELETE_UPLOADS'));
				if (!deleteUploads) {
					e.preventDefault();
				}
			});
		}
		if ($('#delete_db').exists()) {
			$('#delete_db').click(function(e) {
				var deleteDB = confirm(_l('DELETE_DB'));
				if (!deleteDB) {
					e.preventDefault();
				}
			});
		}
		if ($('#delete_config').exists()) {
			$('#delete_config').click(function(e) {
				var deleteConfig = confirm(_l('DELETE_CONFIG'));
				if (!deleteConfig) {
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
		if ($('#keepMenu').exists()) {
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
				window.status = _l('REORDER_MENU');
				return true;
			});
			$('#reorderLink').mouseover(function() {
				window.status = '';
				return true;
			});
			var check_or_no = $('#cb_subcats');
			var includeSubcats = '<div class="control-group"><label for="include_subcats" class="checkbox"><input type="checkbox" id="include_subcats" ' + (check_or_no == 'yes' ? 'checked="checked"' : '') + ' />' + _l('MB_INCLUDE_SUBCATS') + '</label></div>';
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
		if ($('#include_subcats').attr('checked') === false) {
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
		if (check_or_no == 'yes') {
			return;
		}
		var subcats = $('.sub_cat');
		subcats.each(function() {
			$(this).hide();
		});
	},
	serialize: function() {
		var cats = '',
			items = '',
			id, itemsArr = [],
			catsArr = [],
			item_i = 0,
			cat_i = 0,
			vis_i = 1;
		var keepMenu = $('#keepMenu > div');
		keepMenu.each(function(el, i) {
			if (!$(this).is(':visible')) {
				return;
			}
			id = $(this).attr('id').replace('item_', '');
			if ($(this).hasClass('page')) {
				itemsArr[item_i] = 'keepMenu[' + vis_i + ']=' + id;
				item_i++;
			} else {
				catsArr[cat_i] = 'keepMenu[' + vis_i + ']=' + id;
				cat_i++;
			}
			vis_i++;
		});
		items = itemsArr.join('&');
		cats = catsArr.join('&');
		return {
			sections: cats,
			pages: items
		};
	}
});
/*
Reorder itemList
*/
expanse.reorder = Backbone.View.extend({
	sortContainer: Object,
	draggables: Array,
	initialize: function() {
		if ($('#itemList').exists()) {
			this.sortContainer = $('#itemList');
			var itemsCount = this.sortContainer.children().size();
			if (itemsCount <= 1) {
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
		if (($('#order_by').exists() && $('#order_by').val() !== 'order_rank')) {
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
Highlight Inputs
*/
expanse.hiliteInput = Backbone.View.extend({
	initialize: function() {
		var fields = $('.shareField');
		fields.each(function(i) {
			$(this).click(function(e) {
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
		if (obj) {
			var keepStateID = '#keepStateID' + obj.id;
			var keepMessage;
			if (obj.checked === true) {
				keepMessage = _l('KEEP_CHECKED');
			} else {
				keepMessage = _l('KEEP_UNCHECKED');
			}
			if ($(keepStateID).exists()) {
				$('#keepStateContainer').remove();
			}
			$('#' + obj.id).parent().append('<span id="keepStateContainer"><a href="javascript:;" id="keepStateID' + obj.id + '" class="keepState">' + keepMessage + '</a></span>').fadeIn();
			$(keepStateID).mouseover(function() {
				window.status = keepMessage;
				return true;
			});
			$(keepStateID).mouseover(function() {
				window.status = '';
				return true;
			});
			setTimeout(function() {
				$(keepStateID).fadeOut();
			}, 2000);
			$(keepStateID).click(function() {
				that.setCheckBox(obj);
			});
		}
	},
	setCheckBox: function(obj) {
		if (obj.checked === true) {
			$.cookie(obj.id, 'checked');
		} else {
			$.cookie(obj.id, 'unchecked');
		}
	},
	doCheckBoxes: function() {
		var docloc = document.URL;
		var add = /type=add/;
		var edit = /type=edit/;
		if ((docloc.match(add) && !docloc.match(edit))) {
			this.assignRemember();
			this.assignGets();
		}
	},
	getCheck: function(obj) {
		if (obj) {
			var cookie = $.cookie(obj.substring(1, obj.length));
			if (cookie !== null) {
				if (cookie == 'checked') {
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
			$('#' + value.id).click(function() {
				var fade = $(this).parent();
				that.mark(value, fade);
			});
		});
	},
	mark: function(value, fade) {
		if ($('#' + value.id).attr('checked')) {
			fade.closest('.imgBox').css({
				opacity: 0.5
			});
		} else {
			fade.closest('.imgBox').css({
				opacity: 1
			});
		}
	}
});

function loadAlert() {
	$('#post').submit(function() {
		$('body').append('<div style="display:block;position:absolute;top:0;left:0;z-index:90;width:' + $(document).width() + 'px;height:' + $(document).height() + 'px;" id="overlay"></div>');
		$('body').append('<blockquote class="overlayHelp" style="position:fixed;top:50%;left:50%;"><h4>' + _l('WAIT_NOTICE') + '</h4></blockquote>');
		$('#submit').hide();
		$('#submit').after(_l('WAIT_NOTICE'));
		return new expanse.validate();
	});
}
/*
Validae form posts
*/
expanse.validate = Backbone.View.extend({
	initialize: function() {
		$('#post').submit(function() {
			this.validateFields();
		});
	},
	validateFields: function(e) {
		if ($('#confirmpassword') && !$('#edit_user')) {
			if ($('#username') === '' || $('#password') === '') {
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
		if (!options.firstField) {
			return;
		}
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
		this.addLinkID = 'addLink_' + this.fieldName;
		this.removeLinkID = 'removeLink_' + this.fieldName;
		this.resetLinkID = 'resetLink_' + this.fieldName;
		this.run();
	},
	run: function() {
		this.optGroup.append('<a href="javascript:;" class="btn" id="' + this.addLinkID + '">' + this.addText + '</a> ');
		this.optGroup.append('<a href="javascript:;" class="btn btn-info" id="' + this.removeLinkID + '">' + this.removeText + '</a> ');
		this.optGroup.append('<a href="javascript:;" class="btn btn-danger" id="' + this.resetLinkID + '">' + this.clearText + '</a> ');
		$('#' + this.addLinkID).click(_.bind(this.addField, this));
		$('#' + this.removeLinkID).click(_.bind(this.removeFields, this));
		$('#' + this.resetLinkID).click(_.bind(this.resetFields, this));
	},
	addField: function() {
		alert('here');
		var fieldCount = this.countFields() + 1;
		var optID = 'new_cat' + fieldCount;
		var incrementor = this.incrementLabel === true ? ' ' + fieldCount : '';
		var divHTML = '<div id="' + optID + 'Group" class="row-fluid">' + '<div class="span6">' + '<div class="control-group">' + '<label for="' + optID + '">' + this.labelText + incrementor + '</label>' + '<div class="controls">' + '<input type="text" value="" name="' + this.fieldName + '[]" id="' + optID + '" class="text" />' + '</div>' + '</div>' + '</div>' + '</div>';
		$('#' + this.addLinkID).before(divHTML);
		$(optID + 'Descr').redactor({
			buttons: ['html', '|','formatting', '|', 'bold', 'italic', '|', 'image', 'file'],
			imageGetJson: 'javascript/redactor/modules/images.json.php',
			imageUpload: 'javascript/redactor/modules/upload.php',
			fileUpload: 'javascript/redactor/modules/file_upload.php'
		});
	},
	removeFields: function() {
		this.fieldCount = this.countFields();
		if(this.fieldCount === 1) {
			return;
		}
		var fieldset = this.optGroup;
		var olddiv = this.optGroup.attr('id') + this.fieldCount + 'Group';
		$('#' + olddiv).remove();
	},
	resetFields: function() {
		this.fieldCount = this.countFields();
		if (this.fieldCount === 1) {
			return;
		}
		var resetConf = confirm(this.confirmDelete);
		if (resetConf) {
			var fieldset = this.optGroup;
			fieldset.find('div').remove();
			this.addField();
		}
	},
	countFields: function() {
		this.Fields = this.optGroup.find('.row-fluid');
		return this.fieldCount = this.Fields.length + 1;
	}
});
/*
Magic Uploads, create more upload fields.
*/
var magicUploads = magicFields.extend({
	addField: function() {
		var fieldCount = this.countFields() + 1;
		var optID = this.fieldName + fieldCount;
		var incrementor = this.incrementLabel === true ? ' ' + fieldCount : '';
		var divHTML = '<div id="' + optID + 'Group" class="row-fluid">' + '<div class="span8">' + '<div class="control-group">' + '<label for="' + optID + '" class="control-label">' + this.labelText + incrementor + '</label>' + '<div class="controls">' + '<input type="file" name="' + this.fieldName + fieldCount + '"  id="' + optID + '"  class="formfields file" />' + '</div>' + '</div>' + '<div class="control-group">' + '<label for="' + optID + '" class="control-label">Caption' + incrementor + '</label>' + '<div class="controls">' + '<textarea name="caption[' + optID + ']" class="caption ' + optID + 'Descr" id="' + optID + '"></textarea>' + '</div>' + '</div>' + '</div>' + '</div>';
		$('#' + this.addLinkID).before(divHTML);
		$(optID + 'Descr').redactor({
			buttons: ['html', '|','formatting', '|', 'bold', 'italic', '|', 'image', 'file'],
			imageGetJson: 'javascript/redactor/modules/images.json.php',
			imageUpload: 'javascript/redactor/modules/upload.php',
			fileUpload: 'javascript/redactor/modules/file_upload.php'
		});
	}
});
/*
Magic custom fields
*/
var magicCustom = magicFields.extend({
	initialize: function() {
		magicFields.prototype.initialize.apply(this, arguments);
		var label, field, customVar, num = 0;
		$.each($('#customGroup > div'), function(index, value) {
			num++;
			label = $('#' + value.id).find('.fieldLabel');
			field = $('#' + value.id).find('.fieldValue');
			customVar = $('#' + value.id).find('.variableField');
			this.makeCustomVar(customVar, label);
			this.insertSizers(field, num);
/*
			new AutoSuggest(label,this.customLabels);
			*/
		}.bind(this));
	},
	addField: function() {
		var fieldCount = this.countFields() + 1;
		var optID = this.fieldName + fieldCount;
		var incrementor = this.incrementLabel === true ? ' ' + fieldCount : '';
		var customLabelTxt = this.labelInnerText;
		var customLabelID = 'customLabel' + fieldCount;
		var customValueID = 'customValue' + fieldCount;
		var customVarID = 'customVar' + fieldCount;
		var customValueTxt = this.fieldInnerText;
		var divHTML = '<div id="' + optID + 'Group" class="row-fluid customFieldGroup">' + '<div class="span6">' + '<div class="control-group">' + '<div class="controls">' + '<input type="text" value="" class="span=12 fieldLabel text" id="' + customLabelID + '" name="custom[' + fieldCount + '][label]" autocomplete="off" value="" placeholder="Label" />' + '</div>' + '</div>' + '<div class="control-group">' + '<div class="controls">' + '<textarea id="' + customValueID + '" name="custom[' + fieldCount + '][value]" class="span12 customFieldGroupDescr" placeholder="Value">' + '</textarea>' + '</div>' + '</div>' + '<div class="control-group">' + '<label for="' + customVarID + '">' + _l('CUSTOM_VARIABLE_TEXT') + '</label>' + '<div class="controls">' + '<input type="text" class="shareField variableField uneditable-input" readonly="readonly" id="' + customVarID + '">' + '</div>' + '</div>' + '</div>' + '</div>';
		$('#' + this.addLinkID).before(divHTML);
		var label = $('#' + customLabelID);
		var field = $('#' + customValueID);
		var customVar = $('#' + customVarID);
		this.makeCustomVar(customVar, label);
		this.insertSizers(field, fieldCount);
		$('.customFieldGroupDescr').redactor({
			buttons: ['html', '|','formatting', '|', 'bold', 'italic', '|', 'image', 'file'],
			imageGetJson: 'javascript/redactor/modules/images.json.php',
			imageUpload: 'javascript/redactor/modules/upload.php',
			fileUpload: 'javascript/redactor/modules/file_upload.php'
		});
/*
		new AutoSuggest(label,this.customLabels);
		*/
	},
	insertSizers: function(obj, num) {
		var that = this;
		var sizerCont = '<span class="sizerContainer" id="customValue' + obj.attr('id') + num + '">' + '<a href="javascript:;" id="increaseBox' + num + '">+</a>' + '<a href="javascript:;" id="decreaseBox' + num + '">-</a>' + '<a href="javascript:;" title="' + _l('CUSTOM_DELETE_FIELD') + '" id="deleteLink_' + num + '" class="deleteLink">' + _l('CUSTOM_DELETE_FIELD') + '</a>' + '</span>';
		obj.after(sizerCont);
		var increaseBox = $('#increaseBox' + num);
		var decreaseBox = $('#decreaseBox' + num);
		increaseBox.mouseover(function() {
			window.status = _l('INCREASE_FIELD_SIZE');
			return true;
		});
		increaseBox.mouseout(function() {
			window.status = '';
			return true;
		});
		decreaseBox.mouseover(function() {
			window.status = _l('DECREASE_FIELD_SIZE');
			return true;
		});
		decreaseBox.mouseout(function() {
			window.status = '';
			return true;
		});
		$('#increaseBox' + num).click(function() {
			$(obj).css("height", "+=100");
		});
		$('#decreaseBox' + num).click(function() {
			$(obj).css("height", "-=100");
		});
		var deleteLink = $('#deleteLink_' + num);
		var optID = this.fieldName + num;
		deleteLink.click((function() {
			$('#' + optID).parent().parent().parent().parent().remove();
			if (this.countFields() === 0) {
				this.addField();
			}
		}).bind(this));
	},
	makeCustomVar: function(customVar, label) {
		var that = this;
		label.keyup(function() {
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
		customVar.val('{' + 'custom_var' + label.attr('id').replace(/[^0-9]/gi, '') + '}');
	}
});
/*
Magic custom Subcats
*/
var magicSubs = magicFields.extend({
	addField: function() {
		var fieldCount = this.countFields();
		fieldCount = fieldCount++;
		var optID = 'new_cat' + fieldCount;
		var incrementor = this.incrementLabel === true ? ' ' + fieldCount : '';
		this.labelDescrText = _l('SUBCAT_DESCR');
		var divHTML = '<hr>' + '<div id="' + optID + 'Group" class="row-fluid">' + '<div class="span12">' + '<div class="control-group">' + '<label for="' + optID + '" class="control-label">' + this.labelText + incrementor + '</label>' + '<div class="controls">' + '<input type="text" value="" name="new_cat[' + fieldCount + ']" id="' + optID + '" class="text" />' + '</div>' + '</div>' + '<div class="control-group">' + '<label for="' + optID + '" class="control-label">' + this.labelDescrText + incrementor + '</label>' + '<div class="controls">' + '<textarea name="new_cat_descr[' + fieldCount + ']" class="span12 descr"></textarea>' + '</div>' + '</div>' + '</div>';
		$('#' + this.addLinkID).before(divHTML);
		$('.descr').redactor();
	}
});
/*
Chack all boxes if an admin user
*/
function checkAll(obj) {
	if (obj) {
		if (!$('#noteText').exists()) {
			var sptxt = '<span id="noteText" class="alert alert-info formNote" style="visibility: visible; display: none;">' + _l('ADMIN_RIGHTS') + '</span>';
			obj.parent().parent().after(sptxt);
		}
		obj.click(function() {
			$('#noteText').fadeToggle();
			if ($('#disabled').attr('checked', false)) {
				var checkBoxes = $("input[name=permissions\\[\\]]");
				checkBoxes.attr("checked", !checkBoxes.attr("checked"));
			}
		});
	}
}
expanse.sortCats = Backbone.View.extend({
	initialize: function() {
		var sort_submit = $('#sort_submit');
		if (!sort_submit.exists()) {
			return;
		}
		sort_submit.click(_.bind(this.changeMethod, this));
	},
	changeMethod: function() {
		form = $("#post");
		form.attr("method", "get");
		form.submit();
	}
});
$(function() {
	if ($('#file_contents').exists()) {
		var textarea = $('textarea[name="file_contents"]').hide();
		editor.getSession().setValue(textarea.val());
		$('#theme_editor_form').submit(function() {
			var editor = ace.edit("file_contents");
			textarea.val(editor.getSession().getValue());
		});
	}
	new expanse.toggleBox();
	new expanse.sortCats();
	new expanse.confirmUninstall();
	new expanse.reorderMenu();
	new expanse.reorder();
	new expanse.hiliteInput();
	new expanse.setChecks();
	new expanse.markDelete();
	$('.descr').redactor({
		imageGetJson: 'javascript/redactor/modules/images.json.php',
		imageUpload: 'javascript/redactor/modules/upload.php',
		fileUpload: 'javascript/redactor/modules/file_upload.php',
	//	pastePlainText: true
	});
	$('.customFieldGroupDescr').redactor({
		buttons: ['html', '|','formatting', '|', 'bold', 'italic', '|', 'image', 'file'],
		imageGetJson: 'javascript/redactor/modules/images.json.php',
		imageUpload: 'javascript/redactor/modules/upload.php',
		fileUpload: 'javascript/redactor/modules/file_upload.php',
	//	pastePlainText: true
	});
	loadAlert();
	new magicFields({
		firstField: '#option1',
		fieldName: 'extraoptions',
		addText: _l('ADD_OPTION'),
		clearText: _l('CLEAR_OPTION'),
		removeText: _l('REMOVE_OPTION'),
		labelText: _l('OPTION_LABEL'),
		confirmDelete: _l('OPTION_CLEAR_CONFIRM'),
		incrementLabel: true
	});
	new magicUploads({
		firstField: '#additional_images1',
		fieldName: 'additional_images',
		addText: _l('ADD_IMAGE'),
		clearText: _l('CLEAR_IMAGE'),
		removeText: _l('REMOVE_IMAGE'),
		labelText: ($('additionalImages') && $('additionalImages').className == 'addFiles') ? _l('IMAGE_LABEL_FILE') : _l('IMAGE_LABEL'),
		confirmDelete: _l('IMAGE_CLEAR_CONFIRM'),
		incrementLabel: true
	});
	new magicCustom({
		firstField: '#customLabel1',
		fieldName: 'customLabel',
		incrementLabel: true
	});
	new magicSubs({
		firstField: '#new_cat1',
		fieldName: 'addSubcats',
		addText: _l('ADD_SUBCAT'),
		clearText: _l('CLEAR_SUBCAT'),
		removeText: _l('REMOVE_SUBCAT'),
		labelText: _l('SUBCAT_LABEL'),
		confirmDelete: _l('SUBCAT_CLEAR_CONFIRM')
	});
	checkAll($('#adminCheck'));
	$('#toggleBox').click(function() {
		toggleBoxes('del');
	});
	confirmEula();
	if (typeof window.Expanse == 'function') {
		// function exists, so we can now call it
		Expanse();
	}
	$("[rel=popover]").popover(); /* Fix for dropdown menus, until Bootstrap manage to merge a change to fix officially */
	$('body').on('touchstart.dropdown', '.dropdown-menu', function(e) {
		e.stopPropagation();
	}).on('touchstart.dropdown', '.dropdown-submenu', function(e) {
		e.preventDefault();
	});
	if($("#totalsChart").exists()) {
		var ctx = $("#totalsChart").get(0).getContext("2d");
		new Chart(ctx).Doughnut(data1);
	}
	if($("#summaryChart").exists()) {
		var ctx2 = $("#summaryChart").get(0).getContext("2d");
		new Chart(ctx2).Bar(data);
	}
});