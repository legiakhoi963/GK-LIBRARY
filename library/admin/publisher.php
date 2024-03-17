<?php
include '../database_connection.php';
include '../function.php';

if(!is_admin_login())
{
	header('location:../admin_login.php');
}

$message = '';
$error = '';

if(isset($_GET["action"], $_GET["id"], $_GET["status"]) && $_GET["action"] == 'change')
{
	$publisher_id = $_GET["id"];
	$status = $_GET["status"];
	$data = array(
		':publisher_status'			=>	$status,
		':publisher_id'				=>	$publisher_id
	);
	$query = "
	UPDATE publisher
	SET publisher_status = :publisher_status
	WHERE publisher_id = :publisher_id 
	";

	$statement = $connect->prepare($query);
	$statement->execute($data);

	header('location:publisher.php?msg='.strtolower($status).'');
}

if(isset($_GET["action"], $_GET["id"]) && $_GET["action"] == 'delete')
{
	$publisher_id = $_GET["id"];
	$query = "
	DELETE FROM publisher
	WHERE publisher_id = '".$publisher_id."'
	";

	$statement = $connect->prepare($query);
	$statement->execute($data);

	header('location:publisher.php?msg=delete');
}

if(isset($_POST['add_publisher']))
{
	$formdata = array();

	if(empty($_POST['publisher_name']))
	{
		$error .= '<li>Publisher Name is required</li>';
	}
	else
	{
		$formdata['publisher_name'] = trim($_POST['publisher_name']);
	}

	if(empty($_POST['publisher_website']))
	{
		$error .= '<li>Publisher Website is required</li>';
	}
	else
	{
		$formdata['publisher_website'] = trim($_POST['publisher_website']);
	}

	if(empty($_POST['publisher_address']))
	{
		$error .= '<li>Publisher Address is required</li>';
	}
	else
	{
		$formdata['publisher_address'] = trim($_POST['publisher_address']);
	}

	if($error == '')
	{
		$query = "
		SELECT * FROM publisher
        WHERE publisher_name = '".$formdata['publisher_name']."'
		";

		$statement = $connect->prepare($query);
		$statement->execute();

		if($statement->rowCount() > 0)
		{
			$error = '<li>Publisher Name Already Exists</li>';
		}
		else
		{
			$data = array(
				':publisher_name'			=>	$formdata['publisher_name'],
				':publisher_website'		=>	$formdata['publisher_website'],
				':publisher_address'		=>	$formdata['publisher_address'],
				':publisher_status'			=>	'Enable'
			);

			$query = "
			INSERT INTO publisher 
            (publisher_name, publisher_website, publisher_address, publisher_status) 
            VALUES (:publisher_name, :publisher_website, :publisher_address, :publisher_status)
			";

			$statement = $connect->prepare($query);
			$statement->execute($data);

			header('location:publisher.php?msg=add');
		}
	}
}

if(isset($_POST["edit_publisher"]))
{
	$formdata = array();

	if(empty($_POST["publisher_name"]))
	{
		$error .= '<li>Publisher Name is required</li>';
	}
	else
	{
		$formdata['publisher_name'] = $_POST['publisher_name'];
	}

	if(empty($_POST['publisher_website']))
	{
		$error .= '<li>Publisher Website is required</li>';
	}
	else
	{
		$formdata['publisher_website'] = trim($_POST['publisher_website']);
	}

	if(empty($_POST['publisher_address']))
	{
		$error .= '<li>Publisher Address is required</li>';
	}
	else
	{
		$formdata['publisher_address'] = trim($_POST['publisher_address']);
	}

	if($error == '')
	{
		$publisher_id = $_POST['publisher_id'];

		$query = "
		SELECT * FROM publisher 
        WHERE publisher_name = '".$formdata['publisher_name']."' 
        AND publisher_id != '".$publisher_id."'
		";

		$statement = $connect->prepare($query);
		$statement->execute();

		if($statement->rowCount() > 0)
		{
			$error = '<li>Publisher Name Already Exists</li>';
		}
		else
		{
			$data = array(
				':publisher_name'		=>	$formdata['publisher_name'],
				':publisher_website'	=>	$formdata['publisher_website'],
				':publisher_address'	=>	$formdata['publisher_address'],
				':publisher_id'			=>	$publisher_id
			);

			$query = "
			UPDATE publisher 
            SET publisher_name = :publisher_name,
				publisher_website = :publisher_website,
				publisher_address = :publisher_address
            WHERE publisher_id = :publisher_id
			";

			$statement = $connect->prepare($query);
			$statement->execute($data);

			header('location:publisher.php?msg=edit');
		}
	}
}


