<?php

$connection     = mysqli_connect('localhost:4306','root','','database');

if(mysqli_connect_errno()){
    echo "Error in connection ".mysqli_connect_errno();
}

?>