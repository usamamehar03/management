require.config({
	baseUrl: 'http://localhost/A.internship/IDprop/assets/js',
	paths:{
		knockout: 'vendor/knockout',
		jquery: "jquery-3.2.1.min",

		text: 'text',
		'notify' : 'https://cdn.jsdelivr.net/npm/bootstrap-notify@3.1.3/bootstrap-notify',
		'popper':"../plugins/bootstrap4/popper",
		'bootstrap': "https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.2.1/js/bootstrap.bundle.min",
		'TweenMax':"../plugins/greensock/TweenMax.min",
		'TimelineMax':"../plugins/greensock/TimelineMax.min",
		'ScrollMagic':"../plugins/scrollmagic/ScrollMagic.min",
		'animation':"../plugins/greensock/animation.gsap.min",
		'scrollToPlugin':"../plugins/greensock/ScrollToPlugin.min",
		'slick':"../plugins/slick-1.8.0/slick",
		'owl':"../plugins/OwlCarousel2-2.2.1/owl.carousel",
		'scrollTo':"../plugins/scrollTo/jquery.scrollTo.min",
		'easing':"../plugins/easing/easing",
		'moment':"https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment",
		'bootstrapToggle':"https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min",
		'TweenLite' : 'https://cdnjs.cloudflare.com/ajax/libs/gsap/2.0.2/TweenLite.min',

		//VM
		maintenanceOrdersViewModel:'viewModels/maintenanceOrdersViewModel',
		supplierAvailabilityViewModel:'viewModels/supplierAvailabilityViewModel',
		supplierDetailsViewModel:'viewModels/supplierDetailsViewModel',
		supplierFeesApprovalViewModel:'viewModels/supplierFeesApprovalViewModel',
		supplierFeesViewModel:'viewModels/supplierFeesViewModel',
		supplierOrdersViewModel:'viewModels/supplierOrdersViewModel',
		tenantOrdersViewModel:'viewModels/tenantOrdersViewModel',
		tenantOrdersViewModel_addversion:'viewModels/tenantOrdersViewModel_addversion',
		paymentRequestViewModel: 	'viewModels/paymentRequestViewModel',
		paymentsViewModel: 			'viewModels/paymentsViewModel',
		supplierMaterialsViewModel: 'viewModels/supplierMaterialsViewModel',
		journalViewModel: 			'viewModels/journalViewModel',
		tenantOrdersViewViewModel:  'viewModels/tenantOrdersViewViewModel',
		supplierFinalViewModel: 	'viewModels/supplierFinalViewModel',
		tenantOrderFeedbackViewModel:'viewModels/tenantOrderFeedbackViewModel',
		ledgerViewModel:  			'viewModels/ledgerViewModel',
		invoiceTemplateViewModel:   'viewModels/invoiceTemplateViewModel',
		invoiceViewModel :  		'viewModels/invoiceViewModel',
		invoiceDisplayViewModel : 	'viewModels/invoiceDisplayViewModel',
		feesViewModel:  			'viewModels/feesViewModel',
		
		//Components
		fileInput: 'components/fileInput',

		cropper: 'vendor/cropper',
		modal: 'bindingHandlers/modal',
		timedUpdate: 'bindingHandlers/timedUpdate',

		/*Extenders*/
		personalEmail: 'extenders/personalEmail',
	},
	shim: {
		'bootstrap':['jquery'],
		'TweenLite':['jquery'],
		'TweenMax':['jquery'],
		'TimelineMax':['jquery'],
		'owl':['jquery'],
		'bootstrapToggle':['jquery'],
		'ScrollMagic':['jquery'],
		'tweenMax':['jquery'],
		'timelineMax':['jquery'],
		'animation':['jquery','TweenMax','TimelineMax','ScrollMagic'],
		'easing':['jquery'],
		'scrollToPlugin':['TweenMax'],
		'slick':['jquery'],
	},

})