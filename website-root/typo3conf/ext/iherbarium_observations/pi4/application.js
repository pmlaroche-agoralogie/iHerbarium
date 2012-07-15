/*
 * jQuery File Upload Plugin JS Example 5.0.1
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://creativecommons.org/licenses/MIT/
 */

/*jslint nomen: false */
/*global $ */

$(function () {

	// Initialize the jQuery File Upload widget:
	$('#fileupload').fileupload({
		
		url: 'typo3conf/ext/iherbarium_observations/pi4/upload.php',
		
		    autoUpload: true,

		    addFilesToken: 
		document
		    .getElementById("addFilesFormDiv")
		    .children["addFilesForm"]
		    .children["addFilesToken"],
		
		    formData: function (form) {
		    var toSend = form.serializeArray();
				
		    var addFilesToken=
			document
			.getElementById("addFilesFormDiv")
			.children["addFilesForm"]
			.children["addFilesToken"];
				
		    toSend.push({name: 'addFilesToken', value: addFilesToken.value});
				
		    return toSend;
		},
			
		    destroy: function (e, data) {
		    
		    // Get addFilesToken.
		    var addFilesToken=
			document
			.getElementById("addFilesFormDiv")
			.children["addFilesForm"]
			.children["addFilesToken"];
		
		    // Add the addFilesToken to GET parameters.
		    data.url = data.url + (/\?/.test(data.url) ? '&' : '?') + $.param({addFilesToken: addFilesToken.value});
				
		    // Call parent function.
		    $.blueimpUI.fileupload.prototype
			.options.destroy.call(this, e, data);
		}
	    })
	
	    .bind('fileuploadadd', function (e, data) {		
		    /*
		      var obj = "";
		
		      $.each(data.files, function (index, file) {
		      $.each(file, function(i, val) {
		      obj = obj + "(" + i + "," + val + ")";
		      });
		      });
		
		
		      $.each(data.files, function (index, file) {
		      alert('Added file: ' + obj);
		      });
		    */
		})
	
	    .bind('fileuploaddone', function (e, data) {
		    
		    var fileUploadButtonBar = 
			$(this).find('.fileupload-buttonbar');

		    var doneButton =
			fileUploadButtonBar.find('.done');

                    doneButton.fadeIn();

		    return;
		})
	
	    .bind('fileuploaddestroy', function (e, data) {
		    return;
		});
	
	// Open download dialogs via iframes,
	// to prevent aborting current uploads:
	$('#fileupload .files a:not([target^=_blank])').live('click', function (e) {
		e.preventDefault();
		$('<iframe style="display:none;"></iframe>')
		    .prop('src', this.href)
		    .appendTo('body');
	    });

    });