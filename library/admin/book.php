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
	$book_id = $_GET["id"];
	$status = $_GET["status"];
	$data = array(
		':book_status'			=>	$status,
		':book_id'				=>	$book_id
	);
	$query = "
	UPDATE book
	SET book_status = :book_status
	WHERE book_id = :book_id 
	";

	$statement = $connect->prepare($query);
	$statement->execute($data);

	header('location:book.php?msg='.strtolower($status).'');
}

if(isset($_GET["action"], $_GET["id"]) && $_GET["action"] == 'delete')
{
	$book_id = $_GET["id"];
	$query = "
	DELETE FROM book
	WHERE book_id = '".$book_id."'
	";

	$statement = $connect->prepare($query);
	$statement->execute($data);

	header('location:book.php?msg=delete');
}

if(isset($_POST['add_book']))
{
	$formdata = array();

	if(empty($_POST['book_code']))
	{
		$error .= '<li>Book Code is required</li>';
	}
	else
	{
		$formdata['book_code'] = trim($_POST['book_code']);
	}

	if(empty($_POST['book_name']))
	{
		$error .= '<li>Book Name is required</li>';
	}
	else
	{
		$formdata['book_name'] = trim($_POST['book_name']);
	}

	if(empty($_POST["book_publisher"]))
	{
		$error .= '<li>Book Publisher is required</li>';
	}
	else
	{
		$formdata['book_publisher'] = trim($_POST["book_publisher"]);
	}

	if(empty($_POST["book_location"]))
	{
		$error .= '<li>Book Location is required</li>';
	}
	else
	{
		$formdata['book_location'] = trim($_POST["book_location"]);
	}

	if(empty($_POST["book_copies"]))
	{
		$error .= '<li>Book Copies is required</li>';
	}
	else
	{
		$formdata['book_copies'] = trim($_POST["book_copies"]);
	}

	foreach($_POST["author_name"] as $author_name_array)
	{
		if(empty($author_name_array))
		{
			$error .= '<li>Book Author is required</li>';
		}
	}
	
	foreach($_POST["category_name"] as $category_name_array)
	{
		if(empty($category_name_array))
		{
			$error .= '<li>Book Category is required</li>';
		}
	}

	if($error == '')
	{
		$query = "
		SELECT * FROM book
        WHERE book_code = '".$formdata['book_code']."' 
		OR book_name = '".$formdata['book_name']."'
		";

		$statement = $connect->prepare($query);
		$statement->execute();

		if($statement->rowCount() > 0)
		{
			$error = '<li>Book Already Exists</li>';
		}
		else
		{
			$query = "
			SELECT publisher_id FROM publisher 
			WHERE publisher_name = '".$formdata['book_publisher']."'
			";
			$publisher_data = $connect->prepare($query);
			$publisher_data->execute();

			foreach($publisher_data->fetchAll() as $row) {
				$formdata['book_publisher'] = $row['publisher_id'];
			}

			$data = array(
				':book_code'			=>	$formdata['book_code'],
				':book_name'			=>	$formdata['book_name'],
				':book_publisher'		=>	$formdata['book_publisher'],
				':book_location'		=>	$formdata['book_location'],
				':book_copies'			=>	$formdata['book_copies'],
				':book_status'			=>	'Enable'
			);

			$query = "
			INSERT INTO book
			(book_code, book_name, book_publisher, book_location, book_copies, book_status) 
			VALUES (:book_code, :book_name, :book_publisher, :book_location, :book_copies, :book_status)
			";

			$statement = $connect->prepare($query);
			$statement->execute($data);

			foreach($_POST["author_name"] as $author_name_array)
			{
				$formdata['author_name'] = trim($author_name_array);

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
			}

			foreach($_POST["category_name"] as $category_name_array)
			{
				$formdata['category_name'] = trim($category_name_array);

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
				SELECT category_id FROM category
				WHERE category_name = '".$formdata['category_name']."'
				";
				$category_data = $connect->prepare($query);
				$category_data->execute();

				foreach($category_data->fetchAll() as $row) {
					$formdata['category_id'] = $row['category_id'];
				}

				$data = array(
					':book_id'				=>	$formdata['book_id'],
					':category_id'			=>	$formdata['category_id'],
				);

				$query = "
				INSERT INTO book_category
				(book_id, category_id) 
				VALUES (:book_id, :category_id) 
				";

				$statement = $connect->prepare($query);
				$statement->execute($data);
			}

			header('location:book.php?msg=add');
		}
	}
}

