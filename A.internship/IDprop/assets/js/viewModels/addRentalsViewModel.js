var propertyData = null;


define(['knockout', 'jquery', 'moment', 'bootstrap', 'notify', 'modal', 'timedUpdate'], function (ko, $, moment) {
	function addRentalsViewModel() {
		self.timeUpdate = ko.observable(false);
		self.inited = ko.observable(false);
		self.adding = ko.observable(false);
		self.loading = ko.observable(true);
		self.endClients = ko.observableArray([]);
		self.options1 =   ko.observableArray([]);
		self.nameErr = ko.observable(null);
		self.newAddress = ko.observable(null);
		self.newCity = ko.observable(null);		
		self.newCurrency = ko.observable(null);
		self.newNationality = ko.observable(null);
		self.newPostCode = ko.observable(null);
		self.newBedrooms = ko.observable(null);
		self.newAskingPrice = ko.observable(null);
		self.newCounty = ko.observable(null);
		self.addEndClientModal = ko.observable(null);
		self.timer = ko.observable(false);
		self.selectedType = ko.observable(null);
		self.ManagementCompaniestxt =  ko.observable(''),
		self.options =  [];

		self.ManagementCompaniestxt1 =  ko.observable(''),


		self.ManagementCompanies1 =  ko.observable('');
		self.ManagementCompanies =  ko.observable('');

		self.ManagementCompanies.subscribe(function(newValue) {
  
		self.options1.removeAll();


		$.each(propertyData, function (i, item) {

		if(item.propertyType==newValue){
		if(item.propertyType=="Bungalow"){

		self.options1.push({ text: item.bungalowType, value: item.bungalowType });


		}

		if(item.propertyType=="Apt"){

		self.options1.push({ text: item.aptType, value: item.aptType });



		}

		if(item.propertyType=="House"){
		self.options1.push({ text: item.houseType, value: item.houseType });



		}


		}

		});


		});

		self.closeEndClientModal = function () {
			$('.side-bar').toggle('fast');
		}
		self.activeTab = ko.observable('All');
		self.toggleTab = function (target) {
			self.activeTab(target);
		}
		self.mainMessage = ko.observable(null);
		

		self.getRentals = function () {
			self.loading(true);
			getRentals()
				.done(function (data) {
					var tmp = $.map(data, function (endClient) {
						return new EndClient(endClient);
					})
					self.endClients(tmp);
					setTimeout(function () { self.timeUpdate(true) }, 3000);
				})
				.always(function (data) {
					self.loading(false);
				})
		}


		self.addRentOffer = function () {
			self.addRentOffer(true);
			$('.side-bar').toggle('fast');
		}
		self.nations = ko.observableArray([]);
		self.ntss = ko.observableArray([]);
		self.availableNationalities = ko.observableArray([]);
		self.getDropdowns = function () {
			getDropdowns()
				.done(function (data) {
					var tmp = $.map(data.nationalities, function (nation) {
						return new Nation(nation);
					})
					var sorted = tmp.sort(function (a, b) {
						var keyA = a.country(),
							keyB = b.country();

						var a = keyA.toLowerCase();
						var b = keyB.toLowerCase();
						return a.localeCompare(b);
					});
					self.nations(sorted);
					self.nations.unshift(new Nation({ ID: 1, Value: 'UK', Nationality: 'British', Country: 'United Kingdom' }));
					self.ntss(self.nations());
					$.map(self.nations(), function (nation) {
						self.availableNationalities.push(nation.country());
					})
				});
		}
		self.propertiesDropdown = ko.observableArray([]);
		self.propertyTypes = ko.observableArray([]);
		self.selectedPropertyType = ko.observable(null);
	
		self.getPropertyTypes = function () {
			getPropertyTypes()
				.done(function (data) {
					propertyData = data;

					var tmp = $.map(data,function(property){
						if(!self.propertiesDropdown().includes(property.propertyType)){
							self.propertiesDropdown().push(property.propertyType);
						}
						return new PropertyType(property);
					})
					self.propertyTypes(tmp);
				})
		}

		self.option=self.propertiesDropdown;

		self.subTypes = ko.observableArray([]);

		self.computePerSelectedType = ko.computed(function(){
			var selectedType = self.selectedPropertyType();
			var propertyTypes = self.propertyTypes();
		var tmp = [];
		//	var tmp = ko.utils.arrayFilter(propertyTypes,function(property){
				//if(property.propertyType() == selectedType){
				//	return property;
				//}
			//})

			return tmp;
		})

self.actionWhenChange = 	function (event) {
	var tmp = [];
  self.selectedType($(this).find(":selected").val());
$.each(propertyData, function (i, item) {

if(item.propertyType==self.selectedType()){
 if(item.propertyType=="Bungalow"){
  $('#sa5').append('<option value="'+item.bungalowType+'">'+item.bungalowType+'</option>');


}

}
});

};

		function PropertyType(data) {
			var pt = this;
			pt.ID = ko.observable(data.ID);
			pt.propertyType = ko.observable(data.propertyType);
			pt.aptType = ko.observable(data.aptType);
			pt.bungalowType = ko.observable(data.bungalowType);
			pt.houseType = ko.observable(data.houseType);
		}


		function EndClient(data) {
			var ec = this;
			ec.ID = ko.observable(data.ID ? data.ID : null);
			ec.lId = data.lId ? data.lId : null;
			ec.highLight = ko.observable(data.highLight);
			setTimeout(function () { ec.highLight(false) }, 3000);
			ec.name = ko.observable(data.name ? data.name : null);
			ec.city = ko.observable(data.city ? data.city : null);
			ec.address = ko.observable(data.address ? data.address : null);
			ec.lettingUser_id = ko.observable(data.lettingUser_id ? data.lettingUser_id : null);
			ec.postPone = ko.observable(true);

			setTimeout(function () { ec.postPone(false) }, 500);
			ec.toggleSlide = function () {
				$('#' + ec.ID()).next().slideToggle("fast");
				$('#' + ec.ID()).find('i').toggle();
			}
			ec.properties = ko.observableArray(data.properties ? $.map(data.properties, function (property) {
				return new Property(property, ec);
			}) : []);

			ec.add = function () {
				self.adding(true);

				var obj = {
					'askingPrice': self.newAskingPrice(),
					'Address': self.newAddress(),
					'City': self.newCity(),
					'Nationality': self.newNationality() ? self.newNationality().value() : null,
					'PostCode': self.newPostCode(),
					'bedrooms': self.newBedrooms(),
					'County': self.newCounty(),
					'currency': self.newCurrency(),
					'landlord_id': ec.lId,
					'propertySub': self.ManagementCompanies(),
					'propertyType': self.ManagementCompanies1(),
				}
				addRentOffer(obj)
					.done(function (data) {
						// obj.ID = data;
						// ec.properties.push(new Property(obj, ec));
						self.newAskingPrice(null);
						self.newAddress(null);
						self.newCity(null);
						self.newNationality(null);
						self.newPostCode(null);
						self.newCounty(null);
						self.newBedrooms(null),
						self.adding(false);
						self.getRentals()
					})
					.always(function () {
						self.adding(false);
					})

			}
		}


		function Property(data, parent) {
			var pr = this;
			pr.id = data.id;
			pr.FirstLine = ko.observable(data.FirstLine ? data.FirstLine : (data.Address ? data.Address : null));
			pr.FirstLine.subscribe(function (newVal) {
				editProperty({ 'id': pr.id, 'FirstLine': newVal });
			})
			pr.PostCode = ko.observable(data.PostCode ? data.PostCode : null);
			pr.PostCode.subscribe(function (newVal) {
				editProperty({ 'id': pr.id, 'PostCode': newVal });
			})
			pr.City = ko.observable(data.City ? data.City : null);
			pr.City.subscribe(function (newVal) {
				editProperty({ 'id': pr.id, 'City': newVal });
			})
			pr.County = ko.observable(data.County ? data.County : null);
			pr.County.subscribe(function (newVal) {
				editProperty({ 'id': pr.id, 'County': newVal });
			})
			pr.Country = ko.observable(data.Country ? data.Country : null);
			pr.Country.subscribe(function (newVal) {
				if (self.timeUpdate()) editProperty({ 'id': pr.id, 'Country': newVal });
			})
			
			pr.bedrooms = ko.observable(data.numberBedrooms ? data.numberBedrooms : null);
			pr.bedrooms.subscribe(function (newVal) {
				if(newVal){
					editProperty({ 'id': pr.id, 'numberBedrooms': newVal });
				}				
			})

			pr.currency = ko.observable(data.currency ? data.currency : null);

			pr.askingPrice = ko.observable(data.askingPrice ? data.askingPrice : null);
			pr.askingPrice.subscribe(function (newVal) {
				editProperty({ 'id': pr.id, 'askingPrice': newVal });
			})
			pr.propertiesDropdown = ko.pureComputed(function(){
				return self.propertiesDropdown();
			})
			pr.propertyType = ko.observable(data.propertyType ? data.propertyType : null);
			pr.type = ko.observable(data.type ? data.type : '');

			pr.availableNationalities = ko.pureComputed(function () {
				return self.availableNationalities();
			})
			
			pr.selectedPropertyType = ko.observable(data.propertyType ? data.propertyType : null);
			pr.selectedSubType = ko.observable(data.type ? data.type : null);
			pr.newType = ko.observable(data.type ? data.type : null);

			pr.toggleSlide = function () {
				$('#pr' + pr.id).next().slideToggle("fast");
				$('#pr' + pr.id).find('i').toggle();
			}
			pr.edit = ko.observable(false);
			pr.editType = function(){
				pr.edit(!pr.edit());
			}

			pr.deleteType = function(){
				deleteProperty({ 'id': pr.id}); // , 'propertyTypeID': selected.ID(),'ec':parent 
			}

			pr.saveType = function(){
				var type = null;
				type = pr.newType()
				/*
				if (pr.newType().aptType()) {
					type = pr.newType().aptType();
				} else if (pr.newType().bungalowType()) {
					type = pr.newType().bungalowType();
				} else {
					type = pr.newType().houseType();
				}*/

				var selected = [];
				var propertyTypes = self.propertyTypes ? self.propertyTypes() : [];
				var tmp = ko.utils.arrayFilter(propertyTypes,function(property){
					var selectedType = pr.selectedPropertyType();
					if(property.propertyType() == selectedType){
						// return property;
						console.log(selectedType, pr.newType())
						if(selectedType == 'Bungalow' && pr.newType() == property.bungalowType()){
							selected = property
						}else if(selectedType == 'Apt' && pr.newType() == property.aptType()){
							selected = property
						}else if(selectedType == 'House' && pr.newType() == property.houseType()){
							selected = property
						}						
					}
				})

				pr.type(type);
				pr.editType();
				editProperty({ 'id': pr.id, 'propertyTypeID': selected.ID() });
			}	
			
			pr.computePerSelectedType = ko.computed(function(){
				var selectedType = pr.selectedPropertyType();
				var propertyTypes = self.propertyTypes ? self.propertyTypes() : [];
				pr.newValue = ko.observable(data.type ? data.type : null);

				var subTypes = []

				var tmp = ko.utils.arrayFilter(propertyTypes,function(property){
					if(property.propertyType() == selectedType){
						// return property;
						if(selectedType == 'Bungalow'){
							subTypes.push(property.bungalowType())
						}else if(selectedType == 'Apt'){
							subTypes.push(property.aptType())
						}else if(selectedType == 'House'){
							subTypes.push(property.houseType())
						}						
					}
				})

				ko.utils.arrayForEach(subTypes, function (subType) {
					// console.log(subType)
					// pr.computePerSelectedType.push(subType);
				});

				// console.log(tmp)

				return subTypes
				// return tmp;
			})

			pr.newValue = ko.observable(data.type ? data.type : null);
		}


		self.computeEndClients = ko.computed(function () {
			var tmp = self.endClients();
			var sorted = tmp.sort(function (a, b) {
				var keyA = a.name(),
					keyB = b.name();
				if (keyA < keyB) return -1;
				if (keyA > keyB) return 1;
				return 0;
			});
			return sorted;
		})
		function Nation(data) {
			var nation = this;
			nation.id = data.ID;
			nation.value = ko.observable(data.Value);
			nation.nationality = ko.observable(data.Nationality);
			nation.country = ko.observable(data.Country);
			nation.selected = ko.observable(data.selected ? data.selected : false);
		}
		function addRentOffer(o) {
			var d = $.Deferred();
			$.post('../actions/forms.php', { 'act': 'addRentOffer', 'data': o, 'FORM_TOKEN': FORM_TOKEN })
				.done(function (data) {
					if (data) {
						if (data.status == 'ok') {
							d.resolve(data.data ? data.data : []);
						} else {
							d.reject();
						}
					}
				})
			return d;
		}

		function getRentals() {
			var d = $.Deferred();
			$.post('../actions/forms.php', { 'act': 'getRentals', 'FORM_TOKEN': FORM_TOKEN })
				.done(function (data) {
					if (data) {
						if (data.status == 'ok') {
							d.resolve(data.data ? data.data : []);
						} else {
							d.reject();
						}
					}
				})
			return d;
		}

		function getPropertyTypes() {
			var d = $.Deferred();
			$.post('../actions/forms.php', { 'act': 'getPropertyTypes', 'FORM_TOKEN': FORM_TOKEN })
				.done(function (data) {
					if (data) {
						if (data.status == 'ok') {
							d.resolve(data.data ? data.data : []);
						} else {
							d.reject();
						}
					}
				})
			return d;
		}

		function deleteEndClient(id) {
			var d = $.Deferred();
			$.post('../actions/forms.php', { 'act': 'deleteEndClient', 'endClient': id, 'FORM_TOKEN': FORM_TOKEN })
				.done(function (data) {
					if (data) {
						if (data.status == 'ok') {
							d.resolve(data.data ? data.data : []);
						} else {
							d.reject();
						}
					}
				})
			return d;
		}


		function editProperty(o, res) {
			$.post('../actions/forms.php', { 'act': 'editProperty', 'changes': o, 'FORM_TOKEN': FORM_TOKEN })
				.done(function (data) {
					if (data) {
						if (data.status == 'ok') {
							if (data.data == true) {
								if (res) (data.data);
								$.notify({
									message: 'Update successful.'
								}, {
									type: 'primary',
									delay: 2000,
									timer: 2000,
									showProgressbar: true,
									placement: {
										from: "bottom",
										align: "center"
									}
								});
							} else {
								if (data.data) {
									$.map(data.data, function (error) {
										if (error) {
											$.notify({
												message: error
											}, {
												type: 'danger',
												delay: 5500,
												timer: 5500,
												showProgressbar: true,
												placement: {
													from: "bottom",
													align: "center"
												}
											});
										}
									})
								}
							}

						}
					}
				})
				.fail(function () {

				})
				.always(function () {

				})
		}



function deleteProperty(o, res) {
			$.post('../actions/forms.php', { 'act': 'deleteProperty', 'changes': o, 'FORM_TOKEN': FORM_TOKEN })
				.done(function (data) {
					if (data) {
						if (data.status == 'ok') {
							if (res) (data.data);
							$.notify({
								message: 'Delete successful.'
							}, {
								type: 'primary',
								delay: 2000,
								timer: 2000,
								showProgressbar: true,
								placement: {
									from: "bottom",
									align: "center"
								}
							});
							self.getRentals();
						} else {
							if (data.data) {
								$.map(data.data, function (error) {
									if (error) {
										$.notify({
											message: error
										}, {
											type: 'danger',
											delay: 5500,
											timer: 5500,
											showProgressbar: true,
											placement: {
												from: "bottom",
												align: "center"
											}
										});
									}
								})
							}
						}
					}
				})
				.fail(function () {

				})
				.always(function () {

				})
		}












		function editProperty(o, res) {
			var d = $.Deferred()
			$.post('../actions/forms.php', { 'act': 'editProperty', 'changes': o, 'FORM_TOKEN': FORM_TOKEN })
				.done(function (data) {
					if (data) {
						if (data.status == 'ok') {
							d.resolve(data.data ? data.data : []);
							$.notify({
								message: 'Update successful.'
							}, {
								type: 'primary',
								delay: 2000,
								timer: 2000,
								showProgressbar: true,
								placement: {
									from: "bottom",
									align: "center"
								}
							});

						}
					}
				})
				.fail(function () {
					d.reject();
				})
			return d;
		}

		
		function getDropdowns() {
			var d = $.Deferred()
			$.post('../actions/forms.php', {
				'act': 'getDropdowns',
				'FORM_TOKEN': FORM_TOKEN
			}).done(function (data) {
				if (data.status == 'ok') {
					d.resolve(data.data ? data.data : []);
				} else {
					d.reject();
				}
			})
				.fail(function () {
					d.reject();
				})
			return d;
		}
		self.getPropertyTypes();
		self.getDropdowns();
		self.getRentals();
	}
	var em = document.getElementById('addRentalsPage');
	if (em) ko.applyBindings(new addRentalsViewModel(), em);
});