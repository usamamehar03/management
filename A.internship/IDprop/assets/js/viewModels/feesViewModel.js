define(['knockout', 'jquery', 'moment', 'bootstrap', 'notify', 'modal', 'timedUpdate'], function (ko,$,moment){
	$(".data_input").on('input',function() {
		if($(this).val()!="")
		{
			$(this).next(".error").html("");
			$(this).css({"background-color": "white"});
			$(this).attr('style', 'background-color: white !important');
			if ($(this).attr('id')!='managementChargeType')
			{
				$("#addingError").html("");
				$("#addingError").attr('style', 'background-color: white !important');
			}
		}
	});
	function feesViewModel(){
		self.managementChargeType= 		ko.observable(null);
		self.managementFeeResidential= 	ko.observable(null);
		self.managementFeeStorage= 		ko.observable(null);
		self.managementFeeCommercial= 	ko.observable(null);
		self.managementFeeAssociations= ko.observable(null);
		self.flatFeePropertyManagement= ko.observable(null);
		self.onboardingFee= 	ko.observable(null); //owner
		self.reserveFundFee= 	ko.observable(null);
		self.discount= 			ko.observable(null);
		self.lateFee= 			ko.observable(null);
		self.daysLate= 			ko.observable(null);
		self.adminCharge= 		ko.observable(null);
		self.maxLateFees= 		ko.observable(null);
		self.findersFee= 		ko.observable(null);
		self.advertisingFee= 	ko.observable(null);
		self.screeningFeeBasic= ko.observable(null);
		self.screeningFeeAdvanced=ko.observable(null);
		self.earlyCancellationFee=ko.observable(null);
		self.lockoutFee= 		ko.observable(null);
		self.evictionFee= 		ko.observable(null);
		self.maintenanceFee= 	ko.observable(null);
		self.petDepositFee= 	ko.observable(null);
		self.petFee= 			ko.observable(null);
		self.petRent= 			ko.observable(null);
		self.nsfFee= 	        ko.observable(null);
		self.adding=            ko.observable(false);

		
		self.add = function(){
			self.adding(true);
			var obj = {
				'managementChargeType': 	self.managementChargeType(),
				"managementFeeResidential": self.managementFeeResidential(),
				"managementFeeStorage": 	self.managementFeeStorage(),
				"managementFeeCommercial": 	self.managementFeeCommercial(),
				"managementFeeAssociations":self.managementFeeAssociations(),
				"onboardingFee": 	   self.onboardingFee(), //owner
				"discount": 		   self.discount(),
				"lateFee":			   self.lateFee(),
				"daysLate": 		   self.daysLate(),
				"adminCharge": 		   self.adminCharge(),
				"maxLateFees": 		   self.maxLateFees(),
				"findersFee":		   self.findersFee(),
				"advertisingFee": 	   self.advertisingFee(),
				"screeningFeeBasic":   self.screeningFeeBasic(),
				"screeningFeeAdvanced":self.screeningFeeAdvanced(),
				"earlyCancellationFee":self.earlyCancellationFee(),
				"lockoutFee": 		self.lockoutFee(),
				"evictionFee": 		self.evictionFee(),
				"petDepositFee":	self.petDepositFee(),
				"petFee": 			self.petFee(),
				"petRent": 			self.petRent(),
				"nsfFee": 	        self.nsfFee()

				// "flatFeePropertyManagement":self.flatFeePropertyManagement(),
				// "reserveFundFee": 	   self.reserveFundFee(),
				// "maintenanceFee": 	self.maintenanceFee(),
			}
	 	 	addDefaultFees(obj)
			.done(function(data){
				self.managementChargeType(null);
				self.managementFeeResidential(null);
				self.managementFeeStorage(null);
				self.managementFeeCommercial(null);
				self.managementFeeAssociations(null);
				self.flatFeePropertyManagement(null);
				self.onboardingFee(null); //owner
				self.reserveFundFee(null);
				self.discount(null);
				self.lateFee(null);
				self.daysLate(null);
				self.adminCharge(null);
				self.maxLateFees(null);
				self.findersFee(null);
				self.advertisingFee(null);
				self.screeningFeeBasic(null);
				self.screeningFeeAdvanced(null);
				self.earlyCancellationFee(null);
				self.lockoutFee(null);
				self.evictionFee(null);
				self.maintenanceFee(null);
				self.petDepositFee(null);
				self.petFee(null);
				self.petRent(null);
				self.nsfFee(null);
			})
			.fail(function(data){
				if(data.status == 'err' )
				{
					if (!data.data['managementChargeTypeError'] && data.data['addingError']==true)
					{
						$("#addingError").html("Enter Atleast one Fees");
						$("#addingError").attr('style', 'background-color: #f8d7da !important');
						// $("#managementChargeTypeError").siblings('.data_input').children().css({"background-color": "white"});
					}
					else
					{
						for (var key of Object.keys(data.data)) 
						{
							if (key!='addingError')
							{
								if (key=="managementChargeTypeError")
								{
									$("#"+key).html("Empty");
								}
								else if(key=="daysLateError" || key=="maxLateFeesError")
								{
									$("#"+key).html("Only Number");
								}
								else
								{
									$("#"+key).html("Only Decimals");
								}
							}
							// $("#"+key).siblings('.data_input').css({"background-color": "#f8d7da"});
							$("#"+key).siblings('.data_input').attr('style', 'background-color: #f8d7da !important');
							$("#"+key).siblings('.data_input').children().css({"background-color": "white"});
						}
					}					
				}
			})
			.always(function(){
				self.adding(false);
			})

		}		
			
		function addDefaultFees(o){
			var d = $.Deferred();
			$.post( '../actions/forms/fees.php', { 'act':'addDefaultFees', 'data':o,'FORM_TOKEN':FORM_TOKEN})
			.done(function( data ) {
				// alert(data);
				// data=JSON.parse(data);
				if( data )
				{
					if( data.status == 'ok' )
					{
						d.resolve(data.data?data.data:[]);
					}
					else
					{
						d.reject(data.data?data:[]);
					}
				}
			})
			return d;
		}

		function AddTenantInvoices()
		{
			var d = $.Deferred();
			$.post( '../actions/forms/fees.php', { 'act':'CreateInvoices', 'FORM_TOKEN':FORM_TOKEN})
			.done(function( data ) {
				alert(data);
				data=JSON.parse(data);
				if( data )
				{
					if( data.status == 'ok' )
					{
						d.resolve(data.data?data.data:[]);
					}
					else
					{
						d.reject(data.data?data:[]);
					}
				}
			})
			return d;
		}

		self.CreaeTenantInvoices=function()
		{
			AddTenantInvoices()
			.done(function(data){
				alert('Tenant invoices creation successful');
			})
			.fail(function(data){
				if (data.status=='fail')
				{
					alert('No new Tenant invoice for create')
				}
				else
				{
					alert('sorry there was problem to complete this request. please contact your Administrator!');
				}
			})
		}

		function AddOwnerInvoices()
		{
			var d = $.Deferred();
			$.post( '../actions/forms/fees.php', { 'act':'CreateOwnerInvoices', 'FORM_TOKEN':FORM_TOKEN})
			.done(function( data ) {
				alert(data);
				data=JSON.parse(data);
				if( data )
				{
					if( data.status == 'ok' )
					{
						d.resolve(data.data?data.data:[]);
					}
					else
					{
						d.reject(data.data?data:[]);
					}
				}
			})
			return d;
		}

		self.CreatOwnerInvoices=function()
		{
			AddOwnerInvoices()
			.done(function(data){
				alert('owner invoices creation successful');
			})
			.fail(function(data){
				if (data.status=='fail')
				{
					alert('No new owner invoice for create')
				}
				else
				{
					alert('sorry there was problem to complete this request. please contact your Administrator!');
				}
			})
		}




		// create invoices 
		// self.CreaeTenantInvoices();
		// self.CreatOwnerInvoices();




		//all gets
		// self.getLateFees=function()
		// {
		// 	obj={
		// 		'state': 'latefess'
		// 	}
		// 	getUserData(obj)
		// 	.done(function(){
		// 		//
		// 	})
		// }
		// self.getLateFees();
		//
	}
	var em = document.getElementById('feesPage');
	if(em) ko.applyBindings(new feesViewModel(), em);
});

