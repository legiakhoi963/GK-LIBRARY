<?php
include 'database_connection.php';
include 'function.php';

if(is_user_login())
{
	header('location:issue_book_details.php');
}

$message = '';

if(isset($_POST["login_button"]))
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

	if(empty($_POST['user_password']))
	{
		$message .= '<li>Password is required</li>';
	}	
	else
	{
		$formdata['user_password'] = trim($_POST['user_password']);
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
			foreach($statement->fetchAll() as $row)
			{
				if($row['user_password'] == $formdata['user_password'])
				{
					$_SESSION['user_id'] = $row['user_id'];
					header('location:issue_book_details.php');
				}
				else
				{
					$message = '<li>Wrong Password</li>';
				}
			}
		}
		else
		{
			$message = '<li>Wrong Email Address</li>';
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
			echo '<div class="alert alert-danger"><ul class="mb-0">'.$message.'</ul></div>';
		}
		?>

		<div class="card">
			<div class="card-header mt-1">User Login</div>
			<div class="card-body">
				<form method="POST">
					<div class="mb-3">
						<label class="form-label">Email address</label>
						<input type="text" name="user_email" id="user_email" class="form-control" />
					</div>

					<div class="mb-3">
						<label class="form-label">Password</label>
						<input type="password" name="user_password" id="user_password" class="form-control" />
					</div>

					<div class="d-flex align-items-center justify-content-between mt-4 mb-0">
						<input type="submit" name="login_button" class="btn btn-primary" value="Login" />
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<?php 
include 'footer.php';
?>