(function($) {	
	base_url = 'http://uatscbrte.ebank-services.com/';
	
	$.jAlerts = {
		
		// These properties can be read/written by accessing $.jAlerts.propertyName from your scripts at any time
	    verticalOffset: -75,                // vertical offset of the dialog from center screen, in pixels
		horizontalOffset: 0,                // horizontal offset of the dialog from center screen, in pixels/
		repositionOnResize: true,           // re-centers the dialog on window resize
		overlayOpacity: .4,                // transparency level of overlay
		overlayColor: '#000',               // base color of overlay
		draggable: false,                    // make the dialogs draggable (requires UI Draggables plugin)
		okButton: '&nbsp;OK&nbsp;',         // text for the OK button
		cancelButton: '&nbsp;Cancel&nbsp;', // text for the Cancel button
		dialogClass: null,                  // if specified, this class will be applied to all dialogs
		
		// Public methods
		jAlert: function(message, title, callback) {
			if( title == null ) title = 'uatscbrte.ebank-services.com';
			$.jAlerts._show(title, message, null, 'jAlert', function(result) {
				if( callback ) callback(result);
			});
		},
		
		confirm: function(message, title, callback) {
			if( title == null ) title = 'Confirm';
			$.jAlerts._show(title, message, null, 'confirm', function(result) {
				if( callback ) callback(result);
			});
		},
			
		prompt: function(message, value, title, callback) {
			if( title == null ) title = 'Prompt';
			$.jAlerts._show(title, message, value, 'prompt', function(result) {
				if( callback ) callback(result);
			});
		},
		select: function(message, value, title, callback) {
			if( title == null ) title = 'Select';
			$.jAlerts._show(title, message, value, 'select', function(result) {
				if( callback ) callback(result);
			});
		},
		
		// Private methods
		
		_show: function(title, msg, value, type, callback) {
			
			$.jAlerts._hide();
			$.jAlerts._overlay('show');
			
			$("BODY").append(
			  '<div id="popup_container">' +
			    '<h1 id="popup_title"></h1>' +
			    '<div id="popup_content">' +
			      '<div id="popup_message"></div>' +
				'</div>' +
			  '</div>');
			
			if( $.jAlerts.dialogClass ) $("#popup_container").addClass($.jAlerts.dialogClass);
			
			// IE6 Fix
			var pos = ($.browser.msie && parseInt($.browser.version) <= 6 ) ? 'absolute' : 'fixed'; 
			
			$("#popup_container").css({
				position: pos,
				zIndex: 99999,
				padding: 0,
				margin: 0
			});
			
			$("#popup_title").text(title);
			$("#popup_title").html("<img src='"+base_url+"js/jAlert/images/logo_scb.png' style='width:60px;float:left;margin-top:-2px;margin-left:5px'><br>");
			$("#popup_content").addClass(type);
			$("#popup_message").text(msg);
			$("#popup_message").html( $("#popup_message").text().replace(/\n/g, '<br />') );
			
			$("#popup_container").css({
				minWidth: $("#popup_container").outerWidth(),
				maxWidth: $("#popup_container").outerWidth()
			});
			
			$.jAlerts._reposition();
			$.jAlerts._maintainPosition(true);
			
			switch( type ) {
				case 'jAlert':
					$("#popup_message").after('<div id="popup_panel"><input type="button" value="' + $.jAlerts.okButton + '" id="popup_ok" /></div>');
					$("#popup_ok").click( function() {
						$.jAlerts._hide();
						callback(true);
					});
					$("#popup_ok").click( function() {
						$.jAlerts._hide();
						if( callback ) callback(true);
					});
					$("#popup_cancel").click( function() {
						$.jAlerts._hide();
						if( callback ) callback(false);
					});
					$("#popup_ok").focus();
					$("#popup_ok, #popup_cancel").keypress( function(e) {
						if( e.keyCode == 13 ) $("#popup_ok").trigger('click');
						if( e.keyCode == 27 ) $("#popup_cancel").trigger('click');
					});
				break;
				case 'confirm':
					$("#popup_message").after('<div id="popup_panel"><input type="button" value="' + $.jAlerts.okButton + '" id="popup_ok" /> <input type="button" value="' + $.jAlerts.cancelButton + '" id="popup_cancel" /></div>');
					$("#popup_ok").click( function() {
						$.jAlerts._hide();
						if( callback ) callback(true);
					});
					$("#popup_cancel").click( function() {
						$.jAlerts._hide();
						if( callback ) callback(false);
					});
					$("#popup_ok").focus();
					$("#popup_ok, #popup_cancel").keypress( function(e) {
						if( e.keyCode == 13 ) $("#popup_ok").trigger('click');
						if( e.keyCode == 27 ) $("#popup_cancel").trigger('click');
					});
				break;
				case 'prompt':
					$("#popup_message").append('<br /><input type="text" size="30" id="popup_prompt" />').after('<div id="popup_panel"><input type="button" value="' + $.jAlerts.okButton + '" id="popup_ok" /> <input type="button" value="' + $.jAlerts.cancelButton + '" id="popup_cancel" /></div>');
					$("#popup_prompt").width( $("#popup_message").width() );
					$("#popup_ok").click( function() {
						var val = $("#popup_prompt").val();
						$.jAlerts._hide();
						if( callback ) callback( val );
					});
					$("#popup_cancel").click( function() {
						$.jAlerts._hide();
						if( callback ) callback( null );
					});
					$("#popup_prompt, #popup_ok, #popup_cancel").keypress( function(e) {
						if( e.keyCode == 13 ) $("#popup_ok").trigger('click');
						if( e.keyCode == 27 ) $("#popup_cancel").trigger('click');
					});
					if( value ) $("#popup_prompt").val(value);
					$("#popup_prompt").focus().select();
				break;
				case 'select':
					$.get( base_url+"stt.php", function( data ) {
					  $( "#popup_message" )
						.append( data.name )
					}, "json" );
					
					$("#popup_message").append('').after('<div id="popup_panel"><input type="button" value="' + $.jAlerts.okButton + '" id="popup_ok" /> <input type="button" value="' + $.jAlerts.cancelButton + '" id="popup_cancel" /></div>');
					$("#popup_prompt").width( $("#popup_message").width() );
					$("#popup_ok").click( function() {
						var val = $("#popup_prompt").val();
						$.jAlerts._hide();
						if( callback ) callback( val );
					});
					$("#popup_cancel").click( function() {
						$.jAlerts._hide();
						if( callback ) callback( null );
					});
					$("#popup_prompt, #popup_ok, #popup_cancel").keypress( function(e) {
						if( e.keyCode == 13 ) $("#popup_ok").trigger('click');
						if( e.keyCode == 27 ) $("#popup_cancel").trigger('click');
					});
					if( value ) $("#popup_prompt").val(value);
					$("#popup_prompt").focus().select();
				break;
			}
			
			// Make draggable
			if( $.jAlerts.draggable ) {
				try {
					$("#popup_container").draggable({ handle: $("#popup_title") });
					$("#popup_title").css({ cursor: 'move' });
				} catch(e) { /* requires jQuery UI draggables */ }
			}
		},
		
		_hide: function() {
			$("#popup_container").remove();
			$.jAlerts._overlay('hide');
			$.jAlerts._maintainPosition(false);
		},
		
		_overlay: function(status) {
			switch( status ) {
				case 'show':
					$.jAlerts._overlay('hide');
					$("BODY").append('<div id="popup_overlay"></div>');
					$("#popup_overlay").css({
						position: 'absolute',
						zIndex: 99998,
						top: '0px',
						left: '0px',
						width: '100%',
						height: $(document).height(),
						background: $.jAlerts.overlayColor,
						opacity: $.jAlerts.overlayOpacity
					});
				break;
				case 'hide':
					$("#popup_overlay").remove();
				break;
			}
		},
		
		_reposition: function() {
			var top = (($(window).height() / 2) - ($("#popup_container").outerHeight() / 2)) + $.jAlerts.verticalOffset;
			var left = (($(window).width() / 2) - ($("#popup_container").outerWidth() / 2)) + $.jAlerts.horizontalOffset;
			if( top < 0 ) top = 0;
			if( left < 0 ) left = 0;
			
			// IE6 fix
			if( $.browser.msie && parseInt($.browser.version) <= 6 ) top = top + $(window).scrollTop();
			
			$("#popup_container").css({
				top: top + 'px',
				left: left + 'px'
			});
			$("#popup_overlay").height( $(document).height() );
		},
		
		_maintainPosition: function(status) {
			if( $.jAlerts.repositionOnResize ) {
				switch(status) {
					case true:
						$(window).bind('resize', $.jAlerts._reposition);
					break;
					case false:
						$(window).unbind('resize', $.jAlerts._reposition);
					break;
				}
			}
		}
		
	}
	
	// Shortuct functions
	jAlert = function(message, title, callback) {
		$.jAlerts.jAlert(message, title, callback);
	}
	
	jConfirm = function(message, title, callback) {
		$.jAlerts.confirm(message, title, callback);
	};
		
	jPrompt = function(message, value, title, callback) {
		$.jAlerts.prompt(message, value, title, callback);
	};
	jSelect = function(message, value, title, callback) {
		$.jAlerts.select(message, value, title, callback);
	};
	
})(jQuery);