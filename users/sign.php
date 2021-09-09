<?php

require '../core/load.php';

if (isset($_POST['first-name']) && !empty($_POST['first-name'])) {
    $upFirst = $_POST['first-name'];
    $upLast = $_POST['last-name'];
    $upEmailMobile = $_POST['email-mobile'];
    $upPassword = $_POST['up-password'];
    $birthDay = $_POST['birth-day'];
    $birthMonth = $_POST['birth-month'];
    $birthYear = $_POST['birth-year'];
    if (!empty($_POST['gen'])) {
        $upgen = $_POST['gen'];
    }
    $birth = '' . $birthYear . '-' . $birthMonth . '-' . $birthDay . '';

    // if any form fields are empty, else security screen input
    if (empty($upFirst) or empty($upLast) or empty($upEmailMobile) or empty($upgen)) {
        $error = 'All fields are required';
    } else {
        $first_name = $loadFromUser->checkInput($upFirst);
        $last_name = $loadFromUser->checkInput($upLast);
        $email_mobile = $loadFromUser->checkInput($upEmailMobile);
        $password = $loadFromUser->checkInput($upPassword);
        // create screen-name with random link if name already existz / check if name exists in database
        $screenName = '' . $first_name . '_' . $last_name . '';
        if (DB::query('SELECT screenName FROM users WHERE screenName = :screenName', array(':screenName' => $screenName))) {
            $screenRand = rand();
            $userLink = '' . $screenName . '' . $screenRand . '';
        } else {
            $userLink = $screenName;
        }
        // regex email
        if (!preg_match("^[_a-z0-9-]+(\.[_a-z0-9]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^", $email_mobile)) {
            // regex phone (11 numberz)           
            if (!preg_match("^[0-9]{11}^", $email_mobile)) {
                $error = 'Email id or mobile number iz not correct. Please try again';
            } else {
                $mob = strlen((string)$email_mobile);

                if ($mob > 11 || $mob < 11) {
                    $error = 'Mobile number iz not valid';
                } else if (strlen($password) < 5 || strlen($password) >= 60) {
                    $error = 'Password iz not correct';
                } else {
                    if (DB::query(
                        'SELECT mobile FROM users WHERE mobile=:mobile',
                        array(':mobile' => $email_mobile)
                    )) {
                        $error = 'Mobile number iz already in use';
                    } else {
                        $user_id = $loadFromUser->create('users', array('users', array('first_name' => $first_name, 'last_name' => $last_name, 'mobile' => $email_mobile, 'password' => password_hash($password, PASSWORD_BCRYPT), 'screenName' => $screenName, 'userLink' => $userLink, 'birthday' => $birth, 'gender' => $upgen)));

                        $tstrong = true;
                        // bin2hex — Convert binary data into hexadecimal representation
                        // openssl_random_pseudo_bytes - Generates a string of pseudo-random bytes, with the number of bytes determined by the length parameter
                        $token = bin2hex(openssl_random_pseudo_bytes(64, $tstrong));
                        $loadFromUser->create('token', array('token' => $token, 'user_id' => $user_id));

                        // cookie name - value - expiration date(7 days) - server port of cookie, set to slash, cookie available within entire domain - domain name of cookie - wether or not cookie should be set for secure connection only, default is false - hhtp only, set to true, only accessible through http, and not a script, important for security
                        setcookie('FBID', $token, time() + 60 * 60 * 24 * 7, '/', NULL, NULL, true);

                        // direct user to next page after sign up form validates
                        header('Location: index.php');
                    }
                }
            }
        } else {
            if (!filter_var($email_mobile)) {
                $error = "Invalid Email Format";
            } else if (strlen($first_name) > 20) {
                $error = "Name must be between 2-20 characterz";
            } else if (strlen($password) < 5 && strlen($password) >= 60) {
                $error = "The password is either too short or too long";
            } else {
                if ((filter_var($email_mobile, FILTER_VALIDATE_EMAIL)) && $loadFromUser->checkEmail($email_mobile) === true) {
                    $error = "Email is already in use";
                } else {
                    $user_id = $loadFromUser->create('users', array('first_name' => $first_name, 'last_name' => $last_name, 'email' => $email_mobile, 'password' => password_hash($password, PASSWORD_BCRYPT), 'screenName' => $screenName, 'userLink' => $userLink, 'birthday' => $birth, 'gender' => $upgen));

                    $tstrong = true;
                    // bin2hex — Convert binary data into hexadecimal representation
                    // openssl_random_pseudo_bytes - Generates a string of pseudo-random bytes, with the number of bytes determined by the length parameter
                    $token = bin2hex(openssl_random_pseudo_bytes(64, $tstrong));
                    $loadFromUser->create('token', array('token' => $token, 'user_id' => $user_id));

                    // cookie name - value - expiration date(7 days) - server port of cookie, set to slash, cookie available within entire domain - domain name of cookie - wether or not cookie should be set for secure connection only, default is false - hhtp only, set to true, only accessible through http, and not a script, important for security
                    setcookie('FBID', $token, time() + 60 * 60 * 24 * 7, '/', NULL, NULL, true);

                    // direct user to next page after sign up form validates
                    header('Location: index.php');
                }
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <title>test</title>
</head>

<body>
    <div class="header">
        <div class="logo">Fakebook</div>
        <form action="sign.php" method="post">
            <div class="mobile_input">
                <div class="input-text">Email or phone</div>
                <input type="text" name="in-email-mobile" id="email-mobile" class="input-text-field">
            </div>
            <div class="passord-input">
                <div class="password-input">
                    <div>Password</div>
                    <input type="password" name="in-pass" id="in-password" class="input-text-field">
                    <div class="forgotten-acc">Forgotten account</div>
                </div>
                <div class="login-button">
                    <input type="submit" value="Log in" class="sign-in login">
                </div>
            </div>
        </form>
    </div>

    <div class="main">
        <div class="left-side">
            <img src="../assets/image/Signin-image.png" class="img-test" alt="signin image">
        </div>
        <div class="right-side">
            <div class="error">
                <?php if (!empty($error)) {
                    echo $error;
                } ?>
            </div>
            <h1 class="heading-primary">Create account</h1>
            <div class="heading-secondary">It's free and always will be</div>
            <form action="sign.php" method="post" name="user-sign-up">
                <div class="sign-up-form">
                    <div class="sign-up-name">
                        <input type="text" name="first-name" id="first-name" class="text-field" placeholder="First Name">
                        <input type="text" name="last-name" id="last-name" placeholder="Last Name" class="text-field">
                    </div>
                    <div class="sign-wrap-mobile">
                        <input type="text" name="email-mobile" id="up-email" placeholder="Mobile number or email address" class="text-input">
                    </div>
                    <div class="sign-up-password">
                        <input type="password" name="up-password" id="up-password" class="text-input">
                    </div>
                    <div class="sign-up-birthday">
                        <div class="bday">Birthday</div>
                        <div class="form-birthday">
                            <select name="birth-day" id="days" class="select-body"></select>
                            <select name="birth-month" id="months" class="select-body"></select>
                            <select name="birth-year" id="years" class="select-body"></select>
                        </div>
                    </div>
                    <div class="gender-wrap">
                        <input type="radio" name="gen" id="fem" value="female" class="m0">
                        <label for="fem" class="gender">Female</label>
                        <input type="radio" name="gen" id="male" value="male" class="m0">
                        <label for="male" class="gender">Male</label>
                    </div>
                    <div class="term">
                        By clicking Sign Up, you agree to our termz, Data policy & cookie policy.
                    </div>
                    <input type="submit" value="Sign Up" class="sign-up">
                </div> <!-- / sign-up-form  -->
            </form>
        </div>
    </div>
    <script src="../assets/js/jquery-3-6-0-min.js"></script>
    <script src="../assets/js/main.js"></script>
</body>

</html>