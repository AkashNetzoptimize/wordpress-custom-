<?php
/* Template name: signup */
get_header();
?>

<?php echo do_shortcode('[signup_form]'); ?>

<?php
if (isset($_POST['signup'])) {
    global $wpdb;

    $user = sanitize_text_field($_POST['username']);
    $email = sanitize_email($_POST['email']);
    $pass = $_POST['password1'];
    $pass1 = $_POST['password2'];

    $error = array();

    if (empty($user) || empty($email) || empty($pass) || empty($pass1)) {
        $error['empty'] = "Please fill in all fields.";
    }

    //  username exists
    $user_query = $wpdb->prepare("SELECT ID FROM {$wpdb->users} WHERE user_login = %s", $user);
    $user_id = $wpdb->get_var($user_query);
    if ($user_id) {
        $error['username'] = "Username already exists.";
    }

    // email exists
    $email_query = $wpdb->prepare("SELECT ID FROM {$wpdb->users} WHERE user_email = %s", $email);
    $email_id = $wpdb->get_var($email_query);
    if ($email_id) {
        $error['email'] = do_action('confirm_email');
    }

    if ($pass !== $pass1) {
        $error['password'] = "Passwords do not match.";
    }

    if (empty($error)) {
        $hashed_password = wp_hash_password($pass);
        $wpdb->insert(
            $wpdb->users,
            array(
                'user_login' => $user, 'user_pass' => $hashed_password, 'user_email' => $email,
            )
        );

        if ($wpdb->insert_id) {
            echo '<div class="thank-you-message">';
            do_action('signup_form'); 
            echo '</div>';
        } else {
            echo "<h3>Error creating user</h3>";
        }
        } else {
            foreach ($error as $err) {
                echo "<h3>$err</h3>";
            }
        }
        
    }

get_footer();
?>

