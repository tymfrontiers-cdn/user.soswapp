<?php
namespace TymFrontiers;
require_once "../.appinit.php";
require_once APP_ROOT . "/src/Helper.php";
$gen = new Generic;
$data = new Data;
$params = $gen->requestParam([
  "rdt" => ["rdt","url"]
],'get',[]);
if ($session->isLoggedIn()) {
  $rdt = empty($params["rdt"])
    ? "/app/user"
    : $params["rdt"];
  HTTP\Header::redirect($rdt);
}
$img_idx = [1,2, 3, 4];
?>
<!DOCTYPE html>
<html lang="en" dir="ltr" manifest="/site.webmanifest">
  <head>
    <meta charset="utf-8">
    <title>Account Login | <?php echo PRJ_TITLE; ?></title>
    <?php include PRJ_INC_ICONSET; ?>
    <meta name='viewport' content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0'>
    <meta name="author" content="<?php echo PRJ_AUTHOR; ?>">
    <meta name="creator" content="<?php echo PRJ_CREATOR; ?>">
    <meta name="publisher" content="<?php echo PRJ_PUBLISHER; ?>">
    <meta name="robots" content='nofollow'>
    <!-- Theming styles -->
    <link rel="stylesheet" href="/app/soswapp/font-awesome.soswapp/css/font-awesome.min.css">
    <link rel="stylesheet" href="/app/soswapp/theme.soswapp/css/theme.min.css">
    <link rel="stylesheet" href="/app/soswapp/theme.soswapp/css/theme-<?php echo PRJ_THEME; ?>.min.css">
    <!-- optional plugin -->
    <link rel="stylesheet" href="/app/soswapp/plugin.soswapp/css/plugin.min.css">
    <link rel="stylesheet" href="/app/soswapp/dnav.soswapp/css/dnav.min.css">
    <link rel="stylesheet" href="/app/soswapp/faderbox.soswapp/css/faderbox.min.css">
    <!-- Project styling -->
    <link rel="stylesheet" href="<?php echo \html_style("base.css"); ?>">
    <link rel="stylesheet" href="/app/tymfrontiers-cdn/user.soswapp/css/user.min.css">
  </head>
  <body>
    <?php \TymFrontiers\Helper\setup_page("user-signin", "user", true, PRJ_HEADER_HEIGHT); ?>
    <?php include PRJ_INC_HEADER; ?>
    <div id="usrbg-covr">
      <img src="/app/tymfrontiers-cdn/user.soswapp/img/usr-login-bg<?php echo $img_idx[\array_rand($img_idx)]; ?>-phone.jpg" class="show-phone">
      <img src="/app/tymfrontiers-cdn/user.soswapp/img/usr-login-bg<?php echo $img_idx[\array_rand($img_idx)]; ?>-tablet.jpg" class="show-tablet">
      <img src="/app/tymfrontiers-cdn/user.soswapp/img/usr-login-bg<?php echo $img_idx[\array_rand($img_idx)]; ?>-laptop.jpg" class="show-laptop">
      <img src="/app/tymfrontiers-cdn/user.soswapp/img/usr-login-bg<?php echo $img_idx[\array_rand($img_idx)]; ?>-desktop.jpg" class="show-desktop">
    </div>
    <section id="main-content">
      <div class="view-space">
        <div class="grid-5-tablet grid-6-laptop grid-7-desktop"></div>
        <div class="grid-11-phone grid-7-tablet grid-6-laptop grid-5-desktop center-phone uncenter-tablet">
          <div class="sec-div color face-primary bg-white drop-shadow">
            <header class="padding -p20 border -bmedium -bbottom">
              <span class="fa-stack fa-2x push-right color-text">
                <i class="fas fa-stack-2x fa-circle"></i>
                <i class="fas fa-stack-1x fa-sign-in-alt fa-inverse"></i>
              </span>
              <h1 class="fw-lighter">Sign in to continue</h1>
            </header>
            <div class="padding -p20">
              <form
                id="long-form"
                class="block-ui"
                method="post"
                action="/app/tymfrontiers-cdn/user.soswapp/src/SignIn.php"
                data-validate="false"
                onsubmit="sos.form.submit(this, signIn); return false;"
              >
              <input type="hidden" name="rdt" value="<?php echo !empty($params['rdt']) ? $params['rdt'] : ''; ?>">
                <input type="hidden" name="CSRF_token" value="<?php echo $session->createCSRFtoken('long-form'); ?>">
                <input type="hidden" name="form" value="long-form">

                <div class="grid-12-tablet">
                  <label for="email"><i class="fas fa-asterisk fa-border fa-sm"></i> Email address</label>
                  <input type="email" name="email" autocomplete="email" id="email" placeholder="myname@website.com" required>
                </div>
                <div class="grid-12-tablet">
                  <label for="password"><i class="fas fa-asterisk fa-border fa-sm"></i> Password</label>
                  <input type="password" name="password" autocomplete="off" placeholder="Password" required id="password">
                </div>
                <div class="grid-12-tablet">
                  <input type="checkbox" class="solid" name="remember" value="1" id="remember">
                  <label for="remember" class="bold color-text">Remember me</label>
                </div>
                <div class="grid-8-phone">
                  <button type="submit" id="rsd-click" class="sos-btn face-primary">Sign in <i class="fas fa-sign-in-alt"></i></button>
                </div>

                <br class="c-f">
              </form>
            </div>
            <div class="padding -p20 align-c border -bthin -btop">
              I want to  <a href="<?php echo Generic::setGet("/app/user/password-reset", ["rdt"=>$params['rdt']]) ?>"> <i class="fas fa-sync-alt fa-sm"></i> Reset my password</a>
              <br> <br>
              I don't have an account | <a href="<?php echo Generic::setGet("/app/user/sign-up",["rdt"=>$params['rdt']]) ?>"> <i class="fas fa-plus"></i> Sign up now</a>

            </div>
          </div>
          <p class="align-c padding -p20">
          </p>
        </div>
        <br class="c-f">
      </div>
    </section>
    <?php include PRJ_INC_FOOTER; ?>
    <!-- Required scripts -->
    <script src="/app/soswapp/jquery.soswapp/js/jquery.min.js">  </script>
    <script src="/app/soswapp/js-generic.soswapp/js/js-generic.min.js">  </script>
    <script src="/app/soswapp/theme.soswapp/js/theme.min.js"></script>
    <!-- optional plugins -->
    <script src="/app/soswapp/plugin.soswapp/js/plugin.min.js"></script>
    <script src="/app/soswapp/dnav.soswapp/js/dnav.min.js"></script>
    <script src="/app/soswapp/faderbox.soswapp/js/faderbox.min.js"></script>
    <!-- project scripts -->
    <script src="<?php echo \html_script ("base.min.js"); ?>"></script>
    <script src="/app/tymfrontiers-cdn/user.soswapp/js/user.min.js"></script>
    <script type="text/javascript">
      $("#res-cnt-view").hide();
    </script>
  </body>
</html>
