define(['knockout', 'jquery','moment','bootstrap','notify','modal'], function (ko,$,moment){
	$(document).on("input",".input, .intem_select", function(){
		if(($(this).val()!="" && $(this).val()!="type"))
		{
			if($(this).attr("class")=='input filter' || $(this).attr("id")=='Creditp' || $(this).attr("id")=='Debitp')
			{
				var idindex=$(this).attr("id");
				if (self.isvisiblAlpabetical()==true)
				{
					idindex=idindex.substring(idindex.length - 1, idindex.length);
				}
				else
				{
					idindex=idindex.substring(idindex.length - 2, idindex.length);
				}

				$('#debit'+idindex).siblings('.filtererror').html("");
				$('#credit'+idindex).siblings('.filtererror').html("");

				$('#debit'+idindex).attr('style', 'background-color: white !important');
				$('#credit'+idindex).attr('style', 'background-color: white !important');
			}
			else
			{
				$(this).next(".error").html("");
				$(this).attr('style', 'background-color: white !important');
			}
		}
	});

	
	function journalViewModel() {
		//under worke
		self.sortselected=ko.observable('Alphabetically');
		self.isvisiblAlpabetical=ko.observable(true);
		self.isvisibleProperty=ko.observable(false);
		self.isEnableCredit=ko.observable(true);
		self.isEnableDebit=ko.observable(true);
		
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
			//refresh list when change tab
			self.journal.removeAll();
			self.addjournal();
			
		})
		self.journal=ko.observableArray([{date:null, assign:'', description:null,
			ledger:'', Ref:null, Debit:null, Credit:null}]);
		self.addjournal=function()
		{
			self.journal.push({date:null, assign:'', description:null,
			ledger:'', Ref:null, Debit:null, Credit:null});
		}

		self.remove = function (data) {
			var Index = self.journal.indexOf(data);
			journal.splice(Index, 1);
	        // self.journal.remove(journal);
	    };
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
		self.add = function(){
			//self.adding(true)
			var obj = {
				//'highLight':true,
				//'approved':IS_SENIOR ? true : false,
				'journal':self.journal()
			}
			addJournal(obj)
			.done(function(data){
				self.journal.removeAll();
				self.addjournal();
			})
			.fail(function(data){
				if(data.status='err')
				{
					for (var key of Object.keys(data.data))
					{
						data.data[key].state?self.displayerror(key,data.data[key].state):self.displayerror(key);
					}
				}
			})
		}
		self.displayerror=function(key,isdcnull=null)
		{
			var tempkey=key.substring(0,key.length - 6);
			if (self.isvisiblAlpabetical()!=true)
			{
				key=key.replace('Error','pError');
			}
			//display error
			if(tempkey=='ledger')
			{
				$("#"+key).html('Empty');
			}
			else if (tempkey=='description')
			{
				$("#"+key).html('Text only');
			}
			else if (tempkey=='Ref')
			{
				$("#"+key).html('Number only');
			}
			else
			{
				if (isdcnull)
				{
					$("#"+key).html('Enter debit or credit');
				}
				else
				{
					$("#"+key).html('Number only');
				}
				
			}
			$("#"+key).siblings('.input').css({"background-color": "#f8d7da"});
			$("#"+key).siblings('.intem_select').attr('style', 
				'background-color: #f8d7da !important');
		 	$("#"+key).siblings('.intem_select').children().css({
		 		"background-color": "white"});
		}
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
		// 	var d = $.Deferred()
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
		function addJournal(o){
			var d = $.Deferred();
			$.post( '../actions/forms/journal.php', { 'act':'addJournal', 'data':o,'FORM_TOKEN' : FORM_TOKEN,})
			.done(function( data ){
				// alert(data);
				// data=JSON.parse(data);
				if(data)
				{
					if(data.status == 'ok' )
					{
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
		//
		self.debit_credit_handler=function()
		{
			$(document).on('input','.filter', function(){
				var id=$(this).attr("id");
				if (self.isvisiblAlpabetical()==true)
				{
					id=id.substring(0,id.length - 1);
				}
				else
				{
					id=id.substring(0,id.length - 2);
				}
				//
				if (id=='debit')
				{
					var path=$(this).parent().siblings('.creditparent').children('.filter');
					self.enable_disable($(this).val(),path);
				}
				else
				{
					var path=$(this).parent().siblings('.debitparent').children('.filter');
					self.enable_disable($(this).val(),path);
				}
			});
		}
		self.enable_disable=function(value,path)
		{
			if (value!='')
			{path.attr('disabled','disabled');}
			else
			{path.removeAttr('disabled');}
		}
		self.debit_credit_handler();	
	}
	var em = document.getElementById('journalPage');
	if(em) ko.applyBindings(new journalViewModel(), em);
});