<?php
// including the database connection file
include_once("config.php");

if(isset($_POST['update']))
{

    $id = mysqli_real_escape_string($mysqli, $_POST['id']);

    $SubCategoryNo = mysqli_real_escape_string($mysqli, $_POST['SubCategoryNo']);
    $SubCategoryName = mysqli_real_escape_string($mysqli, $_POST['SubCategoryName']);
    $Margin = mysqli_real_escape_string($mysqli, $_POST['Margin']);
    $MarginNewEgg = mysqli_real_escape_string($mysqli, $_POST['MarginNewEgg']);

    // checking empty fields
    if(empty($SubCategoryNo) || empty($SubCategoryName) || empty($Margin) || empty($MarginNewEgg)) {

        if(empty($SubCategoryNo)) {
            echo "<font color='red'>Sub Categoty No field is empty.</font><br/>";
        }

        if(empty($SubCategoryName)) {
            echo "<font color='red'>Sub Category Name field is empty.</font><br/>";
        }

        if(empty($Margin)) {
            echo "<font color='red'>Margin field is empty.</font><br/>";
        }

        if(empty($MarginNewEgg)) {
            echo "<font color='red'>Margin New Egg field is empty.</font><br/>";
        }
    } else {
        //updating the table
        $result = mysqli_query($mysqli, "UPDATE ".categoryTableName." SET Margin='$Margin',MarginNewEgg='$MarginNewEgg' WHERE Id=$id");

        //redirectig to the display page. In our case, it is index.php
        header("Location: subcategory.php");
    }
}
?>
<?php
//getting id from url
$id = $_GET['id'];

//selecting data associated with this particular id
$result = mysqli_query($mysqli, "SELECT * FROM ".categoryTableName." WHERE Id=$id");

while($res = mysqli_fetch_array($result))
{
    $SubCategoryNo = $res['SubCategoryNo'];
    $SubCategoryName = $res['SubCategoryName'];
    $Margin = $res['Margin'];
    $MarginNewEgg = $res['MarginNewEgg'];
}
?>
<html>
<head>
    <title>Edit Sub Category</title>
</head>

<body>
<a href="subcategory.php">Home</a>
<br/><br/>

<form name="form1" method="post" action="editSubCategory.php">
    <table border="0">
        <tr>
            <td>Sub Category No</td>
            <td><input type="text" name="SubCategoryNo" value="<?php echo $SubCategoryNo;?>" readonly></td>
        </tr>
        <tr>
            <td> Sub Category Name</td>
            <td><input type="text" name="SubCategoryName" value="<?php echo $SubCategoryName;?>" readonly></td>
        </tr>
        <tr>
            <td>Margin</td>
            <td><input type="text" name="Margin" value="<?php echo $Margin;?>"></td>
        </tr>
         <tr>
            <td>MarginNewEgg</td>
            <td><input type="text" name="MarginNewEgg" value="<?php echo $MarginNewEgg;?>"></td>
        </tr>
        <tr>
            <td><input type="hidden" name="id" value=<?php echo $_GET['id'];?>></td>
            <td><input type="submit" name="update" value="Update"></td>
        </tr>
    </table>
</form>
</body>
</html>
