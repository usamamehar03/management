define(['knockout', 'jquery','moment','bootstrap','notify','modal'], function (ko,$,moment){
	$(document).on("input", ".input", function(){
		if(($(this).val()!="" && $(this).val()!="type") || 
			self.selectedclient()!=null || self.selectedclient!=null)
		{
			$(this).next(".error").html("");
			$(this).attr('style', 'background-color: white !important');
		} 
	});
	function invoiceTemplateViewModel() {
		self.timer = ko.observable(false);
		self.inited = ko.observable(false);
		self.filter = ko.observable(null);
		self.filter.subscribe(function(newVal){
			self.getData();
		})
		self.adding = ko.observable(false);
		self.invoiceTemplate = ko.observableArray([]);
		self.err = ko.observable(false);
		self.addInvoiceTemplateModal = ko.observable(null);		
		self.templateName = ko.observable(null);
		self.taxName = ko.observable(null);
		self.taxRate = ko.observable(null);
		self.terms = ko.observable(null);
		self.logo = ko.observable(null);
		//client type
		self.selectedclient=ko.observable().extend({deferred: true});
		self.clientlist = ko.observableArray(null);
		if (user_type=='SeniorManagement')
		{
			self.clientlist.push({name:'Property Owner', type:'PropertyOwner'},
				{name:'Storage Owner', type:'StorageOwner'},
				{name:'Investor', type:'Investor'});
		}
		else if (user_type=='Supplier_SM')
		{
			self.clientlist.push({name:'Property Manager',type:'PropertyManager'});
		}
		//name
		self.selectedname=ko.observable();
		self.namelist = ko.observableArray([{name:null, owner_id:null}]);
		self.add_name=function(name,owner_id)
		{
			self.namelist.push({name:name, owner_id:owner_id});
		}	
		//	
		
		self.add = function(){
			self.adding(true);
			
				var obj = {
					
					'templateName': self.templateName(),
					//'highLight':true,
					//'approved':IS_SENIOR ? true : false,
					'taxName': 		self.taxName(),
					'taxRate': 		self.taxRate(),
					'terms': 		self.terms(),
					'client_type': 	self.selectedclient()==null? null:
						self.selectedclient().type,
					'owner_id':   	self.selectedname()==null? null:
						self.selectedname().owner_id,
					'logo': 		self.logo()					
				
			}
	 	 	addInvoiceTemplate(obj)
			.done(function(data){
				//obj.ID = data;
				//self.invoiceTemplate.push(new InvoiceTemplate(obj));
				//alert('invoiceTemplate');
				// obj.ID = data;
				// self.supplierFees.push(new SupplierFees(obj));
				self.templateName(null);
				self.taxRate(null);
				self.terms(null);
				self.logo(null);
				self.selectedname(null);				
				self.adding(false);

			})
			.fail(function(data){
				if(data.status == 'err' )
				{
					for (var key of Object.keys(data.data))
					{
						if (key=='templateNameError')
						{
							$("#"+key).html('only text');
						}
						else if (key=='taxRateError')
						{
							$("#"+key).html('tax should be between 0 to 30');
						}
						else
						{
							$("#"+key).html('Empty');
						}

						// $("#"+key).siblings('.input').css({"background-color": "#f8d7da"});
						$("#"+key).siblings('.input').attr('style', 
							'background-color: #f8d7da !important');
						$("#"+key).siblings('.input').children().css({"background-color": "white"});		
					}
				}
			})
			.always(function(){
				self.adding(false);
			})			
		}
		function addInvoiceTemplate(o){	
			var d = $.Deferred();
			var jsonData = ko.toJSON(o);
				var obj = JSON.parse(jsonData);
				var logo =obj.logo;
				String.prototype.filename=function(extension){
	    			var s= this.replace(/\\/g, '/');
				    s= s.substring(s.lastIndexOf('/')+ 1);
				    return extension? s.replace(/[?#].+$/, ''): s.split('.')[1];
				}
			if((logo!=null) && (logo.filename() == 'jpeg' || logo.filename() == 'png' || logo.filename() == 'PNG' || logo.filename() == 'jpg'))
			{
				$.post( '../actions/forms/invoice_template.php', { 'act':'addInvoiceTemplate', 'data':o,'FORM_TOKEN' : FORM_TOKEN})
				.done(function( data ) {
					// alert(data);
					// data= JSON.parse(data);
					if(data)
					{
						if( data.status == 'ok' )
						{
							alert('Data Inserted :)');
							d.resolve(data.data?data.data:[]);
						}
						else
						{
							d.reject(data.data?data:[]);
						}
					}
				})
			}
			else
			{
				$("#logoError").html('Only .Jpeg and .png are allowed');
				$("#logoError").siblings('.input').css({"background-color": "#f8d7da"});
				$("#logoError").siblings('.input').attr('style', 'background-color: #f8d7da !important');
			}
			return d;
		}
		function getData(obj) {
			var d = $.Deferred()
			$.post('../actions/forms/invoice_template.php',{'act':'getData',
				'data':obj, 'FORM_TOKEN' : FORM_TOKEN})
			.done(function(data) {
				// alert(data);
				// data=JSON.parse(data);
				if (data.status == 'ok') 
				{
					d.resolve(data.data?data.data:[]);
				}
				else
				{
					d.reject(data.data?data:[]);
				}
			})
			.fail(function(){
				d.reject();
			})
			return d;
		}
		self.getnamelist=function(type)
		{
			var obj={
				'type': type
			}
			getData(obj)
			.done(function(data)
			{
				self.namelist.removeAll();
				self.selectedname(null);	
				for (var key of Object.keys(data))
				{
					self.add_name(data[key].companyName, data[key].owner_id); 
				} 
			})
			.fail(function(data){
				self.namelist.removeAll();
				self.selectedname(null);
			})
		}
		selectedclient.subscribe(function(val)
		{
			if (val!=null)
			{
				self.getnamelist(val.type);
			}
			else
			{
				self.namelist.removeAll();
				self.selectedname(null);
			}
		})
		//end here










		self.addInvoiceTemplate = function(){
			self.addInvoiceTemplateModal(true);
			$('.side-bar').toggle('fast');

		}
		
		self.mainMessage = ko.observable(null);
		self.getData = function(){
			getData()
			.done(function(data){
				var tmp = $.map(data,function(invoiceTemplate){
					return new InvoiceTemplate(invoiceTemplate);
				})
				self.invoiceTemplate(tmp);
				self.timeUpdate(true);
				setTimeout(function(){self.timeUpdate(false)},3000);
			});
		}
				
		/*
		self.submitInvoiceTemplate = function(){
			if(!self.err()){
				var o = {'templateName':self.newTemplateName(),'taxName':self.newTaxName(),'taxRate':self.newTaxRate(),'terms':self.newTerms(),'logo':self.Logo()()};
				addInvoiceTemplate(o)
				.done(function(data){
					self.getData();
					self.newTemplateName(null);
					self.newTaxName(null);
					self.newTaxRate(null);
					self.newTerms(null);
					self.newLogo(null);					
				});
			}
		}
		self.getData = function(){
			self.invoiceTemplate([]);
			getData()
			.done(function(data){
				var tmp = [];
				tmp = $.map(data,function(d){
					return new InvoiceTemplate(d);
				})
				self.invoiceTemplate(tmp);
			});
			self.inited(true);
		}
		/*
		function InvoiceTemplate(data){
			var invoiceTemplate = this;
			invoiceTemplate.ID = data.ID;
			invoiceTemplate.templateName = ko.observable(data.templateName ? data.templateName : null);
			invoiceTemplate.templateName.subscribe(function(newVal){
				editInvoiceTemplate({'ID':invoiceTemplate.ID,'templateName':newVal});
			})	
			invoiceTemplate.taxName = ko.observable(data.taxName ? data.taxName : null);
			invoiceTemplate.taxName.subscribe(function(newVal){
				editInvoiceTemplate({'ID':invoiceTemplate.ID,'taxName':newVal});	
			})	
			invoiceTemplate.taxRate = ko.observable(data.taxRate ? data.taxRate : null);
			invoiceTemplate.taxRate.subscribe(function(newVal){
				editInvoiceTemplate({'ID':invoiceTemplate.ID,'taxRate':newVal});
			})
			invoiceTemplate.terms = ko.observable(data.terms ? data.terms : null);			
			invoiceTemplate.terms.subscribe(function(newVal){
				editInvoiceTemplate({'ID':invoiceTemplate.ID,'terms':newVal});
			})
			invoiceTemplate.logo = ko.observable(data.logo ? data.logo : null);
			invoiceTemplate.logo.subscribe(function(newVal){
				editInvoiceTemplate({'ID':invoiceTemplate.ID,'logo':newVal});
			})			
			invoiceTemplate.deleteMe = function(){
				deleteInvoiceTemplate(invoiceTemplate.ID)
				.done(function(data){
					self.invoiceTemplate.remove(invoiceTemplate);
				});
			}
		}
		
		function getData() {
			var d = $.Deferred()
			$.post('../actions/forms/invoice_template.php',{
				'act':'getData',
				'filter':self.filter() == 'All' ? null : self.filter(),
				//'FORM_TOKEN' : FORM_TOKEN,
			}).done(function(data) {
				alert(data);
				if (data.status == 'ok') {
					d.resolve(data.data?data.data:[]);
				}else{
					d.reject();
				}
			})
			.fail(function () {
				d.reject();
			})
			return d;
		}
		*/

		/*function editInvoiceTemplate(o,res){
			$.post( 'actions/forms/invoice_template.php', { 'act':'editInvoiceTemplate', 'changes':o,'FORM_TOKEN' : FORM_TOKEN,})
			.done(function( data ) {
				if( data ){
					if( data.status == 'ok' ){

					}else{

					}
				}
			})
			.fail(function() {

			})
		}
		function deleteInvoiceTemplate(order_id) {
			var d = $.Deferred()
			$.post('actions/forms/invoice_template.php',{
				'act':'deleteInvoiceTemplate',
				'order_id':order_id,
				'FORM_TOKEN' : FORM_TOKEN,
			}).done(function(data) {
				if (data.status == 'ok') {
					d.resolve(data.data?data.data:[]);
				}else{
					d.reject();
				}
			})
			.fail(function () {
				d.reject();
			})
			return d;
		}
		self.getData();	
		*/	
	}
	ko.options.deferUpdates = true;
	var em = document.getElementById('invoiceTemplatePage');
	if(em) ko.applyBindings(new invoiceTemplateViewModel(), em);
});