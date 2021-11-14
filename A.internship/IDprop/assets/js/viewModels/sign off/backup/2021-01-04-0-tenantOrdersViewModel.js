define(['knockout', 'jquery','moment','bootstrap','notify','modal'], function (ko,$,moment){
function eraseErrors()
{
	$(".invo_input, .invo_select").focusout(function() {
		if($(this).val()!="")
		{
			$(this).next(".error").html("");
			$(this).css({"background-color": "white"});
			$(this).attr('style', 'background-color: white !important');
		}
	});
}
eraseErrors();
	function tenantOrdersViewModel() {
		self.timer = ko.observable(false);
		self.inited = ko.observable(false);
		self.filter = ko.observable(null);
		self.maintenanceType= ko.observable(null);
		// self.filter.subscribe(function(newVal){
		// 	self.getData();
		// })
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
		self.add = function(){
				var obj = {
					'maintenanceType':self.maintenanceType(),
					'details':self.details(),
					'urgency':self.urgency(),
					'availability':self.availability(),
					'startOrder':self.startOrder(),
					'tenantFeedback':self.tenantFeedback()
				}
				addTenantOrder(obj)
				.done(function(data){
					self.details(null);
					self.urgency(null);
					self.availability(null);
					self.startOrder(null);
					self.tenantFeedback(null);
					self.maintenanceType('type');
				});
			//}
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
		// function TenantOrder(data){
		// 	var order = this;
		// 	order.ID = data.ID;
		// 	order.orderDetails = ko.observable(data.orderDetails ? data.orderDetails : null);
		// 	order.orderDetails.subscribe(function(newVal){
		// 		editTenantOrder({'ID':order.ID,'orderDetails':newVal});
		// 	})	
		// 	order.orderUrgency = ko.observable(data.orderUrgency ? data.orderUrgency : null);
		// 	order.orderUrgency.subscribe(function(newVal){
		// 		editTenantOrder({'ID':order.ID,'orderUrgency':newVal});	
		// 	})	
		// 	order.orderAvailability = ko.observable(data.orderAvailability ? data.orderAvailability : null);
		// 	order.orderAvailability.subscribe(function(newVal){
		// 		editTenantOrder({'ID':order.ID,'orderAvailability':newVal});
		// 	})
		// 	order.orderStartOrder = ko.observable(data.orderStartOrder ? data.orderStartOrder : null);			
		// 	order.orderStartOrder.subscribe(function(newVal){
		// 		editTenantOrder({'ID':order.ID,'orderStartOrder':newVal});
		// 	})
		// 	order.orderTenantFeedback = ko.observable(data.orderTenantFeedback ? data.orderTenantFeedback : null);
		// 	order.orderTenantFeedback.subscribe(function(newVal){
		// 		editTenantOrder({'ID':order.ID,'orderTenantFeedback':newVal});			
		// 	}
		// 	order.deleteMe = function(){
		// 		deleteTenantOrder(order.ID)
		// 		.done(function(data){
		// 			self.tenantOrders.remove(order);
		// 		});
		// 	}
		// }
		
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
			// $.post( 'actions/forms/tenant_orders.php', { 'act':'addTenantOrder', 
			//'data':o,'FORM_TOKEN' : FORM_TOKEN,});
			$.post( '../actions/forms/tenant_orders.php', { 'act':'addTenantOrder', 'data':o})
			.done(function( data ) {
				data=JSON.parse(data);
				if( data ){
					if( data.status == 'ok' )
					{
						d.resolve(data.data?data.data:[]);
					}
					else
					{
						if (data.status == 'err')
						{
							for (var key of Object.keys(data.data)) 
							{
								if (key=="3err")
								{
									$("#"+key).html("empty");
								}
								else
								{
									$("#"+key).html("empty");				
								}
								$("#"+key).siblings('.invo_input').css({"background-color": "#f8d7da"});
								$("#"+key).siblings('.invo_select').attr('style', 'background-color: #f8d7da !important');
								$("#"+key).siblings('.invo_select').children().css({"background-color": "white"});
							}
						}
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
		// self.getData();		
	}
	var em = document.getElementById('tenantOrdersPage');
	if(em) ko.applyBindings(new tenantOrdersViewModel(), em);
});