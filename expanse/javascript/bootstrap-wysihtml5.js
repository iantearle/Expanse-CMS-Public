!function($, wysi) {
    "use strict";
     (function(wysi) {
          var undef;
          wysi.commands.customSpan = {
              exec: function(composer, command, sty) {
                  return wysi.commands.formatInline.exec(composer, 'insertHTML', "p", sty, new RegExp(sty));
              },
              state: function(composer, command, sty) {
                  return wysi.commands.formatInline.state(composer, 'insertHTML', "p", sty, new RegExp(sty));
              },

              value: function() {
                  return undef;
              }
        };
    })(wysi);

    var tpl = {

        "font-styles": function(locale) {
            var tmpl = "<li class='dropdown'>" +
                "<a class='btn dropdown-toggle' data-toggle='dropdown' href='#'>" +
                "<i class='icon-font'></i>&nbsp;<span class='current-font'></span>&nbsp;<b class='caret'></b>" +
                "</a>" +
                "<ul class='dropdown-menu'>";
            var stylesToRemove = locale.font_styles.remove || [];
            $.each(['normal','h1','h2','h3'], function(idx, key) {
                 if (stylesToRemove.indexOf(key) < 0) {
                   tmpl += "<li><a data-wysihtml5-command='formatBlock' data-wysihtml5-command-value='" + key + "'>" + locale.font_styles[key] + "</a></li>";
                 }
            });
            locale.font_styles.custom = locale.font_styles.custom || [];
            $.each(locale.font_styles.custom, function(style, displayName) {
                tmpl += "<li><a data-wysihtml5-command='customSpan' data-wsyihtml5-command-value='" + style + "'>" + displayName + "</a></li>";
            });
            tmpl += "</ul>"
            return tmpl;
        },

        "multiple-font-styles": function(locale) {
        	var tmpl = "<li class='dropdown'>" +
                "<a class='btn dropdown-toggle' data-toggle='dropdown' href='#'>" +
                "<i class='icon-chevron-left'></i><i class='icon-chevron-right'></i>&nbsp;<span class='current-font'></span>&nbsp;<b class='caret'></b>" +
                "</a>" +
                "<ul class='dropdown-menu'>";
            var stylesToRemove = locale.multiple_font_styles.remove || [];
            $.each(['div','h1','h2','h3'], function(idx, key) {
                 if (stylesToRemove.indexOf(key) < 0) {
                   tmpl += "<li><a data-wysihtml5-command='formatBlock' data-wysihtml5-command-value='" + key + "'>" + locale.multiple_font_styles[key] + "</a></li>";
                 }
            });
            locale.multiple_font_styles.custom = locale.multiple_font_styles.custom || [];
            $.each(locale.multiple_font_styles.custom, function(style, displayName) {
                tmpl += "<li><a data-wysihtml5-command='customSpan' data-wsyihtml5-command-value='" + style + "'>" + displayName + "</a></li>";
            });
            tmpl += "</ul>"
            return tmpl;
            /*
            return "<li>" +
                  "<div class='btn-group'>" +
                    "<a class='btn btn-wysihtml5' data-wysihtml5-command='formatBlock' data-wysihtml5-command-value='h1' title=''>" + locale.multiple_font_styles.h1+ "</a>" +
                    "<a class='btn btn-wysihtml5' data-wysihtml5-command='formatBlock' data-wysihtml5-command-value='h2' title=''>" + locale.multiple_font_styles.h2 + "</a>" +
                    "<a class='btn btn-wysihtml5' data-wysihtml5-command='formatBlock' data-wysihtml5-command-value='h3' title=''>" + locale.multiple_font_styles.h3 + "</a>" +
                  "</div>" +
                "</li>";
            */
        },

        "emphasis": function(locale) {
            return "<li>" +
                  "<div class='btn-group'>" +
                    "<a class='btn btn-wysihtml5' data-wysihtml5-command='bold' title='CTRL+B'>" + locale.emphasis.bold + "</a>" +
                    "<a class='btn btn-wysihtml5' data-wysihtml5-command='italic' title='CTRL+I'>" + locale.emphasis.italic + "</a>" +
                    "<a class='btn btn-wysihtml5' data-wysihtml5-command='underline' title='CTRL+U'>" + locale.emphasis.underline + "</a>" +
                  "</div>" +
                "</li>";
        },

        "lists": function(locale) {
            return "<li>" +
                  "<div class='btn-group'>" +
                    "<a class='btn btn-wysihtml5' data-wysihtml5-command='insertUnorderedList' title='" + locale.lists.unordered + "'><i class='icon-list'></i></a>" +
                    "<a class='btn btn-wysihtml5' data-wysihtml5-command='insertOrderedList' title='" + locale.lists.ordered + "'><i class='icon-th-list'></i></a>" +
                    "<a class='btn btn-wysihtml5' data-wysihtml5-command='Outdent' title='" + locale.lists.outdent + "'><i class='icon-indent-right'></i></a>" +
                    "<a class='btn btn-wysihtml5' data-wysihtml5-command='Indent' title='" + locale.lists.indent + "'><i class='icon-indent-left'></i></a>" +
                  "</div>" +
                "</li>";
        },

        "link": function(locale) {
            return "<li>" +
                  "<div class='bootstrap-wysihtml5-insert-link-modal modal hide fade'>" +
                    "<div class='modal-header'>" +
                      "<a class='close' data-dismiss='modal'>&times;</a>" +
                      "<h3>" + locale.link.insert + "</h3>" +
                    "</div>" +
                    "<div class='modal-body'>" +
                      "<input value='http://' class='bootstrap-wysihtml5-insert-link-url input-xlarge'>" +
                    "</div>" +
                    "<div class='modal-footer'>" +
                      "<a href='#' class='btn' data-dismiss='modal'>" + locale.link.cancel + "</a>" +
                      "<a href='#' class='btn btn-primary' data-dismiss='modal'>" + locale.link.insert + "</a>" +
                    "</div>" +
                  "</div>" +
                  "<a class='btn btn-wysihtml5' data-wysihtml5-command='createLink' title='" + locale.link.insert + "'><i class='icon-share'></i></a>" +
                "</li>";
        },

        "image": function(locale) {
            return "<li>" +
                  "<div class='bootstrap-wysihtml5-insert-image-modal modal hide fade'>" +
                    "<div class='modal-header'>" +
                      "<a class='close' data-dismiss='modal'>&times;</a>" +
                      "<ul class='nav nav-tabs'>"+
                        "<li class=''><a href='#images-insert' data-toggle='tab'>" + locale.image.insert + "</a></li>" +
                      "</ul>" +
                    "</div>" +
                    "<div class='modal-body'>" +
                      "<div class='tab-content'>" +
                          "<div class='tab-pane ' id='images-insert'>" +
                          	"<form class='form-inline'>" +
                            "<input type='text' value='http://' class='bootstrap-wysihtml5-insert-image-url' /> " +
                            "<a href='#' class='btn btn-primary' data-dismiss='modal'>" + locale.image.insert + "</a>" +
                            "</form>" +
                          "</div>" +
                      "</div>" +
                    "</div>" +
                    "<div class='modal-footer'>" +
                      "<a href='#' class='btn' data-dismiss='modal'>" + locale.image.cancel + "</a>" +
                    "</div>" +
                  "</div>" +
                  "<a class='btn btn-wysihtml5' data-wysihtml5-command='insertImage' title='" + locale.image.insert + "'><i class='icon-picture'></i></a>" +
                "</li>";
        },
        "image-features": function(locale) {
            var feat = {
                "list": {
                    "tab":
                        "<li class='active'><a href='#images-select' data-toggle='tab'>" + locale.image.select + "</a></li>",
                    "pane":
                        "<div class='tab-pane active' id='images-select'>" +
                          "<table class='table table-condensed table-bordered table-hover pointer' id='images-list'>" +
                          "</table>" +
                          "<form class='form-inline'>" +
                          "<input type='text' value='' class='new-width-value input-small' placeholder='Width' /> " +
                          "<input type='text' value='' class='new-height-value input-small' placeholder='Height' /> " +
                          "<a class='btn btn-primary selected-image'>" + locale.image.insert + "</a>" +
                          "</form>" +
                        "</div>"
                },
                "upload": {
                    "tab":
                        "<li><a href='#images-upload' data-toggle='tab'>" + locale.image.upload + "</a></li>",
                    "pane":
                        "<div class='tab-pane' id='images-upload'>" +
                          "<form action='' method='post' class='image-upload-form' enctype='multipart/form-data'>" +
                            "<input type='file' name='asset' id='asset' />" +
                            "<iframe class='hidden' name='upload-iframe' id='uploadiframe' src='' style='display:none;'>" +
                            "</iframe>"+
                            "<div class='progress progress-striped active' style='display:none;'><div class='bar' style='width: 100%;'></div></div>" +
                          "</form>" +
                        "</div>"
                }
            }, tmpl = '';

            return feat;
        },
        "html": function(locale) {
            return "<li>" +
                  "<div class='btn-group'>" +
                    "<a class='btn btn-wysihtml5' data-wysihtml5-action='change_view' title='" + locale.html.edit + "'><i class='icon-pencil'></i></a>" +
                  "</div>" +
                "</li>";
        },

        "color": function(locale) {
            return "<li class='dropdown'>" +
                  "<a class='btn dropdown-toggle btn-wysihtml5' data-toggle='dropdown' href='#'>" +
                    "<span class='current-color'>" + locale.colours.black + "</span>&nbsp;<b class='caret'></b>" +
                  "</a>" +
                  "<ul class='dropdown-menu'>" +
                    "<li><div class='wysihtml5-colors' data-wysihtml5-command-value='black'></div><a class='wysihtml5-colors-title' data-wysihtml5-command='foreColor' data-wysihtml5-command-value='black'>" + locale.colours.black + "</a></li>" +
                    "<li><div class='wysihtml5-colors' data-wysihtml5-command-value='silver'></div><a class='wysihtml5-colors-title' data-wysihtml5-command='foreColor' data-wysihtml5-command-value='silver'>" + locale.colours.silver + "</a></li>" +
                    "<li><div class='wysihtml5-colors' data-wysihtml5-command-value='gray'></div><a class='wysihtml5-colors-title' data-wysihtml5-command='foreColor' data-wysihtml5-command-value='gray'>" + locale.colours.gray + "</a></li>" +
                    "<li><div class='wysihtml5-colors' data-wysihtml5-command-value='maroon'></div><a class='wysihtml5-colors-title' data-wysihtml5-command='foreColor' data-wysihtml5-command-value='maroon'>" + locale.colours.maroon + "</a></li>" +
                    "<li><div class='wysihtml5-colors' data-wysihtml5-command-value='red'></div><a class='wysihtml5-colors-title' data-wysihtml5-command='foreColor' data-wysihtml5-command-value='red'>" + locale.colours.red + "</a></li>" +
                    "<li><div class='wysihtml5-colors' data-wysihtml5-command-value='purple'></div><a class='wysihtml5-colors-title' data-wysihtml5-command='foreColor' data-wysihtml5-command-value='purple'>" + locale.colours.purple + "</a></li>" +
                    "<li><div class='wysihtml5-colors' data-wysihtml5-command-value='green'></div><a class='wysihtml5-colors-title' data-wysihtml5-command='foreColor' data-wysihtml5-command-value='green'>" + locale.colours.green + "</a></li>" +
                    "<li><div class='wysihtml5-colors' data-wysihtml5-command-value='olive'></div><a class='wysihtml5-colors-title' data-wysihtml5-command='foreColor' data-wysihtml5-command-value='olive'>" + locale.colours.olive + "</a></li>" +
                    "<li><div class='wysihtml5-colors' data-wysihtml5-command-value='navy'></div><a class='wysihtml5-colors-title' data-wysihtml5-command='foreColor' data-wysihtml5-command-value='navy'>" + locale.colours.navy + "</a></li>" +
                    "<li><div class='wysihtml5-colors' data-wysihtml5-command-value='blue'></div><a class='wysihtml5-colors-title' data-wysihtml5-command='foreColor' data-wysihtml5-command-value='blue'>" + locale.colours.blue + "</a></li>" +
                    "<li><div class='wysihtml5-colors' data-wysihtml5-command-value='orange'></div><a class='wysihtml5-colors-title' data-wysihtml5-command='foreColor' data-wysihtml5-command-value='orange'>" + locale.colours.orange + "</a></li>" +
                  "</ul>" +
                "</li>";
        }
    };

    var templates = function(key, locale) {
        return tpl[key](locale);
    };

    var Wysihtml5 = function(el, options) {
        this.el = el;
        var toolbarOpts = options || defaultOptions;
        // add custom classes to the save class list */
        for(var k in toolbarOpts.customStyles) {
            toolbarOpts.parserRules.classes[k] = 1;
        }
        for(var t in toolbarOpts.customTemplates) {
          tpl[t] = toolbarOpts.customTemplates[t];
        }
        this.toolbar = this.createToolbar(el, toolbarOpts);
        this.editor =  this.createEditor(options);

        window.editor = this.editor;

        $('iframe.wysihtml5-sandbox').each(function(i, el){
            $(el.contentWindow).off('focus.wysihtml5').on({
                'focus.wysihtml5' : function(){
                    $('li.dropdown').removeClass('open');
                }
            });
        });
    };

    Wysihtml5.prototype = {

        constructor: Wysihtml5,

        createEditor: function(options) {
            options = options || {};
            options.toolbar = this.toolbar[0];

            var editor = new wysi.Editor(this.el[0], options);

            if(options && options.events) {
                for(var eventName in options.events) {
                    editor.on(eventName, options.events[eventName]);
                }
            }
            return editor;
        },

        createToolbar: function(el, options) {

            var self = this;
            var toolbar = $("<ul/>", {
                'class' : "wysihtml5-toolbar",
                'style': "display:none"
            });


            var culture = options.locale || defaultOptions.locale || "en";
            var imageFeatureTemplates = templates('image-features', locale[culture])

            locale[culture].font_styles.custom = options.customStyles;
            locale[culture].font_styles.remove = options.removeStyles;

            for(var key in defaultOptions) {

                var value = false;

                if(options[key] !== undefined) {
                    if(options[key] === true) {
                        value = true;
                    }
                } else {
                    value = defaultOptions[key];
                }


                if(key === 'imagesUrl' && typeof options[key] === 'string') {
                    toolbar.find('.nav.nav-tabs').append(imageFeatureTemplates.list.tab);
                    toolbar.find('.tab-content').append(imageFeatureTemplates.list.pane);

                    this.getImages(options[key]);
                }

                if (key === 'imageUpload') {
                    toolbar.find('.nav.nav-tabs').append(imageFeatureTemplates.upload.tab);
                    toolbar.find('.tab-content').append(imageFeatureTemplates.upload.pane);

                    options[key](toolbar);
                }

                if(value === true) {
                    toolbar.append(templates(key, locale[culture]));

                    if(key === "html") {
                        this.initHtml(toolbar);
                    }

                    if(key === "link") {
                        this.initInsertLink(toolbar);
                    }

                    if(key === "image") {
                        this.initInsertImage(toolbar);
                    }
                }
            }

            if(options.toolbar) {
                for(key in options.toolbar) {
                    toolbar.append(options.toolbar[key]);
                }
            }

            toolbar.find("a[data-wysihtml5-command='formatBlock']").click(function(e) {
                var target = e.target || e.srcElement;
                var el = $(target);
                self.toolbar.find('.current-font').text(el.html());
            });

            toolbar.find("a[data-wysihtml5-command='foreColor']").click(function(e) {
                var target = e.target || e.srcElement;
                var el = $(target);
                self.toolbar.find('.current-color').text(el.html());
            });

            this.el.before(toolbar);

            return toolbar;
        },

        getImages: function(imagesUrl) {
            var self = this;
            $.getJSON(imagesUrl, function(data) {
                var items = [];
                for (var key in data) {
                    if (data.hasOwnProperty(key)) {
                        items.push("<tr class='image-url pointer' data-image-url='" + data[key].url + "' data-image-width='" + data[key].width + "' data-image-height='" + data[key].height + "'><td><img src='" + data[key].url + "' width='50' height='auto' /> " + data[key].name + "</td></tr>");
                    }
                }
                $("#images-list").html(items.join())
                $('.image-url').on('click', function() {
                    var modal = $('.bootstrap-wysihtml5-insert-image-modal');
                	$('.selected-image').data('image-url', $(this).data('image-url'));
                	$('.new-width-value').val($(this).data('image-width'));
                	$('.new-height-value').val($(this).data('image-height'));
                    $('.selected-image').data('image-width', $(this).data('image-width'));
                    $('.selected-image').data('image-height', $(this).data('image-height'));
                });

                $('.new-width-value').keyup(function() {
		            var old_width = $('.image-url').data('image-width');
		            var old_height = $('.image-url').data('image-height');
		            var new_width = $(this).val();
		            var new_height = Math.round(new_width / (old_width / old_height));
		            $('.new-height-value').val(new_height);
		            $('.selected-image').data('image-width', new_width);
		            $('.selected-image').data('image-height', new_height);
			    });

			    $('new-height-value').keyup(function() {
			            var old_width = $('.image-url').data('image-width');
			            var old_height = $('.image-url').data('image-height');
			            var new_height = $(this).val();
			            var new_width = Math.round((old_width / old_height) * new_height);
			            $('.new-value-width').val(new_width);
			            $('.selected-image').data('image-width', new_width);
			            $('.selected-image').data('image-height', new_height);
			    });

                $('.selected-image').on('click', function() {
                	var url = $(this).data('image-url');
                    var width = $(this).data('image-width');
                    var height = $(this).data('image-height');
	                self.editor.composer.commands.exec("insertImage", {
	                    src: url,
	                	width: width,
	                	height: height
                    });
                    $('.bootstrap-wysihtml5-insert-image-modal').modal('hide');
                });
            });

        },

        initHtml: function(toolbar) {
            var changeViewSelector = "a[data-wysihtml5-action='change_view']";
            toolbar.find(changeViewSelector).click(function(e) {
                toolbar.find('a.btn').not(changeViewSelector).toggleClass('disabled');
            });
        },

        initInsertImage: function(toolbar) {
            var self = this;
            var insertImageModal = toolbar.find('.bootstrap-wysihtml5-insert-image-modal');
            var urlInput = insertImageModal.find('.bootstrap-wysihtml5-insert-image-url');
            var insertButton = insertImageModal.find('a.btn-primary');
            var initialValue = urlInput.val();

            var insertImage = function() {
                var url = urlInput.val();
                urlInput.val(initialValue);
                self.editor.currentView.element.focus();
                self.editor.composer.commands.exec("insertImage", {
                	src: url,
                	width: width,
                	height: height
                });
            };

            urlInput.keypress(function(e) {
                if(e.which == 13) {
                    insertImage();
                    insertImageModal.modal('hide');
                }
            });

            insertButton.click(insertImage);

            insertImageModal.on('shown', function() {
                urlInput.focus();
            });

            insertImageModal.on('hide', function() {
                self.editor.currentView.element.focus();
            });

            toolbar.find('a[data-wysihtml5-command=insertImage]').click(function() {
                var activeButton = $(this).hasClass("wysihtml5-command-active");

                if (!activeButton) {
                    insertImageModal.appendTo('body').modal('show');
                    insertImageModal.on('click.dismiss.modal', '[data-dismiss="modal"]', function(e) {
                        e.stopPropagation();
                    });
                    return false;
                }
                else {
                    return true;
                }
            });
        },

        initInsertLink: function(toolbar) {
            var self = this;
            var insertLinkModal = toolbar.find('.bootstrap-wysihtml5-insert-link-modal');
            var urlInput = insertLinkModal.find('.bootstrap-wysihtml5-insert-link-url');
            var insertButton = insertLinkModal.find('a.btn-primary');
            var initialValue = urlInput.val();

            var insertLink = function() {
                var url = urlInput.val();
                urlInput.val(initialValue);
                self.editor.currentView.element.focus();
                self.editor.composer.commands.exec("createLink", {
                    href: url,
                    target: "_blank",
                    rel: "nofollow"
                });
            };
            var pressedEnter = false;

            urlInput.keypress(function(e) {
                if(e.which == 13) {
                    insertLink();
                    insertLinkModal.modal('hide');
                }
            });

            insertButton.click(insertLink);

            insertLinkModal.on('shown', function() {
                urlInput.focus();
            });

            insertLinkModal.on('hide', function() {
                self.editor.currentView.element.focus();
            });

            toolbar.find('a[data-wysihtml5-command=createLink]').click(function() {
                var activeButton = $(this).hasClass("wysihtml5-command-active");

                if (!activeButton) {
                    insertLinkModal.appendTo('body').modal('show');
                    insertLinkModal.on('click.dismiss.modal', '[data-dismiss="modal"]', function(e) {
                        e.stopPropagation();
                    });
                    return false;
                }
                else {
                    return true;
                }
            });
        }
    };

    // these define our public api
    var methods = {
        resetDefaults: function() {
            $.fn.wysihtml5.defaultOptions = $.extend(true, {}, $.fn.wysihtml5.defaultOptionsCache);
        },
        bypassDefaults: function(options) {
            return this.each(function () {
                var $this = $(this);
                $this.data('wysihtml5', new Wysihtml5($this, options));
            });
        },
        shallowExtend: function (options) {
            var settings = $.extend({}, $.fn.wysihtml5.defaultOptions, options || {});
            var that = this;
            return methods.bypassDefaults.apply(that, [settings]);
        },
        deepExtend: function(options) {
            var settings = $.extend(true, {}, $.fn.wysihtml5.defaultOptions, options || {});
            var that = this;
            return methods.bypassDefaults.apply(that, [settings]);
        },
        init: function(options) {
            var that = this;
            return methods.shallowExtend.apply(that, [options]);
        },

    };

    $.fn.wysihtml5 = function ( method ) {
        if ( methods[method] ) {
            return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof method === 'object' || ! method ) {
            return methods.init.apply( this, arguments );
        } else {
            $.error( 'Method ' +  method + ' does not exist on jQuery.wysihtml5' );
        }
    };

    $.fn.wysihtml5.Constructor = Wysihtml5;

    var defaultOptions = $.fn.wysihtml5.defaultOptions = {
        "font-styles": true,
        "multiple-font-styles": false,
        "color": false,
        "emphasis": true,
        "lists": true,
        "html": true,
        "link": true,
        "image": true,
        "imagesUrl": '/assets.json',
        "imageUpload": true,
        events: {},
        customStyles: {},
        removeStyles: [],
        parserRules: {
            classes: {
                // (path_to_project/lib/css/wysiwyg-color.css)
                "wysiwyg-color-silver" : 1,
                "wysiwyg-color-gray" : 1,
                "wysiwyg-color-white" : 1,
                "wysiwyg-color-maroon" : 1,
                "wysiwyg-color-red" : 1,
                "wysiwyg-color-purple" : 1,
                "wysiwyg-color-fuchsia" : 1,
                "wysiwyg-color-green" : 1,
                "wysiwyg-color-lime" : 1,
                "wysiwyg-color-olive" : 1,
                "wysiwyg-color-yellow" : 1,
                "wysiwyg-color-navy" : 1,
                "wysiwyg-color-blue" : 1,
                "wysiwyg-color-teal" : 1,
                "wysiwyg-color-aqua" : 1,
                "wysiwyg-color-orange" : 1,
                "row": 1,
                "row-fluid": 1,
                "span1": 1,
                "span2": 1,
                "span3": 1,
                "span4": 1,
                "span5": 1,
                "span6": 1,
                "span7": 1,
                "span8": 1,
                "span9": 1,
                "span10": 1,
                "span11": 1,
                "span12": 1,
                "thumbnails": 1,
                "thumbnail": 1
            },
            tags: {
                "b":  {},
                "i":  {},
                "br": {},
                "ol": {},
                "ul": {},
                "li": {},
                "h1": {},
                "h2": {},
                "h3": {},
                "h4": {},
                "h5": {},
                "h6": {},
                "blockquote": {},
                "u": 1,
                "img": {
                    "check_attributes": {
                        "width": "numbers",
                        "alt": "alt",
                        "src": "url",
                        "height": "numbers"
                    }
                },
                "a":  {
                    set_attributes: {
                        target: "_blank",
                        rel:    "nofollow"
                    },
                    check_attributes: {
                        href:   "url" // important to avoid XSS
                    }
                },
                "span": {},
                "div": {},
                "p": "p",
                "small": 1,
                "hr": 1

            }
        },
        stylesheets: ["./css/wysiwyg-color.css", "./css/bootstrap.css", "./css/bootstrap-responsive.css"], // (path_to_project/lib/css/wysiwyg-color.css)
        locale: "en"
    };

    if (typeof $.fn.wysihtml5.defaultOptionsCache === 'undefined') {
        $.fn.wysihtml5.defaultOptionsCache = $.extend(true, {}, $.fn.wysihtml5.defaultOptions);
    }

    var locale = $.fn.wysihtml5.locale = {
        en: {
            font_styles: {
                normal: "Normal text",
                h1: "Heading 1",
                h2: "Heading 2",
                h3: "Heading 3"
            },
            multiple_font_styles: {
            	div: "div",
                h1: "Heading 1",
                h2: "Heading 2",
                h3: "Heading 3"
            },
            emphasis: {
                bold: "Bold",
                italic: "Italic",
                underline: "Underline"
            },
            lists: {
                unordered: "Unordered list",
                ordered: "Ordered list",
                outdent: "Outdent",
                indent: "Indent"
            },
            link: {
                insert: "Insert link",
                cancel: "Cancel"
            },
            image: {
                insert: "Insert image",
                select: "Select from library",
                upload: "Upload image",
                cancel: "Cancel"
            },
            html: {
                edit: "Edit HTML"
            },
            colours: {
                black: "Black",
                silver: "Silver",
                gray: "Grey",
                maroon: "Maroon",
                red: "Red",
                purple: "Purple",
                green: "Green",
                olive: "Olive",
                navy: "Navy",
                blue: "Blue",
                orange: "Orange"
            }
        }
    };

}(window.jQuery, window.wysihtml5);
