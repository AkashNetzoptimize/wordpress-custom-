<form method="post" class="signup-form">
    Username:
    <input name="username" placeholder="username here" type="text"><br>
   <?php do_action('add_field') ?>
    Password:
    <input name="password1" placeholder="password here" type="password"><br>
    Confirm password:
    <input name="password2" placeholder="confirm password here" type="password"><br>
    <input name="signup" type="submit">
</form>

