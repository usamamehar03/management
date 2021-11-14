// alert("here");
define(['knockout', 'jquery', 'moment', 'bootstrap', 'notify', 'modal', 'timedUpdate'], function (ko,$,moment){
	$(".invo_input, .invo_select").on("focusout", function(){
		if(($(this).val()!="" && $(this).val()!="type"))
		{
			$(this).next(".error").html("");
			$(this).attr('style', 'background-color: white !important');
		}
	});
	function supplierMaterialsViewModel() {
		self.allsuplierorders=ko.observable(null);
		self.supplierorderscount=ko.observable(null);
		self.supplierordersindex=ko.observable(null);
		self.isavail=ko.observable(false); 
		self.maintenanceorderid=ko.observable(null);
		self.supplierorder_id=ko.observable(null);
		self.companyName=ko.observable(null);
		self.maintenanceType=ko.observable(null);
		self.urgent=ko.observable(null);
		self.overtime=ko.observable(null);
		self.weekend=ko.observable(null);
		self.notes=ko.observable(null);
		self.supplierNotes = ko.observable(null);
		self.property_address=ko.observable(null);
		self.mobile=ko.observable(null);
		self.fixedQuote=ko.observable(null);
		self.schedule=ko.observable(null);
		self.startdate=ko.observable(null);
		self.starttime=ko.observable(null);
		self.materialcost=ko.observableArray([{label:null, name: null,rate:null,
			materialcostid:null, aprovepart:null}]);
		self.addpart=function(label,name,rate,materialcostid,aprovepart)
		{
			self.materialcost.push({label:label,name:name,rate:rate,
				materialcostid:materialcostid, aprovepart:aprovepart});
		}
		self.response=ko.observable(null);
		
		// self.id= ko .observable('rate'+self.index()+"Error");
		self.activeTab = ko.observable('All');
		self.toggleTab = function(target){
			self.activeTab(target);
		}
		self.mainMessage = ko.observable(null);
		self.addstaff = function(){
		    self.stafflist.push({id:staffid, name:staffname});
	    }		
	    
		//start materailcodt modal
		self.addSupplierMaterial=function()
		{
			var parts=[];
			if (Object.keys(self.materialcost()).length>0)
			{
				for (var key of Object.keys(self.materialcost()))
				{
					parts.push({'materialcostid':self.materialcost()[key].materialcostid
						,'aprovepart':self.materialcost()[key].aprovepart});
				}
			}
			var obj={
				'suppliernotes':self.response()=='Rejected'? 'The order was cancelled as your quote was not approved': null,
				'fixedApproved':self.response(),
				'maintenanceorder_id': self.maintenanceorderid(),
				'supplierorder_id':self.supplierorder_id(),
				'ispartsrejected':'false',
				'response':'Rejected',
				'parts':parts
			}
			addsuppliermaterial(obj)
			.done(function(data){
				self.next();
				// if(self.response()=='Rejected')
				// {
				// 	location.replace("http://localhost/A.internship/IDprop/forms/MaintenanceOrders.php");
				// }
			})
		}
		self.next=function()
		{
			if (self.supplierordersindex()<self.supplierorderscount())
			{
				self.call_loadSupplierMaterial_data();
			}
			else
			{
				self.allsuplierorders(null);
				self.supplierordersindex(null);
				self.supplierorderscount(null);
				self.loadSupplierMaterial_data();
				self.materialcost.removeAll();
				self.isavail(false);
			}
		}
		function addsuppliermaterial(o){
			var d = $.Deferred();
			$.post( '../actions/forms/supplier_materials.php', { 'act':'AddSupplierMaterial', 'data':o,'FORM_TOKEN' : FORM_TOKEN})
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
		function getdata(){
			var d = $.Deferred();
			$.post( '../actions/forms/supplier_materials.php', { 'act':'GetMaterialCostData', 'FORM_TOKEN' : FORM_TOKEN})
			.done(function( data ) {
				// 	alert(data);
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
		// self.loadpropertid=function()
		// {
		// 	var obj={
		// 		'state' :"getPropertyid"
		// 	}
		// 	getdata(obj)
		// 	.done(function(data){
		// 		self.property_id(data[0].building+', '+data[0].firstline+', '+
		// 			data[0].city+', '+data[0].country+'   '+data[0].postcode);
		// 	})
		// 	.fail(function(data){
		// 		self.property_id(null);
		// 	})
		// }
		self.getdata=function()
		{
			setTimeout(function(){
				getdata()
				.done(function(data){
					count = Object.keys(data).length;   //get supplier length
					if (count>1)
					{
						// alert(count);
						self.allsuplierorders(data);
						self.supplierorderscount(count-1);
						self.supplierordersindex(0);
						self.isavail(true);
						self.call_loadSupplierMaterial_data();
					}
				})
				.fail(function(data){
					self.allsuplierorders(null);
					self.supplierordersindex(null);
					self.supplierorderscount(null);
					self.loadSupplierMaterial_data();
					self.materialcost.removeAll();
				})
			},100);
		}
		//load ahajx data
		self.loadSupplierMaterial_data=function(companyname=null,maintenancetype=null,
			urgent=null,overtime=null,weekend=null,propertymanagernotes=null,
			suppliernotes=null,property_address=null,mobile=null,fixedquote=null,
			schedule=null,startdate=null,starttime=null,supplierorderid=null, 
			maintenanceorderid=null)
		{
			self.companyName(companyname);
			self.maintenanceType(maintenancetype);
			self.urgent(urgent);
			self.overtime(overtime);
			self.weekend(weekend);
			self.notes(propertymanagernotes);
			self.supplierNotes(suppliernotes);
			self.property_address(property_address);
			self.mobile(mobile);
			self.fixedQuote(fixedquote);
			self.schedule(schedule);
			self.startdate(startdate);
			self.starttime(starttime);
			self.supplierorder_id(supplierorderid);
			self.maintenanceorderid(maintenanceorderid);
			self.supplierordersindex(self.supplierordersindex()+1);
		}
		//pass argument  in loadSupplierMaterial_data()
		self.call_loadSupplierMaterial_data=function()
		{
			var index=self.allsuplierorders()[self.supplierordersindex()];
			var property_address=index.buildingname+', '+index.firstline+', '+
				index.city+', '+index.country+'   '+index.postcode;
			var mobile=index.mobile+',  '+index.firstname+' '+index.surname;
			var date=index.start;
			date=date.split(" ");
			//pass arguments
			self.loadSupplierMaterial_data(index.companyname,index.maintenancetype,
			index.urgent==0?'No':'Yes',index.overtime==0?'No':'Yes',
			index.weekend==0?'No':'Yes',index.propertymanagernotes,
			index.suppliernotes,property_address,mobile,
			index.fixedquote==0?'NO':index.fixedquote,index.schedule,
			date[0],date[1],index.supplierorderid,index.maintenanceorder_id);
			//display material list
			self.materialcost.removeAll();
			var parts=self.allsuplierorders()['partslist'][index.supplierorderid]
			if (Object.keys(parts).length>0)
			{
				for (var key of Object.keys(parts))
				{
					self.addpart('materialcost'+key,parts[key].partname,
						parts[key].price, parts[key].materialcostid,'0');
				}
			}
		}
		self.getdata();
		//end materialcost modal
	}	
	var em = document.getElementById('supplierMaterialsPage');
	if(em) ko.applyBindings(new supplierMaterialsViewModel(), em);
});