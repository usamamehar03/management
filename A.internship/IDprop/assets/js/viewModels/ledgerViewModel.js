define(['knockout', 'jquery','moment','bootstrap','notify','modal'], function (ko,$,moment){
	function ledgerViewModel() {
		//under worke
		self.sortselected=ko.observable('Alphabetically');
		self.isvisiblAlpabetical=ko.observable(true);
		self.isvisibleProperty=ko.observable(false);
		self.isEnableCredit=ko.observable(true);
		self.isEnableDebit=ko.observable(true);
		self.selectedledger=ko.observable(null);
		self.sortselected.subscribe(function(val){
			if (val=='Alphabetically')
			{

				self.isvisiblAlpabetical(true);
				self.isvisibleProperty(false);
			}
			else
			{
				self.isvisiblAlpabetical(false);
				self.isvisibleProperty(true);
			}		
		})
		//
		self.ledgertable=ko.observableArray([{date:null,assign:'',description:null,
			ledger:selectedledger,Ref:null,Debit:null,Credit:null}]);
		self.addledgertable=function(date=null,assign=null,description=null,
			ledger=null,ref=null,debit=null,credit=null)
		{
			self.ledgertable.push({date:date,assign:assign,description:description,
			ledger:ledger,Ref:ref,Debit:debit,Credit:credit});
		}
		
		function getData(o){
			var d = $.Deferred();
			$.post('../actions/forms/ledger.php',{'act':'getData', 'data':o ,'FORM_TOKEN' : FORM_TOKEN})
			.done(function(data){
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
			.fail(function () {
				d.reject();
			})
			return d;
		}
		self.loaddata=function()
		{
			setTimeout(function(){
				var obj={
					'chartofAccount':self.selectedledger()
				}
				getData(obj)
				.done(function(data){
					self.ledgertable.removeAll();
					for (var key of Object.keys(data))
					{
						debit=Number(data[key].debit).toLocaleString();
						credit=Number(data[key].credit).toLocaleString();
						self.addledgertable(data[key].Date,'',data[key].Description,
							selectedledger,data[key].ref,debit,credit);
					}
				})
				.fail(function(data){
					self.ledgertable.removeAll();
					self.addledgertable();
				})
			},100);
		}
		//end here

		// self.timer = ko.observable(false);
		// self.inited = ko.observable(false);
		// self.filter = ko.observable(null);
		// self.filter.subscribe(function(newVal){
		// 	self.getData();
		// })
		// self.journal = ko.observableArray([]);
		// self.err = ko.observable(false);
		// self.addJournalModal = ko.observable(null);
		// self.assignFilters = ko.observableArray(['journal_id','building_id','property_id','landlord_id']);
		// self.chartOfAccountFilters = ko.observableArray(['Accounts Payable','Accounts Receivable','Accrued Liabilities','Accumulated Depreciation','Additional Paid-In Capital','Advertising and Promotion','Bank Non Cash','Bank Service Charges','Cash','Cleaning','Common Stock','Computer and Internet Expenses','Conveyancing','Cost of Goods Sold','Credit Cards','Deposits Received','Dues and Subscriptions','Fixed Assets','Ground Rent','Insurance Expense','Inventory','Long-Term Loans','Loss on Asset Sale','Notes Payable','Office Rent and Utilities','Office Supplies','Other','Other Assets','Other Service Income','Other Taxes Payable','Payroll Expenses','Payroll Liabilities','Pre-Paid Expenses','Professional Accounting and Legal Fees','Property Management Income','Repairs and Maintenance','Retained Earnings','Short-Term Loans','Sub Contractor Expenses','Telephone','Travel','Utilities','VAT Input','VAT Output']);
		// self.accountName = ko.observable(null);			

		// self.mainMessage = ko.observable(null);
		// self.getData = function(){
		// 	getData()
		// 	.done(function(data){
		// 		var tmp = $.map(data,function(journal){
		// 			return new journal(journal);
		// 		})
		// 		self.journal(tmp);
		// 		self.timeUpdate(true);
		// 		setTimeout(function(){self.timeUpdate(false)},3000);
		// 	});
		// }
		
		// self.getData = function(){
		// 	self.journal([]);
		// 	getData()
		// 	.done(function(data){
		// 		var tmp = [];
		// 		tmp = $.map(data,function(d){
		// 			return new Journal(d);
		// 		})
		// 		self.journal(tmp);
		// 	});
		// 	self.inited(true);
		// }
		
		
		
		// function getData() {
		// 	var d = $.Deferred();
		// 	$.post('../actions/forms/journal.php',{
		// 		'act':'getData',
		// 		'filter':self.filter() == 'All' ? null : self.filter(),
		// 		'FORM_TOKEN' : FORM_TOKEN,
		// 	}).done(function(data) {
		// 		if (data.status == 'ok') {
		// 			d.resolve(data.data?data.data:[]);
		// 		}else{
		// 			d.reject();
		// 		}
		// 	})
		// 	.fail(function () {
		// 		d.reject();
		// 	})
		// 	return d;
		// }
		
	}
	var em = document.getElementById('ledgerPage');
	if(em) ko.applyBindings(new ledgerViewModel(), em);
});