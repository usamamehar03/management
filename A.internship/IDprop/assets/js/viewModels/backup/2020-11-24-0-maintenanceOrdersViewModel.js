define(['knockout', 'jquery', 'moment', 'bootstrap', 'notify', 'modal', 'timedUpdate'], function (ko,$,moment){
	function maintenanceOrdersViewModel() {
		self.timeUpdate = ko.observable(false);
		self.inited = ko.observable(false);
		self.adding = ko.observable(false);
		self.nameErr = ko.observable(null);
		self.maintenanceType = ko.observable(null);
		self.urgent = ko.observable(null);
		self.overtime = ko.observable(null);
		self.weekend = ko.observable(null);	
		self.schedule = ko.observable(null);			
		self.supplier1 = ko.observableArray([]);
		self.supplier2 = ko.observableArray([]);
		self.supplier3 = ko.observableArray([]);		
		self.supplier1HourlyRate = ko.observableArray([]);
		self.supplier2HourlyRate = ko.observableArray([]);
		self.supplier3HourlyRate = ko.observableArray([]);
		self.supplier1AverageRate = ko.observableArray([]);
		self.supplier2AverageRate = ko.observableArray([]);
		self.supplier3AverageRate = ko.observableArray([]);
		self.notes = ko.observable(null);		
		
			
		self.addMaintenanceOrdersModal = ko.observable(null);
		self.closeMaintenanceOrdersModal = function() {
			$('.side-bar').toggle('fast');
		}
		self.activeTab = ko.observable('All');
		self.toggleTab = function(target){
			self.activeTab(target);
		}
		self.mainMessage = ko.observable(null);
		self.getMaintenanceOrders = function(){
			getMaintenanceOrders()
			.done(function(data){
				var tmp = $.map(data,function(maintenanceOrders){
					return new MaintenanceOrders(maintenanceOrders);
				})
				self.maintenanceOrders(tmp);
				self.timeUpdate(true);
				setTimeout(function(){self.timeUpdate(false)},3000);
			});
		}
		self.addMaintenanceOrders = function(){
			self.addMaintenanceOrdersModal(true);
			$('.side-bar').toggle('fast');
		}
		self.add = function(){
			self.adding(true);
			var obj = {
				'addMaintenanceOrders':self.MaintenanceOrders(),
				'maintenanceType':self.maintenanceType(),
				'highLight':true,
				'approved':IS_SENIOR ? true : false,//Senior OR Admin can approve
				'urgent':self.urgent(),
				'overtime':self.overtime(),
				'weekend':self.weekend(),				
				'schedule':self.start(),				
				'supplier1':self.supplier1(),
				'supplier2':self.supplier2(),
				'supplier3':self.supplier3(),
				'supplier1HourlyRate':self.supplier1HourlyRate(),
				'supplier2HourlyRate':self.supplier2HourlyRate(),
				'supplier3HourlyRate':self.supplier3HourlyRate(),
				'supplier1AverageRate':self.supplier1HourlyRate(),
				'supplier2AverageRate':self.supplier2HourlyRate(),
				'supplier3AverageRate':self.supplier3HourlyRate(),
				'notes':self.notes()				
			}
			addMaintenanceOrders(obj)
			.done(function(data){
				obj.ID = data;
				self.maintenanceOrders.push(new MaintenanceOrders(obj));
				self.maintenanceType(null);
				self.urgent(null);
				self.overtime(null);
				self.weekend(null);					
				self.schedule(null);
				self.supplier1(null);	
				self.supplier2(null);
				self.supplier3(null);
				self.supplier1HourlyRate(null);	
				self.supplier2HourlyRate(null);
				self.supplier3HourlyRate(null);
				self.supplier1AverageRate(null);	
				self.supplier2AverageRate(null);
				self.supplier3AverageRate(null);
				self.notes(null);		

			})
			.always(function(){
				self.adding(false);
			})

		}		
			
		function MaintenanceOrders(data){
			var maintenanceOrders = this;		
			maintenanceOrders.ID = data.ID;
			maintenanceOrders.maintenanceType = ko.observable(data.maintenanceType ? data.maintenanceType : null);
			maintenanceOrders.maintenanceType.subscribe(function(newVal){
				editMaintenanceOrders({'ID':maintenanceOrders.ID,'maintenanceType':newVal});
			})	
			maintenanceOrders.urgent = ko.observable(data.urgent ? data.urgent : null);
			maintenanceOrders.urgent.subscribe(function(newVal){
				editMaintenanceOrders({'ID':maintenanceOrders.ID,'urgent':newVal});
			})
			maintenanceOrders.overtime = ko.observable(data.overtime ? data.overtime : null);
			maintenanceOrders.overtime = ko.observable(data.overtime ? data.overtime : null);
			maintenanceOrders.overtime.subscribe(function(newVal){
				editMaintenanceOrders({'ID':maintenanceOrders.ID,'overtime':newVal});
			})
			maintenanceOrders.weekend = ko.observable(data.weekend ? data.weekend : null);
			maintenanceOrders.weekend = ko.observable(data.weekend ? data.weekend : null);
			maintenanceOrders.weekend.subscribe(function(newVal){
				editMaintenanceOrders({'ID':maintenanceOrders.ID,'weekend':newVal});
			})			
			maintenanceOrders.schedule = ko.observable(data.schedule ? data.schedule : null);
			maintenanceOrders.schedule = ko.observable(data.schedule ? data.schedule : null);
			maintenanceOrders.schedule.subscribe(function(newVal){
				editMaintenanceOrders({'ID':maintenanceOrders.ID,'schedule':newVal});
			})			
			maintenanceOrders.notes = ko.observable(data.notes ? data.notes : null);
			maintenanceOrders.notes = ko.observable(data.notes ? data.notes : null);
			maintenanceOrders.notes.subscribe(function(newVal){
				editMaintenanceOrders({'ID':maintenanceOrders.ID,'notes':newVal});
			})
			
			maintenanceOrders.deleteMe = function(){
				deleteMaintenanceOrders(maintenanceOrders.ID)
				.done(function(data){
					self.maintenanceOrders.remove(maintenanceOrders);
				});
			}
		}	
			
			
		
		self.maintenanceOrdersNotApproved = ko.pureComputed(function(){
			var end = self.maintenanceOrders();
			var tmp = [];
			tmp = ko.utils.arrayFilter(end,function(maintenanceOrders){
				if(!maintenanceOrders.approved()){
					return maintenanceOrders;
				}
			})
			return tmp;
		})
		self.computeMaintenanceOrders = ko.computed(function(){
			var tmp = self.maintenanceOrders();
			var sorted = tmp.sort(function(a, b){
				var keyA = a.name(),
				keyB = b.name();
				if(keyA < keyB) return -1;
				if(keyA > keyB) return 1;
				return 0;
			});
			return sorted;
		})
		
		function findRequestedValue(value,arr,target,index){
			var tmp = arr && arr.length ? ko.utils.arrayFirst(arr,function(item){
				return item[index]() == value;
			}) : null;
			return tmp ? tmp[target]() : null;
		}		
		function addMaintenanceOrders(o){
			var d = $.Deferred();
			$.post( '../actions/forms/maintenance_orders.php', { 'act':'addMaintenanceOrders', 'data':o,'FORM_TOKEN':FORM_TOKEN})
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
		/*function getMaintenanceOrders(){
			var d = $.Deferred();
			$.post( '../actions/forms/maintenance_orders.php', { 'act':'getAllMaintenanceOrders','FORM_TOKEN':FORM_TOKEN})
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
		
		function getMaintenanceOrders(){
			var d = $.Deferred();
			$.ajax({
				type: 'POST',
				url:'../actions/forms/maintenance_orders.php', 
				data: 'act=getAllMaintenanceOrders&FORM_TOKEN='+FORM_TOKEN,
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
		/*function deleteMaintenanceOrders(id){
			var d = $.Deferred();
			$.post( '../actions/forms/maintenance_orders.php', { 'act':'deleteMaintenanceOrders', 'maintenanceOrders':id,'FORM_TOKEN':FORM_TOKEN})
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
		function deleteMaintenanceOrders(id) {
			var d = $.Deferred();
			$.ajax({
				type: 'POST',
				url:'../actions/forms/maintenance_orders.php', 
				data: 'act=deleteMaintenanceOrders&maintenanceOrders='+id+'&FORM_TOKEN='+FORM_TOKEN,
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
			
		self.getDropdowns();
		self.getSuppliers();
		// $('#btnAdd').click(function() {
		// 	self.add();
		// 	//location.reload(true);
		// });
		// $('#deleteMe').click(function() {
		// 	self.add();
		// 	location.reload(true);
		// });

		

	}
	var em = document.getElementById('maintenanceOrdersPage');
	if(em) ko.applyBindings(new maintenanceOrdersViewModel(), em);
});

