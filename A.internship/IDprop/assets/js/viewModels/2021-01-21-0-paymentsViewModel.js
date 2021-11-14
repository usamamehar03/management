define(['knockout', 'jquery','moment','bootstrap','notify','modal'], function (ko,$,moment){
	function paymentsViewModel() {
		self.timer = ko.observable(false);
		self.inited = ko.observable(false);
		self.filter = ko.observable(null);
		self.filter.subscribe(function(newVal){
			self.getData();
		})
		self.payments = ko.observableArray([]);
		self.err = ko.observable(false);
		self.addPaymentsModal = ko.observable(null);		
		self.bank = ko.observable(null);
		self.confirmation = ko.observable(null);
		self.amountPaid = ko.observable(null);
		self.timestamp = ko.observable(null);

		self.biller=ko.observable(null);
		self.purpose=ko.observable(null);
		self.amount=ko.observable(null);
		self.dueDate=ko.observable(null);		
				
		self.mainMessage = ko.observable(null);
		self.getData = function(){
			getData()
			.done(function(data){
				var tmp = $.map(data,function(payments){
					return new Payments(payments);
				})
				self.payments(tmp);
				self.timeUpdate(true);
				setTimeout(function(){self.timeUpdate(false)},3000);
			});
		}
		self.add = function(){
			self.adding(true);			
				var obj = {
					//'addPayments':self.Payments(),
					'payments':self.payments(),
					//'highLight':true,
					//'approved':IS_SENIOR ? true : false,
					'taxBank':self.taxBank(),
					'taxConfirmation':self.taxConfirmation(),
					'amountPaid':self.amountPaid(),
					'timestamp':self.timestamp()							
			}
				addPayments(obj)
				.done(function(data){
					obj.ID = data;
					self.taxBank.push(new taxBank(obj));
					self.taxConfirmation(null);
					self.amountPaid(null);
					self.timestamp(null);							

			})
				.always(function(){
					self.adding(false);
			})	
		}	
		self.submitPayments = function(){
			if(!self.err()){
				var o = {'bank':self.newBank(),'confirmation':self.newConfirmation(),'amountPaid':self.newAmountPaid(),'timestamp':self.newTimestamp()};
				addPayments(o)
				.done(function(data){
					self.getData();
					self.newBank(null);
					self.newConfirmation(null);
					self.newAmountPaid(null);
					self.newTimestamp(null);
					
				});
			}
		}
		// self.getData = function(){
		// 	self.payments([]);
		// 	getData()
		// 	.done(function(data){
		// 		var tmp = [];
		// 		tmp = $.map(data,function(d){
		// 			return new Payments(d);
		// 		})
		// 		self.payments(tmp);
		// 	});
		// 	self.inited(true);
		// }
		function Payments(data){
			var payments = this;
			payments.ID = data.ID;
			payments.bank = ko.observable(data.bank ? data.bank : null);
			payments.bank.subscribe(function(newVal){
				editPayments({'ID':payments.ID,'bank':newVal});
			})	
			payments.confirmation = ko.observable(data.confirmation ? data.confirmation : null);
			payments.confirmation.subscribe(function(newVal){
				editPayments({'ID':payments.ID,'confirmation':newVal});	
			})	
			payments.amountPaid = ko.observable(data.amountPaid ? data.amountPaid : null);
			payments.amountPaid.subscribe(function(newVal){
				editPayments({'ID':payments.ID,'amountPaid':newVal});
			})
			payments.timestamp = ko.observable(data.timestamp ? data.timestamp : null);			
			payments.timestamp.subscribe(function(newVal){
				editPayments({'ID':payments.ID,'timestamp':newVal});
			})
			/*Buyer can't delete payment request
			payments.deleteMe = function(){
				deletePayments(payments.ID)
				.done(function(data){
					self.payments.remove(payments);
				});
			}
			*/
		}
		
		function getData() {
			var d = $.Deferred()
			$.post('../actions/forms/payments.php',{'act':'getData','FORM_TOKEN' : FORM_TOKEN})
			.done(function(data) {
				// alert(data);
				// data=JSON.parse(data);
				if (data.status == 'ok') {
					d.resolve(data.data?data.data:[]);
				}else{
					d.reject();
				}
			})
			// .fail(function () {
			// 	d.reject();
			// })
			return d;
		}
		function addPayments(o){
			var d = $.Deferred();
			$.post( '../actions/forms/payments.php', { 'act':'addPayments', 'data':o,'FORM_TOKEN' : FORM_TOKEN,})
			.done(function( data ) {
				if( data ){
					if( data.status == 'ok' ){
						d.resolve(data.data?data.data:[]);
					}else{
						d.reject();
					}
				}
			})
			return d;
		}
		function editPayments(o,res){
			$.post( '../actions/forms/payments.php', { 'act':'editPayments', 'changes':o,'FORM_TOKEN' : FORM_TOKEN,})
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
		// function deletePayments(order_id) {
		// 	var d = $.Deferred()
		// 	$.post('../actions/forms/payments.php',{
		// 		'act':'deletePayments',
		// 		'order_id':order_id,
		// 		'FORM_TOKEN' : FORM_TOKEN,
		// 	}).done(function(data) {
		// 		if (data.status == 'ok') {
		// 			d.resolve(data.data?data.data:[]);
		// 		}else{
		// 			d.reject();
		// 		}
		// 	})
		// 	.fail(function () {
		// 		d.reject();
		// 	})
		// 	return d;
		// }
		// alert("ok");
		getData()
		.done(function(data){
			self.biller(data[0].companyname);
			self.amount(data[0].amount);
			self.purpose(data[0].purpose);
			self.dueDate(data[0].duedate);
		})
		.fail(function(){
			self.biller(null);
			self.amount(null);
			self.purpose(null);
			self.dueDate(null);
		})		
	}
	var em = document.getElementById('paymentsPage');
	if(em) ko.applyBindings(new paymentsViewModel(), em);
});