$query = "
	SELECT * FROM publisher
	ORDER BY publisher_id ASC
";

$statement = $connect->prepare($query);
$statement->execute();
include '../header.php';
?>

<div class="container-fluid px-4">
	<div class="row mt-3">
		<div class="col-md-12">
			<h1>Publisher</h1>
		</div>
	</div>
	
	<div class="col-md-12 mb-3">
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

		if(isset($_GET['msg']))
		{
			if($_GET['msg'] == 'add')
			{
				echo '<div class="alert alert-success alert-dismissible fade show mb-0" role="alert">New Publisher Added<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
			}
	
			if($_GET["msg"] == 'edit')
			{
				echo '<div class="alert alert-success alert-dismissible fade show mb-0" role="alert">Publisher Data Edited <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
			}
			if($_GET["msg"] == 'disable')
			{
				echo '<div class="alert alert-success alert-dismissible fade show mb-0" role="alert">Publisher Status Change to Disable <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
			}
	
			if($_GET['msg'] == 'enable')
			{
				echo '<div class="alert alert-success alert-dismissible fade show mb-0" role="alert">Publisher Status Change to Enable <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
			}
	
			if($_GET['msg'] == 'delete')
			{
				echo '<div class="alert alert-success alert-dismissible fade show mb-0" role="alert">Publisher Deleted <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
			}
		}
		?>
	</div>


	<!-- Add -->
	<?php
	if(isset($_GET['action']))
	{
		if($_GET['action'] == 'add')
		{
	?>

	<div class="row justify-content-center">
		<div class="col-md-8">
			<div class="card mb-3">
				<div class="card-header">
					<div class="col col-md-6 mt-1">
						<i class="fa-solid fa-users-rectangle me-1"></i> Add New Publisher
					</div>
                </div>
                <div class="card-body">

                	<form method="POST">

                		<div class="mb-3">
                			<label class="form-label">Publisher Name</label>
                			<input type="text" name="publisher_name" id="publisher_name" class="form-control" />
                		</div>

						<div class="mb-3">
                			<label class="form-label">Publisher Website</label>
                			<input type="text" name="publisher_website" id="publisher_website" class="form-control" />
                		</div>

						<div class="mb-3">
                			<label class="form-label">Publisher Address</label>
                			<input type="text" name="publisher_address" id="publisher_address" class="form-control" />
                		</div>

                		<div class="mt-3 mb-0">
                			<input type="submit" name="add_publisher" value="Add" class="btn btn-success" />
                		</div>

                	</form>

                </div>
            </div>
		</div>
	</div>
	<!-- Add -->

	<!-- Edit -->
	<?php 
		}
		else if($_GET["action"] == 'edit')
		{
			$publisher_id = $_GET["id"];

			if($publisher_id > 0)
			{
				$query = "
				SELECT * FROM publisher 
                WHERE publisher_id = '$publisher_id'
				";

				$publisher_result = $connect->query($query);

				foreach($publisher_result as $publisher_row)
				{
	?>
	
	<div class="row justify-content-center">
		<div class="col-md-8">
			<div class="card mb-3">
				<div class="card-header">
					<div class="col col-md-6 mt-1">
						<i class="fa-solid fa-users-rectangle me-1"></i> Edit Publisher Details
					</div>	
				</div>
				<div class="card-body">

					<form method="post">

						<div class="mb-3">
							<label class="form-label">Publisher Name</label>
							<input type="text" name="publisher_name" id="publisher_name" class="form-control" value="<?php echo $publisher_row['publisher_name']; ?>" />
						</div>

						<div class="mb-3">
                			<label class="form-label">Publisher Website</label>
                			<input type="text" name="publisher_website" id="publisher_website" class="form-control" value="<?php echo $publisher_row['publisher_website']; ?>" />
                		</div>

						<div class="mb-3">
                			<label class="form-label">Publisher Address</label>
                			<input type="text" name="publisher_address" id="publisher_address" class="form-control" value="<?php echo $publisher_row['publisher_address']; ?>" />
                		</div>

						<div class="mt-3 mb-0">
							<input type="hidden" name="publisher_id" value="<?php echo $_GET['id']; ?>" />
							<input type="submit" name="edit_publisher" class="btn btn-primary" value="Edit" />
						</div>

					</form>

				</div>
			</div>
		</div>
	</div>
	<!-- Edit -->

	<?php 
				}
			}
		}
	}
	else
	{	
	?>

	<!-- Table -->
	<div class="row justify-content-center">
		<div class="col-md-12">
			<div class="card mb-3">
				<div class="card-header">
					<div class="row">
						<div class="col col-md-6 mt-1">
							<i class="fa-solid fa-users-rectangle me-1"></i> Publisher Management
						</div>
						<div class="col col-md-6" align="right">
							<a href="publisher.php?action=add" class="btn btn-success btn-sm">Add</a>
						</div>
					</div>
				</div>

				<div class="card-body">
					<table id=datatablesSimple>
						<thead>
							<tr>
								<th>Publisher Name</th>
								<th>Website</th>
								<th>Address</th>
								<th>Status</th>
								<th>Action</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th>Publisher Name</th>
								<th>Website</th>
								<th>Address</th>
								<th>Status</th>
								<th>Action</th>
							</tr>
						</tfoot>
						<tbody>
							<?php
							if($statement->rowCount() > 0)
							{
								foreach($statement->fetchAll() as $row)
								{
									if($row['publisher_status'] == 'Enable')
									{
										$publisher_status = '<div class="badge bg-success">Enable</div>';
									}
									else
									{
										$publisher_status = '<div class="badge bg-danger">Disable</div>';
									}
									echo '
									<tr>
										<td>'.$row["publisher_name"].'</td>
										<td>'.$row["publisher_website"].'</td>
										<td>'.$row["publisher_address"].'</td>
										<td>'.$publisher_status.'</td>
										<td>
											<div class="col">
												<div class="row mb-1">
													<a href="publisher.php?action=edit&id='.$row["publisher_id"].'" 
														class="btn btn-sm btn-primary">
														Edit
													</a>
												</div>
												<div class="row mb-1">
													<button
														class="btn btn-warning btn-sm" 
														onclick="change_status(`'.$row["publisher_id"].'`, `'.$row["publisher_status"].'`)">
														Change
													</button>
												</div>
												<div class="row">
													<button
														class="btn btn-danger btn-sm" 
														onclick="delete_record(`'.$row["publisher_id"].'`)">
														Delete
													</button>
												</div>
											</div>
										</td>
									</tr>
									';
								}
							}
							?>
						</tbody>
					</table>
					<script>
						function change_status(id, status)
						{
							var new_status = 'Enable';

							if(status == 'Enable')
							{
								new_status = 'Disable';
							}

							if(confirm("Are you sure you want to "+new_status+" this Publisher?"))
							{
								window.location.href="publisher.php?action=change&id="+id+"&status="+new_status+"";
							}
						}
						function delete_record(id)
						{
							if(confirm("Are you sure you want to detele this Publisher?"))
							{
								window.location.href="publisher.php?action=delete&id="+id;
							}
						}
					</script>
				</div>
			</div>
		</div>
	</div>
	<!-- Table -->
	<?php 
	}
	?>
</div>

<?php 
include '../footer.php';
?>