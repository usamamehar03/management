define(['knockout', 'jquery', 'moment', 'bootstrap', 'notify', 'modal', 'timedUpdate'], function (ko,$,moment){
	$(".input_data").focusout(function() {
		if($(this).val()!="")
		{
			$(this).next(".error").html("");
			$(this).attr('style', 'background-color: white !important');
		}
	});
	function supplierFeesApprovalViewModel() {
		self.timeUpdate = ko.observable(false);
		self.inited = ko.observable(false);
		self.adding = ko.observable(false);
		self.nameErr = ko.observable(null);
		self.maintenanceType = ko.observable(null);
		self.maintenanceid=ko.observable(null);
		self.callOutCharge= ko.observable(null);
		self.billingIncrement = ko.observable(null);
		self.hourlyRate = ko.observable(null);
		self.overtimeRate = ko.observable(null);
		self.weekendRate = ko.observable(null);
		self.fixedRates = ko.observable(null);	
		self.Note=ko.observable(null);	
		self.addSupplierFeesApprovalModal = ko.observable(null);

		self.isavail= ko.observable(false);
		self.suppliers = ko.observableArray([]);
		self.totaljobs = ko.observableArray([]);
		self.supplierid = ko.observable(null);
		self.company = ko.observable(null);
		self.approved = ko.observable(null);
		self.fixrateapproved = ko.observable(null);
		self.addnext=ko.observable(false);
		self.isvisible = ko.observable(false);
		self.jobindex=ko.observable(0);
		self.suppliercount = ko.observable(0);
		self.supplierindex=ko.observable(0);
		self.joblabel = ko.observable(null);
		self.isavailable=ko.observable(false);
		self.job = ko.observable(null);
		self.min = ko.observable(null);
		self.max = ko.observable(null);
		self.joblist = ko.observableArray([{label : joblabel, jobtype: job, minrate:min,maxrate:max}]);
		self.addjob = function(){
		    self.joblist.push({label : joblabel, jobtype: job, minrate:min, maxrate:max});
	    }

		self.closeSupplierFeesApprovalModal = function() {
			$('.side-bar').toggle('fast');
		}
		self.activeTab = ko.observable('All');
		self.toggleTab = function(target){
			self.activeTab(target);
		}
		self.mainMessage = ko.observable(null);
		// self.getSupplierFees = function(){
		// 	getSupplierFees()
		// 	.done(function(data){
		// 		var tmp = $.map(data,function(supplierFeesApproval){
		// 			return new SupplierFees(supplierFeesApproval);
		// 		})
		// 		self.supplierFeesApproval(tmp);
		// 		self.timeUpdate(true);
		// 		setTimeout(function(){self.timeUpdate(false)},3000);
		// 	});
		// }
		self.addSupplierFeesApproval = function(){
			self.addSupplierFeesApprovalModal(true);
			$('.side-bar').toggle('fast');
		}
		self.add = function(){
			self.adding(true);
			if (self.isavail()==true) 
			{
				var obj = {
					//'addSupplierFeesApproval':self.SupplierFeesApproval(),
					//'highLight':true,
					//'approved':IS_SENIOR ? true : false,
					'approved': self.approved(),
					'fixrateapproved': self.fixrateapproved(),
					'supplierid':self.supplierid(),
					'maintenanceType_id': self.maintenanceid(),
					'note':self.Note(),
					'state' : 'addall'					
					
				}
			}
			else
			{
			
				var obj = {
					//'addSupplierFeesApproval':self.SupplierFeesApproval(),
					//'highLight':true,
					//'approved':IS_SENIOR ? true : false,
					'approved': self.approved(),
					'supplierid':self.supplierid(),
					'maintenanceType_id': self.maintenanceid(),
					'note':self.Note()					
					
				}
			}
			addSupplierFeesApproved(obj)
			.done(function(){
				self.joblist.removeAll();
			 	self.isvisible(false);
			 	self.isavail(false);
			 	self.supplierindex(supplierindex()+1);
			 	if(self.supplierindex()<self.suppliercount())
			 	{
			 		displayjobs();
			 	}
			 	else
			 	{
			 		self.addnext(false);
			 		self.maintenanceType(null);
					self.callOutCharge(null);
					self.billingIncrement(null);
					self.hourlyRate(null);
					self.overtimeRate(null);
					self.weekendRate(null);
					self.fixedRates(null);
					self.approved(null);
					self.fixrateapproved(null);
					self.supplierid(null);
					self.isavailable(false);
					self.Note(null);
					self.company(null);
					self.maintenanceid(null);
			 	}
			 })
			.always(function(){
				self.adding(false);
			})
		}		
			
		// function SupplierFeesApproval(data){
		// 	var ec = this;
		// 	//(The property manager cannot edit/decide fees on behalf of the supplier.
		//  They can approve or disapprove.
			
			
		// 	ec.deleteMe = function(){
		// 		deleteSuppliersFees(ec.ID())
		// 		.done(function(data){
		// 			self.suppliersFees.remove(ec);
		// 			//location.reload(true);
		// 		})
		// 	}
		// }
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
		// self.computeSupplierFees = ko.computed(function(){
		// 	var tmp = self.supplierFeesApproval();
		// 	var sorted = tmp.sort(function(a, b){
		// 		var keyA = a.name(),
		// 		keyB = b.name();
		// 		if(keyA < keyB) return -1;
		// 		if(keyA > keyB) return 1;
		// 		return 0;
		// 	});
		// 	return sorted;
		// })
		
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
				// alert(data);
				// data=JSON.parse(data);
				if( data ){
					if( data.status == 'ok' ){
						d.resolve(data.data?data.data:[]);
					}
					else
					{
						if (data.status == 'err') 
						{
							for (var key of Object.keys(data.data))
							{
								if (key=='noteError')
								{
									$("#"+key).html('Letters Only');
								}
								else
								{
									$("#"+key).html('empty');
								}
								$("#"+key).siblings('.input_data').attr('style', 
									'background-color: #f8d7da !important');
							 	$("#"+key).siblings('.input_data').children().css({
							 		"background-color": "white"});
							} 
						}
						d.reject();
					}
				}
			})
			return d;
		}
		function getSupplierFees(){
			var d = $.Deferred();
			$.post( '../actions/forms/supplier_fees_approval.php', { 'act':'getAllSupplierFees','FORM_TOKEN':FORM_TOKEN})
			.done(function( data ) {
				// alert(data);
				// data=JSON.parse(data);
				if( data )
				{
					if( data.status == 'ok' )
					{
						self.suppliers(data.data);
						d.resolve(data.data?data.data:[]);
					}
					else
					{
						d.reject();
					}
				}
			})
			return d;
		}
		function diplayformdata()
		{
			self.maintenanceType(suppliers()[supplierindex()].Type);
			self.callOutCharge(suppliers()[supplierindex()].CallOutCharge);
			self.billingIncrement(suppliers()[supplierindex()].BillingIncrement);
			self.hourlyRate(suppliers()[supplierindex()].HourlyRate);
			self.overtimeRate(suppliers()[supplierindex()].OvertimeRate);
			self.weekendRate(suppliers()[supplierindex()].WeekendRate);
			self.fixedRates(suppliers()[supplierindex()].SupplierOffersFixed=='1' ? 'Yes' : 'No');
			self.supplierid(suppliers()[supplierindex()].Supplier_ID);
			self.company(suppliers()[supplierindex()].suppliercompany);
			self.maintenanceid(suppliers()[supplierindex()].MaintenanceType_ID);
		}
		
		function getSupplierjobs(supplierid)
		{
			var d = $.Deferred();
			$.post('../actions/forms/supplier_fees_approval.php', { 'act':'getSupplierjobs','data':supplierid,'FORM_TOKEN':FORM_TOKEN})
			.done(function( data ) {
				// alert(data);
				// data=JSON.parse(data);
				if( data )
				{
					if( data.status == 'ok' )
					{
						self.totaljobs(data.data);
						d.resolve(data.data?data.data:[]);
					}
					else
					{
						d.reject();
					}
				}
			})
			return d;
		}
		function displayjobs()
		{
			if (suppliers()[supplierindex()].SupplierHasFixed==0)
			{
				diplayformdata();
			}
			else
			{
				self.jobindex(0);
				diplayformdata(supplierindex());
				getSupplierjobs(suppliers()[supplierindex()].Supplier_ID)  //get jobs
				.done(function(data){     //done jobs
					jobcount = Object.keys(totaljobs()).length;   //get jobs length
					if (jobcount>0)
					{
						self.isvisible(true);
						self.isavail(true);
						while(jobindex() < jobcount)
						{
							self.joblabel = ko.observable("job "+jobindex());
							self.job = ko.observable(totaljobs()[jobindex()].ItemType);
							self.min = ko.observable(totaljobs()[jobindex()].Min);
							self.max = ko.observable(totaljobs()[jobindex()].Max);
							self.addjob();
							self.jobindex(jobindex()+1);
						}
					}
				})
			}
		}
		/*function deleteSupplierFees(id){
			var d = $.Deferred();
			$.post( '../actions/forms/supplier_fees.php', { 'act':'deleteSupplierFees', 'supplierFeesApproval':id,
			//'FORM_TOKEN':FORM_TOKEN})
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
		getSupplierFees()
		.done(function(data){     //supplier done
			scount = Object.keys(suppliers()).length;   //get supplier length
			self.suppliercount(scount);
			if (self.suppliercount()>0)
			{
				self.joblist.removeAll();
				isavailable(true);
				self.addnext(true);
				displayjobs();
			}
		})
		//self.getSuppliers();
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

