define(['knockout', 'jquery', 'moment', 'bootstrap', 'notify', 'modal', 'timedUpdate'], function (ko,$,moment){
	$(".invo_input, .invo_select").on("focusout", function(){
		if(($(this).val()!="" && $(this).val()!="type"))
		{
			$(this).next(".error").html("");
			$(this).attr('style', 'background-color: white !important');
		}
	});
	function supplierOrdersViewModel() {
		// self.test = function(data) {
		//  	alert(data[0].id());
		// // 	$('#'+data.id()).css({'background-color': 'red'});
		// }
		self.supplierid= ko .observable(null);
		self.maintenanceordersid=ko.observable(null);
		self.timeUpdate = ko.observable(false);
		self.inited = ko.observable(false);
		self.adding = ko.observable(false);		
		self.start = ko.observable(null);
		self.startdate= ko.observable(null);
		self.starttime= ko.observable(null);		
		// self.rate = ko.observableArray([]);
		self.fixedQuote = ko.observable(null);
		self.fixedApproved = ko.observable(null);
		self.billableHours = ko.observable(null);
		self.response = ko.observableArray([]);
		self.timestamp = ko.observableArray([]);
		self.supplierNotes = ko.observableArray([]);
		self.nameErr = ko.observable(null);

		self.companyName=ko.observable(null);
		self.maintenanceType=ko.observable(null);
		self.urgent=ko.observable(null);
		self.overtime=ko.observable(null);
		self.weekend=ko.observable(null);
		self.notes=ko.observable(null);
		self.property_id=ko.observable(null);
		self.mobile=ko.observable(null);	
		self.hourly=ko.observable(null);
		self.isavail=ko.observable(false);
		hourly.subscribe(function (val){
			if (val!=null)
			{
				if (self.hourly()=="fixed" )
				{
					self.isavail(true);
					$('.billingtype').css({'width': '45%'});
				}
				else
				{
					self.isavail(false);
					$('.billingtype').css({'width': '95%'});
				}
			}
		});
		self.price=ko.observable(null);
		self.supplierStaff_ID=ko.observable(null);
		self.isvisible= ko.observable(true);

		self.index=ko.observable(0);
		self.materiallabel=ko.observable('Material Costs '+ (self.index()+1));
		self.materialname=ko.observable(null);
		self.materialrate=ko.observable(null);
		self.id= ko .observable('rate'+self.index()+"Error");
		self.idm= ko .observable('material'+self.index()+"Error");
		self.materialcost=ko.observableArray([{label:materiallabel, name: materialname,rate:materialrate
			,priceid: id, materialid: idm}]);
		self.addmaterial = function(){
			self.index(self.index()+1);
			self.materiallabel=ko.observable('Material Costs '+ (self.index()+1));
			self.materialname=ko.observable(null);
			self.materialrate=ko.observable(null);
			self.id= ko .observable('rate'+self.index()+"Error");
			self.idm= ko .observable('material'+self.index()+"Error");
		    self.materialcost.push({label:materiallabel, name: materialname,rate:materialrate
		    ,priceid: id, materialid: idm});
	    }
		self.addSupplierOrdersModal = ko.observable(null);
		self.closeSupplierOrdersModal = function() {
			$('.side-bar').toggle('fast');
		}
		self.activeTab = ko.observable('All');
		self.toggleTab = function(target){
			self.activeTab(target);
		}
		self.mainMessage = ko.observable(null);
		//get supllier data
		var callstate={'state':'getsupplier'}
		getSupplierOrders(callstate)
		.done(function(data){
			self.supplierid(data.supplierid);
			self.maintenanceordersid(data.id);

			self.companyName(data.companyname);
			self.maintenanceType(data.maintenancetype);
			data.urgent=='0' ? self.urgent('No') : self.urgent('YES');
			data.overtime=='0' ? self.overtime('No') : self.overtime('YES');
			data.weekend=='0' ? self.weekend('No') : self.weekend('YES');
			self.notes(data.notes);
			self.property_id(data.firstline+', '+data.city+', '+data.county+', '+data.postcode+', '
				+data.county);
			self.mobile(data.salutation+' '+data.firstname+' '+data.surname+', '+data.mobile);	
		})
		//get staff
		self.staffid=ko.observable(null);
		self.staffname=ko.observable(null);
		self.selectedstaff=ko.observableArray(null);
		self.requestedstaff=ko.observable(null);
		self.stafflist=ko.observableArray([{id:staffid, name:staffname}]);
		selectedstaff.subscribe(function (val){
	    	if (val!=null)
	    	{
	    		self.requestedstaff(val.id());
	    	}
	    	else{
	    		self.requestedstaff(null);
	    	}
	    });
		self.addstaff = function(){
		    self.stafflist.push({id:staffid, name:staffname});
	    }
		var callstate={'state':'getstaff'}
		getSupplierOrders(callstate)
		.done(function(data){
			self.stafflist.removeAll();
			staffcount = Object.keys(data).length;
			if (staffcount>0)
			{
				var i=0;
				while(i<staffcount)
				{ //alert(data);
					self.staffid=ko.observable(data[i].staffid);
					self.staffname=ko.observable(data[i].firstname+' '+data[i].surname);
					self.addstaff();
					i++;
				}
			}
		})

		// self.getSupplierOrders = function(){
		// 	getSupplierOrders()
		// 	.done(function(data){
		//		var tmp = $.map(data,function(supplierOrders){
		//			return new SupplierOrders(supplierOrders);
		//		})
		//		self.supplierOrders(tmp);
		//		self.timeUpdate(true);
		//		setTimeout(function(){self.timeUpdate(false)},3000);
		// 	})
		// }

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
				'maintenanceordersid':self.maintenanceordersid(),
				'supplier_id':self.supplierid(),
				'supplierstaff_id': self.requestedstaff(),
				'startdate': self.startdate(),
				'starttime': self.starttime(),
				'bilingtype': self.hourly(),
				'fixedQuote':self.fixedQuote(),
				'response':self.response(),
				'supplierNotes':self.notes(),
				'index':self.index()
			}
			var i=0;
			while(i<=self.index())
			{
				obj['material'+i]=self.materialcost()[i].name();   
				obj['rate'+i]=self.materialcost()[i].rate();
				i++;
			}
			addSupplierOrders(obj)
			// .done(function(data){
			// 	// obj.ID = data;
			// 	// self.supplierOrders.push(new SupplierOrders(obj));				
			// 	self.start(null);
			// 	self.rate(null);	
			// 	self.fixedQuote(null);
			// 	self.fixedApproved(null);
			// 	self.billableHours(null);
			// 	self.response(null);
			// 	self.timestamp(null);
			// 	self.supplierNotes(null);				

			// })
			// .always(function(){
			// 	self.adding(false);
			// })

		}		
			
			
		
		
		function findRequestedValue(value,arr,target,index){
			var tmp = arr && arr.length ? ko.utils.arrayFirst(arr,function(item){
				return item[index]() == value;
			}) : null;
			return tmp ? tmp[target]() : null;
		}		
		function addSupplierOrders(o){
			var d = $.Deferred();
			$.post( '../actions/forms/supplier_orders.php', { 'act':'addSupplierOrders', 'data':o})
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
							if (key.substring(0, 4)=='rate' || key.substring(0, 8)=='material')
							{
								$("#"+key).hide();
								if(key.substring(0, 4)=='rate')
								{
									$("#"+key).html('only numbers');
								}
								else if (key.substring(0, 8)=='material')
								{
									$("#"+key).html('only alphabet');
								}
								$("#"+key).show("slow").delay(5000).hide("slow");
								// $("#myElem").show("slow").delay(5000).hide("slow");
							}
							else
							{
								if (key=='startdateError' || key=='starttimeError' )
								{
									if(data.data[key].state=="empty" || data.data[key].timestate=="empty")
									{
										$("#"+key).html('empty');
									}
									else
									{
										if (key=='starttimeError')
										{
											$("#"+key).html('Can not be past time of today');
										}
										else
										{
											$("#"+key).html('Can not be past date');
										}
									}									
								}
								else if (key=='fixedQuoteError')
								{
									$("#"+key).html('only numbers');
								}
								//$("#"+key).html('empty');
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
		function getSupplierOrders(o){
			var d = $.Deferred();
			$.post( '../actions/forms/supplier_orders.php', { 'act':'getAllSupplierOrders','data':o})
			.done(function( data ) {
				data=JSON.parse(data);
				if( data )
				{
					if( data.status == 'ok' )
					{
						if (data.state=='staff')
						{
							d.resolve(data.data?data.data:[]);
						}
						else
						{
							d.resolve(data.data?data.data[0]:[]);
						}
					}
					else
					{
						d.reject();
					}
				}
			})
			return d;
		}
		
		// function getSupplierOrders(){
		// 	var d = $.Deferred();
		// 	$.ajax({
		// 		type: 'POST',
		// 		url:'../actions/forms/supplier_orders.php', 
		// 		data: 'act=getAllSupplierOrders&FORM_TOKEN='+FORM_TOKEN,
		// 		dataType: "json",
				
		// 		success:function(data) {

		// 			if (data.status == 'ok') {
		// 				d.resolve(data.data?data.data:[]);
		// 			}else{
		// 				d.reject();
		// 			}
		// 		},
		// 		error:function() {
		// 			d.reject();
		// 		}
				
		// 	});
			
			
		// 	return d;
		// }

	}
	var em = document.getElementById('supplierOrdersPage');
	if(em) ko.applyBindings(new supplierOrdersViewModel(), em);
});