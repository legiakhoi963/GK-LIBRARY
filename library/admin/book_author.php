<?php
include '../database_connection.php';
include '../function.php';

if(!is_admin_login())
{
	header('location:../admin_login.php');
}

$message = '';
$error = '';

if(isset($_POST['add_book_author']))
{
	foreach($_POST["author_name"] as $author_name_array)
	{

		$formdata = array();

		if(empty($_POST['book_name']))
		{
			$error .= '<li>Book Name is required</li>';
		}
		else
		{
			$formdata['book_name'] = trim($_POST['book_name']);
		}

		if(empty($author_name_array))
		{
			$error .= '<li>Author Name is required</li>';
		}
		else
		{
			$formdata['author_name'] = trim($author_name_array);
		}

		if($error == '')
		{
			$query = "
			SELECT book_id FROM book
			WHERE book_name = '".$formdata['book_name']."'
			";
			$book_data = $connect->prepare($query);
			$book_data->execute();

			foreach($book_data->fetchAll() as $row) {
				$formdata['book_id'] = $row['book_id'];
			}

			$query = "
			SELECT author_id FROM author
			WHERE author_name = '".$formdata['author_name']."'
			";
			$author_data = $connect->prepare($query);
			$author_data->execute();

			foreach($author_data->fetchAll() as $row) {
				$formdata['author_id'] = $row['author_id'];
			}

			$data = array(
				':book_id'				=>	$formdata['book_id'],
				':author_id'			=>	$formdata['author_id'],
			);

			$query = "
			INSERT INTO book_author
			(book_id, author_id) 
			VALUES (:book_id, :author_id) 
			";

			$statement = $connect->prepare($query);
			$statement->execute($data);

			header('location:book_author.php?msg=add');
		}
	}
}

if(isset($_GET["action"], $_GET["id"]) && $_GET["action"] == 'delete')
{
	$ba_id = $_GET["id"];
	$query = "
	DELETE FROM book_author
	WHERE ba_id = '".$ba_id."'
	";

	$statement = $connect->prepare($query);
	$statement->execute($data);

	header('location:book_author.php?msg=delete');
}

$query = "
	SELECT * FROM book_author
";

$statement = $connect->prepare($query);
$statement->execute();
include '../header.php';
?>

<div class="container-fluid px-4">
	<div class="row mt-3">
		<div class="col-md-12">
			<h1>Book Author</h1>
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
						<i class="fa-solid fa-address-card me-1"></i> Add New Book Author
					</div>
                </div>
                <div class="card-body">

                	<form method="POST">

                		<div class="mb-3">
                			<label class="form-label">Book Name</label>
                			<select name="book_name" id="book_name" class="form-control">
        						<?php echo fill_book($connect); ?>
        					</select>
                		</div>

						<div class="mb-3" id="author_name_field">
                			<label class="form-label">Book Author</label>
							<input type="button" value="Add Author" onclick="createNewElement();"/>
							<input type="button" value="Delete Author" onclick="deleteNewElement();"/>
                			<select name="author_name[]" id="author_name" class="form-control">
        						<?php echo fill_author($connect); ?>
        					</select>
                		</div>

                		<div class="mt-3 mb-0">
                			<input type="submit" name="add_book_author" value="Add" class="btn btn-success" />
                		</div>

						<script>
							var i=1;
							function createNewElement() {
								// First create a DIV element.
								var selectAuthor = document.createElement('select');
								selectAuthor.setAttribute('name', 'author_name[]');
								selectAuthor.setAttribute('id', 'author_name_extra['+i+']');
								selectAuthor.setAttribute('class', 'mt-2 mb-2 form-control');

								// Then add the content (a new input box) of the element.
								selectAuthor.innerHTML = '<?php echo fill_author($connect); ?>';

								// Finally put it where it is supposed to appear.
								document.getElementById("author_name_field").appendChild(selectAuthor);
								i++;
							}

							function deleteNewElement() {
								document.getElementById("author_name_extra["+(i-1)+"]").remove();
								i--;
							}
						</script>

                	</form>

                </div>
            </div>
		</div>
	</div>
	<!-- Add -->

	<?php 
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
							<i class="fa-solid fa-address-card me-1"></i> Book Author Management
						</div>
						<div class="col col-md-6" align="right">
							<a href="book_author.php?action=add" class="btn btn-success btn-sm">Add</a>
						</div>
					</div>
				</div>

				<div class="card-body">
					<table id=datatablesSimple>
						<thead>
							<tr>
								<th>Book Name</th>
								<th>Author Name</th>
								<th>Action</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th>Book Name</th>
								<th>Author Name</th>
								<th>Action</th>
							</tr>
						</tfoot>
						<tbody>
							<?php
							if($statement->rowCount() > 0)
							{
								foreach($statement->fetchAll() as $row)
								{
									$query = "
                					SELECT * FROM book 
                					WHERE book_id = '".$row["book_id"]."'
                					";
									$book_data = $connect->prepare($query);
									$book_data->execute();

									$query = "
                					SELECT * FROM author
                					WHERE author_id = '".$row["author_id"]."'
                					";
									$author_data = $connect->prepare($query);
									$author_data->execute();
									
									echo '<tr>';
									foreach($book_data as $book_data)
									{
									echo '
										<td>'.$book_data ["book_name"].'</td>
									';
									}

									foreach($author_data as $author_data)
									{
									echo '
										<td>'.$author_data ["author_name"].'</td>
									';
									}

									echo '
										<td>
											<button
												class="btn btn-danger btn-sm" 
												onclick="delete_record(`'.$row["ba_id"].'`)">
												Delete
											</button>
										</td>
									';
									echo '</tr>';
								}
							}
							?>
						</tbody>
					</table>
					<script>
						function delete_record(id)
						{
							if(confirm("Are you sure you want to detele this Book Author?"))
							{
								window.location.href="book_author.php?action=delete&id="+id;
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