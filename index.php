<?php

include 'config.php';
session_start();
$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
};

if(isset($_GET['logout'])){
   unset($user_id);
   session_destroy();
   header('location:login.php');
};

if(isset($_POST['add_to_cart'])){

   $product_name = $_POST['product_name'];
   $product_price = $_POST['product_price'];
   $product_image = $_POST['product_image'];
   $product_quantity = $_POST['product_quantity'];

   $select_cart = mysqli_query($connection, "SELECT * FROM `cart` WHERE name = '$product_name' AND user_id = '$user_id'") or die('query failed');

   if(mysqli_num_rows($select_cart) > 0){
      $message[] = 'product already added to cart!';
   }else{
      mysqli_query($connection, "INSERT INTO `cart`(user_id, name, price, image, quantity) VALUES('$user_id', '$product_name', '$product_price', '$product_image', '$product_quantity')") or die('query failed');
      $message[] = 'product added to cart!';
   }

};

if(isset($_POST['update_cart'])){
   $update_quantity = $_POST['cart_quantity'];
   $update_id = $_POST['cart_id'];
   mysqli_query($connection, "UPDATE `cart` SET quantity = '$update_quantity' WHERE id = '$update_id'") or die('query failed');
   $message[] = 'cart quantity updated successfully!';
}

if(isset($_GET['remove'])){
   $remove_id = $_GET['remove'];
   mysqli_query($connection, "DELETE FROM `cart` WHERE id = '$remove_id'") or die('query failed');
   header('location:index.php');
}
  
if(isset($_GET['delete_all'])){
   mysqli_query($connection, "DELETE FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
   header('location:index.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>shopping cart</title>

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php
if(isset($message)){
   foreach($message as $message){
      echo '<div class="message" onclick="this.remove();">'.$message.'</div>';
   }
}
?>

<div class="container">

<div class="user-profile">

   <?php
      $select_user = mysqli_query($connection, "SELECT * FROM `user_info` WHERE id = '$user_id'") or die('query failed');
      if(mysqli_num_rows($select_user) > 0){
         $fetch_user = mysqli_fetch_assoc($select_user);
      };
   ?>
   
   <p> User Name : <span><?php echo $fetch_user['username']; ?></span> </p>
   <p> Email : <span><?php echo $fetch_user['email']; ?></span> </p>
   <div class="flex">
   <?php

      if (isset($_SESSION["user_role"]) && ( $_SESSION["user_role"]=="super_admin" )) {
         echo '<a href="register.php" class="option-btn">Add admin</a>';
      }
   ?>
      
      
      <?php
         if (isset($_SESSION["user_role"]) && ( $_SESSION["user_role"]=="super_admin" )) {
            echo '<a href="product.php" class="option-btn">Add Product</a>';
         }
         elseif(isset($_SESSION["user_role"]) && ( true)) {
            echo '<a href="product.php" class="option-btn">Add Product</a>';
         }

         
         
         
      ?>
      <a href="index.php?logout=<?php echo $user_id; ?>" onclick="return confirm('are your sure you want to logout?');" class="delete-btn">logout</a>
   </div>

</div>

<div class="products">

   <h1 class="heading">Available    Products</h1>

   <div class="box-container">

   <?php
      $select_product = mysqli_query($connection, "SELECT * FROM products") or die('query failed');
      if(mysqli_num_rows($select_product) > 0){
         while($fetch_product = mysqli_fetch_assoc($select_product)){
   ?>
      <form method="post" class="box" action="">
         <img src="images/<?php echo $fetch_product['image']; ?>" alt="">
         <div class="productname"><?php echo $fetch_product['productname']; ?></div>
         <div class="price">$<?php echo $fetch_product['price']; ?>/-</div>
         <input type="number" min="1" name="product_quantity" value="1">
         <input type="hidden" name="product_image" value="<?php echo $fetch_product['image']; ?>">
         <input type="hidden" name="product_name" value="<?php echo $fetch_product['productname']; ?>">
         <input type="hidden" name="product_price" value="<?php echo $fetch_product['price']; ?>">
         <input type="submit" value="add to cart" name="add_to_cart" class="btn">
      </form>
   <?php
      };
   };
   ?>

   </div>

</div>

<div class="shopping-cart">

   <h1 class="heading">shopping cart</h1>

   <table>
      <thead>
         <th>image</th>
         <th>name</th>
         <th>price</th>
         <th>quantity</th>
         <th>total price</th>
         <th>action</th>
      </thead>
      <tbody>
    <?php
    $cart_query = mysqli_query($connection, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('Query failed');
    $grand_total = 0;

    if (mysqli_num_rows($cart_query) > 0) {
        while ($fetch_cart = mysqli_fetch_assoc($cart_query)) {
            $price = isset($fetch_cart['price']) ? floatval($fetch_cart['price']) : 0.0;
            $quantity = isset($fetch_cart['quantity']) ? intval($fetch_cart['quantity']) : 0;
            $sub_total = $price * $quantity;
            $grand_total += $sub_total;
    ?>
            <tr>
                <td><img src="images/<?php echo $fetch_cart['image']; ?>" height="100" alt=""></td>
                <td><?php echo $fetch_cart['name']; ?></td>
                <td>$<?php echo number_format($price, 2); ?>/-</td>
                <td>
                    <form action="" method="post">
                        <input type="hidden" name="cart_id" value="<?php echo $fetch_cart['id']; ?>">
                        <input type="number" min="1" name="cart_quantity" value="<?php echo $quantity; ?>">
                        <input type="submit" name="update_cart" value="Update" class="option-btn">
                    </form>
                </td>
                <td>$<?php echo number_format($sub_total, 2); ?>/-</td>
                <td><a href="index.php?remove=<?php echo $fetch_cart['id']; ?>" class="delete-btn" onclick="return confirm('Remove item from cart?');">Remove</a></td>
            </tr>
    <?php
        }
    } else {
        echo '<tr><td style="padding:20px; text-transform:capitalize;" colspan="6">No items added</td></tr>';
    }
    ?>
    <tr class="table-bottom">
        <td colspan="4">Grand Total:</td>
        <td>$<?php echo number_format($grand_total, 2); ?>/-</td>
        <td><a href="index.php?delete_all" onclick="return confirm('Delete all from cart?');" class="delete-btn <?php echo ($grand_total > 1) ? '' : 'disabled'; ?>">Delete All</a></td>
    </tr>
</tbody>

   </table>

   <div class="cart-btn">  
      <a href="#" class="btn <?php echo ($grand_total > 1)?'':'disabled'; ?>">proceed to checkout</a>
   </div>

</div>

</div>

</body>
</html>