define(['knockout', 'jquery', 'moment', 'bootstrap', 'notify', 'modal', 'timedUpdate'], function (ko,$,moment){
	$(".invo_input, .invo_select").on("focusout", function(){
		if(($(this).val()!="" && $(this).val()!="type"))
		{
			$(this).next(".error").html("");
			$(this).attr('style', 'background-color: white !important');
		}
	});
	function supplierOrdersViewModel() {
		//dusplay supplier
		self.SupplierOrderCount= ko.observable(0);
		self.SupplierOrdersAll= ko.observable(null);
		self.Supplierindex=ko.observable(0);
		self.isavailSupplier=ko.observable(false);
		//display staff
		self.selectedstaff=ko.observableArray(null);
		self.requestedstaff=ko.observable(null);
		self.stafflist=ko.observableArray([{id:null, name:null}]);
		self.addstaff = function(staffid,staffname){
		    self.stafflist.push({id:staffid, name:staffname});
	    }
		selectedstaff.subscribe(function (val){
	    	if (val!=null)
	    	{
	    		self.requestedstaff(self.selectedstaff().id);
	    	}
	    	else{
	    		self.requestedstaff(null);
	    	}
	    });

		self.supplierid= ko .observable(null);
		self.maintenanceordersid=ko.observable(null);
		self.timeUpdate = ko.observable(false);
		self.inited = ko.observable(false);
		self.adding = ko.observable(false);		
		// for add in db
		self.fixedQuote = ko.observable(null);
		self.fixedApproved = ko.observable(null);
		self.billableHours = ko.observable(null);
		self.response = ko.observableArray([]);
		self.startdate= ko.observable(null);
		self.starttime= ko.observable(null);
		self.suppliernotes= ko.observable(null);
		//for display in form
		self.companyName=ko.observable(null);
		self.maintenanceType=ko.observable(null);
		self.urgent=ko.observable(null);
		self.overtime=ko.observable(null);
		self.weekend=ko.observable(null);
		self.notes=ko.observable(null);
		self.property_id=ko.observable(null);
		self.mobile=ko.observable(null);	
		self.hourly=ko.observable(null);
		self.schedule=ko.observable(null);
		self.isavail=ko.observable(false);
		hourly.subscribe(function (val){
			if (val!=null)
			{
				if (self.hourly()=="fixed" )
				{
					self.isavail(true);
					$('.billingtype').css({'width': '45%'});
					self.fixedQuote(null);
				}
				else
				{
					self.isavail(false);
					$('.billingtype').css({'width': '97%'});
				}
			}
		});
		self.price=ko.observable(null);
		self.supplierStaff_ID=ko.observable(null);
		self.isvisible= ko.observable(true);
		//for matericalcost
		self.index=ko.observable(0);
		self.materiallabel=ko.observable('Materials '+ (self.index()+1));
		self.materialname=ko.observable(null);
		self.materialrate=ko.observable(null);
		self.id= ko .observable('rate'+self.index()+"Error");
		self.idm= ko .observable('material'+self.index()+"Error");
		self.materialcost=ko.observableArray([{label:materiallabel, name: materialname,rate:materialrate
			,priceid: id, materialid: idm}]);
		self.addmaterial = function(){
			self.index(self.index()+1);
			self.materiallabel=ko.observable('Materials '+ (self.index()+1));
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
				'reallocated': (self.response()=='cancelled'||self.response()=='rejected')? '1':'0',
				'suppliernotes':self.suppliernotes(),
				'maintenance_order_schedule':self.schedule(),
				'start': null,
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
			.done(function(data){
				// obj.ID = data;
				// self.supplierOrders.push(new SupplierOrders(obj));				
				self.startdate(null);
				self.starttime(null);
				self.fixedQuote(null);
				self.suppliernotes(null);
				self.selectedstaff(null);
				self.requestedstaff(null);
				self.materialcost.removeAll();
				self.index(-1);
				self.addmaterial();
				self.nextsupplier();				
			})
			.fail(function(data){
				for (var key of Object.keys(data.data))
				{
					if (key.substring(0, 4)=='rate' || key.substring(0, 8)=='material')
					{
						$("#"+key).hide();
						if(key.substring(0, 4)=='rate')
						{
							$("#"+key).html('Numbers only');
						}
						else if (key.substring(0, 8)=='material')
						{
							$("#"+key).html('Text only');
						}
						$("#"+key).show("slow").delay(5000).hide("slow");
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
									$("#"+key).html('Cannot be after the last completion time');
								}
								else
								{
									if (data.data[key].state=='shcedule_diff')
									{
										$("#"+key).html('Cannot be after the last completion date');
									}
									else
									{
										$("#"+key).html('Can not be past date');	
									}
								}
							}									
						}
						else if (key=='fixedQuoteError')
						{
							$("#"+key).html('Numbers only');
						}
						else
						{
							$("#"+key).html('Text only');
						}
						$("#"+key).siblings('.invo_input').css({"background-color": "#f8d7da"});
						$("#"+key).siblings('.invo_select').attr('style', 
							'background-color: #f8d7da !important');
					 	$("#"+key).siblings('.invo_select').children().css({
					 		"background-color": "white"});
					}
						
				}
			})
			.always(function(){
				self.adding(false);
			})
		}
		self.nextsupplier=function()
		{
			if (self.Supplierindex()<self.SupplierOrderCount())
			{
				self.loaddata();
			}
			else
			{
				DisplaySupplierOrders();
				self.isavailSupplier(false);
				alert("All orders have been reviewed");
			}
		}		
		function findRequestedValue(value,arr,target,index){
			var tmp = arr && arr.length ? ko.utils.arrayFirst(arr,function(item){
				return item[index]() == value;
			}) : null;
			return tmp ? tmp[target]() : null;
		}		
		function addSupplierOrders(o){
			var d = $.Deferred();
			$.post( '../actions/forms/supplier_orders.php', { 'act':'addSupplierOrders', 'data':o,'FORM_TOKEN' : FORM_TOKEN})
			.done(function( data ) {
				// alert(data);
				// data=JSON.parse(data);
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
		//get supplier and staff
		function getSupplierOrders(o){
			var d = $.Deferred();
			$.post( '../actions/forms/supplier_orders.php', { 'act':'getAllSupplierOrders','data':o,'FORM_TOKEN' : FORM_TOKEN})
			.done(function( data ) {
				// alert(data);
				// data=JSON.parse(data);
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
							d.resolve(data.data?data.data:[]);
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
		//display supplier
		function DisplaySupplierOrders(supplierid=null,id=null,companyname=null,
			maintenancetype=null,urgent=null,overtime=null,weekend=null,notes=null,
			schedule=null,property_id=null,mobile=null)
		{
			self.supplierid(supplierid);
			self.maintenanceordersid(id);
			self.companyName(companyname);
			self.maintenanceType(maintenancetype);
			self.urgent(urgent);
			self.overtime(overtime);
			self.weekend(weekend);
			self.notes(notes);
			self.schedule(schedule);
			self.property_id(property_id);
			self.mobile(mobile);
			self.Supplierindex(self.Supplierindex()+1);
		}
		self.loaddata=function()
		{
			//data analysis
			var index=self.SupplierOrdersAll()[self.Supplierindex()];
			var urgent=index.urgent=='0' ? 'No':'Yes';
			var overtime=index.overtime=='0' ? 'No':'Yes';
			var weekend=index.weekend=='0' ? 'No':'Yes';
			var property_id=index.buildingname+', '+index.firstline+', '+index.city
				+', '+index.county+', '+index.postcode+', '+index.country;
			var mobile=index.salutation+' '+index.firstname+' '+index.surname+', '+
				index.mobile;
			//display data
			DisplaySupplierOrders(index.supplierid,index.id,index.companyname,
				index.maintenancetype,urgent,overtime,weekend,index.notes,
				index.schedule,property_id,mobile);
		}
		//get supllier data
		var callstate={'state':'getsupplier'}
		getSupplierOrders(callstate)
		.done(function(data){
			count = Object.keys(data).length;   //get supplier length
			self.SupplierOrderCount(count);
			if (self.SupplierOrderCount()>0)
			{
				self.SupplierOrdersAll(data);
				self.isavailSupplier(true);
				self.loaddata();
			}
		})
		//get staff
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
					self.addstaff(data[i].staffid, data[i].firstname+' '+data[i].surname);
					i++;
				}
			}
		})
	}
	var em = document.getElementById('supplierOrdersPage');
	if(em) ko.applyBindings(new supplierOrdersViewModel(), em);
});