if(isset($_POST["edit_book"]))
{
	$formdata = array();

	if(empty($_POST['book_code']))
	{
		$error .= '<li>Book Code is required</li>';
	}
	else
	{
		$formdata['book_code'] = trim($_POST['book_code']);
	}

	if(empty($_POST['book_name']))
	{
		$error .= '<li>Book Name is required</li>';
	}
	else
	{
		$formdata['book_name'] = trim($_POST['book_name']);
	}

	if(empty($_POST["book_publisher"]))
	{
		$error .= '<li>Book Publisher is required</li>';
	}
	else
	{
		$formdata['book_publisher'] = trim($_POST["book_publisher"]);
	}

	if(empty($_POST["book_location"]))
	{
		$error .= '<li>Book Location is required</li>';
	}
	else
	{
		$formdata['book_location'] = trim($_POST["book_location"]);
	}

	if(empty($_POST["book_copies"]))
	{
		$error .= '<li>Book Copies is required</li>';
	}
	else
	{
		$formdata['book_copies'] = trim($_POST["book_copies"]);
	}

	if($error == '')
	{
		$book_id = $_POST['book_id'];

		$query = "
		SELECT * FROM book
        WHERE (book_code = '".$formdata['book_code']."' OR book_name = '".$formdata['book_name']."')
        AND book_id != '".$book_id."'
		";

		$statement = $connect->prepare($query);
		$statement->execute();

		if($statement->rowCount() > 0)
		{
			$error = '<li>Book Already Exists</li>';
		}
		else
		{
			$query = "
			SELECT publisher_id FROM publisher 
			WHERE publisher_name = '".$formdata['book_publisher']."'
			";
			$publisher_data = $connect->prepare($query);
			$publisher_data->execute();
	
			foreach($publisher_data->fetchAll() as $row) {
				$formdata['book_publisher'] = $row['publisher_id'];
			}

			$data = array(
				':book_code'			=>	$formdata['book_code'],
				':book_name'			=>	$formdata['book_name'],
				':book_publisher'		=>	$formdata['book_publisher'],
				':book_location'		=>	$formdata['book_location'],
				':book_copies'			=>	$formdata['book_copies'],
				':book_id'				=>	$book_id
			);

			$query = "
			UPDATE book
            SET book_code = :book_code,
				book_name = :book_name,
				book_publisher = :book_publisher,
				book_location = :book_location,
				book_copies = :book_copies
            WHERE book_id = :book_id
			";

			$statement = $connect->prepare($query);
			$statement->execute($data);

			header('location:book.php?msg=edit');
		}
	}
}

$query = "
	SELECT * FROM book
	ORDER BY book_id DESC
";
$statement = $connect->prepare($query);
$statement->execute();

include '../header.php';
?>

