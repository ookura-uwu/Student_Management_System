<?php

require_once 'core/init.php';
$user = new User();

if (!Session::exists('username') && !Session::exists('password')) 
{
    Redirect::to('index.php');
}

if (Input::exists()) 
{
    if (Token::check(Input::get('token'))) 
    {
        if ($user->loginStudent(Input::get('code'))) 
        {
            Redirect::to('home.php');
        } 
        else 
        {
            Session::flash('verification_result', '<div class="alert alert-danger">Invalid Verification Code</div>');
            Redirect::to('verify_login.php');
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
            <a class="navbar-brand" href="./" id="text-logo">
                <img src="img/logo.png" height="50"> Laguna Science and Technology College
            </a>
        </div>
    </nav>

    <div class="container" id="_content" style="display: none;">
        
        <div class="jumbotron" style="background: white !important">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <?php (Session::exists('result') ? print Session::flash('result') : '') ?>
                </div>
            </div>
            <h4 class="display-4 text-center">Verify Login</h4>
            <hr>
            <form action="" method="post">
                <div class="row">
                    <div class="col-lg-8 mx-auto">
                        <?php (Session::exists('verification_result') ? print Session::flash('verification_result') : '') ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-5 mx-auto">
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Verification Code" name="code" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-5 mx-auto">
                        <div class="form-group">
                            <input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
                            <button type="submit" class="btn btn-primary btn-block" name="verify">Verify</button>
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