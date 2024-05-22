<?php

include 'config.php';

session_start();
if (!isset($_SESSION["user_id"]) &&
($_SESSION["user_role"] == "admin" || 
$_SESSION["user_role"] == "super_admin")) {
    header("Location: login.php");
    exit();
}

$productname = $price = $image = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $productname = mysqli_real_escape_string($connection, $_POST['productname']);
    $price = intval($_POST["price"]); 
    $image = $_FILES['image']['name'];

    $target_directory = "images/";
    $target_file = $target_directory . basename($image);
    move_uploaded_file($_FILES['image']['tmp_name'], $target_file);

  //  $sql = "INSERT INTO `products`( `productname`, `price`, `image`) VALUES ($productname, $price, $image)";
    $sql = "INSERT INTO `products` (`productname`, `price`, `image`) VALUES ('$productname', $price, '$image')";
    $result = mysqli_query($connection, $sql);

    if ($result) {
        header("Location: product.php"); 
        exit();
    } else {
        $error = "Error: " . $sql . "<br>" . mysqli_error($connection);
    }
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link rel="stylesheet" href="css/style.css">
    
</head>
<body>
    
    <div class="form-container add-product-form">
        
    <form method="POST" enctype="multipart/form-data">
        <h1>Add Product</h1>
        <p><?php echo $error; ?></p>
        <label for="productname">Product Name:</label>
        <input type="text" id="productname" name="productname" required>
        <br>
        <label for="price">Product Price:</label>
        <input type="number" id="price" name="price"  required>
        <br>
        <label for="image">Product Image:</label>
        <input type="file" id="image" name="image" accept="image/*" required>
        <br>
        <input type="submit" value="Add Product">
    </form>
    </div>
    
</body>
</html>

