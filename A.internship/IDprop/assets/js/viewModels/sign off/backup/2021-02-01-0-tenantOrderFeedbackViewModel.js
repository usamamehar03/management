// alert("here");
define(['knockout', 'jquery', 'moment', 'bootstrap', 'notify', 'modal', 'timedUpdate'], function (ko,$,moment){
	$(".invo_input, .invo_select").on("focusout", function(){
		if(($(this).val()!="" && $(this).val()!="type"))
		{
			$(this).next(".error").html("");
			$(this).attr('style', 'background-color: white !important');
		}
	});
	function tenantOrderFeedbackViewModel() {
		//tennant feedback
		self.tenantordersid=ko.observable(null);
		self.property_id=ko.observable(null);
		self.ratingPropertyManager = ko.observable(null);
		self.ratingSupplier = ko.observable(null);
		self.tenantFeedback = ko.observable(null);
		
		self.activeTab = ko.observable('All');
		self.toggleTab = function(target){
			self.activeTab(target);
		}
		self.mainMessage = ko.observable(null);
		self.addstaff = function(){
		    self.stafflist.push({id:staffid, name:staffname});
	    }		
	    //add tenant feeback
		self.addfeedback=function()
		{
			var obj = {
				//'addSupplierMaterials':self.SupplierMaterials(),				
				//'highLight':true,
				//'approved':IS_SENIOR ? true : false,//Senior OR Admin can approve	
				'tenantOrdersID':self.tenantordersid(),								
				'ratingPropertyManager':self.ratingPropertyManager(),
				'ratingSupplier':self.ratingSupplier(),
				'tenantFeedback':self.tenantFeedback()			
			}
			addcall(obj)
			.done(function(data){
				self.loadpropertid();
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
			$.post( '../actions/forms/tenant_order_feedback.php', { 'act':'TenantFeedback', 'data':o,'FORM_TOKEN' : FORM_TOKEN})
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
			$.post( '../actions/forms/tenant_order_feedback.php', { 'act':'GetPropertyID', 'FORM_TOKEN' : FORM_TOKEN})
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
		self.loadpropertid=function()
		{
			getdata()
			.done(function(data){
				self.property_id(data.propert_adress);
				self.tenantordersid(data.order.tenantOrdersID);
			})
			.fail(function(data){
				self.property_id(null);
				self.tenantordersid(null);
				self.ratingPropertyManager(null);
				self.ratingSupplier(null);
				self.tenantFeedback(null);
			})
		}
		self.loadpropertid();
	}	
	var em = document.getElementById('tenantOrderFeedbackPage');
	if(em) ko.applyBindings(new tenantOrderFeedbackViewModel(), em);
});