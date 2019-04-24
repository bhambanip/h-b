<?php
//including the database connection file
include_once("config.php");

//fetching data in descending order (lastest entry first)
//$result = mysql_query("SELECT * FROM users ORDER BY id DESC"); // mysql_query is deprecated
$result = mysqli_query($mysqli, "SELECT * FROM dhCatList Group By CategoryNo Order by Id"); // using mysqli_query instead
?>

<html>
<head>	
	<title>Edit Categories</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {background-color: #f2f2f2;}
    </style>
</head>

<body>
<a href="subcategory.php">Edit Sub Category</a><br/><br/>

	<table>

	<tr bgcolor='#CCCCCC'>
		<th>Category No</th>
		<th>Category Name</th>
		<th>Margin</th>
		<th>MarginNewEgg</th>
		<th>Update</th>
	</tr>
	<?php 
	//while($res = mysql_fetch_array($result)) { // mysql_fetch_array is deprecated, we need to use mysqli_fetch_array 
	while($res = mysqli_fetch_array($result)) { 		
		echo "<tr>";
		echo "<td>".$res['CategoryNo']."</td>";
		echo "<td>".$res['CategoryName']."</td>";
        if($res['Margin'] > '0.00') {
            echo "<td style='color: red'>".$res['Margin']."</td>";
        } else {
            echo "<td>".$res['Margin']."</td>";
        }
        if($res['MarginNewEgg'] > '0.00') {
            echo "<td style='color: green'>".$res['MarginNewEgg']."</td>";
        } else {
            echo "<td>".$res['MarginNewEgg']."</td>";
        }
		echo "<td><a href=\"edit.php?CategoryNo=$res[CategoryNo]\">Edit</a></td>";
	}
	?>
	</table>
</body>
</html>
