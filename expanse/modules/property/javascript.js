function Expanse() {
	var localSearch = new GlocalSearch();

	function usePointFromPostcode(postcode, callbackFunction) {
	    localSearch.setSearchCompleteCallback(null,
	        function() {

	            if (localSearch.results[0]) {
	                var resultLat = localSearch.results[0].lat;
	                var resultLng = localSearch.results[0].lng;

	                document.forms['post'].latitude.value=resultLat;
	                document.forms['post'].longitude.value=resultLng;
	            }else{
	                alert("Postcode not found!");
	            }
	        });

	    localSearch.execute(postcode);
	}

		$('#title').focus();
		$('#postcode').blur(function() {
			var postcode = $("input#postcode").val()
			usePointFromPostcode(postcode);
		});
		if(!$('#latitude').val() && !$('#longitude').val() && $('input#postcode').val()) {
			var postcode = $("input#postcode").val()
			usePointFromPostcode(postcode);
		}
		$('#getPostcode').click(function() {
			var postcode = $("input#postcode").val()
			usePointFromPostcode(postcode);
		});

		$('.descr-admission').wysihtml5({
			imagesUrl: './javascript/images.json.php',
			imageUpload: function(el) {
				var checkComplete, form;
				form = $(el).find('.image-upload-form');
				checkComplete = function() {
					var iframeContents, response, url;
					iframeContents = el.find('iframe').contents().find('body').text();
					if (iframeContents === "") {
						return setTimeout(checkComplete, 2000);
					} else {
						response = $.parseJSON(iframeContents);
						url = response[0].url;
						self.editor.composer.commands.exec("insertImage", url);
						$('div.progress.upload').remove();
						$('.bootstrap-wysihtml5-insert-image-modal').modal('hide');
						return form.find('.progress').hide();
					}
				};
				return form.on('change', function() {
					form.attr('target', 'upload-iframe').attr('action', '/assets');
					form.find('.progress').show();
					form.submit();
					return checkComplete();
				});
			}
		});

		$('.descr-events').wysihtml5({
			imagesUrl: './javascript/images.json.php',
			imageUpload: function(el) {
				var checkComplete, form;
				form = $(el).find('.image-upload-form');
				checkComplete = function() {
					var iframeContents, response, url;
					iframeContents = el.find('iframe').contents().find('body').text();
					if (iframeContents === "") {
						return setTimeout(checkComplete, 2000);
					} else {
						response = $.parseJSON(iframeContents);
						url = response[0].url;
						self.editor.composer.commands.exec("insertImage", url);
						$('div.progress.upload').remove();
						$('.bootstrap-wysihtml5-insert-image-modal').modal('hide');
						return form.find('.progress').hide();
					}
				};
				return form.on('change', function() {
					form.attr('target', 'upload-iframe').attr('action', '/assets');
					form.find('.progress').show();
					form.submit();
					return checkComplete();
				});
			}
		});

		$('.descr-dates').wysihtml5({
			imagesUrl: './javascript/images.json.php',
			imageUpload: function(el) {
				var checkComplete, form;
				form = $(el).find('.image-upload-form');
				checkComplete = function() {
					var iframeContents, response, url;
					iframeContents = el.find('iframe').contents().find('body').text();
					if (iframeContents === "") {
						return setTimeout(checkComplete, 2000);
					} else {
						response = $.parseJSON(iframeContents);
						url = response[0].url;
						self.editor.composer.commands.exec("insertImage", url);
						$('div.progress.upload').remove();
						$('.bootstrap-wysihtml5-insert-image-modal').modal('hide');
						return form.find('.progress').hide();
					}
				};
				return form.on('change', function() {
					form.attr('target', 'upload-iframe').attr('action', '/assets');
					form.find('.progress').show();
					form.submit();
					return checkComplete();
				});
			}
		});

		$('.descr-other').wysihtml5({
			imagesUrl: './javascript/images.json.php',
			imageUpload: function(el) {
				var checkComplete, form;
				form = $(el).find('.image-upload-form');
				checkComplete = function() {
					var iframeContents, response, url;
					iframeContents = el.find('iframe').contents().find('body').text();
					if (iframeContents === "") {
						return setTimeout(checkComplete, 2000);
					} else {
						response = $.parseJSON(iframeContents);
						url = response[0].url;
						self.editor.composer.commands.exec("insertImage", url);
						$('div.progress.upload').remove();
						$('.bootstrap-wysihtml5-insert-image-modal').modal('hide');
						return form.find('.progress').hide();
					}
				};
				return form.on('change', function() {
					form.attr('target', 'upload-iframe').attr('action', '/assets');
					form.find('.progress').show();
					form.submit();
					return checkComplete();
				});
			}
		});

}