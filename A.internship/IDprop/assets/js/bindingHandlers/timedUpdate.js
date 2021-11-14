define(['knockout', 'jquery'], function (ko,$){
	ko.extenders.timedUpdate = function(target, option) {
		var callback = option.callback;
		var preventCallback = option.preventCallback;

		var _storedValue = undefined;
		target.waiting = ko.observable(false);
		var _ignore = false;

		target.notifySubscribers = function() {
			if ( !target.waiting()  ) {
				ko.subscribable.fn.notifySubscribers.apply(this, arguments);
			}
		};
		target.subscribe(function (oldValue) {
			_storedValue = oldValue;
		}, null, 'beforeChange');
		target.subscribe(function(newVal) {
			if( _ignore ){
				_ignore = false;
			}else{
				if( !preventCallback || !preventCallback() ){
					target.waiting(true);
					var d = callback(newVal);
					d.done(function () {
						target(newVal);
						_ignore = true;
						target.waiting(false);
						target.valueHasMutated();
					}).fail(function (err) {
						//console.log("WHaaaaaaaa: " + err);
						target(_storedValue);
						_ignore = true;
						target.waiting(false);
						target.valueHasMutated();
					});
				}
			}
		});
		return target;
	};
});