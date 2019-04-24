<?php
// including the database connection file
include_once("config.php");

if (isset($_POST['update'])) {

    $CategoryNo = mysqli_real_escape_string($mysqli, $_POST['CategoryNo']);

    $CategoryName = mysqli_real_escape_string($mysqli, $_POST['CategoryName']);
    $Margin = mysqli_real_escape_string($mysqli, $_POST['Margin']);
    $MarginNewEgg = mysqli_real_escape_string($mysqli, $_POST['MarginNewEgg']);

    // checking empty fields
    if (empty($CategoryNo) || empty($CategoryName) || empty($Margin) || empty($MarginNewEgg)) {

        if (empty($CategoryNo)) {
            echo "<font color='red'>Categoty No field is empty.</font><br/>";
        }

        if (empty($CategoryName)) {
            echo "<font color='red'>Category Name field is empty.</font><br/>";
        }

        if (empty($Margin)) {
            echo "<font color='red'>Margin field is empty.</font><br/>";
        }

        if (empty($MarginNewEgg)) {
            echo "<font color='red'>Margin New Egg field is empty.</font><br/>";
        }
    } else {
        //updating the table
        $result = mysqli_query($mysqli, "UPDATE " . categoryTableName . " SET Margin='$Margin',MarginNewEgg='$MarginNewEgg' WHERE CategoryNo='$CategoryNo'");

        //redirectig to the display page. In our case, it is index.php
        header("Location: index.php");
    }
}
?>
<?php
//getting id from url
$CategoryNo = $_GET['CategoryNo'];

//selecting data associated with this particular id
$result = mysqli_query($mysqli, "SELECT * FROM " . categoryTableName . " WHERE CategoryNo='" . $CategoryNo . "' Group by CategoryNo");

while ($res = mysqli_fetch_array($result)) {
    $CategoryNo = $res['CategoryNo'];
    $CategoryName = $res['CategoryName'];
    $Margin = $res['Margin'];
    $MarginNewEgg = $res['MarginNewEgg'];
}
?>
<html>
<head>
    <title>Edit Data</title>
</head>

<body>
<a href="index.php">Home</a>
<br/><br/>

<form name="form1" method="post" action="edit.php">
    <table border="0">
        <tr>
            <td>Category No</td>
            <td><input type="text" name="CategoryNo" value="<?php echo $CategoryNo; ?>" readonly></td>
        </tr>
        <tr>
            <td>Category Name</td>
            <td><input type="text" name="CategoryName" value="<?php echo $CategoryName; ?>" readonly></td>
        </tr>
        <tr>
            <td>Margin</td>
            <td><input type="text" name="Margin" value="<?php echo $Margin; ?>"></td>
        </tr>
        <tr>
            <td>MarginNewEgg</td>
            <td><input type="text" name="MarginNewEgg" value="<?php echo $MarginNewEgg; ?>"></td>
        </tr>
        <tr>
            <td><input type="submit" name="update" value="Update"></td>
        </tr>
    </table>
</form>
</body>
</html>
