define(['knockout', 'jquery','moment','bootstrap','notify','modal'], function (ko,$,moment){
	$(".invo_input, .invo_select").on("input", function(){
		$(this).next(".error").html("");
		if($(this).is(':disabled'))
		{
			$(this).attr('style', 'background-color: #0000000a ');
		}
		else
		{
			$(this).attr('style', 'background-color: white ');
		}
	});
	function paymentRequestViewModel() {
		self.timer = ko.observable(false);
		self.inited = ko.observable(false);
		self.filter = ko.observable(null);
		self.adding = ko.observable(false);
		self.paymentRequest = ko.observableArray([]);
		self.err = ko.observable(false);
		self.addPaymentRequestModal = ko.observable(null);		
		self.client = ko.observable(null);
		//add 
		self.clientname= ko.observable(null);
		self.email = ko.observable(null);
		self.amount = ko.observable(null);
		self.dueDate = ko.observable(null);
		self.purpose = ko.observable(null);
		self.notes = ko.observable(null);
		self.refrencenumber= ko.observable(null);
		self.invoicenumber=ko.observable(null);
		self.isnew_invoice=ko.observable(false);
		//owner setup
		self.ownertype=ko.observable('Owner');
		self.selectedowner= ko.observable(null);
		self.ownerList=ko.observableArray([{id:null}]);
		self.add_ownerList=function(id)
		{
			 self.ownerList.push({id:id});
		}
		//client
		self.selectedclient=ko.observable();
		self.clientlist = ko.observableArray([{name:null, user_id:null,
			owner_id:null}]);
		self.add_client=function(name, user_id, owner_id)
		{
			self.clientlist.push({name:name,user_id:user_id,owner_id:owner_id});
		}
		
		//decisions
		self.isamount=ko.observable(false);
		self.isduedate=ko.observable(false);
		//invoice
		self.selectedinvoice=ko.observable();
		self.invoicelist = ko.observableArray([{name:null}]);
		self.add_invoice=function(name)
		{
			 self.invoicelist.push({name:name});
		}
		self.removeInvoice = function (refrenceNumber) {
		    self.invoicelist.remove(function(invoicelist) {
		        return invoicelist.name == refrenceNumber;
		    });
		}
		//clients
		// self.selectedclient=ko.observable();
		// self.clientlist = ko.observableArray([{id:null, name:null, 
		// 	enduser:null,tenant_userid:null}]);
		// self.add_client=function(id,name,enduser,tenant_userid)
		// {
		// 	self.clientlist.push({id:id, name:name, enduser:enduser, 
		// 		tenant_userid:tenant_userid});
		// }
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
				'invoice_id': self.invoice_id(),
				'user_id':self.user_id(),
				'enduser': self.selectedclient()!=undefined?
							self.selectedclient().enduser:null,
				'tenant_userid': self.selectedclient()!=undefined?
							self.selectedclient().tenant_userid:null,
				'paymentclient_id': self.selectedclient()!=undefined?
							self.selectedclient().id:self.paymentclient_id(),
				'contactdetails_id':self.contactdetails_id(),
				'contact_id':self.contact_id(),
				'email':self.email(),
				'amount':self.amount(),
				'duedate':self.dueDate(),
				'purpose':self.purpose(),
				'isnewinvoice':self.isnew_invoice(),
				'refrencenumber':self.refrencenumber(),
				'invoicenumber':self.invoicenumber(),
				'notes':self.notes()					
			}
	 	 	addPaymentRequest(obj)
			.done(function(data){
				self.DisplayData();
				if (self.selectedinvoice()!=null)
				{
					self.removeInvoice(self.selectedinvoice().name);
				}
				self.selectedinvoice(null);
				self.selectedclient(null);
				self.refrencenumber(null);
				self.invoicenumber(null);
				self.invoicelist.removeAll();
				$(".invo_input, .invo_select").trigger('input');
			})
			.fail(function(data){
				if (data.status=='err')
				{
					for (var key of Object.keys(data.data))
					{
						if (key=='amountError')
						{
							$("#"+key).html('empty');
						}
						else if (key=='clientError')
						{
							$("#"+key).html('Select Client OR Invoice');
						}
						else if (key=='duedateError')
						{
							$("#"+key).html('empty');
							// if (empty($value))
							// {
							// 	$errorlist[$key.'Error']['state']='empty';
							// }
							// else
							// {
							// 	$datediff=filter\date_difference($present, $future)
							// }
						}
						else if (key=='purposeError' || key=='notesError'||
						key=='invoicenumberError'||key=='invoicenumberError')
						{
							$("#"+key).html('Text only');
						}
						$("#"+key).siblings('.invo_input').css({"background-color": "#f8d7da"});
						$("#"+key).siblings('.invo_select').attr('style', 
							'background-color: #f8d7da !important');
					 	$("#"+key).siblings('.invo_select').children().css({
					 		"background-color": "white"});
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
				data=JSON.parse(data);
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
		self.DisplayData=function(email=null,name=null,amount=null,duedate=null, decision1=false,
			decision2=false,service=null,description=null,invoice_id=null,
			user_id=null,contactdetails_id=null,contact_id=null,paymentclient_id=null)
		{
			self.email(email);
			self.clientname(name);
			self.amount(amount);
			self.dueDate(duedate);
			self.isamount(decision1);
			self.isduedate(decision2);
			self.purpose(service);
			self.notes(description);
			self.invoice_id(invoice_id);
			self.user_id(user_id);
			self.paymentclient_id(paymentclient_id);
			self.contactdetails_id(contactdetails_id);
			self.contact_id(contact_id);
		}
		self.LoadData=function(data,decision1,decision2)
		{
			var amount=data.amount? data.amount:null;
			var duedate=data.duedate? data.duedate:null;
			var service=data.service? data.service:null;
			var description=data.description? data.description:null;
			var paymentclient_id=data.paymentclient_id?data.paymentclient_id:null;
			var invoice_id=data.invoice_id?data.invoice_id:null;
			self.DisplayData(data.email, data.name,amount, duedate,decision1,decision2,
				service, description,invoice_id, data.user_id,
				data.contactdetails_id,data.contact_id,paymentclient_id);
		}
		//invoice along each client
		self.getclients_InvoiceList=function()
		{
			setTimeout(function(){
				if (self.selectedclient()!=undefined)
				{
					var obj={
								'state':'getclientinvoice',
								'paymentclient_id':self.selectedclient().id,
								'enduser': self.selectedclient().enduser,
								'tenant_userid':selectedclient().tenant_userid
							}
					GetUserWithEmail(obj)
					.done(function(data){
						self.invoicelist.removeAll();
						invoicecount = Object.keys(data).length;
						if (invoicecount>0)
						{
							var i=0;
							while(i<invoicecount)
							{
								self.add_invoice(data[i].InvoiceNumber);
								i++;
							}
						}
					})
					.fail(function(data)
					{
						self.invoicelist.removeAll();	
					})
				}
				else
				{
					self.getInvoiceList();
				}
			}, 100);
		}
		//get data from invoice setup 
		self.getInvoiceList=function()
		{
			var obj={'state':'getinvoice'}
			GetUserWithEmail(obj)
			.done(function(data){
				self.invoicelist.removeAll();
				invoicecount = Object.keys(data).length;
				if (invoicecount>0)
				{
					var i=0;
					while(i<invoicecount)
					{
						self.add_invoice(data[i].InvoiceNumber);
						i++;
					}
				}
			})
		}
		self.getInvoiceDetail=function()
		{
			setTimeout(function(){
				self.isnew_invoice(false);
				if (self.selectedinvoice()!=undefined)
				{
					var obj={
						'state':'GetInvoiceDetail',
						'invoicenumber':self.selectedinvoice().name
					}
					GetUserWithEmail(obj)
					.done(function(data){
						self.LoadData(data[0],true,true);
						// self.selectedclient(null);
						$(".invo_input, .invo_select").trigger('input');
					})
					.fail(function(){
						self.DisplayData();
						// self.selectedclient(null);
					})
				}
				else
				{
					if (self.selectedclient()!=undefined)
					{
						self.DisplayData(self.email(), self.clientname(),
							null,null, false,false,null,null,null,
							self.user_id());
						// self.selectedclient(null);
						$(".invo_input, .invo_select").trigger('input');
					}
					else
					{
						self.DisplayData();
						// self.selectedclient(null);
						$(".invo_input, .invo_select").trigger('input');
					}
				}
			}, 100);
		}
		//get payment client  setup
		// self.getClientList=function()
		// {
		// 	var obj={'state':'getclient'}
		// 	GetUserWithEmail(obj)
		// 	.done(function(data){
		// 		self.clientlist.removeAll();
		// 		count = Object.keys(data).length;
		// 		if (count>0)
		// 		{
		// 			var i=0;
		// 			while(i<count)
		// 			{
		// 				self.add_client(data[i].paymentclient_id,
		// 				 data[i].name, data[i].enduser, data[i].user_id);
		// 				i++;
		// 			}
		// 		}
		// 	})

		// }
		self.getClientData=function()
		{
			setTimeout(function(){
				self.isnew_invoice(false);
				if (self.selectedclient()!=undefined)
				{
					var obj={
						'state':'GetClientDetail',
						'userid':self.selectedclient().id
					}
					GetUserWithEmail(obj)
					.done(function(data){
						data[0].name=self.selectedclient().name;
						self.LoadData(data[0],false,false);
						self.selectedinvoice(null);
						$(".invo_input, .invo_select").trigger('input');
					})
					.fail(function(){
						self.DisplayData();
						self.selectedinvoice(null);
					})
				}
				else
				{
					self.DisplayData();
					self.selectedinvoice(null);
					$(".invo_input, .invo_select").trigger('input');
				}
			}, 100);
		}
		//get invoice and clients list
		// self.getInvoiceList();
		// self.getClientList();
		// //get invoice detail list
		// $("#invoice").on("change", function(){
		// 	self.getInvoiceDetail();
		// });
		// //get payment client list
		// $("#client").on("change", function(){
		// 	self.getClientData();
		// 	self.getclients_InvoiceList();
		// });
		// //create new invoice
		// $("#amount").on("focusout", function(){
		// 	if ($(this).val()!='')
		// 	{
		// 		self.isnew_invoice(true);
		// 	}
		// 	else
		// 	{
		// 		self.isnew_invoice(false);
		// 	}
		// });
		//
		//all gets
		self.getownerList=function(name)
		{
			var obj={'state':name}
			GetUserWithEmail(obj)
			.done(function(data){
				self.ownerList.removeAll();
				self.selectedowner(null);
				self.clientlist.removeAll();
				self.selectedclient(null);

				ownercount = Object.keys(data).length;
				if (ownercount>0)
				{
					for (var key of Object.keys(data))
					{
						self.add_ownerList(data[key].id);
					}
				}
			})
			.fail(function(){
				self.ownerList.removeAll();
				self.selectedowner(null);
				self.clientlist.removeAll();
				self.selectedclient(null);
			})
		}
		self.getClientList=function(name,id)
		{
			var obj={
				'state':name,
				'id': id
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
							data[i].owner_id);
						i++;
					}
				}
			})
			.fail(function(){
				self.clientlist.removeAll();
				self.selectedclient(null);
			})

		}
		//get owner_list
		ownertype.subscribe(function(val){
			if (val!='Owner')
			{
				if (val=='Property')
				{
					self.getownerList('propertyid_list');
				}
				else if (val=='Storage')
				{
					self.getownerList('storageid_list');
				}
			}
			else
			{
				self.ownerList.removeAll();
				self.selectedowner(null);
				self.clientlist.removeAll();
				self.selectedclient(null);
			}
		})
		//get clients
		selectedowner.subscribe(function(val){
			if (val!=null )
			{
				if (val.id!=null)
				{
					if (self.ownertype()=='Property')
					{
						self.getClientList('property-client-list', val.id);
					}
					else if (self.ownertype()=='Storage')
					{
						self.getClientList('storage-client-list', val.id);
					}
				}
				else
				{
					self.clientlist.removeAll();
					self.selectedclient(null);
				}
			}
			else
			{
				self.clientlist.removeAll();
				self.selectedclient(null);
			}
		})
	}
	var em = document.getElementById('paymentRequestPage');
	if(em) ko.applyBindings(new paymentRequestViewModel(), em);
});