
define(['knockout', 'jquery', 'moment', 'bootstrap', 'notify', 'modal', 'timedUpdate'], function (ko,$,moment){
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
	function supplierFeesViewModel() {
		self.isaddanother=ko.observable(false);
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
		self.itemType1 = ko.observable(null);
		self.itemType1Min = ko.observable(null);
		self.itemType1Max = ko.observable(null);
		self.UserID=ko.observable(null);
		self.hidetop= ko.observable(true);
		self.addSupplierFeesModal = ko.observable(null);
		self.hideitems= 0;
		
		self.inserthandler=function()
		{
			if (self.hidetop()==false)
			{
				getUserID()
				.done(function(data)
				{
					if (self.fixedRates()=='1')
					{
						self.hidetop(false);
						var ob = 
						{
							'itemType1':self.itemType1(),
							'itemType1Min':self.itemType1Min(),
							'itemType1Max':self.itemType1Max(),
							'supplierid': data.Supplier_ID,
							'maintenanceid':data.MaintenanceType_ID,
							'state':'addjob'
						}
				 	 	addSupplierFees(ob)
						.done(function(data){
							self.itemType1(null);
							self.itemType1Min(null);
							self.itemType1Max(null);
							self.hidetop(true);
						})
					}
				})
			}
			else
			{
				add()
			}
		}

		self.hideitems=  ko.pureComputed(function()
		{
			return self.fixedRates()==1;
		},self);
		self.closeSupplierFeesModal = function() {
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
				var tmp = $.map(data,function(supplierFees){
					return new SupplierFees(supplierFees);
				})
				self.supplierFees(tmp);
				self.timeUpdate(true);
				setTimeout(function(){self.timeUpdate(false)},3000);
			});
		}
		self.addSupplierFees = function(){
			self.addSupplierFeesModal(true);
			$('.side-bar').toggle('fast');
		}
		//work here
		self.addnext=function()
		{
			getUserID()
			.done(function(data)
			{
				if (self.fixedRates()=='1')
				{
					if (self.hidetop()==true)
					{
						self.isaddanother(true);
						self.add();
					}
					else
					{
						///enter below code here
						var ob = 
						{
							'itemType1':self.itemType1(),
							'itemType1Min':self.itemType1Min(),
							'itemType1Max':self.itemType1Max(),
							'supplierid': data.Supplier_ID,
							'maintenanceid':data.MaintenanceType_ID,
							'state':'addjob'
						}
				 	 	addSupplierFees(ob)
						.done(function(data){
							self.itemType1(null);
							self.itemType1Min(null);
							self.itemType1Max(null);
						})
					}
				}
				else
				{
					//add in db  just horly and reload 
					add()
				}
			})
		}
		function getUserID()
		{
			var d = $.Deferred();
			$.post( '../actions/forms/supplier_fees.php', { 'act':'getUserID'})
			.done(function( data ) {
				data=JSON.parse(data);
				if( data )
				{
					if( data.status == 'ok' )
					{
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
		self.add = function(){
			self.adding(true);
			if (self.fixedRates()==1)
			{
				var obj = {
					//'addSupplierFees':self.SupplierFees(),
					'maintenanceType':self.maintenanceType(),
					//'highLight':true,
					//'approved':IS_SENIOR ? true : false,
					'callOutCharge':self.callOutCharge(),
					'billingIncrement':self.billingIncrement(),
					'hourlyRate':self.hourlyRate(),
					'overtimeRate':self.overtimeRate(),
					'weekendRate':self.weekendRate(),
					'fixedRates':self.fixedRates(),				
					'itemType1':self.itemType1(),
					'itemType1Min':self.itemType1Min(),
					'itemType1Max':self.itemType1Max()
				}
			}
			else
			{
				var obj = {
					//'addSupplierFees':self.SupplierFees(),
					'maintenanceType':self.maintenanceType(),
					//'highLight':true,
					//'approved':IS_SENIOR ? true : false,
					'callOutCharge':self.callOutCharge(),
					'billingIncrement':self.billingIncrement(),
					'hourlyRate':self.hourlyRate(),
					'overtimeRate':self.overtimeRate(),
					'weekendRate':self.weekendRate(),
					'fixedRates':self.fixedRates()	
				}
			}
	 	 	addSupplierFees(obj)
			.done(function(data){
				// obj.ID = data;
				// self.supplierFees.push(new SupplierFees(obj));
				self.maintenanceType(null);
				self.callOutCharge(null);
				self.billingIncrement(null);
				self.hourlyRate(null);
				self.overtimeRate(null);
				self.weekendRate(null);
				self.itemType1(null);
				self.itemType1Min(null);
				self.itemType1Max(null);
				if (self.isaddanother()==true)
				{
					self.hidetop(false);
					self.isaddanother(false);
				}
				else
				{
					self.fixedRates(null);
				}
			})
			.fail(function(){
				self.isaddanother(false);
			})
			.always(function(){
				self.adding(false);
			})

		}		
			
		 function SupplierFees(data){
		 	var ec = this;
		 	//(For now we are not offering "edit" to reduce supplier fraud)
			
			
		 	ec.deleteMe = function(){
		 		deleteSuppliers(ec.ID())
		 		.done(function(data){
		 			self.suppliers.remove(ec);
		 			//location.reload(true);
		 		})
		 	}
		 }
		 self.supplierFeesNotApproved = ko.pureComputed(function(){
		 	var end = self.supplierFees();
		 	var tmp = [];
		 	tmp = ko.utils.arrayFilter(end,function(supplierFees){
		 		if(!supplierFees.approved()){
		 			return supplierFees;
		 		}
		 	})
		 	return tmp;
		 })
		 // self.computeSupplierFees = ko.computed(function(){
		 // 	var tmp = self.supplierFees();
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
		function addSupplierFees(o){
			var d = $.Deferred();
			// $.post( '../actions/forms/supplier_fees.php', { 'act':'addSupplierFees', 'data':o,'FORM_TOKEN':FORM_TOKEN})
			$.post( '../actions/forms/supplier_fees.php', { 'act':'addSupplierFees', 'data':o})
			.done(function( data ) {
				data=JSON.parse(data);
				if( data )
				{
					if( data.status == 'ok' )
					{
						d.resolve(data.data?data.data:[]);
					}
					else
					{
						if (data.status == 'invalid' )
						{
							$("#itemType1Error_alert").html("This is a duplicate entry");
							$("#itemType1").css({"background-color": "#f8d7da"});
							//$("#itemType1Error_alert").siblings('.invo_input').css({"background-color": "#f8d7da"});
						}

						if(data.status == 'fail' )
						{
							// alert("sorry db insert failed");
						}
						else
						{
							var index="_alert";
							for (var key of Object.keys(data.data)) 
							{
								if (key=="itemType1Error" || key=="itemTypeError")
								{
									$("#"+key+index).html("letters only");
								}
								else if(key=="maintenanceTypeError" || key=="billingIncrementError" || key=="fixedRatesError")
								{
									$("#"+key+index).html("empty");
								}
								else if(key=="itemType1MaxErrormax")
								{
									key="itemType1MaxError";
									$("#"+key+index).html("shoulder greater than min rate");
								}
								else
								{
									$("#"+key+index).html("numbers only");
								}
								$("#"+key+index).siblings('.invo_input').css({"background-color": "#f8d7da"});
								$("#"+key+index).siblings('.invo_select').attr('style', 'background-color: #f8d7da !important');
								$("#"+key+index).siblings('.invo_select').children().css({"background-color": "white"});
							}
						}
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
		 }
		 function deleteSupplierFees(id){
		 	var d = $.Deferred();
		 	$.post( '../actions/forms/supplier_fees.php', { 'act':'deleteSupplierFees', 'supplierFees':id,'FORM_TOKEN':FORM_TOKEN})
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
		 function deleteSupplierFees(id) {
		 	var d = $.Deferred();
		 	$.ajax({
		 		type: 'POST',
		 		url:'../actions/forms/supplier_fees.php', 
		 		data: 'act=deleteSupplierFees&supplierFees='+id+'&FORM_TOKEN='+FORM_TOKEN,
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
        
			
		// self.getSuppliers();
		// $('#btnAdd').click(function() {
		// 	self.add();
		// 	//location.reload(true);
		// });
		// $('#deleteMe').click(function() {
		// 	self.add();
		// 	location.reload(true);
		// });		
	}
	var em = document.getElementById('supplierFeesPage');
	if(em) ko.applyBindings(new supplierFeesViewModel(), em);
});

