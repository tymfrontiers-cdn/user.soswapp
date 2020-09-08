<?php
namespace TymFrontiers;
require_once "../.appinit.php";
\require_login();
$user = \SOS\User::profile($session->name,'id');
?>
<!DOCTYPE html>
<html lang="en" dir="ltr" manifest="/site.webmanifest">
  <head>
    <meta charset="utf-8">
    <title>Settings | <?php echo PRJ_TITLE; ?></title>
    <?php include PRJ_INC_ICONSET; ?>
    <meta name='viewport' content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0'>
    <meta name="robots" content='nofollow'>
    <!-- Theming styles -->
    <link rel="stylesheet" href="/app/soswapp/font-awesome.soswapp/css/font-awesome.min.css">
    <link rel="stylesheet" href="/app/soswapp/theme.soswapp/css/theme.min.css">
    <link rel="stylesheet" href="/app/soswapp/theme.soswapp/css/theme-<?php echo PRJ_THEME; ?>.min.css">
    <!-- optional plugin -->
    <link rel="stylesheet" href="/app/soswapp/plugin.soswapp/css/plugin.min.css">
    <link rel="stylesheet" href="/app/soswapp/dnav.soswapp/css/dnav.min.css">
    <link rel="stylesheet" href="/app/soswapp/faderbox.soswapp/css/faderbox.min.css">
    <link rel="stylesheet" href="/app/soswapp/jcrop.soswapp/css/jcrop.min.css">
    <!-- Project styling -->
    <link rel="stylesheet" href="<?php echo \html_style("base.min.css"); ?>">
  </head>
  <body>
    <?php \TymFrontiers\Helper\setup_page('user-setting','user', true); ?>
    <?php include PRJ_INC_HEADER; ?>
    <br class="c-f">
    <section id="main-content">
      <div class="view-space">

        <div class="grid-6-tablet grid-4-desktop push-left">
          <div class="sec-div color face-primary">
            <header class="padding -p20 color-text border -bthin -bbottom">
              <h2> <i class="fas fa-user-cog"></i> Account setting</h2>
            </header>
            <div class="padding -p20">
              <p>Set your account/login detail.</p>
              <ul style="list-style:none">
                <li class="padding -p5"> <a href="#" onclick="faderBox.url('/app/tymfrontiers-cdn/user.soswapp/service/set-alias.php',{},{exitBtn:true});"> <i class="fas fa-hashtag"></i> Account Alias</a> </li>

                <li class="padding -p5"> <a href="#" onclick="faderBox.url('/app/tymfrontiers-cdn/user.soswapp/service/set-password.php',{},{exitBtn:true});"> <i class="fas fa-key"></i> Password</a> </li>

                <li class="padding -p5"> <a href="#" onclick="faderBox.url('/app/tymfrontiers-cdn/user.soswapp/service/set-email.php',{},{exitBtn:true});"> <i class="fas fa-at"></i> Contact Email</a> </li>
                <li class="padding -p5"> <a href="#" onclick="faderBox.url('/app/tymfrontiers-cdn/user.soswapp/service/set-phone.php',{},{exitBtn:true});"> <i class="fas fa-phone"></i> Contact Phone number</a> </li>
              </ul>
            </div>
          </div>
        </div>

        <div class="grid-6-tablet grid-4-desktop push-left">
          <div class="sec-div color blue">
            <header class="padding -p20 border -bbottom -bthin">
              <h2 class="color-text"> <i class="fas fa-user-tie"></i> Profile setting</h2>
            </header>
            <div class="padding -p20">
              <p>Profile management: names, contact/mailing information.</p>
              <ul style="list-style:none">
                <li class="padding -p5"> <a href="#" class="blue" onclick="faderBox.url('/app/tymfrontiers-cdn/user.soswapp/service/set-profile.php',{},{exitBtn:true});"> <i class="fas fa-user-circle"></i> Profile &amp; mailing contact</a> </li>
              </ul>
            </div>
          </div>
        </div>

        <br class="c-f">
      </div>
    </section>
    <?php include PRJ_INC_FOOTER; ?>
    <!-- Required scripts -->
    <script src="/app/soswapp/jquery.soswapp/js/jquery.min.js">  </script>
    <script src="/app/soswapp/jcrop.soswapp/js/jcrop.min.js">  </script>
    <script src="/app/soswapp/js-generic.soswapp/js/js-generic.min.js">  </script>
    <script src="/app/soswapp/theme.soswapp/js/theme.min.js" ></script>
    <!-- optional plugins -->
    <script src="/app/soswapp/plugin.soswapp/js/plugin.min.js" ></script>
    <script src="/app/soswapp/dnav.soswapp/js/dnav.min.js" ></script>
    <script src="/app/soswapp/faderbox.soswapp/js/faderbox.min.js" ></script>
    <!-- project scripts -->
    <script src="<?php echo \html_script ("base.min.js"); ?>" ></script>
    <script src="/app/tymfrontiers-cdn/user.soswapp/js/user.min.js" ></script>
    <script type="text/javascript">
    </script>
  </body>
</html>
