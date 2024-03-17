<?php

function is_admin_login()
{
	if(isset($_SESSION['admin_id']))
	{
		return true;
	}
	return false;
}

function is_user_login()
{
	if(isset($_SESSION['user_id']))
	{
		return true;
	}
	return false;
}

function fill_author($connect)
{
	$query = "
	SELECT author_name FROM author 
	WHERE author_status = 'Enable' 
	ORDER BY author_name ASC
	";

	$result = $connect->query($query);

	$output = '<option value="">Select Author</option>';

	foreach($result as $row)
	{
		$output .= '<option value="'.$row["author_name"].'">'.$row["author_name"].'</option>';
	}

	return $output;
}

function fill_category($connect)
{
	$query = "
	SELECT category_name FROM category 
	WHERE category_status = 'Enable' 
	ORDER BY category_name ASC
	";

	$result = $connect->query($query);

	$output = '<option value="">Select Category</option>';

	foreach($result as $row)
	{
		$output .= '<option value="'.$row["category_name"].'">'.$row["category_name"].'</option>';
	}

	return $output;
}

function fill_publisher($connect)
{
	$query = "
	SELECT publisher_name FROM publisher 
	WHERE publisher_status = 'Enable' 
	ORDER BY publisher_name ASC
	";

	$result = $connect->query($query);

	$output = '<option value="">Select Publisher</option>';

	foreach($result as $row)
	{
		$output .= '<option value="'.$row["publisher_name"].'">'.$row["publisher_name"].'</option>';
	}

	return $output;
}

function fill_book($connect)
{
	$query = "
	SELECT book_code, book_name, book_copies FROM book 
	WHERE book_status = 'Enable' 
	ORDER BY book_code DESC
	";

	$result = $connect->query($query);

	$output = '<option value="">Select Book</option>';

	foreach($result as $row)
	{
		$output .= '<option value="'.$row["book_name"].'">'.$row["book_code"]. " - " .$row["book_name"]. " - Copies: " .$row["book_copies"].'</option>';
	}

	return $output;
}

function fill_user($connect)
{
	$query = "
	SELECT user_id, user_name, user_email FROM user 
	WHERE user_status = 'Enable' 
	ORDER BY user_name ASC
	";

	$result = $connect->query($query);

	$output = '<option value="">Select User</option>';

	foreach($result as $row)
	{
		$output .= '<option value="'.$row["user_id"].'">'.$row["user_name"]. " - " .$row["user_email"].'</option>';
	}

	return $output;
}

function get_date_time($connect)
{
	date_default_timezone_set('Asia/Saigon');

	return date("d-m-Y H:i:s",  STRTOTIME(date('h:i:sa')));
}

function get_one_day_fines($connect)
{
	$output = 10000;
	
	return $output;
}

function get_user_status($connect, $user_id)
{
	$output = '';

	$query = "
	SELECT user_status AS status FROM user
	WHERE user_id = '".$user_id."' 
	";

	$result = $connect->query($query);

	foreach($result as $row)
	{
		$output = $row["status"];
	}
	return $output;
}

function get_book_request_limit_per_user($connect)
{
	$output = 3;

	return $output;
}

function get_total_book_request_per_user($connect, $user_id)
{
	$output = 0;

	$query = "
	SELECT COUNT(issue_book_id) AS Total FROM issue_book 
	WHERE user_id = '".$user_id."' 
	AND issue_book_status = 'Pending'
	";

	$result = $connect->query($query);

	foreach($result as $row)
	{
		$output = $row["Total"];
	}
	return $output;
}

function get_book_issue_limit_per_user($connect)
{
	$output = 3;

	return $output;
}

function get_total_book_issue_per_user($connect, $user_id)
{
	$output = 0;

	$query = "
	SELECT COUNT(issue_book_id) AS Total FROM issue_book 
	WHERE user_id = '".$user_id."' 
	AND (issue_book_status = 'Issue' OR issue_book_status = 'Not Return')
	";

	$result = $connect->query($query);

	foreach($result as $row)
	{
		$output = $row["Total"];
	}
	return $output;
}

function get_total_book_issue_day($connect)
{
	$output = 3;

	return $output;
}

function Count_total_issue_book_number($connect)
{
	$total = 0;

	$query = "SELECT COUNT(issue_book_id) AS Total FROM issue_book 
			  WHERE (issue_book_status != 'Pending') AND (issue_book_status != 'Decline') ";

	$result = $connect->query($query);

	foreach($result as $row)
	{
		$total = $row["Total"];
	}

	return $total;
}

function Count_total_returned_book_number($connect)
{
	$total = 0;

	$query = "
	SELECT COUNT(issue_book_id) AS Total FROM issue_book 
	WHERE issue_book_status = 'Return'
	";

	$result = $connect->query($query);

	foreach($result as $row)
	{
		$total = $row["Total"];
	}

	return $total;
}

function Count_total_not_returned_book_number($connect)
{
	$total = 0;

	$query = "
	SELECT COUNT(issue_book_id) AS Total FROM issue_book 
	WHERE issue_book_status = 'Not Return'
	";

	$result = $connect->query($query);

	foreach($result as $row)
	{
		$total = $row["Total"];
	}

	return $total;
}

function Count_total_fines_received($connect)
{
	$total = 0;

	$query = "
	SELECT SUM(issue_book_fines) AS Total FROM issue_book 
	WHERE issue_book_status = 'Return'
	";

	$result = $connect->query($query);

	foreach($result as $row)
	{
		$total = $row["Total"];
	}

	return $total;
}

function Count_total_book_number($connect)
{
	$total = 0;

	$query = "
	SELECT COUNT(book_id) AS Total FROM book 
	WHERE book_status = 'Enable'
	";

	$result = $connect->query($query);

	foreach($result as $row)
	{
		$total = $row["Total"];
	}

	return $total;
}

function Count_total_author_number($connect)
{
	$total = 0;

	$query = "
	SELECT COUNT(author_id) AS Total FROM author 
	WHERE author_status = 'Enable'
	";

	$result  = $connect->query($query);

	foreach($result as $row)
	{
		$total = $row["Total"];
	}

	return $total;
}

function Count_total_category_number($connect)
{
	$total = 0;

	$query = "
	SELECT COUNT(category_id) AS Total FROM category 
	WHERE category_status = 'Enable'
	";

	$result = $connect->query($query);

	foreach($result as $row)
	{
		$total = $row["Total"];
	}
	return $total;
}

function Count_total_publisher_number($connect)
{
	$total = 0;

	$query = "
	SELECT COUNT(publisher_id) AS Total FROM publisher 
	WHERE publisher_status = 'Enable'
	";

	$result = $connect->query($query);

	foreach($result as $row)
	{
		$total = $row["Total"];
	}
	return $total;
}

?>