<div class="container-fluid px-4">
	<div class="row mt-3">
		<div class="col-md-12">
			<h1>Book</h1>
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
				echo '<div class="alert alert-success alert-dismissible fade show mb-0" role="alert">New Book Added<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
			}
	
			if($_GET["msg"] == 'edit')
			{
				echo '<div class="alert alert-success alert-dismissible fade show mb-0" role="alert">Book Data Edited <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
			}
			if($_GET["msg"] == 'disable')
			{
				echo '<div class="alert alert-success alert-dismissible fade show mb-0" role="alert">Book Status Change to Disable <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
			}
	
			if($_GET['msg'] == 'enable')
			{
				echo '<div class="alert alert-success alert-dismissible fade show mb-0" role="alert">Book Status Change to Enable <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
			}
	
			if($_GET['msg'] == 'delete')
			{
				echo '<div class="alert alert-success alert-dismissible fade show mb-0" role="alert">Book Deleted <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
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
						<i class="fa-solid fa-book-open me-1"></i> Add New Book
					</div>
                </div>
                <div class="card-body">

                	<form method="POST">

						<div class="mb-3">
                			<label class="form-label">Book Name</label>
                			<input type="text" name="book_name" id="book_name" class="form-control" />
                		</div>

						<div class="row">
							<div class="col-md-6 mb-3">
								<label class="form-label">Book Code</label>
								<input type="text" name="book_code" id="book_code" class="form-control" />
							</div>

							<div class="col-md-6 mb-3">
                			<label class="form-label">Book Publisher</label>
                			<select name="book_publisher" id="book_publisher" class="form-control">
        						<?php echo fill_publisher($connect); ?>
        					</select>
                		</div>
						</div>

						<div class="row">
							<div class="col-md-6 mb-3" id="author_name_field">
								<label class="form-label">Book Author</label>
								<input type="button" value="Add" onclick="addAuthor();"/>
								<input type="button" value="Delete" onclick="deleteAuthor();"/>
								<select name="author_name[]" id="author_name" class="form-control">
									<?php echo fill_author($connect); ?>
								</select>
							</div>

							<div class="col-md-6 mb-3" id="category_name_field">
								<label class="form-label">Book Category</label>
								<input type="button" value="Add" onclick="addCategory();"/>
								<input type="button" value="Delete" onclick="deleteCategory();"/>
								<select name="category_name[]" id="category_name" class="form-control">
									<?php echo fill_category($connect); ?>
								</select>
                			</div>
						</div>

						<div class="row">
							<div class="col-md-6 mb-3">
								<label class="form-label">Book Location</label>
								<input type="text" name="book_location" id="book_location" class="form-control" />
							</div>

							<div class="col-md-6 mb-3">
								<label class="form-label">Book Copies</label>
								<input type="number" name="book_copies" id="book_copies" step="1" min="1" class="form-control" />
							</div>
						</div>

                		<div class="mt-3 mb-0">
                			<input type="submit" name="add_book" value="Add" class="btn btn-success" />
                		</div>

						<script>
							var nA=1;
							var nC=1;
							function addAuthor() {
								// First create a DIV element.
								var selectAuthor = document.createElement("select");
								selectAuthor.setAttribute("name", "author_name[]");
								selectAuthor.setAttribute("id", "author_name_extra["+nA+"]");
								selectAuthor.setAttribute("class", "mt-2 mb-2 form-control");

								// Then add the content (a new input box) of the element.
								selectAuthor.innerHTML = '<?php echo fill_author($connect); ?>';

								// Finally put it where it is supposed to appear.
								document.getElementById("author_name_field").appendChild(selectAuthor);
								nA++;
							}

							function deleteAuthor() {
								document.getElementById("author_name_extra["+(nA-1)+"]").remove();
								nA--;
							}

							function addCategory() {
								// First create a DIV element.
								var selectCategory = document.createElement("select");
								selectCategory.setAttribute("name", "category_name[]");
								selectCategory.setAttribute("id", "category_name_extra["+nC+"]");
								selectCategory.setAttribute("class", "mt-2 mb-2 form-control");

								// Then add the content (a new input box) of the element.
								selectCategory.innerHTML = '<?php echo fill_category($connect); ?>';

								// Finally put it where it is supposed to appear.
								document.getElementById("category_name_field").appendChild(selectCategory);
								nC++;
							}

							function deleteCategory() {
								document.getElementById("category_name_extra["+(nC-1)+"]").remove();
								nC--;
							}

						</script>

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
			$book_id = $_GET["id"];

			if($book_id > 0)
			{
				$query = "
				SELECT * FROM book 
                WHERE book_id = '$book_id'
				";

				$book_result = $connect->query($query);

				foreach($book_result as $book_row)
				{
	?>
	
	<div class="row justify-content-center">
		<div class="col-md-8">
			<div class="card mb-3">
				<div class="card-header">
					<div class="col col-md-6 mt-1">
						<i class="fa-solid fa-book-open me-1"></i> Edit Book
					</div>
                </div>
                <div class="card-body">

                	<form method="POST">

						<div class="mb-3">
                			<label class="form-label">Book Name</label>
                			<input type="text" name="book_name" id="book_name" class="form-control" value="<?php echo $book_row['book_name']; ?>" />
                		</div>

						<div class="row">
							<div class="mb-3 col-md-6">
								<label class="form-label">Book Code</label>
								<input type="text" name="book_code" id="book_code" class="form-control" value="<?php echo $book_row['book_code']; ?>" />
							</div>

							<div class="mb-3 col-md-6">
								<label class="form-label">
									Book Publisher
								</label>
								<select name="book_publisher" id="book_publisher" class="form-control" >
									<?php
										$query = "
										SELECT * FROM publisher 
										WHERE publisher_id = '".$book_row["book_publisher"]."'
										";
										$publisher_data = $connect->prepare($query);
										$publisher_data->execute();
										
										foreach($publisher_data as $publisher_data)
										{
											echo '
												<option value="'.$publisher_data ["publisher_name"].'">'.$publisher_data ["publisher_name"].'</option>
											';
										}
										echo fill_publisher($connect);
									?>
								</select>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6 mb-3">
								<label class="form-label">Book Location</label>
								<input type="text" name="book_location" id="book_location" class="form-control" value="<?php echo $book_row['book_location']; ?>"/>
							</div>

							<div class="col-md-6 mb-3">
								<label class="form-label">Book Copies</label>
								<input type="number" name="book_copies" id="book_copies" step="1" min="1" class="form-control" value="<?php echo $book_row['book_copies']; ?>" />
							</div>
						</div>

                		<div class="mt-3 mb-0">
							<input type="hidden" name="book_id" value="<?php echo $book_row['book_id']; ?>" />
                			<input type="submit" name="edit_book" value="Edit" class="btn btn-success" />
                		</div>

                	</form>
					<script>
       					document.getElementById('book_category').value = "<?php echo $book_row['book_category']; ?>";
       				</script>
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
							<i class="fa-solid fa-book-open me-1"></i> Book Management
						</div>
						<div class="col col-md-6" align="right">
							<a href="book.php?action=add" class="btn btn-success btn-sm">Add Book</a>
							<a href="book_author.php" class="btn btn-primary btn-sm">Book Author</a>
							<a href="book_category.php" class="btn btn-secondary btn-sm">Book Category</a>
						</div>
					</div>
				</div>

				<div class="card-body">
					<table id=datatablesSimple>
						<thead>
							<tr>
								<th>Code</th>
								<th>Name</th>
								<th>Author</th>
								<th>Category</th>
								<th>Publisher</th>
								<th>Location</th>
								<th>Copies</th>
								<th>Status</th>
								<th>Action</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th>Code</th>
								<th>Name</th>
								<th>Author</th>
								<th>Category</th>
								<th>Publisher</th>
								<th>Location</th>
								<th>Copies</th>
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
									if($row['book_status'] == 'Enable')
									{
										$book_status = '<div class="badge bg-success">Enable</div>';
									}
									else
									{
										$book_status = '<div class="badge bg-danger">Disable</div>';
									}

									$query = "
                					SELECT * FROM publisher 
                					WHERE publisher_id = '".$row["book_publisher"]."'
                					";
									$publisher_data = $connect->prepare($query);
									$publisher_data->execute();

									$query = "
                					SELECT * FROM book_author 
                					WHERE book_id = '".$row["book_id"]."'
                					";
									$book_author_data = $connect->prepare($query);
									$book_author_data->execute();

									$query = "
                					SELECT * FROM book_category 
                					WHERE book_id = '".$row["book_id"]."'
                					";
									$book_category_data = $connect->prepare($query);
									$book_category_data->execute();

									echo '
									<tr>
										<td>'.$row["book_code"].'</td>
										<td>'.$row["book_name"].'</td>
									';

									echo '<td>';
										foreach($book_author_data->fetchAll() as $book_author_data)
										{
											$query = "
											SELECT * FROM author 
											WHERE author_id = '".$book_author_data["author_id"]."'
											";
											$author_data = $connect->prepare($query);
											$author_data->execute();

											foreach($author_data->fetchAll() as $author_data)
											{
											echo '
												<div>'.$author_data ["author_name"].'</div>
											';
											}
										}
									echo '</td>';

									echo '<td>';
										foreach($book_category_data->fetchAll() as $book_category_data)
										{
											$query = "
											SELECT * FROM category 
											WHERE category_id = '".$book_category_data["category_id"]."'
											";
											$category_data = $connect->prepare($query);
											$category_data->execute();

											foreach($category_data->fetchAll() as $category_data)
											{
											echo '
												<div>'.$category_data ["category_name"].'</div>
											';
											}
										}
									echo '</td>';

									foreach($publisher_data as $publisher_data)
									{
									echo '
										<td>'.$publisher_data ["publisher_name"].'</td>
									';
									}

									echo '
										<td>'.$row["book_location"].'</td>
										<td>'.$row["book_copies"].'</td>
										<td>'.$book_status.'</td>
										<td>
										<div class="col">
											<div class="row mb-1">
												<a href="book.php?action=edit&id='.$row["book_id"].'" 
													class="btn btn-sm btn-primary">
													Edit
												</a>
											</div>
											<div class="row mb-1">
												<button
													class="btn btn-warning btn-sm" 
													onclick="change_status(`'.$row["book_id"].'`, `'.$row["book_status"].'`)">
													Change
												</button>
											</div>
											<div class="row">
												<button
													class="btn btn-danger btn-sm" 
													onclick="delete_record(`'.$row["book_id"].'`)">
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

							if(confirm("Are you sure you want to "+new_status+" this Book?"))
							{
								window.location.href="book.php?action=change&id="+id+"&status="+new_status+"";
							}
						}
						function delete_record(id)
						{
							if(confirm("Are you sure you want to detele this Book?"))
							{
								window.location.href="book.php?action=delete&id="+id;
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