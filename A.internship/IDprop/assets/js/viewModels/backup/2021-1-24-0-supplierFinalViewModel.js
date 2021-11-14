define(['knockout', 'jquery', 'moment', 'bootstrap', 'notify', 'modal', 'timedUpdate'], function (ko,$,moment){
	$(".invo_input, .invo_select").on("focusout", function(){
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
		self.property_id=ko.observable(null);
		self.tenantdamage=ko.observable(null);
		self.InvoiceNotes=ko.observable(null);
		self.InvoiceRef=ko.observable(null);
		self.Invoicedate=ko.observable(null);
		self.supplierorderid=ko.observable(null);
		self.rate=ko.observable(null);
		self.price=ko.observable(null);
		self.calloutcharge=ko.observable(null);

		
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
						if (key=='supplierNotesError' || key=='InvoiceNotesError' || key=='serialnumberError')
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
			$.post( '../actions/forms/supplier_final.php', { 'act':'AddSupplierFinal', 'data':o,'FORM_TOKEN' : FORM_TOKEN})
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
		function getdata(){
			var d = $.Deferred();
			$.post( '../actions/forms/supplier_final.php', { 'act':'getsupplierfinaldata', 'FORM_TOKEN' : FORM_TOKEN})
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
			
		getdata()
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
			self.property_id(data.property_adress.building+', '+
				data.property_adress.firstline+', '+data.property_adress.city+', '
				+data.property_adress.country+'   '+data.property_adress.postcode);
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
		//end here supplier final modal
	}	
	var em = document.getElementById('supplierFinalPage');
	if(em) ko.applyBindings(new supplierFinalViewModel(), em);
});