define(['knockout', 'jquery','moment','bootstrap','notify','modal'], function (ko,$,moment){
	function tenantOrdersViewViewModel() 
	{
		self.maintenanceType=ko.observable(null);
		self.tenantname=ko.observable(null);
		self.property_id=ko.observable(null);
		self.details=ko.observable(null);
		self.availability=ko.observable(null);
		self.urgent=ko.observable(null);
		self.tenantorder_id=ko.observable(null);
		self.isapproved=ko.observable(null);
		self.add = function(){
			var obj = {
				'tenantorder_id':self.tenantorder_id(),
				'approved':self.isapproved()					
			}
			addTenantOrder(obj)
			.done(function(data){
				self.loadData();					
			})
		}		
		function addTenantOrder(o){
			var d = $.Deferred();
			$.post('../actions/forms/tenant_orders_view.php', { 'act':'addTenantOrder', 'data':o,'FORM_TOKEN' : FORM_TOKEN})			
			.done(function(data) {
				alert(data);
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
		function getdata(){
			var d = $.Deferred();
			$.post( '../actions/forms/tenant_orders_view.php', { 'act':'GetTenantData','FORM_TOKEN' : FORM_TOKEN})
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
		self.displayTenantData=function(tenantorder_id=null,name=null,property=null,maintenanceType=null, details=null,
			availability=null, urgent=null)
		{
			self.tenantorder_id(tenantorder_id);
			self.tenantname(name);
			self.property_id(property);
			self.maintenanceType(maintenanceType);
			self.details(details);
			self.availability(availability);
			self.urgent(urgent);			
		}
		self.loadData = function(){
			getdata()
			.done(function(data){
				var name=data.name?data.name.fname+' '+data.name.sname:null;
				self.displayTenantData(data.order.ID,name,data.property_id,
					data.order.Type, data.order.details, data.order.availability,
					data.order.Urgency);
			})
			.fail(function(){
				self.displayTenantData();
			})
		}
		self.loadData();					
	}
	var em = document.getElementById('tenantOrdersViewPage');
	if(em) ko.applyBindings(new tenantOrdersViewViewModel(), em);
});