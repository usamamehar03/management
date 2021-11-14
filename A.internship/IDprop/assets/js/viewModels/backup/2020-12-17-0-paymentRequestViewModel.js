define(['knockout', 'jquery','moment','bootstrap','notify','modal'], function (ko,$,moment){
	function paymentRequestViewModel() {
		self.timer = ko.observable(false);
		self.inited = ko.observable(false);
		self.filter = ko.observable(null);
		self.filter.subscribe(function(newVal){
			self.getData();
		})
		self.adding = ko.observable(false);
		self.paymentRequest = ko.observableArray([]);
		self.err = ko.observable(false);
		self.addPaymentRequestModal = ko.observable(null);		
		self.invoice = ko.observable(null);
		self.client = ko.observable(null);
		self.email = ko.observable();
		//self.name = ko.observable(null);
		self.amount = ko.observable(null);
		self.dueDate = ko.observable(null);
		self.purpose = ko.observable(null);
		self.notes = ko.observable(null);
		self.result = ko.observable(null);
		self.email.subscribe(function(newValue){
    //call ajax
    var obj = {

    	'email':self.email,
    }
   // var jsonData = ko.toJSON(obj);
    //var obj = JSON.parse(jsonData);   var d = $.Deferred();
    	$.post( '../actions/forms/payment_request.php', { 'act':'GetUserFromEmail', 'data':obj})
    	
			.done(function( data ) {
				if( data ){

					//alert(data);
					if( data.status == 'ok' )
					{
						
						alert(data);
						//alert(fake[0].data.fname);

						d.resolve(data.data?data.data:[]);
						
					}
					else
					{
						//alert(data);
						var ob = JSON.parse(data);
						//alert(ob.sname + ' '+ ob.fname);
						self.result(ob.sname + ' '+ ob.fname);
						//alert(data);
						
						if(data.status == 'fail' )
						{
							alert("Problem");
						}
						
						
					}
				
				}
				else
				{
					alert('no');
				}
			})
			.fail(function () {
				alert('f');
			})
    //alert(obj.email);
});
            
		self.addPaymentRequest = function(){
			self.addPaymentRequestModal(true);
		}
		self.add = function(){
			self.adding(true);
			
				var obj = {
					'invoice':self.invoice(),
					'client':self.client(),
					'email':self.email(),
					'name':self.result(),
					'amount':self.amount(),
					'dueDate':self.dueDate(),
					'purpose':self.purpose(),
					'notes':self.notes()
					
			}
		
	 	 	addPaymentRequest(obj)
			.done(function(data){
				
				self.invoice(null);
				self.client(null);
				self.email(null);
				//self.name(null);
				self.amount(null);
				self.dueDate(null);
				self.purpose(null);
				self.notes(null);
			
				self.adding(false);

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
			var jsonData = ko.toJSON(o);
				var obj = JSON.parse(jsonData);
				alert(obj);
			$.post( '../actions/forms/payment_request.php', { 'act':'addPaymentRequest', 'data':o})
			.done(function( data ) {
				if( data ){
					alert(data);
					if( data.status == 'ok' ){
						d.resolve(data.data?data.data:[]);
					}else{
						d.reject();
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
	}
	var em = document.getElementById('paymentRequestPage');
	if(em) ko.applyBindings(new paymentRequestViewModel(), em);
});