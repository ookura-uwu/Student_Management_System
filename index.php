<?php

require_once 'core/init.php';
$user = new User(); 

if ($user->isLoggedIn()) 
{
    if ($user->hasPermission('Administrator')) 
    {
        Redirect::to('./administrator');
    } 
    else if ($user->hasPermission('Instructor')) 
    {
        Redirect::to('./instructor');
    } 
    else 
    {
        Redirect::to('home.php');
    }
}

if (Input::exists()) 
{
    if (Token::check(Input::get('token'))) 
    {

        $validate = new Validate();
        $validation = $validate->check($_POST, array(
            'username' => array('name' => 'Username', 'required' => true),
            'password' => array('name' => 'Password', 'required' => true)
        ));

        if ($validate->passed())
        {
            $remember = true;

            $login = $user->login(Input::get('username'), Input::get('password'), $remember);

            if ($login) 
            {
                if ($user->hasPermission('Administrator')) 
                {
                    Redirect::to('./administrator');
                } 
                else if ($user->hasPermission('Instructor')) 
                {
                    Redirect::to('./instructor');
                }
                else
                {
                    Redirect::to('home.php');
                }
            }
            else 
            {
                $error = '<div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i>
                            Invalid Username/Password
                          </div>';

                Session::flash('result', $error);
                Redirect::to('./');
            }
        } 
        else 
        {
            foreach($validate->errors() as $error) 
            {
                $errors = $error .'<br>';
            }

            Session::flash('result', "<div class='alert alert-danger'>{$errors}</div>");
        }
    }
}

?>
<?php include 'header.php'; ?>
<body>
    <div id="progress" class="waiting">
        <dt></dt>
        <dd></dd>
    </div>
    <nav class="navbar navbar-expand-md text-light" style="background-color: #21561e">
        <div class="container">
            <a class="navbar-brand" href="#" id="text-logo">
                Student Management System 
            </a>
        </div>
    </nav>

    <div class="container" id="_content" style="display: none;">
        <div class="jumbotron" style="background: white !important">
            <h4 class="display-4 text-center">Login</h4>
            <hr>
            <form action="" method="post">
                <div class="row">
                    <div class="col-lg-5 mx-auto">
                        <?php (Session::exists('result') ? print Session::flash('result') : '') ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-5 mx-auto">
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Username" name="username" value="<?php echo Input::get('username') ?>" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-5 mx-auto">
                        <div class="form-group">
                            <input type="password" class="form-control" placeholder="Password" name="password" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-5 mx-auto">
                        <div class="form-group">
                            <input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
                            <button type="submit" class="btn btn-primary btn-block" name="login">Login</button>
                        </div>
                    </div>
                </div>
            </form>

            <a role="button" id="refresh" class="float text-light" onclick="window.location.reload()">
                <i class="fas fa-redo-alt icon-float"></i>
            </a>
        </div>
    </div>

    <?php include 'jsplugins.php'; ?>
</body>
</html>