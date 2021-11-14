define(['knockout', 'jquery', 'moment', 'bootstrap', 'notify', 'modal', 'timedUpdate'], function (ko,$,moment){
	$(".invo_input, .invo_select").focusout(function() {
		if(($(this).val()!="" && $(this).val()!="type") || self.requestedsupplier()!=null || self.requestedproperty()!=null)
		{
			$(this).next(".error").html("");
			$(this).attr('style', 'background-color: white !important');
		}
	});
	function maintenanceOrdersViewModel() {
		self.timeUpdate = ko.observable(false);
		self.inited = ko.observable(false);
		self.adding = ko.observable(false);
		self.nameErr = ko.observable(null);
		self.supplier_ID=ko.observable(null);
		self.maintenanceType = ko.observable(null);
		self.urgent = ko.observable(null);	
		self.schedule = ko.observable(null);
		self.property_ID=ko. observable(null);			
		self.itemType_ID=ko.observable(null);
		self.radioselected=ko.observable(null);
		self.notes = ko.observable(null);
		self.hourlytype = ko.observable(null);
		//show hourly rates
		self.feelabel = ko.observable(null);
		self.suppliernumber= ko.observable(null);
		self.suppliercompany=ko.observable(null);
		self.hrate= ko.observable(null);
		self.isvisible=ko.observable(false);		
		self.feelist = ko.observableArray([{label : feelabel, supplier: suppliernumber,name:suppliercompany, rate:hrate}]);
		self.addsupplieroption = function(){
		    self.feelist.push({label : feelabel, supplier: suppliernumber,name:suppliercompany, rate:hrate});
	    }
	    //show  job types
	    self.job=ko.observable(null);
	    self.Selectedjob=ko.observableArray([null]);
	    self.requestedjob=ko.observable(null);
	    self.jobtypelist=ko.observableArray([{jobtype : job}]);
	    self.addjobitem = function(){
		    self.jobtypelist.push({jobtype : job});
	    }
	    Selectedjob.subscribe(function (val){
	    	if (val!=null)
	    	{
	    		self.requestedjob(val);
	    	}
	    });
	    //show fixed job rates
	    self.joblabel = ko.observable(null);
		self.jobnumber= ko.observable(null);
		self.avrate= ko.observable(null);
		self.isjobvisible=ko.observable(false);		
		self.fixedlist = ko.observableArray([{labeel : joblabel, supplier: jobnumber,name:suppliercompany, fixedrate:avrate}]);
		self.addfixedjob = function(){
		    self.fixedlist.push({labeel : joblabel, supplier: jobnumber,name:suppliercompany, fixedrate:avrate});
	    }
	    //handle fixrates
	    self.isjobtype=ko.observable(false);
	    //handle select supplier
	    self.supplierlist=ko.observableArray(null);
	    self.slectedsupplier=ko.observableArray(null);
	    self.requestedsupplier=ko.observable(null);
	    slectedsupplier.subscribe(function (vale){
	    	if (vale!=null)
	    	{
	    		self.requestedsupplier(vale.supplier());
	    	}
	    	else
	    	{
	    		self.requestedsupplier(null);
	    	}
	    	//alert(self.requestedsupplier());
	    });
	    self.ishourlysort=ko.observable(false);
	    //building names
	    self.buildingrawdata=ko.observableArray(null);
	    self.buildingname=ko.observable(null);
	    self.selectedbuildig=ko.observableArray(null);
	    self.buildinglist=ko.observableArray([{name : buildingname}]);
	    self.addbuilding=function()
	    {
	    	self.buildinglist.push({name:buildingname});
	    }
	    //create oroperty id list
	    selectedbuildig.subscribe(function (vale){
	    	if (vale!=null)
	    	{
	    		if (vale.name()=="Bloomington")
	    		{
	    			displaypropertylist(self.buildingrawdata().Bloomington);
	    		}
	    		else if (vale.name()=="Mayfair")
	    		{
	    			displaypropertylist(self.buildingrawdata().Mayfair);
	    		}
	    		else if (vale.name()=="Pall")
	    		{
	    			displaypropertylist(self.buildingrawdata().Pall);
	    		}
	    		else if (vale.name()=="Beach_Front")
	    		{
	    			displaypropertylist(self.buildingrawdata().Beach_Front);
	    		}
	    	}
	    	else
	    	{
	    		self.propertylist.removeAll();
	    	}
	    });
	    //property id list
	    self.propertyname=ko.observable(null);
	    self.propertyid=ko.observable(null);
	    self.propertylist=ko.observableArray([{name:propertyname, id:propertyid}]);
	    self.addproperty = function(){
		    self.propertylist.push({name:propertyname, id:propertyid});
	    }
	    //handle property list selected  value
	    self.slectedproperty=ko.observableArray(null);
	    self.requestedproperty=ko.observable(null);
	    slectedproperty.subscribe(function (vale){
	    	if (vale!=null)
	    	{
	    		self.requestedproperty(vale.id());
	    	}
	    	else
	    	{
	    		self.requestedproperty(null);
	    	}
	    });
	    //
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
		self.next=function()
		{
			self.adding(true);
			if (self.radioselected()=="fixed")
			{
				var obj = {
					//'addMaintenanceOrders':self.MaintenanceOrders(),
					'maintenanceType':self.maintenanceType(),
					'property_ID':self.requestedproperty(),
					// 'highLight':true,
					// 'approved':IS_SENIOR ? true : false,//Senior OR Admin can approve
					'urgent':'0',
					'overtime':'0',
					'weekend':'0',
					'urgent':self.urgent(),				
					'schedule':self.schedule(),				
					'supplierid':self.requestedsupplier(),
					'notes':self.notes()				
				}
			}
			else
			{
				var obj = {
					//'addMaintenanceOrders':self.MaintenanceOrders(),
					'maintenanceType':self.maintenanceType(),
					'property_ID':self.requestedproperty(),
					// 'highLight':true,
					// 'approved':IS_SENIOR ? true : false,//Senior OR Admin can approve
					'urgent':self.urgent(),	
					'ratetype': self.hourlytype(),
					'overtime':'0',
					'weekend':'0',		
					'schedule':self.schedule(),				
					'supplierid':self.requestedsupplier(),
					'notes':self.notes()				
				}
			}
			addMaintenanceOrders(obj)
			.done(function(data){
				location.reload(true);
			})
		}
		self.add = function(){
			self.adding(true);
			if (self.radioselected()=="fixed")
			{
				var obj = {
					//'addMaintenanceOrders':self.MaintenanceOrders(),
					'maintenanceType':self.maintenanceType(),
					'property_ID':self.requestedproperty(),
					// 'highLight':true,
					// 'approved':IS_SENIOR ? true : false,//Senior OR Admin can approve
					'urgent':'0',
					'overtime':'0',
					'weekend':'0',
					'urgent':self.urgent(),				
					'schedule':self.schedule(),				
					'supplierid':self.requestedsupplier(),
					'notes':self.notes()				
				}
			}
			else
			{
				var obj = {
					//'addMaintenanceOrders':self.MaintenanceOrders(),
					'maintenanceType':self.maintenanceType(),
					'property_ID':self.requestedproperty(),
					// 'highLight':true,
					// 'approved':IS_SENIOR ? true : false,//Senior OR Admin can approve
					'urgent':self.urgent(),	
					'ratetype': self.hourlytype(),
					'overtime':'0',
					'weekend':'0',		
					'schedule':self.schedule(),				
					'supplierid':self.requestedsupplier(),
					'notes':self.notes()				
				}
			}
			addMaintenanceOrders(obj)
			.done(function(data){
				// obj.ID = data;
				// self.maintenanceOrders.push(new MaintenanceOrders(obj));
				self.maintenanceType(null);
				self.notes(null);
				self.schedule(null);
				self.property_ID(null);
			})
			.always(function(){
				self.adding(false);
			})

		}		
			
		// function MaintenanceOrders(data){
		// 	var maintenanceOrders = this;		
		// 	maintenanceOrders.ID = data.ID;
		// 	maintenanceOrders.maintenanceType = ko.observable(data.maintenanceType ? data.maintenanceType : null);
		// 	maintenanceOrders.maintenanceType.subscribe(function(newVal){
		// 		editMaintenanceOrders({'ID':maintenanceOrders.ID,'maintenanceType':newVal});
		// 	})	
		// 	maintenanceOrders.urgent = ko.observable(data.urgent ? data.urgent : null);
		// 	maintenanceOrders.urgent.subscribe(function(newVal){
		// 		editMaintenanceOrders({'ID':maintenanceOrders.ID,'urgent':newVal});
		// 	})
		// 	maintenanceOrders.overtime = ko.observable(data.overtime ? data.overtime : null);
		// 	maintenanceOrders.overtime = ko.observable(data.overtime ? data.overtime : null);
		// 	maintenanceOrders.overtime.subscribe(function(newVal){
		// 		editMaintenanceOrders({'ID':maintenanceOrders.ID,'overtime':newVal});
		// 	})
		// 	maintenanceOrders.weekend = ko.observable(data.weekend ? data.weekend : null);
		// 	maintenanceOrders.weekend = ko.observable(data.weekend ? data.weekend : null);
		// 	maintenanceOrders.weekend.subscribe(function(newVal){
		// 		editMaintenanceOrders({'ID':maintenanceOrders.ID,'weekend':newVal});
		// 	})			
		// 	maintenanceOrders.schedule = ko.observable(data.schedule ? data.schedule : null);
		// 	maintenanceOrders.schedule = ko.observable(data.schedule ? data.schedule : null);
		// 	maintenanceOrders.schedule.subscribe(function(newVal){
		// 		editMaintenanceOrders({'ID':maintenanceOrders.ID,'schedule':newVal});
		// 	})			
		// 	maintenanceOrders.notes = ko.observable(data.notes ? data.notes : null);
		// 	maintenanceOrders.notes = ko.observable(data.notes ? data.notes : null);
		// 	maintenanceOrders.notes.subscribe(function(newVal){
		// 		editMaintenanceOrders({'ID':maintenanceOrders.ID,'notes':newVal});
		// 	})
			
		// 	maintenanceOrders.deleteMe = function(){
		// 		deleteMaintenanceOrders(maintenanceOrders.ID)
		// 		.done(function(data){
		// 			self.maintenanceOrders.remove(maintenanceOrders);
		// 		});
		// 	}
		// }	
			
			
		
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
		// self.computeMaintenanceOrders = ko.computed(function(){
		// 	var tmp = self.maintenanceOrders();
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
		function addMaintenanceOrders(o){
			var d = $.Deferred();
			$.post( '../actions/forms/maintenance_orders.php', { 'act':'addMaintenanceOrders', 'data':o})
			// $.post( '../actions/forms/maintenance_orders.php', { 'act':'addMaintenanceOrders', 
			// 	'data':o,'FORM_TOKEN':FORM_TOKEN})
			.done(function( data ) {
				alert(data);
				data=JSON.parse(data);
				if( data ){
					if( data.status == 'ok' ){
						d.resolve(data.data?data.data:[]);
					}
					else
					{
						for (var key of Object.keys(data.data))
						{
							$("#"+key).html('empty');
							$("#"+key).siblings('.invo_input').css({"background-color": "#f8d7da"});
							$("#"+key).siblings('.invo_select').attr('style', 
								'background-color: #f8d7da !important');
						 	$("#"+key).siblings('.invo_select').children().css({
						 		"background-color": "white"});
						} 
						d.reject();
					}
				}
			})
			return d;
		}
		function getHourlyrates(o)
		{
			var d = $.Deferred();
			$.post( '../actions/forms/maintenance_orders.php', { 'act':'getHourlyrates', 'data':o})
			.done(function( data ) {
				//alert(data);
				data=JSON.parse(data);
				if( data ){
					if( data.status == 'ok' )
					{
						d.resolve(data.data?data:[]);
					}
					else
					{
						if (data.status=="err")
						{
							for (var key of Object.keys(data.data))
							{
								if (key=='scheduleerr')
								{
									if(data.data[key].state=="empty")
									{
										$("#"+key).html('empty');
									}
									else
									{
										$("#"+key).html('date cant be past');
									}									
								}
								else
								{
									$("#"+key).html('empty');
								}
								$("#"+key).siblings('.invo_input').css({"background-color": "#f8d7da"});
								$("#"+key).siblings('.invo_select').attr('style', 
										'background-color: #f8d7da !important');
								$("#"+key).siblings('.invo_select').children().css({
								 		"background-color": "white"});
							} 
						}
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
		///here work  for houlry rate too
		function displayhourlyrates(data)
		{
			suppliercount = Object.keys(data).length;
			if (suppliercount>0)
			{
				self.isvisible(true);
				var i=0;
				while(i<suppliercount)
				{
					self.feelabel=ko.observable('Option '+i);
					self.suppliernumber=ko.observable(data[i].Supplier_ID);
					self.suppliercompany=ko.observable(data[i].CompanyName);
					self.hrate=ko.observable(data[i].HourlyRate);
					self.addsupplieroption();
					i++;
				}
				self.supplierlist(self.feelist().slice(0));
			}
		}
		function displayfixedrates(data)
		{
			jobtypecount = Object.keys(data).length;
			if (jobtypecount>0)
			{
				var i=0;
				while(i<jobtypecount)
				{
					self.job=ko.observable(data[i].jobtype);
					self.addjobitem();
					i++;
				}
			}
			self.supplierlist.removeAll();
		}
		function displaypropertylist(name)
		{
			self.propertylist.removeAll();
			propertyidcount = Object.keys(name).length;
			if (propertyidcount>0)
			{
				var i=0;
				while(i<propertyidcount)
				{
					self.propertyname=ko.observable(name[i].firstline+',  '+name[i].city+',  '
						+name[i].country+',  '+name[i].postcode);
					self.propertyid=ko.observable(name[i].propertyid);
					self.addproperty();
					i++;
				}
			}
		}
		function displaybuilding(data)
		{
			self.buildinglist.removeAll();
			buildingcount = Object.keys(data).length;
			if (buildingcount>0)
			{
				for (var key of Object.keys(data))
				{
					self.buildingname=ko.observable(key);
					self.addbuilding();
				}
			}
			self.buildingrawdata(data);
			//alert(self.buildingrawdata());
		}
		function hourlyresponse(val)
		{
			self.buildinglist.removeAll();
			self.propertylist.removeAll();
			if (self.radioselected()=="fixed" )
			{
				self.ishourlysort(false);
				self.hourlytype(null);
				var obj = {
					'maintenanceType':self.maintenanceType(),
					'schedule':self.schedule(),
					'option' :self.radioselected()				
				}
			}
			else
			{
				if (self.radioselected()=="supplierFees")
				{
					self.ishourlysort(true);
				}
				var obj = {
					'maintenanceType':self.maintenanceType(),
					'schedule':self.schedule(),
					'option' :self.radioselected(),
					'hratetype':self.hourlytype()
				}
			}
			if(val!="" && val!="type")
			{
				getHourlyrates(obj)
				.done(function(data){
					//empty all lists
					self.feelist.removeAll();
					self.isvisible(false);
					self.jobtypelist.removeAll();
					self.fixedlist.removeAll();
					self.isjobvisible(true);
					if (self.radioselected()=="supplierFees")
					{
						self.isjobtype(false);
						displayhourlyrates(data.data);
					}
					else
					{
						self.isjobtype(true);
						displayfixedrates(data.data);	                	
					}
					//display building list
					displaybuilding(data.property);
				})
				.fail(function(data){
					//when fail clear all dynmic data
					self.feelist.removeAll();
					self.isvisible(false);
					self.jobtypelist.removeAll();
					self.isjobtype(false);
					self.fixedlist.removeAll();
					self.isjobvisible(true);
					self.supplierlist.removeAll();
				})
			}	
		}
		$(".hourrate").focusout(function() {
			hourlyresponse($(this).val());
		});

		$(".radio").change(function() {
			hourlyresponse($(this).val());
		});
		$(".fixedrate").focusout(function() {
			if (self.requestedjob()!=null)
			{
				var obj = {
					'maintenanceType':self.maintenanceType(),
					'schedule':self.schedule(),
					'option' :self.radioselected(),
					'jobtype':self.requestedjob()
				}
				getHourlyrates(obj)
				.done(function(data){
					displaybuilding(data.property);
					self.fixedlist.removeAll();
					self.isjobvisible(true);
					fixedcount = Object.keys(data.data).length;
					if (fixedcount>0)
					{
						self.isjobvisible(true);
						var i=0;
						while(i<fixedcount)
						{
							self.joblabel=ko.observable('Option '+i);
							self.jobnumber=ko.observable(data.data[i].supplier);
							self.suppliercompany=ko.observable(data.data[i].CompanyName);
							self.avrate=ko.observable(data.data[i].cheap);
							self.addfixedjob();
							i++;
						}
						self.supplierlist(self.fixedlist().slice(0));
						self.propertylist.removeAll();
						displaypropertylist(data);
					}
					self.requestedjob(null);
				})
				.fail(function(data){
					self.fixedlist.removeAll();
					self.isjobvisible(true);
					self.supplierlist.removeAll();
				})
			}
		});
		$(".ratetype").change(function() {
			hourlyresponse($(this).val());
		});
		// self.getDropdowns();   
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
	var em = document.getElementById('maintenanceOrdersPage');
	if(em) ko.applyBindings(new maintenanceOrdersViewModel(), em);
});

