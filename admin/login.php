<?php
include("section/login-db.php");
include_once('include/a-header.php');
?>
    <div class="login-div">
    <h2 class="login-h2">Admin Login</h2>
    <form class="admin-log" method="POST">
        <label class="user-name"><i class="fa-solid fa-user"></i><b>User Name<br></b></label>
        <input type="text" name="username" placeholder="Username" required><br><br>
        <label class="admin-pass"><i class="fa-solid fa-lock"></i></i><b>Password<br></b></label> 
        <input type="password" class="admin-pass" name="password" placeholder="Password" required><br><br>
        <button class="login-btn" type="submit">Login</button>
        <?php if (isset($error)) echo "<p style='color:red'>$error</p>"; ?>
        <p style="margin-top:16px; text-align:center;">
            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal"
               style="color:#4d7cf0; font-size:13px;">Forgot password?</a>
        </p>
    </form>
    </div>
</body>

</html>   