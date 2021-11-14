define(['knockout', 'jquery','moment','bootstrap','notify','modal'], function (ko,$,moment){
	$(".invo_input, .invo_select").on("focusout", function(){
		if(($(this).val()!="" && $(this).val()!="type"))
		{
			$(this).next(".error").html("");
			$(this).attr('style', 'background-color: white !important');
		}
	});
	function paymentRequestViewModel() {
		self.timer = ko.observable(false);
		self.inited = ko.observable(false);
		self.filter = ko.observable(null);
		// self.filter.subscribe(function(newVal){
		// 	self.getData();
		// })
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
		//decisions
		self.isDisabled = ko.observable(false);
		self.isamount=ko.observable(false);
		self.isduedate=ko.observable(false);
		//invoice
		self.selectedinvoice=ko.observable();
		self.invoicelist = ko.observableArray([{name:null}]);
		self.add_invoice=function(name)
		{
			 self.invoicelist.push({name:name});
		}
		//clients
		self.selectedclient=ko.observable();
		self.clientlist = ko.observableArray([{id:null}]);
		self.add_client=function(id)
		{
			self.clientlist.push({id:id});
		}

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
				'invoice_id':self.invoice_id(),
				'user_id':self.user_id(),
				'paymentclient_id':self.paymentclient_id(),
				'contactdetails_id':self.contactdetails_id(),
				'contact_id':self.contact_id(),
				'email':self.email(),
				// 'name':self.result(),
				'amount':self.amount(),
				'duedate':self.dueDate(),
				'purpose':self.purpose(),
				'notes':self.notes()					
			}
	 	 	addPaymentRequest(obj)
			.done(function(data){
				self.loaddata(null,null,null,null,false,false,false);
				self.purpose(null);
				self.notes(null);
				self.selectedinvoice(null);
				self.selectedclient(null);
			})
			.fail(function(data){
				if (data.status=='err')
				{
					for (var key of Object.keys(data.data))
					{
						if (key=='emailError')
						{
							$("#"+key).html('invalid email');
						}
						else if(key=='user_idError')
						{
							$("#emailError").html('user not exist');
						}
						else if (key=='amountError')
						{
							$("#"+key).html('empty');
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
						else if (key=='purposeError' || key=='notesError')
						{
							$("#"+key).html('Alphabats only');
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
		self.submitPaymentRequest = function(){
			if(!self.err()){
				var o = {'invoice':self.newInvoice(),'client':self.newClient(),'email':self.newEmail(),'name':self.newName(),'amount':self.newAmount(),'dueDate':self.newDueDate(),'purpose':self.newPurpose(),'notes':self.Notes()};
				addPaymentRequest(o)
				.done(function(data){
					self.getData();
					self.newInvoice(null);
					self.newClient(null);
					self.newEmail(null);
					self.newName(null);
					self.newAmount(null);	
					self.newDueDate(null);	
					self.newPurpose(null);
					self.newNotes(null);
				});
			}
		}
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
		
		function getData() {
			var d = $.Deferred()
			$.post('../actions/forms/payment_request.php',{
				'act':'getData',
				'filter':self.filter() == 'All' ? null : self.filter(),
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
		*/
		function addPaymentRequest(o){
			var d = $.Deferred();
			$.post( '../actions/forms/payment_request.php', { 'act':'addPaymentRequest', 'data':o})
			.done(function( data ) {
				alert(data);
				data= JSON.parse(data);
				if( data ){
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
		function GetUserWithEmail(o)
		{
			var d = $.Deferred();
    		$.post( '../actions/forms/payment_request.php', { 'act':'GetUserFromEmail', 'data':o})
			.done(function( data ) {
				//alert(data);
				data=JSON.parse(data);
				if( data ){
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
		self.displayname=function()
		{
			var obj = {
	    		'email':self.email(),
	    		'state':'getname'
	    	}
			GetUserWithEmail(obj)
			.done(function(data){
				$("#emailError").html('');
				$("#emailError").siblings('.invo_input').css({"background-color": "white"});
				var name=data[0].fname+', '+data[0].sname;
				self.loaddata(self.email(),name,null,null,false,false,false,null,data[0].user_id,
					data[0].contactdetails_id,data[0].contact_id);
				// self.clientname(data[0].fname+', '+data[0].sname);
			})
			.fail(function(data){
				self.loaddata();
				// self.clientname(null);
				$("#emailError").html('');
				$("#emailError").siblings('.invo_input').css({"background-color": "white"});

				if (data.status=='err')
				{
					$("#emailError").html('invalid email');
					$("#emailError").siblings('.invo_input').css({"background-color": "#f8d7da"});
				}
				else if (data.status=='fail')
				{
					$("#emailError").html('email not exist');
				}
			})
		}
		self.loaddata=function(email=null,name=null,amount=null,duedate=null, decision1=false,
			decision2=false,decision3=false,invoice_id=null,
			user_id=null,contactdetails_id=null,contact_id=null,paymentclient_id=null)
		{
			self.email(email);
			self.clientname(name);
			self.amount(amount);
			self.dueDate(duedate);
			self.isDisabled(decision1);
			self.isamount(decision2);
			self.isduedate(decision3);
			self.invoice_id(invoice_id);
			self.user_id(user_id);
			self.paymentclient_id(paymentclient_id);
			self.contactdetails_id(contactdetails_id);
			self.contact_id(contact_id);
		}
		//get invoice dropdown list
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
		//get invoice detail list
		$("#invoice").on("change", function(){
			setTimeout(function(){
				if (self.selectedinvoice()!=undefined)
				{
					var obj={
						'state':'GetInvoiceDetail',
						'invoicenumber':self.selectedinvoice().name
					}
					GetUserWithEmail(obj)
					.done(function(data){
						self.loaddata(data[0].email, data[0].name, data[0].amount, data[0].duedate,true,
							true,true,data[0].invoice_id, data[0].user_id,data[0].contactdetails_id
							,data[0].contact_id,null);
						self.selectedclient(null);
						$('#email,#amount,#dueDate').next(".error").html("");
						$('#email,#amount,#dueDate').attr('style', 'background-color: white !important');
					})
					.fail(function(){
						self.loaddata();
						self.selectedclient(null);
					})
				}
				else
				{
					self.loaddata();
					self.selectedclient(null);
				}
			}, 100);
		});
		//get payment client dropdown list
		var obj={'state':'getclient'}
		GetUserWithEmail(obj)
		.done(function(data){
			self.clientlist.removeAll();
			count = Object.keys(data).length;
			if (count>0)
			{
				var i=0;
				while(i<count)
				{
					self.add_client(data[i].User_ID);
					i++;
				}
			}
		})
		.fail(function(){
			alert('ops');
		})
		//get payment client list
		$("#client").on("change", function(){
			setTimeout(function(){
				if (self.selectedclient()!=undefined)
				{
					var obj={
						'state':'GetClientDetail',
						'userid':self.selectedclient().id
					}
					GetUserWithEmail(obj)
					.done(function(data){
						self.loaddata(data[0].email,data[0].name,null,null,true,false,false,
							null,data[0].user_id,data[0].contactdetails_id
							,data[0].contact_id,data[0].paymentclient_id);
						self.selectedinvoice(null);
						$('#email').next(".error").html("");
						$('#email').attr('style', 'background-color: white !important');
					})
					.fail(function(){
						self.loaddata();
						self.selectedinvoice(null);
					})
				}
				else
				{
					self.loaddata();
					self.selectedinvoice(null);
				}
			}, 100);
		});
	}
	var em = document.getElementById('paymentRequestPage');
	if(em) ko.applyBindings(new paymentRequestViewModel(), em);
});