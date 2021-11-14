define(['knockout', 'jquery','moment','bootstrap','notify','modal'], function (ko,$,moment){
	function tenantOrdersViewModel() {
		self.timer = ko.observable(false);
		self.inited = ko.observable(false);
		self.filter = ko.observable(null);
		self.filter.subscribe(function(newVal){
			self.getData();
		})
		self.tenantOrders = ko.observableArray([]);
		self.err = ko.observable(false);
		self.addTenantOrderModal = ko.observable(null);		
		self.details = ko.observable(null);
		self.urgency = ko.observable(null);
		self.availability = ko.observable(null);
		self.startOrder = ko.observable(null);
		self.tenantFeedback = ko.observable(null);
		
		self.addTenantOrder = function(){
			self.addTenantOrderModal(self);
		}
		self.submitTenantOrder = function(){
			if(!self.err()){
				var o = {'details':self.newDetails(),'urgency':self.newUrgency(),'availability':self.newAvailability(),'startOrder':self.newStartOrder(),'tenantFeedback':self.newTenantFeedback()};
				addTenantOrder(o)
				.done(function(data){
					self.getData();
					self.newDetails(null);
					self.newUrgency(null);
					self.newAvailability(null);
					self.newStartOrder(null);
					self.newTenantFeedback(null);
				});
			}
		}
		self.getData = function(){
			self.tenantOrders([]);
			getData()
			.done(function(data){
				var tmp = [];
				tmp = $.map(data,function(d){
					return new TenantOrder(d);
				})
				self.tenantOrders(tmp);
			});
			self.inited(true);
		}
		function TenantOrder(data){
			var order = this;
			order.ID = data.ID;
			order.orderDetails = ko.observable(data.orderDetails ? data.orderDetails : null);
			order.orderDetails.subscribe(function(newVal){
				editTenantOrder({'ID':order.ID,'orderDetails':newVal});
			})	
			order.orderUrgency = ko.observable(data.orderUrgency ? data.orderUrgency : null);
			order.orderUrgency.subscribe(function(newVal){
				editTenantOrder({'ID':order.ID,'orderUrgency':newVal});	
			})	
			order.orderAvailability = ko.observable(data.orderAvailability ? data.orderAvailability : null);
			order.orderAvailability.subscribe(function(newVal){
				editTenantOrder({'ID':order.ID,'orderAvailability':newVal});
			})
			order.orderStartOrder = ko.observable(data.orderStartOrder ? data.orderStartOrder : null);			
			order.orderStartOrder.subscribe(function(newVal){
				editTenantOrder({'ID':order.ID,'orderStartOrder':newVal});
			})
			order.orderTenantFeedback = ko.observable(data.orderTenantFeedback ? data.orderTenantFeedback : null);
			order.orderTenantFeedback.subscribe(function(newVal){
				editTenantOrder({'ID':order.ID,'orderTenantFeedback':newVal});			
			}
			order.deleteMe = function(){
				deleteTenantOrder(order.ID)
				.done(function(data){
					self.tenantOrders.remove(order);
				});
			}
		}
		
		function getData() {
			var d = $.Deferred()
			$.post('actions/forms/tenant_orders.php',{
				'act':'getData',
				'filter':self.filter() == 'All' ? null : self.filter(),
				'FORM_TOKEN' : FORM_TOKEN,
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
		function addTenantOrder(o){
			var d = $.Deferred();
			$.post( 'actions/forms/tenant_orders.php', { 'act':'addTenantOrder', 'data':o,'FORM_TOKEN' : FORM_TOKEN,})
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
		function editTenantOrder(o,res){
			$.post( 'actions/forms/tenant_orders.php', { 'act':'editTenantOrder', 'changes':o,'FORM_TOKEN' : FORM_TOKEN,})
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
		function deleteTenantOrder(order_id) {
			var d = $.Deferred()
			$.post('actions/forms/tenant_orders.php',{
				'act':'deleteTenantOrder',
				'order_id':order_id,
				'FORM_TOKEN' : FORM_TOKEN,
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
	}
	var em = document.getElementById('tenantOrdersPage');
	if(em) ko.applyBindings(new tenantOrdersViewModel(), em);
});