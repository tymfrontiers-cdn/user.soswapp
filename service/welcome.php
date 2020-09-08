<?php
namespace TymFrontiers;
use \Michelf\Markdown;
?>
<!DOCTYPE html>
<html lang="en" dir="ltr" manifest="/site.webmanifest">
  <head>
    <meta charset="utf-8">
    <title>User Portal | <?php echo PRJ_TITLE; ?></title>
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
  </head>
  <body>
    <?php \TymFrontiers\Helper\setup_page("user", "user", true, PRJ_HEADER_HEIGHT); ?>
    <?php include PRJ_INC_HEADER; ?>

    <section id="main-content">
      <div class="view-space">
        <div class="grid-7-tablet">
          <div class="sec-div padding -p20">
            <?php if (\file_exists(PRJ_ROOT . "/src/prj-user-notice.md")) {
              echo Markdown::defaultTransform(\file_get_contents(PRJ_ROOT . "/src/prj-user-notice.md"));
            } ?>
            <br class="c-f">
            <p class="align-c">
              <a href="<?php echo WHOST . "/user/sign-up"; ?>" class="sos-btn green"> <i class="fas fa-plus"></i> Sign up now</a>
              <a href="<?php echo WHOST . "/user/sign-in"; ?>" class="sos-btn blue"> <i class="fas fa-sign-in-alt"></i> Sign in</a>
            </p>
          </div>
        </div>
        <div class="grid-5-tablet">
          <div class="sec-div padding -p20">
            <h3> <i class="fas fa-link fa-lg"></i> Useful links</h3>
            <p>Quick links to get you started with your account.</p>
            <ul style="list-style:none" class="color grey">
              <li class="padding -p10 border -bthin -bbottom">
                <a href="/app/user/register"> <i class="fas fa-plus"></i> Create an account</a>
              </li>
              <li class="padding -p10 border -bthin -bbottom">
                <a href="/app/user/login"> <i class="fas fa-sign-in-alt"></i> Sign-in</a>
              </li>
              <li class="padding -p10 border -bthin -bbottom">
                <i class="fas fa-key"></i> Forgot your login detail? <a href="/app/user/login/reset">Reset now</a>
              </li>
            </ul>
          </div>
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
  </body>
</html>
