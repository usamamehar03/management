define(['knockout', 'jquery', 'moment', 'bootstrap', 'notify', 'modal', 'timedUpdate'], function (ko,$,moment){
	function supplierFeesApprovalViewModel() {
		self.timeUpdate = ko.observable(false);
		self.inited = ko.observable(false);
		self.adding = ko.observable(false);
		self.suppliers = ko.observableArray([]);
		self.nameErr = ko.observable(null);
		self.maintenanceType = ko.observable(null);
		self.callOutCharge= ko.observable(null);
		self.billingIncrement = ko.observable(null);
		self.hourlyRate = ko.observable(null);
		self.overtimeRate = ko.observable(null);
		self.weekendRate = ko.observable(null);
		self.fixedRates = ko.observable(null);
		self.approved = ko.observable(null);		
		self.addSupplierFeesApprovalModal = ko.observable(null);
		self.closeSupplierFeesApprovalModal = function() {
			$('.side-bar').toggle('fast');
		}
		self.activeTab = ko.observable('All');
		self.toggleTab = function(target){
			self.activeTab(target);
		}
		self.mainMessage = ko.observable(null);
		self.getSupplierFees = function(){
			getSupplierFees()
			.done(function(data){
				var tmp = $.map(data,function(supplierFeesApproval){
					return new SupplierFees(supplierFeesApproval);
				})
				self.supplierFeesApproval(tmp);
				self.timeUpdate(true);
				setTimeout(function(){self.timeUpdate(false)},3000);
			});
		}
		self.addSupplierFeesApproval = function(){
			self.addSupplierFeesApprovalModal(true);
			$('.side-bar').toggle('fast');
		}
		self.add = function(){
			self.adding(true);
			var obj = {
				'addSupplierFeesApproval':self.SupplierFeesApproval(),
				'maintenanceType':self.maintenanceType(),
				'highLight':true,
				'approved':IS_SENIOR ? true : false,
				'callOutCharge':self.callOutCharge(),
				'billingIncrement':self.billingIncrement(),
				'hourlyRate':self.hourlyRate(),
				'overtimeRate':self.overtimeRate(),
				'weekendRate':self.weekendRate(),
				'fixedRates':self.fixedRates()					
				
			}
			addSupplierFeesApproval(obj)
			.done(function(data){
				obj.ID = data;
				self.supplierFeesApproval.push(new SupplierFeesApproval(obj));
				self.maintenanceType(null);
				self.callOutCharge(null);
				self.billingIncrement(null);
				self.hourlyRate(null);
				self.overtimeRate(null);
				self.weekendRate(null);
				self.fixedRates(null);
				
			})
			.always(function(){
				self.adding(false);
			})

		}		
			
		function SupplierFeesApproval(data){
			var ec = this;
			//(The property manager cannot edit/decide fees on behalf of the supplier.  They can approve or disapprove.
			
			
			ec.deleteMe = function(){
				deleteSuppliersFees(ec.ID())
				.done(function(data){
					self.suppliersFees.remove(ec);
					//location.reload(true);
				})
			}
		}
		self.supplierFeesNotApproved = ko.pureComputed(function(){
			var end = self.supplierFeesApproval();
			var tmp = [];
			tmp = ko.utils.arrayFilter(end,function(supplierFeesApproval){
				if(!supplierFeesApproval.approved()){
					return supplierFeesApproval;
				}
			})
			return tmp;
		})
		self.computeSupplierFees = ko.computed(function(){
			var tmp = self.supplierFeesApproval();
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
		function addSupplierFeesApproved(o){
			var d = $.Deferred();
			$.post( '../actions/forms/supplier_fees_approval.php', { 'act':'addSupplierFeesApproved', 'data':o,'FORM_TOKEN':FORM_TOKEN})
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
		/*function getSupplierFees(){
			var d = $.Deferred();
			$.post( '../actions/forms/supplier_fees.php', { 'act':'getAllSupplierFees','FORM_TOKEN':FORM_TOKEN})
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
		/*
		function getSupplierFees(){
			var d = $.Deferred();
			$.ajax({
				type: 'POST',
				url:'../actions/forms/supplier_fees.php', 
				data: 'act=getAllSupplierFees&FORM_TOKEN='+FORM_TOKEN,
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
		}*/
		/*function deleteSupplierFees(id){
			var d = $.Deferred();
			$.post( '../actions/forms/supplier_fees.php', { 'act':'deleteSupplierFees', 'supplierFeesApproval':id,'FORM_TOKEN':FORM_TOKEN})
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
		*/
		function deleteSupplierFees(id) {
			var d = $.Deferred();
			$.ajax({
				type: 'POST',
				url:'../actions/forms/supplier_fees.php', 
				data: 'act=deleteSupplierFees&supplierFeesApproval='+id+'&FORM_TOKEN='+FORM_TOKEN,
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
		
		} */  
	
		
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
	var em = document.getElementById('supplierFeesApprovalPage');
	if(em) ko.applyBindings(new supplierFeesApprovalViewModel(), em);
});

