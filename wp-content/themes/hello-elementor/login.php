<?php
/* Template Name: logIn */
// get_header(); 


if (is_user_logged_in()) {
   //  echo "<div class='info'>You are already logged in.</div>";
     wp_redirect(home_url());
    exit;
} else {
    // If the user is not logged in, display the login form
    ?>
    <link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/style.css">
    <!-- form starts from here -->
    <form method="post" class="login-form">
        Email:
        <input name="email" placeholder="Enter your email" type="email"><br>
        Password:
        <input name="password" placeholder="Enter your password" type="password"><br>
        <input name="login" type="submit" value="Login">
    </form>
    <?php
}

if (isset($_POST['login'])) {
    $email = sanitize_email($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        echo "<div class='error'>Please fill in all fields.</div>";
    } else {
        $user = custom_authenticate_user($email, $password);

        if ($user) {
            // Set a session variable to indicate user is logged in
            $_SESSION['logged_in'] = true;
        
            // Check if user is an administrator
            if (user_can($user->ID, 'administrator')) {
               
                wp_set_auth_cookie($user->ID); 
                wp_redirect(admin_url()); 
            
            } else {
                
                wp_redirect(home_url());
          
            }
        } else {
            echo "<div class='error'>Login failed. Please check your credentials.</div>";
        }
    }
}

// // Logout functionality
if (isset($_GET['logout'])) {

    unset($_SESSION['logged_in']);

    wp_redirect(home_url('/login'));
    exit;
}

function custom_authenticate_user($email, $password)
{
    global $wpdb;

    $user = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->users} WHERE user_email = %s", $email));
    if ($user) {
        if (wp_check_password($password, $user->user_pass, $user->ID)) {
            return $user;
        }
    }

    return false;
}
?>
