<?php
$conn = mysqli_connect("localhost","root","","Blog");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
