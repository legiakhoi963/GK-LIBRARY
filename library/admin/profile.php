<?php
include '../database_connection.php';
include '../function.php';

if(!is_admin_login())
{
	header('location:../admin_login.php');
}

$message = '';
$error = '';

if(isset($_POST['edit_admin']))
{

	$formdata = array();

	if(empty($_POST['admin_email']))
	{
		$error .= '<li>Email Address is required</li>';
	}
	else
	{
		if(!filter_var($_POST["admin_email"], FILTER_VALIDATE_EMAIL))
		{
			$error .= '<li>Invalid Email Address</li>';
		}
		else
		{
			$formdata['admin_email'] = $_POST['admin_email'];
		}
	}

	if(empty($_POST['admin_password']))
	{
		$error .= '<li>Password is required</li>';
	}
	else
	{
		$formdata['admin_password'] = $_POST['admin_password'];
	}

	if(empty($_POST['admin_name']))
	{
		$error .= '<li>Name is required</li>';
	}
	else
	{
		$formdata['admin_name'] = $_POST['admin_name'];
	}

	if(empty($_POST['admin_address']))
	{
		$error .= '<li>Address is required</li>';
	}
	else
	{
		$formdata['admin_address'] = $_POST['admin_address'];
	}

	if(empty($_POST['admin_contact']))
	{
		$error .= '<li>Contact is required</li>';
	}
	else
	{
		$formdata['admin_contact'] = $_POST['admin_contact'];
	}

	if($error == '')
	{
		$admin_id = $_SESSION['admin_id'];

		$data = array(
			':admin_email'		=>	$formdata['admin_email'],
			':admin_password'	=>	$formdata['admin_password'],
			':admin_name'		=>	$formdata['admin_name'],
			':admin_address'	=>	$formdata['admin_address'],
			':admin_contact'	=>	$formdata['admin_contact'],
			':admin_id'			=>	$admin_id
		);

		$query = "
		UPDATE admin 
            SET admin_email = :admin_email,
            admin_password = :admin_password,
			admin_name = :admin_name,
			admin_address = :admin_address,
			admin_contact = :admin_contact
            WHERE admin_id = :admin_id
		";

		$statement = $connect->prepare($query);

		$statement->execute($data);

		$message = '<li>Profile Details Edited</li>';
	}
}

$query = "
	SELECT * FROM admin 
    WHERE admin_id = ".$_SESSION['admin_id']."
";

$result = $connect->query($query);
include '../header.php';
?>

<div class="container-fluid px-4">
	<div class="row mt-3 justify-content-center">
		<div class="col-md-8">
			<h1>Profile</h1>
		</div>
	</div>

	<div class="row justify-content-center">
		<div class="col-md-8 mb-3">
			<?php 
			if($error != '')
			{
				echo '<div class="alert alert-danger alert-dismissible fade show mb-0" role="alert">
						<ul class="mb-0">'.$error.'</ul> 
						<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
					</div>';
			}

			if($message != '')
			{
				echo '<div class="alert alert-success alert-dismissible fade show mb-0" role="alert">
						<ul class="mb-0">'.$message.'</ul> 
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

					<form method="post">
						<div class="row">
							<div class="col-md-6 mb-3">
								<label class="form-label">Email Address</label>
								<input type="text" name="admin_email" id="admin_email" class="form-control" value="<?php echo $row['admin_email']; ?>" />
							</div>
							<div class="col-md-6 mb-3">
								<label class="form-label">Password</label>
								<input type="text" name="admin_password" id="admin_password" class="form-control" value="<?php echo $row['admin_password']; ?>" />
							</div>
						</div>

						<div class="row">
							<div class="col-md-6 mb-3">
								<label class="form-label">Name</label>
								<input type="text" name="admin_name" id="admin_name" class="form-control" value="<?php echo $row['admin_name']; ?>" />
							</div>
							<div class="col-md-6 mb-3">
								<label class="form-label">Contact Number</label>
								<input type="text" name="admin_contact" id="admin_contact" class="form-control" value="<?php echo $row['admin_contact']; ?>" />
							</div>
						</div>

						<div class="mb-3">
							<label class="form-label">Address</label>
							<input type="text" name="admin_address" id="admin_address" class="form-control" value="<?php echo $row['admin_address']; ?>" />
						</div>

						<div class="mt-3 mb-0">
							<input type="submit" name="edit_admin" class="btn btn-primary" value="Edit" />
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
include '../footer.php';
?>