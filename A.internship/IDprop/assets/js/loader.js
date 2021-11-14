define(['jquery','knockout'],function($, ko){
	ko.components.register('fileInput', {
		viewModel: { require: 'components/fileInput' },
		template: { require: 'text!components/fileInput.html' }
	});

	ko.applyBindings();
})