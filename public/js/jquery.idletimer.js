/**
 *
 */
// Using anonymous function call to prevent clash with other JS Libs
(function($) {

// idleTimer namespace loader
$.fn.idleTimer = function(options) {
	if (methods[options]) {
		return methods[options].apply(this, Array.prototype.slice.call(arguments, 1));
	} else if (typeof options === "object" || !options) {
		return methods.init.apply(this, arguments);
	} else {
		$.error("Method: '" + options + "' doesn't exists in jQuery.idleTimer !");
	}
};

// Default Settings	
$.fn.idleTimer.defaults = {
	// Config Section
	autoResumeOnWakeUp: true
	, enabled: false
	, timeout: 1800 * 1000
	, attachEvent: "keydown DOMMouseScroll mousewheel mousedown"
	
	// Events Section
	, onSleep: function(sender) { }
	, onWakeUp: function(sender) { }
};

var additionalSettings = {
	idle: false
	, timerHandler: -1
	, totalIdle : 0
}

// Method closure(s)
var methods = {
	init: function(options) {
		// Add some additional settings...
		var globalSettings = $.extend({}, $.fn.idleTimer.defaults, options, additionalSettings);
		
		return this.each(function(idx, element) {
			var $element = $(element);
			// Adding support for metadata plugin
			var settings = $.metadata ? $.extend({ }, globalSettings, $element.metadata()) : globalSettings;
			var events = $.trim(settings.attachEvent.split(" ").join(".idleTimer "));
			
			// Binding namespaced events which categorized as activity
			$element.bind(events, function() { activityDetected(element); });
			$element.data("settings.idleTimer", settings);
			
			if (settings.enabled) {
				methods.start();
			}
		});
	}
	
	, start: function() {
		return this.each(function(idx, element) {
			// In here this and element variable refer to the same reference
			var $element = $(element);
			var settings = $element.data("settings.idleTimer");
			
			settings.enabled = true;
			settings.timerHandler = setTimeout(function() { timeoutReached(element) }, settings.timeout);
			
			$element.data("settings.idleTimer", settings);
		});
	}
	
	, stop: function() {
		return this.each(function(idx, element) {
			var $element = $(element);
			var settings = $element.data("settings.idleTimer");
			
			// Kill if there is an active timeout
			if (settings.timerHandler != -1) {
				clearInterval(settings.timerHandler);
			}
			
			settings.timerHandler = -1;
			settings.enabled = false;
			$element.data("settings.idleTimer", settings);
		});
	}
	
	, destroy: function() {
		return this.each(function(idx, element) {
			var $element = $(element);
			var settings = $element.data("settings.idleTimer");
			
			// Kill if there is an active timeout
			if (settings.timerHandler != -1) {
				clearInterval(settings.timerHandler);
			}
			
			$element.unbind(".idleTimer");
			$element.removeData("settings.idleTimer");
		});
	}
};

/**
 * This method called in every event we specify in attachEvent settings
 * Used to tell that the object / user isn't idle (have activity)
 * We should reset the timeout counter if the timer is enabled
 */
function activityDetected(sender) {
	var $element = $(sender);
	var settings = $element.data("settings.idleTimer");
	
	// Kill previous timer instance to prevent unexpected result
	clearTimeout(settings.timerHandler);
	
	if (!settings.enabled) {
		return;
	}
	
	if (settings.idle) {
		// OK idle flag found then invoke the WakeUp Event
		// This flag only set at timeoutReached (Timeout already happen)
		settings.onWakeUp(sender);
		
		// Check whether we should resuming idle timer or not		
		if (!settings.autoResumeOnWakeUp) {
			settings.idle = false;
			$element.data("settings.idleTimer", settings);			
			return;
					
		}				
	}
	
	// Reset timeout counter whatever previous state (idle or active)
	settings.idle = false;		// mark as active	
	settings.timerHandler = setTimeout(function() { timeoutReached(sender); }, settings.timeout);
		
	$element.data("settings.idleTimer", settings);
}

function timeoutReached(sender) {
	var $sender = $(sender);
	var settings = $sender.data("settings.idleTimer");
	// Mark User as idle
	settings.idle = true;
	settings.totalIdle++;
	$sender.data("settings.idleTimer", settings);
	
	settings.onSleep(sender);
}

})(jQuery);
