<!DOCTYPE html>
<html lang="en">
<?php
    session_start();
    if(isset($_SESSION['user_id'])){
    header("location:index.php");
    exit;
}
?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        * {
            box-sizing: border-box;
        }
        .nav .links a:hover , button:hover{
            background-color:#8000ff ;
        }
        body {
            display: flex;
            flex-direction: column;
            align-content: center;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            height: 100px;
            padding: 20px;
            background-color: #607d8b4a;
        }

        .nav {
            display: flex;
            flex-direction: row;
            align-content: center;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            height: 100px;
            padding: 10px;
            margin: 0;
        }

        .nav .links {
            width: 15%;
            display: flex;
            justify-content: space-around;
        }

        .nav .links a {
            color: white;
            padding: 4px 10px;
            background-color: #03a9f47a;
            text-decoration: none;
            border-radius: 4px;
            transition: all 1s;
        }

        .input {
            outline: none;
            border: none;
            width: 300px;
            height: 45px;
            border-radius: 10px;
            padding: 10px;
        }

        .form {
            width: 430px;
            height: 425px;
            display: flex;
            flex-direction: column;
            align-items: center;
            align-content: center;
            justify-content: space-between;
            background-color: rgba(255, 255, 255, 0.423);
            backdrop-filter: blur(30px);
            padding: 30px;
            -webkit-filter-blur: 10%;
            border-radius: 30px;
        }

        form button {
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            color: white;
            background-color: #03a9f47a;
            transition: all 1s;
        }

        form span a {
            text-decoration: none;
            color: black;
            font-weight: bold;
        }

        .remember {
            width: 135px;
            height: 32px;
            display: flex;
            flex-direction: row;
            align-content: center;
            justify-content: space-around;
            align-items: center;
        }
        .wrong{
            color: red;
        }
    </style>
</head>

<body>
    <div class="nav">
        <div class="links">
            <!-- <a href="Login.php">Log in</a> -->
            <!-- <a href="Register.php">Register</a> -->
        </div>
    </div>
    <div>
    <?php
    require_once 'inc/errors.php';
    require_once 'inc/security.php';
    ?>

        <form class="form" action="handle/registerA.php" method="post">
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            
            <h3>Register Here</h3>
            <input placeholder="Enter Name" class="input" type="text" name="name" id="" value="<?php if(isset($_SESSION['name'])) echo htmlspecialchars($_SESSION['name'], ENT_QUOTES, 'UTF-8')  ?>" required>
            <input placeholder="Enter Email" class="input" type="email" name="email" id="" value="<?php if(isset($_SESSION['email'])) echo htmlspecialchars($_SESSION['email'], ENT_QUOTES, 'UTF-8')  ?>" required>
            <input class="input" placeholder="Enter Password" type="password" name="password" id="" required minlength="6">
            <input class="input" placeholder="Enter your phone " type="tel" name="phone" id="" value="<?php if(isset($_SESSION['phone'])) echo htmlspecialchars($_SESSION['phone'], ENT_QUOTES, 'UTF-8')  ?>" required pattern="[0-9]{10,15}">
            <button type="submit" name="submit">Register</button>
            <a href="Login.php">login</a>

        </form>
    </div>
</body>

</html>