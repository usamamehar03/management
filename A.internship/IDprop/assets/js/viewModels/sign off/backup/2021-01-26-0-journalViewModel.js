define(['knockout', 'jquery','moment','bootstrap','notify','modal'], function (ko,$,moment){
	$(".input, .intem_select").on("focusout", function(){
		if(($(this).val()!="" && $(this).val()!="type"))
		{
			if($(this).attr("id")=='Credit' || $(this).attr("id")=='Debit' || $(this).attr("id")=='Creditp' || $(this).attr("id")=='Debitp')
			{
				$('#debitError, #creditError, #debitpError, #creditpError').html("");
				$('#Debit, #Credit,#Debitp, #Creditp').attr('style', 'background-color: white !important');
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
		self.date=ko.observable(null);
		self.assign=ko.observable(null);
		self.description=ko.observable(null);
		self.ledger=ko.observable(null);
		self.Ref=ko.observable(null);
		self.Debit= ko.observable(null);
		self.Credit= ko.observable(null);
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
			//remove errors when tab change
			$('.input, .intem_select').each(function(index, obj){
				$(obj).next('.error').html("");
				$(obj).attr('style', 'background-color: white !important');
			});
		})
		self.Debit.subscribe(function(val){
			self.disableinput(val,self.isEnableCredit);
		})
		self.Credit.subscribe(function(val){
			self.disableinput(val,self.isEnableDebit);
		})
		self.disableinput=function(val,name)
		{
			if (val!='')
			{	name(false); }
			else
			{	name(true); }
		}
		//end here

		self.timer = ko.observable(false);
		self.inited = ko.observable(false);
		self.filter = ko.observable(null);
		self.filter.subscribe(function(newVal){
			self.getData();
		})
		self.journal = ko.observableArray([]);
		self.err = ko.observable(false);
		self.addJournalModal = ko.observable(null);
		self.assignFilters = ko.observableArray(['journal_id','building_id','property_id','landlord_id']);
		self.chartOfAccountFilters = ko.observableArray(['Accounts Payable','Accounts Receivable','Accrued Liabilities','Accumulated Depreciation','Additional Paid-In Capital','Advertising and Promotion','Bank Non Cash','Bank Service Charges','Cash','Cleaning','Common Stock','Computer and Internet Expenses','Conveyancing','Cost of Goods Sold','Credit Cards','Deposits Received','Dues and Subscriptions','Fixed Assets','Ground Rent','Insurance Expense','Inventory','Long-Term Loans','Loss on Asset Sale','Notes Payable','Office Rent and Utilities','Office Supplies','Other','Other Assets','Other Service Income','Other Taxes Payable','Payroll Expenses','Payroll Liabilities','Pre-Paid Expenses','Professional Accounting and Legal Fees','Property Management Income','Repairs and Maintenance','Retained Earnings','Short-Term Loans','Sub Contractor Expenses','Telephone','Travel','Utilities','VAT Input','VAT Output']);
		self.accountName = ko.observable(null);			

		self.mainMessage = ko.observable(null);
		self.getData = function(){
			getData()
			.done(function(data){
				var tmp = $.map(data,function(journal){
					return new journal(journal);
				})
				self.journal(tmp);
				self.timeUpdate(true);
				setTimeout(function(){self.timeUpdate(false)},3000);
			});
		}
		self.add = function(){
			//self.adding(true)
			var obj = {
				//'addJournal':self.journal(),
				// 'journal':self.journal(),
				//'highLight':true,
				//'approved':IS_SENIOR ? true : false,
				// 'newAssignFilters':self.newAssignFilters(),
				// 'newChartOfAccountFilters':self.newChartOfAccountFilters(),
				// 'newDate':self.newDate(),	
				// 'newDescription':self.newDescription(),	
				// 'newRef':self.newRef(),
				// 'newDebit':self.newDebit(),
				// 'newCredit':self.newCredit()					
				// 'newAccountName':self.newAccountName()
				'date':self.date(),
				'assign':self.assign(),
				'description':self.description(),
				'ledger':self.ledger(),
				'ref':self.Ref(),
				'debit':self.Debit(),
				'credit':self.Credit()
			}
			addJournal(obj)
			.done(function(data){

			})
			.fail(function(data){
				if(data.status='err')
				{
					for (var key of Object.keys(data.data))
					{
						if (self.isvisiblAlpabetical()==true)
						{
							data.data[key].state?self.displayerror(key,data.data[key].state):self.displayerror(key);
						}
						else
						{	
							tempkey=key.replace('Error','pError');
							data.data[key].state?self.displayerror(tempkey,data.data[key].state):self.displayerror(tempkey);   
						}
					}
				}
			})
		}
		self.displayerror=function(key,isdcnull=null)
		{
			if(key=='ledgerError')
			{
				$("#"+key).html('Empty');
			}
			else if (key=='descriptionError')
			{
				$("#"+key).html('Text only');
			}
			else if (key=='refError')
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
		self.getData = function(){
			self.journal([]);
			getData()
			.done(function(data){
				var tmp = [];
				tmp = $.map(data,function(d){
					return new Journal(d);
				})
				self.journal(tmp);
			});
			self.inited(true);
		}
		
		
		
		function getData() {
			var d = $.Deferred()
			$.post('../actions/forms/journal.php',{
				'act':'getData',
				'filter':self.filter() == 'All' ? null : self.filter(),
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
		function addJournal(o){
			var d = $.Deferred();
			$.post( '../actions/forms/journal.php', { 'act':'addJournal', 'data':o,'FORM_TOKEN' : FORM_TOKEN,})
			.done(function( data ){
				alert(data);
				data=JSON.parse(data);
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
		
		
		// self.getData();
		// alert(self.journal());		
	}
	var em = document.getElementById('journalPage');
	if(em) ko.applyBindings(new journalViewModel(), em);
});