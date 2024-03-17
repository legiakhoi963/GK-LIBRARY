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
	$author_id = $_GET["id"];
	$status = $_GET["status"];
	$data = array(
		':author_status'			=>	$status,
		':author_id'				=>	$author_id
	);
	$query = "
	UPDATE author
	SET author_status = :author_status
	WHERE author_id = :author_id 
	";

	$statement = $connect->prepare($query);
	$statement->execute($data);

	header('location:author.php?msg='.strtolower($status).'');
}

if(isset($_GET["action"], $_GET["id"]) && $_GET["action"] == 'delete')
{
	$author_id = $_GET["id"];
	$query = "
	DELETE FROM author
	WHERE author_id = '".$author_id."'
	";

	$statement = $connect->prepare($query);
	$statement->execute($data);

	header('location:author.php?msg=delete');
}

if(isset($_POST['add_author']))
{
	$formdata = array();

	if(empty($_POST['author_name']))
	{
		$error .= '<li>Author Name is required</li>';
	}
	else
	{
		$formdata['author_name'] = trim($_POST['author_name']);
	}

	if($error == '')
	{
		$query = "
		SELECT * FROM author 
        WHERE author_name = '".$formdata['author_name']."'
		";

		$statement = $connect->prepare($query);
		$statement->execute();

		if($statement->rowCount() > 0)
		{
			$error = '<li>Author Name Already Exists</li>';
		}
		else
		{
			$data = array(
				':author_name'			=>	$formdata['author_name'],
				':author_status'			=>	'Enable'
			);

			$query = "
			INSERT INTO author 
            (author_name, author_status) 
            VALUES (:author_name, :author_status)
			";

			$statement = $connect->prepare($query);
			$statement->execute($data);

			header('location:author.php?msg=add');
		}
	}
}

if(isset($_POST["edit_author"]))
{
	$formdata = array();

	if(empty($_POST["author_name"]))
	{
		$error .= '<li>Author Name is required</li>';
	}
	else
	{
		$formdata['author_name'] = $_POST['author_name'];
	}

	if($error == '')
	{
		$author_id = $_POST['author_id'];

		$query = "
		SELECT * FROM author
        WHERE author_name = '".$formdata['author_name']."' 
        AND author_id != '".$author_id."'
		";

		$statement = $connect->prepare($query);
		$statement->execute();

		if($statement->rowCount() > 0)
		{
			$error = '<li>Author Name Already Exists</li>';
		}
		else
		{
			$data = array(
				':author_name'		=>	$formdata['author_name'],
				':author_id'			=>	$author_id
			);

			$query = "
			UPDATE author
            SET author_name = :author_name
            WHERE author_id = :author_id
			";

			$statement = $connect->prepare($query);
			$statement->execute($data);

			header('location:author.php?msg=edit');
		}
	}
}


$query = "
	SELECT * FROM author
	ORDER BY author_id ASC
";

$statement = $connect->prepare($query);
$statement->execute();
include '../header.php';
?>

<div class="container-fluid px-4">
	<div class="row mt-3">
		<div class="col-md-12">
			<h1>Author</h1>
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
				echo '<div class="alert alert-success alert-dismissible fade show mb-0" role="alert">New Author Added<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
			}
	
			if($_GET["msg"] == 'edit')
			{
				echo '<div class="alert alert-success alert-dismissible fade show mb-0" role="alert">Author Data Edited <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
			}
			if($_GET["msg"] == 'disable')
			{
				echo '<div class="alert alert-success alert-dismissible fade show mb-0" role="alert">Author Status Change to Disable <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
			}
	
			if($_GET['msg'] == 'enable')
			{
				echo '<div class="alert alert-success alert-dismissible fade show mb-0" role="alert">Author Status Change to Enable <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
			}
	
			if($_GET['msg'] == 'delete')
			{
				echo '<div class="alert alert-success alert-dismissible fade show mb-0" role="alert">Author Deleted <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
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
						<i class="fa-solid fa-address-card me-1"></i> Add New Author
					</div>
                </div>
                <div class="card-body">

                	<form method="POST">

                		<div class="mb-3">
                			<label class="form-label">Author Name</label>
                			<input type="text" name="author_name" id="author_name" class="form-control" />
                		</div>

                		<div class="mt-3 mb-0">
                			<input type="submit" name="add_author" value="Add" class="btn btn-success" />
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
			$author_id = $_GET["id"];

			if($author_id > 0)
			{
				$query = "
				SELECT * FROM author 
                WHERE author_id = '$author_id'
				";

				$author_result = $connect->query($query);

				foreach($author_result as $author_row)
				{
	?>
	
	<div class="row justify-content-center">
		<div class="col-md-8">
			<div class="card mb-3">
				<div class="card-header">
					<div class="col col-md-6 mt-1">
						<i class="fa-solid fa-address-card me-1"></i> Edit Author Details
					</div>
				</div>
				<div class="card-body">

					<form method="post">

						<div class="mb-3">
							<label class="form-label">Author Name</label>
							<input type="text" name="author_name" id="author_name" class="form-control" value="<?php echo $author_row['author_name']; ?>" />
						</div>

						<div class="mt-3 mb-0">
							<input type="hidden" name="author_id" value="<?php echo $_GET['id']; ?>" />
							<input type="submit" name="edit_author" class="btn btn-primary" value="Edit" />
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
							<i class="fa-solid fa-address-card me-1"></i> Author Management
						</div>
						<div class="col col-md-6" align="right">
							<a href="author.php?action=add" class="btn btn-success btn-sm">Add</a>
						</div>
					</div>
				</div>

				<div class="card-body">
					<table id=datatablesSimple>
						<thead>
							<tr>
								<th>Author Name</th>
								<th>Status</th>
								<th>Action</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th>Author Name</th>
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
									if($row['author_status'] == 'Enable')
									{
										$author_status = '<div class="badge bg-success">Enable</div>';
									}
									else
									{
										$author_status = '<div class="badge bg-danger">Disable</div>';
									}
									echo '
									<tr>
										<td>'.$row["author_name"].'</td>
										<td>'.$author_status.'</td>
										<td>
											<a href="author.php?action=edit&id='.$row["author_id"].'" 
												class="btn btn-sm btn-primary">
												Edit
											</a>
											<button
												class="btn btn-warning btn-sm" 
												onclick="change_status(`'.$row["author_id"].'`, `'.$row["author_status"].'`)">
												Change
											</button>
											<button
												class="btn btn-danger btn-sm" 
												onclick="delete_record(`'.$row["author_id"].'`)">
												Delete
										</button>
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

							if(confirm("Are you sure you want to "+new_status+" this Author?"))
							{
								window.location.href="author.php?action=change&id="+id+"&status="+new_status+"";
							}
						}
						function delete_record(id)
						{
							if(confirm("Are you sure you want to detele this Author?"))
							{
								window.location.href="author.php?action=delete&id="+id;
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