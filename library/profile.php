<?php

include 'database_connection.php';
include 'function.php';

if(!is_user_login())
{
	header('location:user_login.php');
}

$message = '';
$success = '';

if(isset($_POST['edit_user']))
{
	$formdata = array();

	if(empty($_POST['user_email']))
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

	if(empty($_POST['user_password']))
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
		$message .= '<li>User Contact Number is required</li>';
	}
	else
	{
		$formdata['user_contact'] = $_POST['user_contact'];
	}

	if($message == '')
	{
		$data = array(
			':user_name'			=>	$formdata['user_name'],
			':user_address'			=>	$formdata['user_address'],
			':user_contact'			=>	$formdata['user_contact'],
			':user_email'			=>	$formdata['user_email'],
			':user_password'		=>	$formdata['user_password'],
			':user_date_updated'	=>	get_date_time($connect),
			':user_id'				=>	$_SESSION['user_id']
		);

		$query = "
		UPDATE user 
            SET user_name = :user_name, 
            user_address = :user_address, 
            user_contact = :user_contact, 
            user_email = :user_email, 
            user_password = :user_password, 
            user_date_updated = :user_date_updated 
            WHERE user_id = :user_id
		";

		$statement = $connect->prepare($query);
		$statement->execute($data);

		$success = '<li>Profile Details Edited</li>';
	}
}


$query = "
	SELECT * FROM user 
	WHERE user_id = '".$_SESSION['user_id']."'
";

$result = $connect->query($query);
include 'header.php';
?>

<div class="container-fluid px-4 bg-dark pt-2 pb-2 rounded-3">
	<div class="row mt-3 justify-content-center">
		<div class="col-md-8 text-white">
			<h1>User Profile</h1>
		</div>
	</div>

	<div class="row justify-content-center">
		<div class="col-md-8 mb-3">
			<?php 
			if($message != '')
			{
				echo '<div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
						<ul class="mb-0">'.$message.'</ul> 
						<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
					</div>';
			}

			if($success != '')
			{
				echo '<div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
						<ul class="mb-0">'.$success.'</ul> 
						<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
					</div>';
			}
			?>
		</div>
	</div>

	<div class="row justify-content-center">
		<div class="col-md-8">
			<div class="card mb-3">
				<div class="card-header">
					<div class="col col-md-6 mt-1">
						<i class="fas fa-user-edit me-1"></i> Edit Profile Details
					</div>
				</div>

				<div class="card-body">

					<?php 
					foreach($result as $row)
					{
					?>

					<form method="POST" enctype="multipart/form-data">
						<div class="row">
							<div class="col-md-6 mb-3">
								<label class="form-label">Email Address</label>
								<input type="text" name="user_email" id="user_email" class="form-control" value="<?php echo $row['user_email']; ?>" />
							</div>
							<div class="col-md-6 mb-3">
								<label class="form-label">Password</label>
								<input type="text" name="user_password" id="user_password" class="form-control" value="<?php echo $row['user_password']; ?>" />
							</div>
						</div>

						<div class="row">
							<div class="col-md-6 mb-3">
								<label class="form-label">User Name</label>
								<input type="text" name="user_name" id="user_name" class="form-control" value="<?php echo $row['user_name']; ?>" />
							</div>
							<div class="col-md-6 mb-3">
								<label class="form-label">User Contact Number</label>
								<input type="text" name="user_contact" id="user_contact" class="form-control" value="<?php echo $row['user_contact']; ?>" />
							</div>
						</div>

						<div class="mb-3">
							<label class="form-label">User Address</label>
							<input type="text" name="user_address" id="user_address" class="form-control" value="<?php echo $row['user_address']; ?>" />
						</div>

						<div class="mt-3 mb-0">
							<input type="submit" name="edit_user" class="btn btn-primary" value="Edit" />
						</div>
					</form>

				<?php
				}
				?>
				</div>
			</div>
		</div>
	</div>
</div>

<?php 
include 'footer.php';
?>