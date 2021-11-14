function getvalue()
	{
		$("button").click(function(){
			var mtype=$('#maintenanceType').val();
			var callout=$('#callOutCharge').val();
			var biling=$('#billingIncrement').val();
			var hourly=$('#hourlyRate').val();
			var overtime=$('#overtimeRate').val();
			var weeekend=$('#weekendRate').val();
			var fixed=$('#fixedRates').val();

			var type1=$('#itemType1').val();
			var min1=$('#itemType1Min').val();
			var max1=$('#itemType1Max').val();
			var type2=$('#itemType2').val();
			var min2=$('#itemType2Min').val();
			var max2=$('#itemType2Max').val();
			var typ3=$('#itemType3').val();
			var min3=$('#itemType3Min').val();
			var max3=$('#itemType3Max').val();
			var person = {maintenanceType:mtype, callOutCharge:callout, billingIncrement:biling,hourlyRate:hourly,overtimeRate:overtime,weekendRate:weeekend,fixedRates:fixed,itemType1:type1,itemType1Min:min1,itemType1Max:max1,itemType2:type2,itemType2Min:min2,itemType2Max:max2};
	  		addSupplierFees(person);
		});
	}
	function addSupplierFees(o)
	{
			var d = $.Deferred();
			// $.post( '../actions/forms/supplier_fees.php', { 'act':'addSupplierFees', 'data':o,'FORM_TOKEN':FORM_TOKEN})
			$.post( '../actions/forms/supplier_fees.php', { 'act':'addSupplierFees', 'data':o})
			.done(function( resp ) {
				resp=JSON.parse(resp);
				// alert(resp.data.callOutChargeError);
				if( resp ){
					if( resp.status == 'ok' ){
						// d.resolve(data.data?data.data:[]);
						alert("new record created successfully");
					}
					else if(resp.status == 'fail' )
					{
						alert("sorry db insert failed");
						//d.reject();
					}
					else
					{
						var index="_alert";
						for (var key of Object.keys(resp.data)) 
						{
							if (key=="itemType1Error" || key=="itemTypeError")
							{
								$("#"+key+index).html("it can be only letters");
							}
							else if(key=="maintenanceTypeError" || key=="billingIncrementError" || key=="fixedRatesError")
							{
								$("#"+key+index).html("it can't be empty");
							}
							else
							{
								$("#"+key+index).html("it can't be empty or letter");
							}
							$("#"+key+index).siblings('.invo_input').css({"background-color": "#f8d7da"});
							$("#"+key+index).siblings('.invo_select').attr('style', 'background-color: #f8d7da !important');
							$("#"+key+index).siblings('.invo_select').children().css({"background-color": "white"});
						}
					}
				}
			})
			.fail(function() {
			    alert( "ajax call failed!" );
			  });
			// return d;
	}