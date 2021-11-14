define(['knockout', 'jquery','moment','bootstrap','notify','modal'], function (ko,$,moment){
	$(document).on("input",".inputs", function(){
		if($(this).val()!='')
		{
			$(this).attr('style', 'background-color: white !important');
			$(this).siblings('.error').html("");
		}
	}); 
	function invoiceViewModel() {
		self.timer = ko.observable(false);
		self.inited = ko.observable(false);
		self.filter = ko.observable(null);
		self.filter.subscribe(function(newVal){
			self.getData();
		})
		self.invoice = ko.observableArray([]);
		self.err = ko.observable(false);
		self.addInvoiceModal = ko.observable(null);	
		self.client = ko.observable(null);
		self.bAddress = ko.observable(null);
		self.cAddress = ko.observable(null);
		self.terms = ko.observable(null);
		// self.service = ko.observable(null);
		// self.description = ko.observable(null);
		// self.amount = ko.observable(null);
		self.notes = ko.observable(null);
		self.adding=ko.observable(null);
		//amount
		self.subtotal_amount=ko.observable(0);
		self.tax_amount=ko.observable(0);
		self.total_amount=ko.observable(0);
		self.paid_amount=ko.observable(0);
		self.due_balance=ko.observable(0);
		//general variables
		self.invoiceNumber = ko.observable(null);
		// self.ReferenceNumber = ko.observable(null);
		self.invoiceDate = ko.observable(null);
		self.dueDate = ko.observable(null);
		//multiple entres
		self.subinvoice_list=ko.observableArray([{ReferenceNumber:null,
			service:null,description:null,amount:null}]);
		self.add_subinvoice=function()
		{
			self.subinvoice_list.push({ReferenceNumber:null,
				service:null,description:null, amount:null});
		}
		self.remove_subinvoice = function (data) {
			var Index = self.subinvoice_list.indexOf(data);
			subinvoice_list.splice(Index, 1);
	    };
		//tempelate list
		self.selectedtemplate=ko.observable(null);
		self.template = ko.observableArray([{id:null,name:null}]);
		self.addtempelate=function(id,name)
		{
			self.template.push({id:id, name:name});
		}
		//client list
		self.selectedclient=ko.observable(null);
		self.client_adtional_fks=ko.observableArray([{
			propertyManagement_id:null, biller_adresss:null}]);
		self.clientlist = ko.observableArray([{paymentclient_id:null,name:null,
			address:null, userid:null, enduser:null}]);
		self.add_client=function(paymentclient_id,name,address, userid, enduser)
		{
			self.clientlist.push({paymentclient_id:paymentclient_id, name:name,
				address:address,  userid:userid, enduser:enduser});
		}
		selectedclient.subscribe(function (val){
	    	if (val!=null)
	    	{ 
	    		$(".inputs").trigger('inputs'); 		
	    		self.cAddress(self.selectedclient().address);
	    		self.getTempelate();
	    	}
	    	else
	    	{
	    		self.cAddress(null);
	    		self.selectedtemplate(null);
	    		self.template.removeAll();
	    		self.selectedclient(null);
	    	}
	    });



		//end jhere
		self.mainMessage = ko.observable(null);
		// self.getData = function(){
		// 	getData()
		// 	.done(function(data){
		// 		var tmp = $.map(data,function(invoice){
		// 			return new invoice(invoice);
		// 		})
		// 		self.invoice(tmp);
		// 		self.timeUpdate(true);
		// 		setTimeout(function(){self.timeUpdate(false)},3000);
		// 	});
		// }

		self.add = function()
		{
			self.adding(true);
			var obj = {
				//'addInvoice':self.invoice(),
				// 'invoice':self.invoice(),
				//'highLight':true,
				//'approved':IS_SENIOR ? true : false,
				// 'client':self.client(),
				// 'invoiceNumber':self.invoiceNumber(),
				// 'sAddress':self.sAddress(),
				// 'bAddress':self.cAddress(),
				// 'template':self.template(),
				// 'terms':self.terms(),
				// 'invoiceDate':self.invoiceDate(),
				// 'dueDate':self.dueDate(),
				// 'ReferenceNumber': self.ReferenceNumber(),
				// 'service':self.service(),
				// 'description':self.description(),
				// 'amount':self.amount(),
				// 'notes':self.notes()

				'user_id':self.selectedclient()!=null?
					self.selectedclient().userid: null,
				'enduser':self.selectedclient()!=null?
					selectedclient().enduser: null,
				'invoicetemplate_id':self.selectedtemplate()!=null?
					self.selectedtemplate().id: null,
				// 'propertymanagment_id':
				// 'maintenaceorder_id':
				'invoiceNumber':self.invoiceNumber(),
				'invoiceDate': self.invoiceDate(),
				'dueDate':self.dueDate(),
				'subinvoice_list':self.subinvoice_list()
				// 'ReferenceNumber':
				// 'service':
				// 'description':
				// 'amount':
				// 'notes':

			}							
			addInvoice(obj)
			.done(function(data){
				// obj.ID = data;
				// self.invoice.push(new invoice(obj));
				// self.client(null);
				// self.invoiceNumber(null);
				// self.sAddress(null);
				// self.bAddress(null);
				// self.template(null);
				// self.terms(null);
				// self.invoiceDate(null);
				// self.dueDate(null);
				// //self.#(null);
				// self.service(null);
				// self.description(null);
				// self.amount(null);
				// self.notes(null);
			})
			.fail(function(data){
				if (data.status=='err')
				{
					for (var key of Object.keys(data.data))
					{
						var tempkey=key.substring(0,key.length - 6);
						if (key=='clientError')
						{
							$("#"+key).html('Empty');
						}
						else if (key=='invoiceDateError' || key=='dueDateError')
						{
							if (data.data[key].state=='empty')
							{
								$("#"+key).html('Empty');
							}
							else
							{
								$("#"+key).html('Must be today or later');
							}
						}
						else if (key=='invoiceNumberError')
						{
							$("#"+key).html('Text only');
						}
						else if (tempkey=='service'|| tempkey=='description')
						{
							$("#"+key).html('Text only');
						}
						else if (tempkey=='ReferenceNumber' || 
							tempkey=='amount')
						{
							$("#"+key).html('Number only');
						}
						$("#"+key).siblings('.inputs').attr('style', 
							'background-color: #f8d7da !important');
					 	$("#"+key).siblings('.inputs').children().css({
					 		"background-color": "white"});
					}
				}
			})
			.always(function(){
				self.adding(false);
			})
		}	
		
		// self.getData = function(){
		// 	self.invoice([]);
		// 	getData()
		// 	.done(function(data){
		// 		var tmp = [];
		// 		tmp = $.map(data,function(d){
		// 			return new Invoice(d);
		// 		})
		// 		self.invoice(tmp);
		// 	});
		// 	self.inited(true);
		// }
		
		// function Invoice(data){
		// 	var invoice = this;
		// 	invoice.ID = data.ID;
		// 	invoice.client = ko.observable(data.client ? data.client : null);
		// 	invoice.client.subscribe(function(newVal){
		// 		editInvoice({'ID':invoice.ID,'client':newVal});
		// 	})	
		// 	invoice.invoiceNumber = ko.observable(data.invoiceNumber ? data.invoiceNumber : null);
		// 	invoice.invoiceNumber.subscribe(function(newVal){
		// 		editInvoice({'ID':invoice.ID,'invoiceNumber':newVal});	
		// 	})	
		// 	invoice.bAddress = ko.observable(data.bAddress ? data.bAddress : null);
		// 	invoice.bAddress.subscribe(function(newVal){
		// 		editInvoice({'ID':invoice.ID,'bAddress':newVal});
		// 	})
		// 	invoice.cAddress = ko.observable(data.cAddress ? data.cAddress : null);
		// 	invoice.cAddress.subscribe(function(newVal){
		// 		editInvoice({'ID':invoice.ID,'cAddress':newVal});
		// 	})
		// 	invoice.template = ko.observable(data.template ? data.template : null);
		// 	invoice.template.subscribe(function(newVal){
		// 		editInvoice({'ID':invoice.ID,'template':newVal});
		// 	})
		// 	invoice.terms = ko.observable(data.terms ? data.terms : null);
		// 	invoice.terms.subscribe(function(newVal){
		// 		editInvoice({'ID':invoice.ID,'terms':newVal});
		// 	})
		// 	invoice.invoiceDate = ko.observable(data.invoiceDate ? data.invoiceDate : null);
		// 	invoice.invoiceDate.subscribe(function(newVal){
		// 		editInvoice({'ID':invoice.ID,'invoiceDate':newVal});
		// 	})
		// 	invoice.dueDate = ko.observable(data.dueDate ? data.dueDate : null);
		// 	invoice.dueDate.subscribe(function(newVal){
		// 		editInvoice({'ID':invoice.ID,'dueDate':newVal});
		// 	})
		// 	invoice.# = ko.observable(data.# ? data.# : null);
		// 	invoice.#.subscribe(function(newVal){
		// 		editInvoice({'ID':invoice.ID,'#':newVal});
		// 	})
		// 	invoice.service = ko.observable(data.service ? data.service : null);			
		// 	invoice.service.subscribe(function(newVal){
		// 		editInvoice({'ID':invoice.ID,'service':newVal});
		// 	})
		// 	invoice.description = ko.observable(data.description ? data.description : null);
		// 	invoice.description.subscribe(function(newVal){
		// 		editInvoice({'ID':invoice.ID,'description':newVal});
		// 	})
		// 	invoice.amount = ko.observable(data.amount ? data.amount : null);
		// 	invoice.amount.subscribe(function(newVal){
		// 		editInvoice({'ID':invoice.ID,'amount':newVal});
		// 	})
		// 	invoice.notes = ko.observable(data.notes ? data.notes : null);
		// 	invoice.notes.subscribe(function(newVal){
		// 		editInvoice({'ID':invoice.ID,'notes':newVal});		
		// 	})
		// 	invoice.deleteMe = function(){
		// 		deleteInvoice(invoice.ID)
		// 		.done(function(data){
		// 			self.invoice.remove(invoice);
		// 		});
		// 	}
		// }
		
		function addInvoice(o){
			var d = $.Deferred();
			$.post('../actions/forms/invoice.php', { 'act':'addInvoice', 'data':o,'FORM_TOKEN' : FORM_TOKEN,})
			.done(function( data ) {
				alert(data);
				data=JSON.parse(data);
				if( data ){
					if( data.status == 'ok' ){
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
		function getData(o) {
			var d = $.Deferred()
			$.post('../actions/forms/invoice.php',{'act':'getData','data':o,
				'FORM_TOKEN' : FORM_TOKEN})
			.done(function(data) {
				alert(data);
				data=JSON.parse(data);
				if (data.status == 'ok') 
				{
					d.resolve(data.data?data.data:[]);
				}
				else
				{
					d.reject(data.data?data:[]);
				}
			})
			return d;
		}
		self.getinvoice=function()
		{
			var obj={
				'state':'getinvoice'
			}
			getData(obj)
			.done(function(data)
			{
				index=data['invoicedata'][0];
				self.terms(index['Terms']);
				self.invoiceNumber(index['InvoiceNumber']);
				self.invoiceDate(index['invoiceDate']);
				self.dueDate(index['DueDate']);
				self.notes(index['Notes']);
				//client address
				building=index['address']['building']? 
					index['address']['building']+', ' :'';
				$("#clientaddress  li:nth-child(1)").html(index['name']);
				$("#clientaddress  li:nth-child(2)").html(building+
					index['address']['firstline']);
				$("#clientaddress  li:nth-child(3)").html(index['address']
					['City']+', '+index['address']['county']);
				$("#clientaddress  li:nth-child(4)").html(index['address']
					['Country']+', '+index['address']['postcode']);
				// ampunt calculation
				self.subtotal_amount(index['Amount']);
				// self.tax_amount=ko.observable('$0');
				// self.total_amount=ko.observable('$0');
				self.paid_amount(index['paidamount']);
				self.due_balance(self.subtotal_amount()-self.paid_amount());
				// $('#invoiceDate').html("2-2-2021");
				//self.clientlist.removeAll();
				// for (var key  of Object.keys(data['clientlist']))
				// {
				// 	var index=data['clientlist'][key];
				// 	var address='';
				// 	for (var key2 of Object.keys(index.address))
				// 	{
				// 		address+= (key2=='postcode'?index.address[key2]
				// 			:index.address[key2]+', ');
				// 	}
				// 	self.add_client(index.paymentclient_id ,index.name,
				// 	 address, index.user_id, index.enduser);
				// }
				// self.client_adtional_fks()[0].propertyManagement_id=data.propertymanagmentid;
			})
		}
		self.getinvoice();


		self.getTempelate=function()
		{
			var obj={
				'state':'getTempelateList',
				'paymentclient_id': self.selectedclient().paymentclient_id,
				'propertyManagement_id':self.client_adtional_fks()[0].propertyManagement_id
			}
			getData(obj)
			.done(function(data){
				self.template.removeAll();
				for (var key of Object.keys(data))
				{
					self.addtempelate(data[key].invoicetemplate_id, 
						data[key].TemplateName);
				}
			})
			.fail(function(){
				self.selectedtemplate(null);
	    		self.template.removeAll();
			})
		}




		//end here
		function editInvoice(o,res){
			$.post('../actions/forms/invoice.php', { 'act':'editInvoice', 'changes':o,'FORM_TOKEN' : FORM_TOKEN})
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
		function deleteInvoice(order_id) {
			var d = $.Deferred()
			$.post('../actions/forms/invoice.php',{
				'act':'deleteInvoice',
				'order_id':order_id,
				'FORM_TOKEN' : FORM_TOKEN
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
		//self.getData();		
	}
	var em = document.getElementById('invoicePage');
	if(em) ko.applyBindings(new invoiceViewModel(), em);
});