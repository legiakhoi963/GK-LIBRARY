<?php
include 'database_connection.php';
include 'function.php';

if(is_user_login())
{
	header('location:issue_book_details.php');
}

$message = '';
$success = '';

if(isset($_POST["register_button"]))
{
	$formdata = array();

	if(empty($_POST["user_email"]))
	{
		$message .= '<li>Email Address is required</li>';
	}
	else
	{
		if(!filter_var($_POST["user_email"], FILTER_VALIDATE_EMAIL))
		{
			$message .= '<li>Invalid Email Address</li>';
		}
		else
		{
			$formdata['user_email'] = trim($_POST['user_email']);
		}
	}

	if(empty($_POST["user_password"]))
	{
		$message .= '<li>Password is required</li>';
	}
	else
	{
		$formdata['user_password'] = trim($_POST['user_password']);
	}

	if(empty($_POST['user_name']))
	{
		$message .= '<li>User Name is required</li>';
	}
	else
	{
		$formdata['user_name'] = trim($_POST['user_name']);
	}

	if(empty($_POST['user_address']))
	{
		$message .= '<li>User Address Detail is required</li>';
	}
	else
	{
		$formdata['user_address'] = trim($_POST['user_address']);
	}

	if(empty($_POST['user_contact']))
	{
		$message .= '<li>User Contact Number Detail is required</li>';
	}
	else
	{
		$formdata['user_contact'] = trim($_POST['user_contact']);
	}

	if($message == '')
	{
		$data = array(
			':user_email'		=>	$formdata['user_email']
		);

		$query = "
		SELECT * FROM user 
        WHERE user_email = :user_email
		";

		$statement = $connect->prepare($query);
		$statement->execute($data);

		if($statement->rowCount() > 0)
		{
			$message = '<li>Email Already Register</li>';
		}
		else
		{
			$data = array(
				':user_name'			=>	$formdata['user_name'],
				':user_address'			=>	$formdata['user_address'],
				':user_contact'			=>	$formdata['user_contact'],
				':user_email'			=>	$formdata['user_email'],
				':user_password'		=>	$formdata['user_password'],
				':user_date_created'	=>	get_date_time($connect)
			);

			$query = "
			INSERT INTO user 
            (user_name, user_address, user_contact, user_email, user_password, user_date_created) 
            VALUES (:user_name, :user_address, :user_contact, :user_email, :user_password, :user_date_created) 
			";

			$statement = $connect->prepare($query);
			$statement->execute($data);

			$success = 'New User Created';
		}

	}
}
include 'header.php';
?>


<div class="d-flex align-items-center justify-content-center bg-dark rounded-3" style="min-height:463px;">
	<div class="col-md-6">

		<?php 
		if($message != '')
		{
			echo '<div class="alert alert-danger mt-3 mb-0"><ul class="mb-0">'.$message.'</ul></div>';
		}

		if($success != '')
		{
			echo '<div class="alert alert-success mt-3">'.$success.'</div>';
		}

		?>
		<div class="card mt-3 mb-3">
			<div class="card-header">New User Registration</div>
			<div class="card-body">
				<form method="POST" enctype="multipart/form-data">
					<div class ="row">
						<div class="col-md-6 mb-3">
							<label class="form-label">Email Address</label>
							<input type="text" name="user_email" id="user_email_address" class="form-control" />
						</div>
						<div class="col-md-6 mb-3">
							<label class="form-label">Password</label>
							<input type="password" name="user_password" id="user_password" class="form-control" />
						</div>
					</div>
					<div class ="row">
						<div class="col-md-6 mb-3">
							<label class="form-label">User Name</label>
							<input type="text" name="user_name" class="form-control" id="user_name" value="" />
						</div>
						<div class="col-md-6 mb-3">
							<label class="form-label">User Contact Number</label>
							<input type="text" name="user_contact" id="user_contact_no" class="form-control" />
						</div>
					</div>
					<div class="mb-3">
						<label class="form-label">User Address</label>
						<input name="user_address" id="user_address" class="form-control"></input>
					</div>
					<div class="d-flex align-items-center justify-content-between mt-3 mb-3">
						<input type="submit" name="register_button" class="btn btn-primary" value="Register" />
					</div>
				</form>
			</div>
		</div>
	</div>
</div>


<?php 
include 'footer.php';
?>