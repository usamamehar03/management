define(['knockout', 'jquery','moment','bootstrap','notify','modal'], function (ko,$,moment){
	function invoiceDisplayViewModel() 
	{
		self.basepath=ko.observable("Invoice.php");
		self.details=ko.observable("invoice details");
		self.pdf_details=ko.observable("invoice pdf");
		// self.isshowsub=ko.observable(false);
		self.invoice_list=ko.observableArray([{invoice_id:null,description:null,
			date:null, url:null, pdf_url:null}]);
		self.add_invoice=function(invoice_id, description, date, url, pdf_url)
		{
			self.invoice_list.push({invoice_id:invoice_id, description:description,
				date:date, url:url, pdf_url:pdf_url});
		}
		function getData(o) 
		{
			var d = $.Deferred()
			$.post('../actions/forms/invoice_display.php',{'act':'getData', 'data':o
				,'FORM_TOKEN':FORM_TOKEN})
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
			return d;
		}
		self.getinvoice=function()
		{
			var obj={
				'state': 'getinvoice'
			}
			getData(obj)
			.done(function(data)
			{
				self.invoice_list.removeAll();
				if (Object.keys(data).length!=0)
				{
					for (var key of Object.keys(data))
					{
						if (key!='userid')
						{
							url=self.basepath()+'?mod='+'view'+'&id='+
								data[key].ID+'&user_id='+data['userid'];
							pdf_url=self.basepath()+'?mod='+'pdf'+'&id='+
								data[key].ID+'&user_id='+data['userid'];
							self.add_invoice(data[key].ID, data[key].Description, 
								data[key].invoiceDate, url, pdf_url);
						}
					}
				}
			})
			.fail(function(data){
				self.invoice_list.removeAll();
			})
		}
		self.getinvoice_pdf=function()
		{
			var obj={
				'state': 'getpdf',
				'id': 	 12
			}
			getData(obj)
			.done(function(data)
			{
				// alert(data);
					//  var modalWidth = $(window).width() - 400;
			 //      var modalHeight = $(window).height() - 400
			 //      var iframeWidth = modalWidth - 20;
			 //      var iframeHeight = modalHeight - 20;
				// $( "#display_dialog").html('<iframe width="' + iframeWidth + 
				// 	'" height="' + iframeHeight + '" src="data:application/pdf;base64,'
				// 	 + data + '"></object>');
				// window.open('/members/megumi/cek_list_wire_rod/get_pdf', '_blank');
				// self.pdf_url(data);
				// pdf.show();
			})
			.fail(function(){
				// self.invoice_list.removeAll();
			})
		}
		self.getinvoice();
	}
	var em = document.getElementById('invoiceDisplayViewPage');
	if(em) ko.applyBindings(new invoiceDisplayViewModel(), em);
});