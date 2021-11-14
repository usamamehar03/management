define(['knockout', 'jquery', 'moment', 'bootstrap', 'notify', 'modal', 'timedUpdate'], function (ko,$,moment){
	$(document).on("input", ".input_data", function(){
		if(($(this).val()!="" && $(this).val()!="type"))
		{
			$(this).next(".error").html("");
			$(this).attr('style', 'background-color: white !important');
		}
	});
	function supplierFinalViewModel() {		
		
		//supplier final modal
		self.finalsupplierNotes=ko.observable(null);
		self.billableHours = ko.observable(null);
		self.minutes=ko.observable(0);
		self.partlist=ko.observableArray([{itempart_id:null,
			materialcostid:null, partname:null, partprice:null,
			serialnumber:null,warranty:null,serialid:null,warrantyid:null}]);
		self.addsupplierpart=function(itempart_id, id, partname, partprice,
			serialnumber, warranty, serialid, warrantyid)
		{
			self.partlist.push({itempart_id:itempart_id,
				materialcostid:id, partname:partname, partprice:partprice,
				serialnumber:serialnumber,warranty:warranty,serialid:serialid,
				warrantyid:warrantyid
			});
		}
		self.property_id=ko.observable(null);
		self.property_insertid=ko.observable(null);
		self.propertymanagmentid=ko.observable(null);
		self.userid=ko.observable(null);
		self.billingincrement=ko.observable(null);
		self.maintenanceorderid=ko.observable(null);
		self.tenantdamage=ko.observable(null);
		self.InvoiceNotes=ko.observable(null);
		self.InvoiceRef=ko.observable(null);
		self.Invoiceduedate=ko.observable(null);
		self.Invoicenumber=ko.observable(null);
		self.supplierorderid=ko.observable(null);
		self.rate=ko.observable(null);
		self.price=ko.observable(null);
		self.calloutcharge=ko.observable(null);
		self.ishours=ko.observable(false);
		//vaiables for next function
		self.supplierfinalorders=ko.observable(null);
		self.supplierfinalcount=ko.observable(0);
		self.supplierfinalindex=ko.observable(0);

		// self.id= ko.observable('rate'+self.index()+"Error");
		self.activeTab = ko.observable('All');
		self.toggleTab = function(target){
			self.activeTab(target);
		}
		self.mainMessage = ko.observable(null);
		self.addstaff = function(){
		    self.stafflist.push({id:staffid, name:staffname});
	    }				
		//start supplier final modal
		self.AddSupplierFinal=function()
		{
			//alert('here');
			var obj={
				'supplierorderid':     self.supplierorderid(),
				'property_id':         self.property_insertid(),
				'propertymanagmentid': self.propertymanagmentid(),
				'userid': 			   self.userid(),
				'maintenanceorderid':  self.maintenanceorderid(),
				'supplierNotes':  self.finalsupplierNotes(),
				'billableHours':  self.billableHours(),
				'minutes':        self.minutes(),
				'tenantdamage':   self.tenantdamage(),
				'InvoiceNotes':   self.InvoiceNotes(),
				'InvoiceRef':     self.InvoiceRef(),
				'Invoicenumber':  self.Invoicenumber(),
				'Invoiceduedate': self.Invoiceduedate(),
				'rate':           self.rate(),
				'price':	      self.price(),
				'calloutcharge':  self.calloutcharge(),
				'billingincrement': self.billingincrement(),
				'purpose': 		  'Supplier',
				'parts':          self.partlist()
			}
			addsupplierfinal(obj)
			.done(function(data){
				self.finalsupplierNotes(null);
				self.billableHours(null);
				self.minutes(0);
				self.InvoiceNotes(null);
				self.InvoiceRef(null);
				self.Invoiceduedate(null);
				self.Invoicenumber(null);
				self.userid(null);
				self.ishours(false);
				self.partlist.removeAll();
				self.next();
			})
			.fail(function(data){
				if (data.status=='err')
				{
					for (var key of Object.keys(data.data))
					{
						if (key=='supplierNotesError' || key=='InvoiceNotesError' || 
							key=='serialnumberError' || key=='InvoiceRefError')
						{
							$("#"+key).html('Only Text');
						}
						else if (key=='InvoiceduedateError' || 
							key.substring(0,8)=='warranty')
						{
							if(data.data[key].state=='invalid')
							{
								$("#"+key).html('Cannot be past date');
							}
							else
							{
								$("#"+key).html('Empty');
							}
						}
						else
						{
							$("#"+key).html('Only Numbers');
						}
						// $("#"+key).siblings('.input_data').css({"background-color": "#f8d7da"});
						$("#"+key).siblings('.input_data').attr('style', 
							'background-color: #f8d7da !important');
					 	$("#"+key).siblings('.input_data').children().css({
					 		"background-color": "white"});		
					}
				}			
			})
		}
		function addsupplierfinal(o){
			var d = $.Deferred();
			$.post( '../actions/forms/supplier_final.php', { 'act':'AddSupplierFinal', 'data':o,'FORM_TOKEN' : FORM_TOKEN})
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
			$.post( '../actions/forms/supplier_final.php', { 'act':'getsupplierfinaldata', 'FORM_TOKEN' : FORM_TOKEN})
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
		function displayorder(property_id=null, property_insertid=null,
			propertymanagmentid=null,userid=null,supplierorderid=null,
			maintenanceorderid=null,rate=null,price=null,calloutcharge=null,
			billingincrement=null)
		{
			self.property_id(property_id);
			self.property_insertid(property_insertid);

			self.propertymanagmentid(propertymanagmentid);
			self.userid(userid);
			self.supplierorderid(supplierorderid);
			self.maintenanceorderid(maintenanceorderid);
			self.rate(rate);
			self.price(price);
			self.calloutcharge(calloutcharge);
			self.billingincrement(billingincrement);
		}
		self.displayparts=function()
		{
			var index=self.supplierfinalorders()[self.supplierfinalindex()];
			var count=Object.keys(index['parts']).length;
			if (count>0)
			{
				for (var key of Object.keys(index['parts'])) 
				{
					self.addsupplierpart(index['parts'][key].itempart_id,
						index['parts'][key].materialcostid,
						index['parts'][key].partname, 
						index['parts'][key].partprice,null,null,
						'serialnumber'+key+'Error','warranty'+key+'Error');
				}
			}
		}
		self.loadorder=function()
		{
			var index=self.supplierfinalorders()[self.supplierfinalindex()];
			var property_id=index.property_adress.building+', '+
				index.property_adress.firstline+', '+index.property_adress.city+', '
				+index.property_adress.country+'   '+index.property_adress.postcode;
			var calloutcharge='';
			var billingincrement='';
			if (index.calloutcharge)
			{
				calloutcharge=index.calloutcharge;
				billingincrement=index.billingincrement;
			}
				//call display function
			displayorder(property_id, index.property_adress.propertyid, 
				index.property_adress.propertymanagmentid,
				index.property_adress.userid, index.supplierorder_id, 
				index.maintenanceorders_id, index.rate, index.price, 
				calloutcharge, billingincrement);
			// call display parts
			self.displayparts();
			self.supplierfinalindex(self.supplierfinalindex()+1);
			if(index.rate!='Fixed')
			{
				self.ishours(true);
			}
		}
		self.next=function()
		{
			self.partlist.removeAll();
			if(self.supplierfinalindex()<self.supplierfinalcount())
			{
				self.loadorder();
			}
			else
			{
				self.supplierfinalcount(0);
				self.supplierfinalindex(0);
				self.supplierfinalorders(null);
				self.ishours(false);
				displayorder();
				alert("There are no more orders to process");
			}
		}
		self.getdata=function()
		{
			getdata()
			.done(function(data){
				self.partlist.removeAll();
				count = Object.keys(data).length;
				if (count>0)
				{
					self.supplierfinalcount(count);
					self.supplierfinalindex(0);
					self.supplierfinalorders(data);
					self.loadorder();
				}
			})
			.fail(function(data){
				self.partlist.removeAll();
				self.supplierfinalcount(0);
				self.supplierfinalindex(0);
				self.supplierfinalorders(null);
				self.ishours(false);
				displayorder();
			})
		}
		self.getdata();
		//end here supplier final modal
	}	
	var em = document.getElementById('supplierFinalPage');
	if(em) ko.applyBindings(new supplierFinalViewModel(), em);
});