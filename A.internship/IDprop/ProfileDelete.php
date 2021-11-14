<?php
session_start();
require_once ("actions/userActions.php");
if(userActions\isLoggedIn()){
	$token = userActions\tokenGenerate();
	echo '<script type="text/javascript"> var FORM_TOKEN = "'.$token.'";</script>';
}else{
	header("Location: notLogged.php");
	die();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=UA-130502260-1"></script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		gtag('js', new Date());
		gtag('config', 'UA-130502260-1');
	</script>

	<title>IDprop - Delete Profile</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="description" content="IDprop">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php
	include_once ("links.php");
	?>
</head>
<body id="profileDeletePage">

<?php include_once('_inc/menu.php'); ?>

<div style="margin-top: 140px;">
	<center style="margin-top: 140px;width: 100%:">
		<div class="col col-6" >
			<a id="m_1_2" class="project-item completed" >
				<span class="ico-area">
					<i class="fas fa-user-times fa-2x"></i>			
				</span>
				<span class="title">This is where you can delete your IDprop account.</span>
				<span>Deleting means that you will have no further access to this platform and all your data will be erased.</span><br><br>
				<button class="btn btn-secondary" data-bind="click:toggleDeletion">Delete account</button>
			</a>
		</div>
	</center>
</div>
<div class="modal" id="deleteModal" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h3 class="modal-title">Delete Account</h3>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body text-center">
				We hate to see you leaving, are you sure you wish to delete your account and all associated data?<br>
				Type CONFIRM in order to proceed.
				<input type="text" data-bind="textInput:deleteText">
			</div>
			<div class="modal-footer">
				<button class="btn btn-secondary" data-bind="click:cancel" >
					<span><i class="fa fa-times"></i> Cancel</span>
				</button>
				<button class="btn btn-danger" data-bind="click:deleteMe,enable:enableDeletion" >
					<span><i class="fa fa-check"></i> Delete</span>
				</button>
				<br>
			</div>
		</div>
	</div>
</div>	
</body>
<?php
include_once ("_inc/footer.php");
?>
<?php include_once ("scripts.php"); ?>
</html>

<script>
	function profileDeleteVM() {
		self.confirmDeleteModal = ko.observable(null);
		self.toggleDeletion = function(){
			self.confirmDeleteModal(self);
		}
		self.cancel = function(){
			self.confirmDeleteModal(null);
		}
		self.deleteMe = function(){
			deleteUser()
			.done(function(data){
				self.confirmDeleteModal(null);
				if(data){
					location.href = 'accountDeleted.php';
				}
			})
		}
		self.enableDeletion = ko.observable(false);
		self.deleteText = ko.observable(null);
		self.deleteText.subscribe(function(newVal){
			if(newVal == 'CONFIRM'){
				self.enableDeletion(true);
			}else{
				self.enableDeletion(false);
			}
		})
		self.compModalVisibility = ko.computed(function(){
			if(self.confirmDeleteModal()){
				$('#deleteModal').modal('show');
			}else{
				$('#deleteModal').modal('hide');
			}
			return true;
		})
		function deleteUser() {
			var d = $.Deferred()
			$.post('actions/profileDelete.php',{'type':'normalUser','FORM_TOKEN' : FORM_TOKEN
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
}
var em = document.getElementById('profileDeletePage');
if(em) ko.applyBindings(new profileDeleteVM(), em);
</script>