define(['knockout', 'jquery','moment','bootstrap','notify','modal'], function (ko,$,moment){
	$(document).on("input",".inputs", function(){
		if($(this).val()!='')
		{
			$(this).attr('style', 'background-color: white !important');
			$(this).siblings('.error').html("");
		}
	}); 
	function invoiceViewModel() {
		self.timer = ko.observable(false);
		self.inited = ko.observable(false);
		self.filter = ko.observable(null);
		self.filter.subscribe(function(newVal){
			self.getData();
		})
		self.invoice = ko.observableArray([]);
		self.err = ko.observable(false);
		self.addInvoiceModal = ko.observable(null);	
		self.client = ko.observable(null);
		self.bAddress = ko.observable(null);
		self.cAddress = ko.observable(null);
		// self.service = ko.observable(null);
		// self.description = ko.observable(null);
		// self.amount = ko.observable(null);
		//general variables
		self.invoiceNumber = ko.observable(null);
		self.terms = ko.observable(null);
		self.invoiceDate = ko.observable(null);
		self.dueDate = ko.observable(null);
		self.notes = ko.observable(null);
		//amount
		self.subtotal_amount=ko.observable(0);
		self.tax_rate= ko.observable(0);
		self.tax_amount=ko.observable(0);
		self.total_amount=ko.observable(0);
		self.paid_amount=ko.observable(0);
		self.due_balance=ko.observable(0);
		//multiple entres
		self.isshowsub=ko.observable(false);
		self.subinvoice_list=ko.observableArray([{number:1,
			service:null,description:null,amount:null}]);
		self.add_subinvoice=function(number,service,description,amount)
		{
			self.subinvoice_list.push({number:number,
				service:service,description:description, amount:amount});
		}
		self.remove_subinvoice = function (data) {
			var Index = self.subinvoice_list.indexOf(data);
			subinvoice_list.splice(Index, 1);
	    };
		//end jhere
		self.mainMessage = ko.observable(null);
		// self.getData = function(){
		// 	getData()
		// 	.done(function(data){
		// 		var tmp = $.map(data,function(invoice){
		// 			return new invoice(invoice);
		// 		})
		// 		self.invoice(tmp);
		// 		self.timeUpdate(true);
		// 		setTimeout(function(){self.timeUpdate(false)},3000);
		// 	});
		// }

		
		
		// self.getData = function(){
		// 	self.invoice([]);
		// 	getData()
		// 	.done(function(data){
		// 		var tmp = [];
		// 		tmp = $.map(data,function(d){
		// 			return new Invoice(d);
		// 		})
		// 		self.invoice(tmp);
		// 	});
		// 	self.inited(true);
		// }
		
		// function Invoice(data){
		// 	var invoice = this;
		// 	invoice.ID = data.ID;
		// 	invoice.client = ko.observable(data.client ? data.client : null);
		// 	invoice.client.subscribe(function(newVal){
		// 		editInvoice({'ID':invoice.ID,'client':newVal});
		// 	})	
		// 	invoice.invoiceNumber = ko.observable(data.invoiceNumber ? data.invoiceNumber : null);
		// 	invoice.invoiceNumber.subscribe(function(newVal){
		// 		editInvoice({'ID':invoice.ID,'invoiceNumber':newVal});	
		// 	})	
		// 	invoice.bAddress = ko.observable(data.bAddress ? data.bAddress : null);
		// 	invoice.bAddress.subscribe(function(newVal){
		// 		editInvoice({'ID':invoice.ID,'bAddress':newVal});
		// 	})
		// 	invoice.cAddress = ko.observable(data.cAddress ? data.cAddress : null);
		// 	invoice.cAddress.subscribe(function(newVal){
		// 		editInvoice({'ID':invoice.ID,'cAddress':newVal});
		// 	})
		// 	invoice.template = ko.observable(data.template ? data.template : null);
		// 	invoice.template.subscribe(function(newVal){
		// 		editInvoice({'ID':invoice.ID,'template':newVal});
		// 	})
		// 	invoice.terms = ko.observable(data.terms ? data.terms : null);
		// 	invoice.terms.subscribe(function(newVal){
		// 		editInvoice({'ID':invoice.ID,'terms':newVal});
		// 	})
		// 	invoice.invoiceDate = ko.observable(data.invoiceDate ? data.invoiceDate : null);
		// 	invoice.invoiceDate.subscribe(function(newVal){
		// 		editInvoice({'ID':invoice.ID,'invoiceDate':newVal});
		// 	})
		// 	invoice.dueDate = ko.observable(data.dueDate ? data.dueDate : null);
		// 	invoice.dueDate.subscribe(function(newVal){
		// 		editInvoice({'ID':invoice.ID,'dueDate':newVal});
		// 	})
		// 	invoice.# = ko.observable(data.# ? data.# : null);
		// 	invoice.#.subscribe(function(newVal){
		// 		editInvoice({'ID':invoice.ID,'#':newVal});
		// 	})
		// 	invoice.service = ko.observable(data.service ? data.service : null);			
		// 	invoice.service.subscribe(function(newVal){
		// 		editInvoice({'ID':invoice.ID,'service':newVal});
		// 	})
		// 	invoice.description = ko.observable(data.description ? data.description : null);
		// 	invoice.description.subscribe(function(newVal){
		// 		editInvoice({'ID':invoice.ID,'description':newVal});
		// 	})
		// 	invoice.amount = ko.observable(data.amount ? data.amount : null);
		// 	invoice.amount.subscribe(function(newVal){
		// 		editInvoice({'ID':invoice.ID,'amount':newVal});
		// 	})
		// 	invoice.notes = ko.observable(data.notes ? data.notes : null);
		// 	invoice.notes.subscribe(function(newVal){
		// 		editInvoice({'ID':invoice.ID,'notes':newVal});		
		// 	})
		// 	invoice.deleteMe = function(){
		// 		deleteInvoice(invoice.ID)
		// 		.done(function(data){
		// 			self.invoice.remove(invoice);
		// 		});
		// 	}
		// }
		function getData(o) {
			var d = $.Deferred()
			$.post('../actions/forms/invoice.php',{'act':'getData','data':o,
				'FORM_TOKEN' : FORM_TOKEN})
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
				'state': 		'getinvoice',
				'invoice_id': 	invoice_id? invoice_id: null,
				'user_id': 		user_id? user_id: null,
			}
			getData(obj)
			.done(function(data)
			{
				index=data['invoicedata'][0];
				self.terms(index['Terms']);
				self.invoiceNumber(index['InvoiceNumber']);
				self.invoiceDate(index['invoiceDate']);
				self.dueDate(index['DueDate']);
				self.notes(index['Notes']);
				//client address
				self.load_address(index,"address",'#clientaddress',
					index['name']);
				//biller address
				self.load_address(index,"billeraddress",'#billeraddress',
					index["billeraddress"]['name']);
				self.calculations(index);
				//sub list show
				self.subinvoice_list.removeAll();
				if (Object.keys(index['subdata']).length!=0)
				{
					self.isshowsub(true);
					for (var key of Object.keys(index['subdata']))
					{
						sub_index=index['subdata'][key];
						self.add_subinvoice(sub_index.Ref,sub_index.PartName,
							sub_index.description, sub_index.Amount);
					}
				}
				if(state=='pdf')
				{
					// $("#test").load('Invoice.php', function() {
		   //              // Code here runs when the load() completes:
		   //              alert('ok');
		   //          });
					// setTimeout(function(){ alert(html);}, 3000);
					// setTimeout(function(){ alert(html);}, 3000);
					//load html file data
					// alert(html);
				}
			})
			.fail(function(){
				self.terms(null);
				self.invoiceNumber(null);
				self.invoiceDate(null);
				self.dueDate(null);
				self.notes(null);
				$("#clientaddress  li").html('');
				$("#billeraddress  li").html('');
				self.subtotal_amount(0);
				self.tax_rate(0);
				self.tax_amount(0);
				self.total_amount(0);
				self.paid_amount(0);
				self.due_balance(0);
				//sub list show
				self.subinvoice_list.removeAll();
				self.isshowsub(false);
			})
		}
		self.load_address=function(data,index,id_name,name)
		{
			var building=data[index]['building']? data[index]['building']+', ' :'';
			$(id_name+" li:nth-child(1)").html(name);
			$(id_name+" li:nth-child(2)").html(building+data[index]['firstline']);
			$(id_name+" li:nth-child(3)").html(data[index]['City']+', '+data[index]['county']);
			$(id_name+" li:nth-child(4)").html(data[index]['Country']+', '+data[index]['postcode']);
		}
		self.calculations=function(index)
		{
			self.subtotal_amount(parseFloat(index['Amount']));
			taxrate=index['TaxRate']? index['TaxRate']:0;
			self.tax_rate(parseFloat(taxrate));
			taxtotal=(self.tax_rate()/100)*self.subtotal_amount();
			self.tax_amount(taxtotal);
			total=self.tax_amount()+self.subtotal_amount();
			self.total_amount(total);
			self.paid_amount(parseFloat(index['paidamount']));
			self.due_balance(self.total_amount()-self.paid_amount());
		}
		self.getinvoice();
		//end here
		function editInvoice(o,res){
			$.post('../actions/forms/invoice.php', { 'act':'editInvoice', 'changes':o,'FORM_TOKEN' : FORM_TOKEN})
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
		function deleteInvoice(order_id) {
			var d = $.Deferred()
			$.post('../actions/forms/invoice.php',{
				'act':'deleteInvoice',
				'order_id':order_id,
				'FORM_TOKEN' : FORM_TOKEN
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
		//self.getData();		
	}
	var em = document.getElementById('invoicePage');
	if(em) ko.applyBindings(new invoiceViewModel(), em);
});