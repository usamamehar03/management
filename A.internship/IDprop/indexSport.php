<?php
require_once ("actions/config.php");
require ("actions/cms/selectM.php");
?>
<!doctype html>
<html lang="en">
    <head>
    <!-- Required meta tags -->
    	<meta charset="utf-8">
    	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    	<title>index page!</title>
    	<script src="assets/js/jquery-3.5.1.min.js"></script>
    </head>
    <body>
    	<h1 class="ml-5">input form</h1>
    	<form class="p-5 "action="actions/handler.php" method="post" style="border: 3px solid black">
			<div class="form-row">
				<div class="form-group col-md-6">
			        <label for="inputEmail4">user name</label>
			        <input type="text" class="form-control" name="uname" placeholder="enter name" required>
			    </div>
			    <div class="form-group col-md-6">
			        <label for="inputEmail4">user email</label>
			        <input type="text" class="form-control" name="email" placeholder="enter email" required>
			    </div>
			    <div class="form-group col-md-6">
			        <label for="inputEmail4">product name</label>
			        <input type="text" class="form-control" name="pname" placeholder="enter product name" required>
			    </div>

			    <div class="form-group col-md-4">
			        <label for="inputState">product type</label>
			        <select name="ptype" class="form-control" required> 
			        	<option >Choose...</option>
			        	<option>cricket</option>
			        	<option>footbal</option>
			        	<option>gym</option>
			        </select>
			    </div>

			    <div class="form-group col-md-6">
			        <label for="inputPassword4">unit price</label>
			        <input type="text" class="form-control" name="uprice" placeholder="enter unit price" required>
			    </div>
			</div>
			<button type="submit" name="submit" class="btn btn-primary">Submit</button>
		</form>




							<!-- 2nd form -->



<h1 class="ml-5 pt-5">our data</h1>

    	<form class="p-5" style="border: 3px solid black">
			<div class="form-row">
			    <div class="form-group col-md-6">
			        <label for="inputEmail4">last user in our table</label>
			        <input type="text" class="form-control"  value="<?php echo getLastUser($CONNECTION_LETFASTER); ?>" disabled>
			    </div>

			    
			    <div class="form-group col-md-6">
			        <label for="inputPassword4">total product</label>
			        <!-- <input type="text" class="form-control"  value="<?php //echo getTotalProduct($CONNECTION_LETFASTER); ?>" disabled> -->
			    </div>

				<div class="form-group col-md-4">
				    <label for="inputAddress"> enter id</label>
				    <input type="text" class="form-control" id="dellid" placeholder="enter id of product you want to delete">
				</div>
			</div>
			<button type="submit" id="dell" class="btn btn-primary">Delete</button>
		</form>
		<div style="border: 3px solid black" class="m-5 d-flex flex-row justify-content-between">
			<div class="flex-row col-md-6">
				<button class="bg-info" id="userbutton">Display users</button>
				<div id="userdiv"></div>
			</div>
			<div class="flex-row col-md-5" >
				<button class="bg-info" id="productbutton">Display products</button>
				<div id="productdiv"></div>
			</div>
		</div>


									<!-- js -->
	<script type="text/javascript">
		function displayusers()
		{
			$(document).ready(function(){
				 $('#userbutton').on('click', function(event) {
		            var name = "disuser";
		            $.ajax({
		                url: 'actions/handler.php',
		                type: 'post',
		               	data: {name: name},
		               	datatype:'json',
		               	success: function(response){
		                	$('#userdiv').html(response);
		               }
		            });
		        });
			});
		}

		function displayproduct()
		{
			$(document).ready(function(){
				 $('#productbutton').on('click', function(event) {
		            var name = "dispproduct";
		            $.ajax({
		                url: 'actions/handler.php',
		                type: 'post',
		               	data: {name: name},
		               	datatype:'json',
		               	success: function(response){
		                	$('#productdiv').html(response);
		               }
		            });
		        });
			});
		}

		function deletuser()
		{
			$(document).ready(function(){
				 $('#dell').on('click', function(event) {
				 	event.preventDefault();
				 	var name = $("#dellid").val();
		            $.ajax({
		                url: 'actions/handler.php',
		                type: 'post',
		               	data: {name: name},
		               	datatype:'json',
		               	success: function(response){
		                	alert(response);
		               }
		            });
		        });
			});
		}
		deletuser();
		 displayproduct();
		 displayusers();
		
	</script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
   </body>
</html>