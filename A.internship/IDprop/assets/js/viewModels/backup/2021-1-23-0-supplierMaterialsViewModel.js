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
		//tennant feedback
		self.property_id=ko.observable(null);
		self.ratingPropertyManager = ko.observable(null);
		self.ratingSupplier = ko.observable(null);
		self.tenantFeedback = ko.observable(null);
		//supplier material
		self.allsuplierorders=ko.observable(null);
		self.supplierorderscount=ko.observable(null);
		self.supplierordersindex=ko.observable(null);
		self.isavail=ko.observable(false); 
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
		//supplier final modal
		self.finalsupplierNotes=ko.observable(null);
		self.billableHours = ko.observable(null);
		self.partlist=ko.observableArray([{materialcostid:null,partname:null,
			partprice:null,serialnumber:null,warranty:null,serialid:null,
			warrantyid:null}]);
		self.addsupplierpart=function(id,partname,partprice,serialnumber,warranty,serialid,
			warrantyid)
		{
			self.partlist.push({materialcostid:id,partname:partname,partprice:partprice,
				serialnumber:serialnumber,warranty:warranty,serialid:serialid,
				warrantyid:warrantyid
			});
		}
		self.tenantdamage=ko.observable(null);
		self.InvoiceNotes=ko.observable(null);
		self.InvoiceRef=ko.observable(null);
		self.Invoicedate=ko.observable(null);
		self.supplierorderid=ko.observable(null);
		self.rate=ko.observable(null);
		self.price=ko.observable(null);
		self.calloutcharge=ko.observable(null);



		
		// self.id= ko .observable('rate'+self.index()+"Error");
		self.activeTab = ko.observable('All');
		self.toggleTab = function(target){
			self.activeTab(target);
		}
		self.mainMessage = ko.observable(null);
		self.addstaff = function(){
		    self.stafflist.push({id:staffid, name:staffname});
	    }		
	    //add tennet feeback
		self.addfeedback=function()
		{
			var obj = {
				//'addSupplierMaterials':self.SupplierMaterials(),				
				//'highLight':true,
				//'approved':IS_SENIOR ? true : false,//Senior OR Admin can approve									
				'ratingPropertyManager':self.ratingPropertyManager(),
				'ratingSupplier':self.ratingSupplier(),
				'tenantFeedback':self.tenantFeedback()			
			}
			addcall(obj)
			.done(function(data){
				self.ratingPropertyManager(null);
				self.ratingSupplier(null);
				self.tenantFeedback(null);
			})
			.fail(function(data){
				if (data.status=="err")
				{
					for (var key of Object.keys(data.data))
					{
						if (key=='tenantFeedbackError')
						{
							$("#"+key).html('only text');
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
			})
		}
		function addcall(o){
			var d = $.Deferred();
			$.post( '../actions/forms/supplier_materials.php', { 'act':'TenantFeedback', 'data':o,'FORM_TOKEN' : FORM_TOKEN})
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
			$.post( '../actions/forms/supplier_materials.php', { 'act':'GetMaterialCostData', 'data':o,'FORM_TOKEN' : FORM_TOKEN})
			.done(function( data ) {
					alert(data);
				data=JSON.parse(data);
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
		self.loadpropertid=function()
		{
			var obj={
				'state' :"getPropertyid"
			}
			getdata(obj)
			.done(function(data){
				self.property_id(data[0].building+', '+data[0].firstline+', '+
					data[0].city+', '+data[0].country+'   '+data[0].postcode);
			})
			.fail(function(data){
				self.property_id(null);
			})
		}
		if ($('#supplierMaterialsPage').hasClass('tenantFeedback_page'))
		{
			
			setTimeout(function(){
				if (self.property_id()==null)
				{
					self.loadpropertid();
				}
			},800);
		}
		//end feedback
		
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
				'fixedApproved':self.response(),
				'supplierorder_id':self.supplierorder_id(),
				'parts':parts
			}
			addsuppliermaterial(obj)
			.done(function(data){
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
				// if(self.response()=='Rejected')
				// {
				// 	location.replace("http://localhost/A.internship/IDprop/forms/MaintenanceOrders.php");
				// }
			})
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
		if ($('#supplierMaterialsPage').hasClass('suppliermaterial_page'))
		{
			setTimeout(function(){
				var obj={
					'state' :"getSupplierOrders"
				}
				getdata(obj)
				.done(function(data){
					// alert(self.allsuplierorders()[0].companyname);
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
			},900);
		}
		//load ahajx data
		self.loadSupplierMaterial_data=function(companyname=null,maintenancetype=null,
			urgent=null,overtime=null,weekend=null,propertymanagernotes=null,
			suppliernotes=null,property_address=null,mobile=null,
			fixedquote=null,schedule=null,startdate=null,starttime=null,supplierorderid=null)
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
			self.supplierordersindex(self.supplierordersindex()+1);
		}
		//pass argument  in loadSupplierMaterial_data()
		self.call_loadSupplierMaterial_data=function(){
			var index=self.allsuplierorders()[self.supplierordersindex()];
			var property_address=index.buildingname+', '+index.firstline+', '+
				index.city+', '+index.country+'   '+index.postcode;
			var mobile=index.mobile+',  '+index.firstname+' '+index.surname;
			var date=index.start;
			date=date.split(" ");
			self.loadSupplierMaterial_data(index.companyname,index.maintenancetype,
			index.urgent==0?'No':'Yes',index.overtime==0?'No':'Yes',
			index.weekend==0?'No':'Yes',index.propertymanagernotes,
			index.suppliernotes,property_address,mobile,
			index.fixedquote==0?'NO':index.fixedquote,index.schedule,
			date[0],date[1],index.supplierorderid);
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
		self.next=function(){
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
		//end materialcost modal
		//start supplier final modal
		self.AddSupplierFinal=function()
		{
			//alert('here');
			var obj={
				'supplierorderid':self.supplierorderid(),
				'supplierNotes':  self.finalsupplierNotes(),
				'billableHours':  self.billableHours(),
				'tenantdamage':   self.tenantdamage(),
				'InvoiceNotes':   self.InvoiceNotes(),
				'InvoiceRef':     self.InvoiceRef(),
				'Invoiceduedate': self.Invoicedate(),
				'rate':           self.rate(),
				'price':	      self.price(),
				'calloutcharge':  self.calloutcharge(),
				'parts':          self.partlist()
			}
			addsupplierfinal(obj)
			.done(function(data){
				self.finalsupplierNotes(null);
				self.billableHours(null);
				self.InvoiceNotes(null);
				self.InvoiceRef(null);
				self.Invoicedate(null);
			})
			.fail(function(data){
				if (data.status=='err')
				{
					for (var key of Object.keys(data.data))
					{
						if (key=='supplierNotesError' || key=='InvoiceNotesError')
						{
							$("#"+key).html('only text');
						}
						//else if (key=='billableHoursError')
						else
						{
							$("#"+key).html('only numbers');
						}
						$("#"+key).siblings('.invo_input').css({"background-color": "#f8d7da"});
						$("#"+key).siblings('.invo_select').attr('style', 
							'background-color: #f8d7da !important');
					 	$("#"+key).siblings('.invo_select').children().css({
					 		"background-color": "white"});		
					}
				}			
			})
		}
		function addsupplierfinal(o){
			var d = $.Deferred();
			$.post( '../actions/forms/supplier_materials.php', { 'act':'AddSupplierFinal', 'data':o,'FORM_TOKEN' : FORM_TOKEN})
			.done(function( data ) {
				alert(data);
				data=JSON.parse(data);
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

		if ($('#supplierMaterialsPage').hasClass('supplierfinal_page'))
		{
			setTimeout(function(){
				if (self.property_id()==null)
				{
					self.loadpropertid();
				}
				var obj={
				state:'getsupplierfinaldata'
				}
				getdata(obj)
				.done(function(data){
					self.partlist.removeAll();
					count = Object.keys(data[0]['parts']).length;
					if (count>0)
					{
						var i=0;
						while(i<count)
						{
							self.addsupplierpart(data[0]['parts'][i].materialcostid,
								data[0]['parts'][i].partname,data[0]['parts'][i].partprice,null,null,
								'serialnumber'+i+'Error','warranty'+i+'Error');
							i++;
						}
					}
					self.supplierorderid(data[0].supplierorder_id);
					self.rate(data[0].rate);
					self.price(data[0].price);
					if (data[0].calloutcharge)
					{
						self.calloutcharge(data[0].calloutcharge);
					}
				})
				.fail(function(data){
					self.partlist.removeAll();
					self.supplierorderid(null);
					self.rate(null);
					self.price(null);
					self.calloutcharge(null);
				})
			},1000);
		}
		//end here supplier final modal
	}	
	var em = document.getElementById('supplierMaterialsPage');
	if(em) ko.applyBindings(new supplierMaterialsViewModel(), em);
});