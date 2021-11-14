define(['knockout', 'jquery', 'moment', 'bootstrap', 'notify', 'modal', 'timedUpdate'], function (ko,$,moment){
	function supplierMaterialsViewModel() {
		self.isorder=ko.observable(false);
		self.fixedApproved=ko.observable(null);
		self.response=ko.observable('Accepted');
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
		self.fixedquote_state=ko.observable('low');
		self.materialcost=ko.observableArray([{label:null, name: null,rate:null,
			materialcostid:null, aprovepart:"1"}]);
		self.addpart=function(label,name,rate,materialcostid,aprovepart)
		{
			self.materialcost.push({label:label,name:name,rate:rate,
				materialcostid:materialcostid, aprovepart:aprovepart});
		}
		self.partsaproval=function()
		{
		    check=ko.utils.arrayFilter(self.materialcost(), function(item) {
		    	if (item.aprovepart=='0')
		    	{
		    		self.isorder(true);
		    		self.fixedApproved('Rejected');
		    		self.response('Rejected');
		    		return true;
		    	}
		    });
		    if (Object.keys(check).length<=0 && self.fixedquote_state()=='low')
		    {
		    	self.isorder(false);
		    	self.fixedApproved('Accepted');
		    	self.response('Accepted');
		    }
		}


		self.activeTab = ko.observable('All');
		self.toggleTab = function(target){
			self.activeTab(target);
		}
		self.mainMessage = ko.observable(null);
		self.addstaff = function(){
		    self.stafflist.push({id:staffid, name:staffname});
	    }		
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
			if (self.fixedApproved()=='Rejected')
			{
				self.response('Rejected');
			}
			var obj={
				'suppliernotes':self.fixedApproved()=='Rejected'? 'The order was cancelled as your quote was not approved': null,
				'fixedApproved':self.fixedApproved(),
				'maintenanceorder_id': self.maintenanceorderid(),
				'supplierorder_id':self.supplierorder_id(),
				'ispartsrejected':'false',
				'response': self.response(),
				// 'response':self.fixedApproved()=='Rejected'?'Rejected':'Accepted',
				'fixedquote_state':self.fixedquote_state(),
				'parts':parts
			}
			addsuppliermaterial(obj)
			.done(function(data){
				self.fixedquote_state('low');
				self.response('Accepted');
				self.isorder(false);
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
				self.isorder(false);
			}
		}
		self.isfixedquote_high=function()
		{
			var index=self.allsuplierorders()[self.supplierordersindex()];
			if (parseInt(index.fixedquote)>0)
			{
				var obj={
				'maintenanceorder_id': index.maintenanceorder_id,
				'state': 	'checkfixedquote'
				}
				getdata(obj)
				.done(function(data){
					if (parseInt(data)<parseInt(index.fixedquote))
					{
						self.isorder(true);
						self.fixedApproved('Rejected');
						self.response('Rejected');
						self.fixedquote_state('high');
					}
				})
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
		function getdata(o){
			var d = $.Deferred();
			$.post( '../actions/forms/supplier_materials.php', { 'act':'GetMaterialCostData', 'data':o, 'FORM_TOKEN' : FORM_TOKEN})
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
		self.getdata=function()
		{
			setTimeout(function(){
				var obj={ 'state': 'getall'}
				getdata(obj)
				.done(function(data){
					count = Object.keys(data).length;   //get supplier length
					if (count>1)
					{
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
			var buildingname= index.buildingname? index.buildingname+', ': '';
			var property_address=buildingname+index.firstline+', '+
				index.city+', '+index.country+'   '+index.postcode;
			var mobile=index.mobile+',  '+index.firstname+' '+index.surname;
			var date=index.start;
			date=date.split(" ");
			//
			self.isfixedquote_high();

			//pass arguments
			self.loadSupplierMaterial_data(index.companyname,index.maintenancetype,
			index.urgent==0?'No':'Yes',index.overtime==0?'No':'Yes',
			index.weekend==0?'No':'Yes',index.propertymanagernotes,
			index.suppliernotes,property_address,mobile,
			(index.fixedquote<=0 || index.fixedquote==''?'No':index.fixedquote),index.schedule,
			date[0],date[1],index.supplierorderid,index.maintenanceorder_id);
			//display material list
			self.materialcost.removeAll();
			var parts=self.allsuplierorders()['partslist'][index.supplierorderid]
			if (Object.keys(parts).length>0)
			{
				for (var key of Object.keys(parts))
				{
					self.addpart('materialcost'+key,parts[key].partname,
						parts[key].price, parts[key].materialcostid,'1');
				}
			}
		}
		self.getdata();
		//end materialcost modal

	}	
	var em = document.getElementById('supplierMaterialsPage');
	if(em) ko.applyBindings(new supplierMaterialsViewModel(), em);
});