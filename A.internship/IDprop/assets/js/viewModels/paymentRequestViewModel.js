define(['knockout', 'jquery','moment','bootstrap','notify','modal'], function (ko,$,moment){
	$(".data_input").on("input", function(){
		$(this).next(".error").html("");
		if($(this).is(':disabled'))
		{
			$(this).attr('style', 'background-color: #e9ecef ');
		}
		else
		{
			$(this).attr('style', 'background-color: white !important');
		}
	});
	function paymentRequestViewModel() {
		var purposefilter=['NSFBankFee', 'LockoutFee','EvictionFee','PetDeposit',
			'PetFee', 'Maintenance', 'ManagementFee','OnboardingFee',
			'AdminFee', 'FindersFee', 'AdvertisingFee', 'ScreeningFeeBasic',
			'ScreeningFeeAdvanced','CancellationFee','MaintenanceFee',
			'ReserveFundFee','ManagementFeeFlat','ManagementFeeAssociation',
			'TenantDeposit' 
		];
		self.purposelist=ko.observableArray([{name:null}]);
		self.timer = ko.observable(false);
		self.inited = ko.observable(false);
		self.filter = ko.observable(null);
		self.adding = ko.observable(false);
		self.paymentRequest = ko.observableArray([]);
		self.err = ko.observable(false);
		self.addPaymentRequestModal = ko.observable(null);		
		self.client = ko.observable(null);
		self.radioselected=ko.observable('oldinvoice');
		//add 
		self.clientname= ko.observable(null);
		self.email = ko.observable(null);
		self.amount = ko.observable(null);
		self.dueDate = ko.observable(null);
		self.description = ko.observable(null);
		self.notes = ko.observable(null);
		self.refrencenumber= ko.observable(null);
		self.invoicenumber=ko.observable(null);
		self.purpose=ko.observable(null);
		self.isold_invoice=ko.observable(true);
		self.isnew_invoice=ko.observable(false);
		// storage setup
		self.allstorage=ko.observable(null);
		self.isstorage=ko.observable(false);
		self.selected_storageunit= ko.observable(null);
		self.storage_unit_List=ko.observableArray([{storageunit_id:null}]);
		self.add_storageList=function(storageunit_id)
		{
			self.storage_unit_List.push({storageunit_id:storageunit_id});
		}
		//owner setup
		self.ownertype=ko.observable('Owner');
		self.selectedowner= ko.observable(null);
		self.ownerList=ko.observableArray(null);
		self.add_ownerList=function(id, address)
		{
			 self.ownerList.push({id:id, address:address});
		}
		//client
		self.selectedclient=ko.observable();
		self.clientlist = ko.observableArray([{name:null, user_id:null,
			owner_id:null, type:null}]);
		self.add_client=function(name, user_id, owner_id, type)
		{
			self.clientlist.push({name:name, user_id:user_id, owner_id:owner_id,
				type:type});
		}
		
		//decisions
		self.isamount=ko.observable(false);
		self.isduedate=ko.observable(false);
		//invoice
		self.selectedinvoice=ko.observable(null);
		self.invoicelist = ko.observableArray([{id:null,name:null}]);
		self.add_invoice=function(id, name)
		{
			 self.invoicelist.push({id:id, name:name});
		}
		self.removeInvoice = function (refrenceNumber) {
		    self.invoicelist.remove(function(invoicelist) {
		        return invoicelist.name == refrenceNumber;
		    });
		}
		//clients
		// self.removeIClient = function (paymentclient_id) {
		//     self.clientlist.remove(function(clientlist) {
		//         return clientlist.id == paymentclient_id;
		//     });
		// }
		//methods
		self.addPaymentRequest = function(){
			self.addPaymentRequestModal(true);
		}
		self.invoice_id=ko.observable(null);
		self.user_id=ko.observable(null);
		self.paymentclient_id=ko.observable(null);
		self.contactdetails_id=ko.observable(null);
		self.contact_id=ko.observable(null);
		self.add = function(){
			self.adding(true);
			var obj = {
				'storage_unit':  	self.selected_storageunit()!=undefined?
									self.selected_storageunit().storageunit_id:null,
				'ownertype': 		self.ownertype(),
				'Property_id': 		self.selectedowner()!=undefined?
									   self.selectedowner().id:null,
				'client': 			self.selectedclient()!=undefined?
										self.selectedclient().user_id:null,
				'owner_id': 		self.selectedclient()!=undefined?
										self.selectedclient().owner_id:null,
				'invoice_id': 		self.selectedinvoice()!=undefined?
				 						self.selectedinvoice().id:null,
				'contactdetails_id':self.contactdetails_id(),
				'contact_id': 		self.contact_id(),
				'email': 			self.email(),
				'amount': 			self.amount(),
				'duedate': 			self.dueDate(),
				'description': 		self.description(),
				'isnewinvoice': 	self.isnew_invoice(),
				'refrencenumber': 	self.refrencenumber(),
				'invoicenumber': 	self.invoicenumber(),
				'purpose': 			self.purpose()!=undefined?
										self.purpose().name:null,
				'purposefilter': 	purposefilter,
				'notes': 			self.notes()					
			}
	 	 	addPaymentRequest(obj)
			.done(function(data){
				self.DisplayData();
				if (self.selectedinvoice()!=null)
				{
					self.removeInvoice(self.selectedinvoice().name);
					if (self.ownertype()=='Property')
					{
						self.getInvoiceList('getproperty_invoice_list', 
							self.selectedowner().id);
					}
					else if (self.ownertype()=='Storage')
					{
						self.getInvoiceList('getstorage_invoice_list', 
							self.selected_storageunit().storageunit_id);
					}
				}
				self.selectedinvoice(null);
				self.refrencenumber(null);
				self.invoicenumber(null);
				// self.purposelist.removeAll();
				self.purpose(null);
				$(".data_input").trigger('input');
			})
			.fail(function(data){
				if (data.status=='err')
				{
					for (var key of Object.keys(data.data))
					{
						if (key=='amountError')
						{
							$("#"+key).html('Numbers Only');
						}
						else if (key=='descriptionError' || key=='notesError'||
						key=='invoicenumberError'||key=='invoicenumberError')
						{
							$("#"+key).html('Text only');
						}
						else
						{
							$("#"+key).html('Empty');
						}
						$("#"+key).siblings('.data_input').css({"background-color": "#f8d7da"});
						$("#"+key).siblings('.data_input').attr('style', 
							'background-color: #f8d7da!important');
					 	$("#"+key).siblings('.data_input').children().attr('style', 
							'background-color: white');
					}
				}
			})
			.always(function(){
				self.adding(false);
			})			
		}
		/*		
		self.getData = function(){
			self.paymentRequest([]);
			getData()
			.done(function(data){
				var tmp = [];
				tmp = $.map(data,function(d){
					return new PaymentRequest(d);
				})
				self.paymentRequest(tmp);
			});
			self.inited(true);
		}
		function PaymentRequest(data){
			var paymentRequest = this;
			paymentRequest.ID = data.ID;
			paymentRequest.invoice = ko.observable(data.invoice ? data.invoice : null);
			paymentRequest.invoice.subscribe(function(newVal){
				editPaymentRequest({'ID':paymentRequest.ID,'invoice':newVal});
			})	
			paymentRequest.client = ko.observable(data.client ? data.client : null);
			paymentRequest.client.subscribe(function(newVal){
				editPaymentRequest({'ID':paymentRequest.ID,'client':newVal});	
			})	
			paymentRequest.email = ko.observable(data.email ? data.email : null);
			paymentRequest.email.subscribe(function(newVal){
				editPaymentRequest({'ID':paymentRequest.ID,'email':newVal});
			})
			paymentRequest.name = ko.observable(data.name ? data.name : null);			
			paymentRequest.name.subscribe(function(newVal){
				editPaymentRequest({'ID':paymentRequest.ID,'name':newVal});
			})
			paymentRequest.amount = ko.observable(data.amount ? data.amount : null);
			paymentRequest.amount.subscribe(function(newVal){
				editPaymentRequest({'ID':paymentRequest.ID,'amount':newVal});
			})
			paymentRequest.dueDate = ko.observable(data.dueDate ? data.dueDate : null);
			paymentRequest.dueDate.subscribe(function(newVal){
				editPaymentRequest({'ID':paymentRequest.ID,'dueDate':newVal});
			})
			paymentRequest.purpose = ko.observable(data.purpose ? data.purpose : null);
			paymentRequest.purpose.subscribe(function(newVal){
				editPaymentRequest({'ID':paymentRequest.ID,'purpose':newVal});
			})
			paymentRequest.notes = ko.observable(data.notes ? data.notes : null);
			paymentRequest.notes.subscribe(function(newVal){
				editPaymentRequest({'ID':paymentRequest.ID,'notes':newVal});
			})
			paymentRequest.deleteMe = function(){
				deletePaymentRequest(paymentRequest.ID)
				.done(function(data){
					self.paymentRequest.remove(paymentRequest);
				});
			}
		}
		
		*/
		function addPaymentRequest(o){
			var d = $.Deferred();
			$.post( '../actions/forms/payment_request.php', { 'act':'addPaymentRequest', 'FORM_TOKEN' : FORM_TOKEN, 'data':o})
			.done(function( data ) {
				// alert(data);
				// data= JSON.parse(data);
				if( data ){
					if( data.status == 'ok' )
					{
						d.resolve(data.data?data.data:[]);
					}
					else
					{
						d.reject(data?data:[]);
					}
				}
			})
			return d;
		}
		function GetUserWithEmail(o)
		{
			var d = $.Deferred();
    		$.post( '../actions/forms/payment_request.php', { 'act':'GetUserFromEmail', 'FORM_TOKEN' : FORM_TOKEN, 'data':o})
			.done(function( data ) {
				// alert(data);
				// data=JSON.parse(data);
				if( data ){
					if( data.status == 'ok' )
					{	
						d.resolve(data.data?data.data:[]);
					}
					else
					{
						d.reject(data?data:[]);
					}				
				}
			})
			return d;
		}
		/*
		function editPaymentRequest(o,res){
			$.post( '../actions/forms/payment_request.php', { 'act':'editPaymentRequest', 'changes':o})
			.done(function( data ) {
				if( data ){
					if( data.status == 'ok' ){

					}else{

					}
				}
			})
			.fail(function() {

			})
		}
		function deletePaymentRequest(order_id) {
			var d = $.Deferred()
			$.post('../actions/forms/payment_request.php',{
				'act':'deletePaymentRequest',
				'order_id':order_id,
				//'FORM_TOKEN' : FORM_TOKEN,
			}).done(function(data) {
				if (data.status == 'ok') {
					d.resolve(data.data?data.data:[]);
				}else{
					d.reject();
				}
			})
			.fail(function () {
				d.reject();
			})
			return d;
		}
		self.getData();	
		*/
		self.disabledhandler=function()
		{
			if($('#amount').is(':disabled')==false)
			{
				if (self.amount()!=null) {
					$('#amount').attr('style', 'background-color: white !important');
				}	
			}
			else
			{
				$('#amount').attr('style', 'background-color: #e9ecef !important');
			}

			if($('#dueDate').is(':disabled')==false)
			{
				if (self.dueDate()!=null)
				{
					$('#dueDate').attr('style', 'background-color: white !important');
				}
			}
			else
			{
				$('#dueDate').attr('style', 'background-color: #e9ecef !important');	
			}
		}
		self.DisplayData=function(decisions=false, decisions1=false, amount=null,
			duedate=null, service=null, description=null)
		{
			self.isamount(decisions);
			self.isduedate(decisions1);
			self.disabledhandler();
			self.amount(amount);
			self.dueDate(duedate);
			self.description(service);
			self.notes(description);
		}
		self.displayclient_data=function(name=null, email=null, contact_id=null,
			contactdetails_id=null)
		{
			self.clientname(name);
			self.email(email);
			self.contact_id(contact_id);
			self.contactdetails_id(contactdetails_id);
		}
		self.LoadData=function(data,decisions,decisions1)
		{
			self.DisplayData(decisions, decisions1, data.amount, data.duedate, 
				data.service, data.description);
		}
		//all gets
		self.load_storageunits=function(data)
		{
			self.storage_unit_List.removeAll();
			for (var key of Object.keys(data))
			{
				self.add_storageList(data[key].id);
			}
			self.isstorage(true);
		}
		self.getownerList=function(name,sub_state=null)
		{
			var obj={
				'state': 		name,
				'sub_state': 	sub_state
			}
			GetUserWithEmail(obj)
			.done(function(data){
				self.ownerList.removeAll();
				self.selectedowner(null);
				self.isstorage(false);
				ownercount = Object.keys(data).length;
				if (ownercount>0)
				{
					for (var key of Object.keys(data))
					{
						self.add_ownerList(data[key].id, data[key].address);
					}
				}
				if (self.ownertype()=='Storage')
				{
					self.allstorage(data);
				}
			})
			.fail(function(){
				self.ownerList.removeAll();
				self.selectedowner(null);
				self.clientlist.removeAll();
				self.selectedclient(null);
				self.invoicelist.removeAll();
				self.selectedinvoice(null);
			})
		}
		self.getClientList=function(name,sub_state=null,id)
		{
			var obj={
				'state': 	name,
				'sub_state':sub_state,
				'id': 		id
			}
			GetUserWithEmail(obj)
			.done(function(data){
				self.clientlist.removeAll();
				self.selectedclient(null);
				count = Object.keys(data).length;
				if (count>0)
				{
					var i=0;
					while(i<count)
					{
						self.add_client(data[i].name, data[i].user_id, 
							data[i].owner_id, data[i].type);
						i++;
					}
				}
			})
			.fail(function(){
				self.clientlist.removeAll();
				self.selectedclient(null);
				self.invoicelist.removeAll();
				self.selectedinvoice(null);
			})

		}
		self.getInvoiceList=function(name, id)
		{
			var obj={
				'state': 	name,
				'id': 		id,
				'user_id': 	self.selectedclient().user_id,
				'owner_id': self.selectedclient().owner_id,
				'type': 	self.selectedclient().type
			}
			GetUserWithEmail(obj)
			.done(function(data){
				self.invoicelist.removeAll();
				self.selectedinvoice(null);
				if(data['invoicelist']!=null)
				{
					invoices=data['invoicelist'];
					invoicecount = Object.keys(invoices).length;
					if (invoicecount>0)
					{
						var i=0;
						while(i<invoicecount)
						{
							self.add_invoice(invoices[i].ID,invoices[i].InvoiceNumber);
							i++;
						}
					}
				}

				if (data['clientdata']!=null)
				{
					self.displayclient_data(self.selectedclient().name,
						data['clientdata'].email, data['clientdata'].contact_id,
						data['clientdata'].contactdetails_id);
				}
			})
			.fail(function(){
				self.invoicelist.removeAll();
				self.selectedinvoice(null);
				self.displayclient_data();
			})
		}

		self.getInvoiceDetail=function(name)
		{
			self.isnew_invoice(false);
			var obj={
				'state': name,
				'invoice_id':self.selectedinvoice().id,
				'invoicenumber':self.selectedinvoice().name,
				'user_id':self.selectedclient().user_id,
				'owner_id':self.selectedclient().owner_id
			}
			GetUserWithEmail(obj)
			.done(function(data){
				self.LoadData(data[0],true,true);
				$(".data_input").trigger('input');
			})
			.fail(function(){
				self.DisplayData();
				$(".data_input").trigger('input');
			})
		}
		//get owner_list
		ownertype.subscribe(function(val){
			if (val!='Owner')
			{
				var sub_state=self.radioselected()=='newinvoice'?'newinvoice'
						: null;
				if (val=='Property')
				{
					self.getownerList('propertyid_list',sub_state);
				}
				else if (val=='Storage')
				{
					self.getownerList('storageid_list',sub_state);
				}
			}
			else
			{
				self.ownerList.removeAll();
				self.selectedowner(null);
			}
		})
		//get clients
		selectedowner.subscribe(function(val){
			if (val!=null )
			{
				if (val.id!=null)
				{
					var sub_state=self.radioselected()=='newinvoice'?'newinvoice'
						: null;
					if (self.ownertype()=='Property')
					{
						self.getClientList('property-client-list',sub_state, 
							val.id);
					}
					else if (self.ownertype()=='Storage')
					{
						self.load_storageunits(self.allstorage()[self.selectedowner().id].storageunits);
					}
				}
				else
				{
					self.clientlist.removeAll();
					self.selectedclient(null);
					self.storage_unit_List.removeAll();
					self.isstorage(false);				
				}
			}
			else
			{
				self.clientlist.removeAll();
				self.selectedclient(null);
				self.storage_unit_List.removeAll();
				self.isstorage(false);
			}
		})
		selected_storageunit.subscribe(function(val)
		{
			var sub_state=self.radioselected()=='newinvoice'?'newinvoice'
						: null;
			if (val!=null )
			{
				self.getClientList('storage-client-list',sub_state, val.storageunit_id);
			}
			else
			{
				self.clientlist.removeAll();
				self.selectedclient(null);
			}
		})
		//get invoice list
		// self.manageclients=function()
		// {

		// }
		// self.manageclients();
		selectedclient.subscribe(function(val){
			if (val!=null )
			{
				self.purposelist.removeAll();
				self.purpose(null);
				if($('#amount').is(':disabled')==false)
					{
						$('#amount').attr('style', 'background-color: white !important');	
					}
				if (self.ownertype()=='Property')
				{
					self.getInvoiceList('getproperty_invoice_list', 
						self.selectedowner().id);
					if (self.selectedclient().type=='tenant')
					{
						self.purposelist.removeAll();
						self.purposelist.push({name:"TenantRent"}
							,{name:"TenantLateFees"},{name:"TenantUtilities"},
							{name:"TenantDamage"},{name:"TenantDeposit"},
							{name:"LockoutFee"},{name:"EvictionFee"},
							{name:"PetDeposit"},{name:"PetFee"},
							{name:"NSFBankFee"},{name:"AdminFee"}
						);	
					}
				}
				else if (self.ownertype()=='Storage')
				{
					self.getInvoiceList('getstorage_invoice_list', 
						self.selected_storageunit().storageunit_id);
					if (self.selectedclient().type=='tenant')
					{
						self.purposelist.removeAll();
						self.purposelist.push({name:"TenantLateFees"},
							{name:"TenantDamage"},{name:"TenantStorage"},
							{name:"TenantDeposit"},{name:"AdminFee"},
							{name:"NSFBankFee"},{name:'EvictionFee'}
						);
					}
				}

				if (self.selectedclient().type=='owner')
				{
					self.purposelist.removeAll();
					self.purposelist.push({name:"OwnerPays"},
						{name:"OwnerReceives"},{name:"InvestorPays"},
						{name:"InvestorReceives"},{name:"MaintenanceFee"},
						{name:"OnboardingFee"},{name:"AdminFee"},
						{name:"FindersFee"},{name:"AdvertisingFee"},
						{name:"ScreeningFeeBasic"},{name:"ScreeningFeeAdvanced"},
						{name:"CancellationFee"},{name:"NSFBankFee"},
						{name:"ReserveFundFee"},{name:"ManagementFeeFlat"},
						{name:"ManagementFeeAssociation"},{name:"Supplier"}
					);
				}
				//
			}
			else
			{
				self.invoicelist.removeAll();
				self.selectedinvoice(null);
				self.displayclient_data();
				self.purposelist.removeAll();
				self.purpose(null);
			}
		})
		//get invoice data
		selectedinvoice.subscribe(function(val){
			if (val!=null )
			{
				if (self.ownertype()=='Property')
				{
					self.getInvoiceDetail('property_invoicedata');
				}
				else if (self.ownertype()=='Storage')
				{
					self.getInvoiceDetail('storage_invoicedata');
				}		
			}
			else
			{
				self.DisplayData();
			}
		})
		//
		radioselected.subscribe(function(val)
		{
			if (val!=null)
			{
				self.ownerList.removeAll();
				self.selectedowner(null);
				if (val=="oldinvoice")
				{
					self.isnew_invoice(false);
					self.isold_invoice(true);
					if (self.ownertype()!=null)
					{
						if (self.ownertype()=='Property')
						{
							self.getownerList('propertyid_list');
						}
						else if (self.ownertype()=='Storage')
						{
							self.getownerList('storageid_list');
						}
					}
				}
				else
				{
					self.isnew_invoice(true);
					self.isold_invoice(false);

					if (self.ownertype()!=null)
					{
						if (self.ownertype()=='Property')
						{
							self.getownerList('propertyid_list','newinvoice');
						}
						else if (self.ownertype()=='Storage')
						{
							self.getownerList('storageid_list','newinvoice');
						}
					}
					// self.getownerList=function(name,sub_state=null);
				}
			}
		})
		purpose.subscribe(function(val)
		{
			if (val!=null)
			{
				if (purposefilter.includes(self.purpose().name))
				{
					self.isamount(true);
					var id= (self.ownertype()=='Property')?self.selectedowner().id
						:self.selected_storageunit().storageunit_id;
					self.getBuildInFee(id);
				}
				else
				{
					self.isamount(false);
					self.amount(null);
					if($('#amount').is(':disabled')==false)
					{
						$('#amount').attr('style', 'background-color: white !important');	
					}
				}
			}
			else
			{
				self.isamount(false);
				self.amount(null);
				self.disabledhandler();
				$(".data_input").trigger('input');
			}
		})
		//
		self.getBuildInFee=function(id)
		{
			var obj={
				'state': 		'getFixedFees',
				'user_id': 		self.selectedclient().user_id,
				'id': 			id,
				'feetype': 		self.purpose().name
			}
			GetUserWithEmail(obj)
			.done(function(data){
				self.amount(data.amount);
				self.disabledhandler();
				$(".data_input").trigger('input');
			})
			.fail(function(data){
				self.amount(null);
				self.disabledhandler();
				$(".data_input").trigger('input');
			})
		}
	}
	var em = document.getElementById('paymentRequestPage');
	if(em) ko.applyBindings(new paymentRequestViewModel(), em);
});