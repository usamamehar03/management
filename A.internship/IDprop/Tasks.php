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

	<title>IDprop - Tasks</title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="description" content="IDprop">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php
	include_once ("links.php");
	?>
</head>
<body id="tasksPage">
<?php
include_once('_inc/menu.php');
?>
<div style="margin-top: 140px;display: block;width: 100%;">
	<div class="form-style-10" style="margin: auto;width: 80%;">
		<center>
			<button class="btn btn-primary" style="padding: 10px;margin-top: -5px;" data-bind="click:addTask"><i class="fa fa-plus"></i> Add Task</button>
			<select id ="salutation" style="display: inline-block;width: 200px;" data-bind="options:availableFilters,optionsText:$data,value:filter"  required>
			</select>
		</center><br>
		<div>
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>#</th>
						<th>Title</th>
						<th>Creation</th>
						<th>Completion</th>
						<th>Status</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody data-bind="foreach:tasks">
					<tr>
						<td><span data-bind="text:$index() +1"></span></td>
						<td><span data-bind="text:taskName()"></span></td>
						<td><span data-bind="text:taskCreationDate()"></span></td>
						<td><span data-bind="text:taskCompletionDate()"></span></td>
						<td><span data-bind="text:taskStatus()"></span></td>
						<td><span data-bind='click:editTask' class="btn btn-primary">View/Edit</span></td>
					</tr>
				</tbody>
			</table>
			<center data-bind="visible:tasks().length == 0" style="display: none;">
				<i class="fa fa-tasks fa-3x" aria-hidden="true"></i><br>
				No <span data-bind="text:(filter() != null) && (filter() != 'All') ?  filter() : ''"></span> Tasks
			</center>
		</div>		
	</div>
	<br><br><bR><br>
</div>
<div class="modal"  tabindex="-1" role="dialog" data-bind='modal:addTaskModal,with:addTaskModal'>
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h3 class="modal-title">Add Task</h3>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="form-style-10" style="width: 100%;">
					<div class="form-group">
						<span style="color: red;" ></span>
						<label for="fname1_1">Title</label>
						<input type="text" data-bind="textInput:newTitle">
					</div>
					<div class="form-group">
						<span style="color: red;" ></span>
						<label for="fname1_1" >Text</label>
						<textarea data-bind="textInput:newText"></textarea>
					</div>
					<div class="form-group">
						<span style="color: red;" ></span>
						<label for="fname1_1">Status</label>
						<select id ="salutation" data-bind="options:availableStatus,optionsText:$data,value:selectedNewStatus"  required>
						</select>
					</div>
				</div>												
			</div>
			<div class="modal-footer">
				<button class="btn btn-secondary" data-dismiss="modal"  >
					<span><i class="fa fa-times"></i> Cancel</span>
				</button>
				<button class="btn btn-success" data-bind="enable: newTitle(),click:submitTask" data-dismiss="modal">
					<span><i class="fa fa-check"></i> Add</span>
				</button>
			</div>
		</div>
	</div>
</div>
<div class="modal"  tabindex="-1" role="dialog" data-bind='modal:taskToBeEdited,with:taskToBeEdited'>
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h3 class="modal-title">View/Edit Task</h3>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="form-style-10" style="width: 100%;">
					<div class="form-group">
						<span style="color: red;" ></span>
						<label for="fname1_1">Title</label>
						<input type="text" data-bind="value:taskName">
					</div>
					<div class="form-group">
						<span style="color: red;" ></span>
						<label for="fname1_1" >Text</label>
						<textarea data-bind="value:taskText"></textarea>
					</div>
					<div class="form-group">
						<span style="color: red;" ></span>
						<label for="fname1_1">Completion</label>
						<input type="date" data-bind="value:taskCompletionDate">
					</div>
					<div class="form-group">
						<span style="color: red;" ></span>
						<label for="fname1_1">Status</label>
						<select id ="salutation" data-bind="options:availableStatus,optionsText:$data,value:taskStatus"  required>
						</select>
					</div>
				</div>												
			</div>
			<div class="modal-footer">
				<button class="btn btn-danger" data-bind="click:deleteMe" data-dismiss="modal">
					<span><i class="fa fa-trash"></i> Delete</span>
				</button>
				<button class="btn btn-success" data-dismiss="modal"  >
					<span><i class="fa fa-check"></i> Save</span>
				</button>
				<button class="btn btn-secondary" data-dismiss="modal"  >
					<span><i class="fa fa-times"></i> Cancel</span>
				</button>
			</div>
		</div>
	</div>
</div>
</body>
<footer class="footer">
	<div class="container">
		<div class="row">
			<div class="col-lg-4">
				<!-- Footer Intro -->
				<div class="footer_intro">
					<!-- Copyright -->
					<div class="footer_cr">

						Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved 
					</div>
				</div>
			</div>				
			<div class="row">
				<div class="col">
					<!-- Copyright --><div class="footer_cr_2">2020 All rights reserved</div>
				</div>
			</div>
		</div>
	</div>
</footer>
</html>
<script  data-main="assets/js/config" src='assets/js/require.js'></script>
<script>
	require(['config'], function(){
		require(['tasksViewModel']);
	});
</script>