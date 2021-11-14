define(['knockout', 'jquery', 'moment', 'bootstrap', 'notify', 'modal', 'timedUpdate'], function (ko,$,moment){
	function supplierOrdersViewModel() {
		self.timeUpdate = ko.observable(false);
		self.inited = ko.observable(false);
		self.adding = ko.observable(false);		
		self.start = ko.observable(null);		
		self.rate = ko.observableArray([]);
		self.fixedQuote = ko.observable(null);
		self.fixedApproved = ko.observable(null);
		self.billableHours = ko.observable(null);
		self.response = ko.observableArray([]);
		self.timestamp = ko.observableArray([]);
		self.supplierNotes = ko.observableArray([]);
		self.nameErr = ko.observable(null);		
			
		self.addSupplierOrdersModal = ko.observable(null);
		self.closeSupplierOrdersModal = function() {
			$('.side-bar').toggle('fast');
		}
		self.activeTab = ko.observable('All');
		self.toggleTab = function(target){
			self.activeTab(target);
		}
		self.mainMessage = ko.observable(null);
		//self.getSupplierOrders = function(){
		//	getSupplierOrders()
		//	.done(function(data){
		//		var tmp = $.map(data,function(supplierOrders){
		//			return new SupplierOrders(supplierOrders);
		//		})
		//		self.supplierOrders(tmp);
		//		self.timeUpdate(true);
		//		setTimeout(function(){self.timeUpdate(false)},3000);
		//	});
		//}
		self.addSupplierOrders = function(){
			self.addSupplierOrdersModal(true);
			$('.side-bar').toggle('fast');
		}
		self.add = function(){
			self.adding(true);
			var obj = {
				//'addSupplierOrders':self.SupplierOrders(),				
				//'highLight':true,
				//'approved':IS_SENIOR ? true : false,//Senior OR Admin can approve					
				'start':self.start(),
				'rate':self.rate(),
				'fixedQuote':self.fixedQuote(),
				'fixedApproved':self.fixedApproved(),
				'billableHours':self.billableHours(),
				'response':self.response(),
				'timestamp':self.timestamp(),				
				'supplierNotes':self.supplierNotes(),
							
			}
			addSupplierOrders(obj)
			.done(function(data){
				obj.ID = data;
				self.supplierOrders.push(new SupplierOrders(obj));				
				self.start(null);
				self.rate(null);	
				self.fixedQuote(null);
				self.fixedApproved(null);
				self.billableHours(null);
				self.response(null);
				self.timestamp(null);
				self.supplierNotes(null);				

			})
			.always(function(){
				self.adding(false);
			})

		}		
			
			
		
		
		function findRequestedValue(value,arr,target,index){
			var tmp = arr && arr.length ? ko.utils.arrayFirst(arr,function(item){
				return item[index]() == value;
			}) : null;
			return tmp ? tmp[target]() : null;
		}		
		function addSupplierOrders(o){
			var d = $.Deferred();
			$.post( '../actions/forms/supplier_orders.php', { 'act':'addSupplierOrders', 'data':o,'FORM_TOKEN':FORM_TOKEN})
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
		/*function getSupplierOrders(){
			var d = $.Deferred();
			$.post( '../actions/forms/supplier_orders.php', { 'act':'getAllSupplierOrders','FORM_TOKEN':FORM_TOKEN})
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
		}*/
		
		function getSupplierOrders(){
			var d = $.Deferred();
			$.ajax({
				type: 'POST',
				url:'../actions/forms/supplier_orders.php', 
				data: 'act=getAllSupplierOrders&FORM_TOKEN='+FORM_TOKEN,
				dataType: "json",
				
				success:function(data) {

					if (data.status == 'ok') {
						d.resolve(data.data?data.data:[]);
					}else{
						d.reject();
					}
				},
				error:function() {
					d.reject();
				}
				
			});
			
			
			return d;
		}
		
		
		

	}
	var em = document.getElementById('supplierOrdersPage');
	if(em) ko.applyBindings(new supplierOrdersViewModel(), em);
});