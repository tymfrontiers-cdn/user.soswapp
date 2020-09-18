<?php
namespace TymFrontiers;
use \SOS\User,
    \TymFrontiers\User\Helper as UH;
require_once "../.appinit.php";
require_once APP_ROOT . "/src/Helper.php";

$gen = new Generic;
$data = new Data;
if (!empty($_GET['referer'])) $_GET['referer'] = \str_replace(["-","."],"",$_GET['referer']);
$params = $gen->requestParam([
  "email" => ["email","email"],
  "name" => ["name","name"],
  "surname" => ["surname","name"],
  "rdt" => ["rdt","url"],
  "rmck" => ["rmck","boolean"],
  "referer" => ["referer","username",3,12,[],"MIXED", ["-"]]
],'get',[]);
if ($session->isLoggedIn()) {
  $rdt = empty($params["rdt"])
    ? WHOST . "/app/user"
    : $params["rdt"];
  HTTP\Header::redirect($rdt);
}
$location = false;
try {
  $location = new Location();
} catch (\Exception $e) {
  // die($e->getMessage());
  $location = false;
}
if (!empty($params['referer'])) $params['referer'] = \strtoupper(\str_replace(["-","."], "", $params['referer']));
if ($params['rmck']) Helper\destroy_cookie("_TFUSRREF");
$ref = NULL;
if (!(bool)$params['rmck']) {
  $ref = !empty($params['referer']) ? $params['referer'] : (
    !empty($_COOKIE["_TFUSRREF"]) ? $data->decodeDecrypt($_COOKIE["_TFUSRREF"]) : NULL
  );
}
$referer = false;
if ($ref) {
  if ($referer = (new  MultiForm(MYSQL_BASE_DB, "user", "_id"))
  ->findBySql("SELECT up.name, up.surname, u._id AS id, u.email, u.phone
    FROM :db:.:tbl: AS u
    LEFT JOIN :db:.user_profile AS up ON up.user = u._id
    WHERE _id='{$database->escapeValue($ref)}'
    OR _id = (
      SELECT user
      FROM :db:.user_alias
      WHERE alias = '{$database->escapeValue($ref)}'
      LIMIT 1
    )
    LIMIT 1")) {
      // save in cookie
      $referer = $referer[0];
      $ref = $referer->id;
      if (!empty($params['referer'])) {
        Helper\destroy_cookie("_TFUSRREF");
        \setcookie("_TFUSRREF", $data->encodeEncrypt($ref), \strtotime("+10 Min"), "/");
      }
    }
}
$field_include = [];
if ($field_include = Helper\setting_get_value("SYSTEM", "USER.REGISTER-INCLUDE-FIELD", PRJ_BASE_DOMAIN)) $field_include = \explode(",",$field_include);
$field_required = [];
if ($field_required = Helper\setting_get_value("SYSTEM", "USER.REGISTER-REQUIRED-FIELD", PRJ_BASE_DOMAIN)) $field_required = \explode(",",$field_required);
if ($user_max_age = Helper\setting_get_value("SYSTEM", "USER.MAX-AGE", PRJ_BASE_DOMAIN)) {
  $user_max_age = (int)$user_max_age;
} else {
  $user_max_age = 85;
}
if ($user_min_age = Helper\setting_get_value("SYSTEM", "USER.MIN-AGE", PRJ_BASE_DOMAIN)) {
  $user_min_age = (int)$user_min_age;
} else {
  $user_min_age = 18;
}

$img_idx = [1, 2, 4, 4, 2, 1];
?>
<!DOCTYPE html>
<html lang="en" dir="ltr" manifest="/site.webmanifest">
  <head>
    <meta charset="utf-8">
    <title>Sign up | <?php echo PRJ_TITLE; ?></title>
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
    <?php \TymFrontiers\Helper\setup_page("user-signup", "user", true, PRJ_HEADER_HEIGHT); ?>
    <?php include PRJ_INC_HEADER; ?>
    <div id="usrbg-covr">
      <img src="/app/tymfrontiers-cdn/user.soswapp/img/usr-signup-bg<?php echo $img_idx[\array_rand($img_idx)]; ?>-phone.jpg" class="show-phone">
      <img src="/app/tymfrontiers-cdn/user.soswapp/img/usr-signup-bg<?php echo $img_idx[\array_rand($img_idx)]; ?>-tablet.jpg" class="show-tablet">
      <img src="/app/tymfrontiers-cdn/user.soswapp/img/usr-signup-bg<?php echo $img_idx[\array_rand($img_idx)]; ?>-laptop.jpg" class="show-laptop">
      <img src="/app/tymfrontiers-cdn/user.soswapp/img/usr-signup-bg<?php echo $img_idx[\array_rand($img_idx)]; ?>-desktop.jpg" class="show-desktop">
    </div>
    <section id="main-content">
      <div class="view-space">
        <div class="grid-5-laptop grid-6-desktop">
          <div class="sec-div padding -p20 border -bmedium -btop bg-light-trans color face-secondary">
            <a name="account-safety"></a>
            <p class="color-text">
              <span class="fa-stack fa-3x push-right">
                <i class="fas fa-stack-2x fa-circle"></i>
                <i class="fas fa-stack-1x fa-user-shield fa-inverse"></i>
              </span>
            </p>
            <h3>Account Safety and password tips</h3>
            <p>Your Account Safety starts with your password. Avoid using weak &amp; easily guessable combinations for password.</p>
            <h3>Here are some guide to help you create new password</h3>
            <ul>
              <li>Your password should contain at least one upper case character and lower case character</li>
              <li>Include at least one/more numeric value.</li>
              <li>Also include one/more of these special characters:[</span> <code style="color: red; font-weight:bold">!@#$%^&*()/-_=+{}[];:,<.></code> ]</li>
              <li>A strong should be between 8-24 character length.</li>
              <li>Avoid using your name, phone number, email, date of birth and other personal detail in password combination.</li>
            </ul>
            <p>Build the habit of using strong/random password every time. Remember it is easier to prevent security breach than recovering from it.</p>
            <!-- <p> <a href="#signup-form" onclick="$('a[name=signup-form]').scroll_view();">Back to form <i class="fas fa-arrow-up"></i></a></p> -->
          </div>
        </div>
        <div class="grid-8-tablet grid-7-laptop grid-6-desktop">
          <div class="sec-div color face-primary bg-white drop-shadow">
            <header class="color-bg padding -p20">
              <span class="fa-stack fa-2x push-right">
                <i class="far fa-stack-2x fa-circle"></i>
                <i class="fas fa-stack-1x fa-plus"></i>
              </span>
              <a name="signup-form"></a>
              <h1 class="fw-lighter"> Create your new account</h1>
              <?php if ($referer): ?>
                <p class="italic">
                  <i class="fas fa-info-circle"></i><b> You were referred by: </b> <br>
                  <?php echo "{$referer->name} {$referer->surname} (<i class=\"fas fa-envelope\"></i> ". Helper\email_mask($referer->email) . " | <i class=\"fas fa-phone\"></i> " . Helper\phone_mask($referer->phone); ?>) | <a href="<?php $get_r = UH\no_ref_qstring(); $get_r["rmck"] = 1; echo Generic::setGet("/app/user/sign-up", $get_r); ?>"> <i class="fas fa-question-circle"></i> Not my referer</a>
                </p>
              <?php endif; ?>
            </header>
            <div class="padding -p20">
              <p>Fill out fields and click <b>sign up</b>
                <!-- | <a href="#account-safety" onclick="$('a[name=account-safety]').scroll_view();"> <i class="fas fa-lightbulb"></i> Read the Account Safety Tips</a> -->
              </p>
              <blockquote>
                <p>Required fields are marked with (<b>*</b>) asterisk</p>
              </blockquote>
              <form
                class="block-ui color asphalt"
                id="user-signup-form"
                method="post"
                action="/app/tymfrontiers-cdn/user.soswapp/src/SignUp.php"
                data-validate="false"
                onsubmit="sos.form.submit(this,signUp); return false;"
              >
              <input type="hidden" name="referer" value="<?php echo $ref; ?>">
              <input type="hidden" name="rdt" value="<?php echo !empty($params['rdt']) ? $params['rdt'] : ''; ?>">
                <input type="hidden" name="CSRF_token" value="<?php echo $session->createCSRFtoken('user-signup-form'); ?>">
                <input type="hidden" name="form" value="user-signup-form">

                <div class="grid-6-tablet">
                  <label for="name" class="placeholder"><i class="fas fa-user-tag"></i> Name <i class="fas fa-asterisk fa-sm rq-tag color-red"></i></label>
                  <input type="text" name="name" value="<?php echo !empty($params['name']) ? $params['name'] : ''; ?>" placeholder="Given name" autocomplete="given-name" id="name" required>
                </div>
                <div class="grid-6-tablet">
                  <label class="placeholder" for="surname"><i class="fas fa-user-tag"></i> Surname <i class="fas fa-asterisk fa-sm rq-tag color-red"></i></label>
                  <input type="text" value="<?php echo !empty($params['surname']) ? $params['surname'] : ''; ?>" name="surname" autocomplete="family-name" placeholder="Family name" id="surname" required>
                </div>
                <?php if (UH\register_field_include($field_include, "middle_name")) { ?>
                  <br class="c-f">
                  <div class="grid-6-tablet">
                    <label class="placeholder" for="middle_name"> <i class="fas fa-user-tag"></i> Middle name <?php if (UH\register_field_requre($field_required, "middle_name")) echo '<i class="fas fa-asterisk fa-sm rq-tag color-red"></i>'; ?></label>
                    <input type="text" <?php if (UH\register_field_requre($field_required, "middle_name")) echo "required"; ?> name="middle_name" placeholder="Middle name" autocomplete="nickname" id="middle_name">
                  </div>
                <?php } ?>
                <?php if (UH\register_field_include($field_include, "dob")) { ?>
                  <br class="c-f">
                  <div class="grid-5-tablet">
                    <label class="placeholder shrink" for="dob"> <i class="fas fa-calendar-star"></i> Date of birth <?php if (UH\register_field_requre($field_required, "dob")) echo '<i class="fas fa-asterisk fa-sm rq-tag color-red"></i>'; ?></label>
                    <input
                      type="date"
                      name="dob"
                      id="dob"
                      autocomplete="bday"
                      <?php if (UH\register_field_requre($field_required, "dob")) echo "required"; ?>
                      max="<?php echo \strftime("%Y-%m-%d",\strtotime("- {$user_min_age} Years")); ?>"
                      min="<?php echo \strftime("%Y-%m-%d",\strtotime("- {$user_max_age} Years")); ?>"
                      >
                  </div>
                <?php } if (UH\register_field_include($field_include, "sex")) { ?>
                <div class="grid-7-tablet push-left">
                  <label class="shrink"><i class="fas fa-venus-mars"></i> Gender <?php if (UH\register_field_requre($field_required, "sex")) echo '<i class="fas fa-asterisk fa-sm rq-tag color-red"></i>'; ?></label> <br>
                  <input type="radio" name="sex" value="MALE" id="sex-MALE" checked>
                  <label for="sex-MALE">Male</label>
                  <input type="radio" name="sex" value="FEMALE" id="sex-FEMALE">
                  <label for="sex-FEMALE">Female</label>
                </div>
              <?php } ?>
                <br class="c-f"> <br>
                <div class="grid-6-tablet">
                  <label class="shrink"> <i class="fas fa-flag"></i> Country <i class="fas fa-asterisk fa-sm rq-tag color-red"></i></label>
                  <select name="country_code" required>
                    <option value="">Choose a country</option>
                  <?php if ($countries = (new MultiForm(MYSQL_DATA_DB, 'country', 'code'))->findAll() ) {
                      foreach ($countries as $country) {
                        echo " <option value='{$country->code}'";
                        echo ($location && ($location->country_code == $country->code))
                          ? " selected "
                          : "";
                        echo ">{$country->name}</option>";
                      }
                    }
                   ?>
                  </select>
                </div>
                <?php if (UH\register_field_include($field_include, "state_code")) { ?>
                <div class="grid-6-tablet">
                  <label class="shrink"> <i class="fas fa-map-marked"></i> State/province <?php if (UH\register_field_requre($field_required, "state_code")) echo '<i class="fas fa-asterisk fa-sm rq-tag color-red"></i>'; ?></label>
                  <select name="state_code" <?php if (UH\register_field_requre($field_required, "state_code")) echo "required"; ?>>
                    <option value="">Choose a state</option>
                    <optgroup label="States">
                      <?php if (($location && !empty($location->country_code)) && $states = (new MultiForm(MYSQL_DATA_DB, 'state', 'code'))->findBySql("SELECT * FROM :db:.:tbl: WHERE country_code='{$location->country_code}' ORDER BY  LOWER(`name`) = '-other', `name` ASC ") ) {
                        foreach ($states as $state) {
                          echo " <option value='{$state->code}'";
                          echo ($location && ($location->state_code == $state->code))
                          ? " selected "
                          : "";
                          echo ">{$state->name}</option>";
                        }
                      }
                      ?>
                    </optgroup>
                  </select>
                </div>
              <?php } ?>
                <?php if (UH\register_field_include($field_include, "city_code")) { ?>
                <div class="grid-6-tablet">
                  <label class="shrink"> <i class="fas fa-map-marker"></i> City <?php if (UH\register_field_requre($field_required, "city_code")) echo '<i class="fas fa-asterisk fa-sm rq-tag color-red"></i>'; ?></label>
                  <select name="city_code" <?php if (UH\register_field_requre($field_required, "city_code")) echo "required"; ?>>
                    <option value="">Choose a city</option>
                    <optgroup label="Cities">
                      <?php if (($location && !empty($location->state_code)) && $cities = (new MultiForm(MYSQL_DATA_DB, 'city', 'code'))->findBySql("SELECT * FROM :db:.:tbl: WHERE state_code='{$location->state_code}' ORDER BY  LOWER(`name`) = '-other', `name` ASC ") ) {
                        foreach ($cities as $city) {
                          echo " <option value='{$city->code}'";
                          echo ($location && ($location->city_code == $city->code))
                          ? " selected "
                          : "";
                          echo ">{$city->name}</option>";
                        }
                      }
                      ?>
                    </optgroup>
                  </select>
                </div>
              <?php } ?>
              <?php if (UH\register_field_include($field_include, "address")) { ?>
                <div class="grid-12-tablet">
                  <label class="placeholder" for="address"> <i class="fas fa-street-view"></i> Address: Street, House/Flat... <?php if (UH\register_field_requre($field_required, "address")) echo '<i class="fas fa-asterisk fa-sm rq-tag color-red"></i>'; ?></label>
                  <input placeholder="Input address here" type="text" <?php if (UH\register_field_requre($field_required, "address")) echo "required"; ?> name="address" autocomplete="street-address" id="address">
                </div>
              <?php } ?>
              <?php if (UH\register_field_include($field_include, "zip_code")) { ?>
                <div class="grid-4-tablet">
                  <label class="placeholder" for="zip_code"> <i class="fas fa-mailbox"></i> Zip code <?php if (UH\register_field_requre($field_required, "zip_code")) echo '<i class="fas fa-asterisk fa-sm rq-tag color-red"></i>'; ?></label>
                  <input type="text" placeholder="Zip" <?php if (UH\register_field_requre($field_required, "zip_code")) echo "required"; ?> name="zip_code" autocomplete="postal-code" id="zip_code">
                </div>
              <?php } ?>
                <br class="c-f"> <br>
                <div class="grid-7-tablet">
                  <label class="placeholder" for="email"> <i class="fas fa-envelope-open-text"></i> Email address <i class="fas fa-asterisk fa-sm rq-tag color-red"></i></label>
                  <input type="email" placeholder="email@mydomain.ext" required name="email" value="<?php echo !empty($params['email']) ? $params['email'] : ''; ?>" autocomplete="email" id="email">
                </div>
                <?php if (UH\register_field_include($field_include, "phone")) { ?>
                  <div class="grid-5-tablet">
                    <label class="placeholder" for="phone"> <i class="fas fa-phone-square"></i> Phone number <?php if (UH\register_field_requre($field_required, "phone")) echo '<i class="fas fa-asterisk fa-sm rq-tag color-red"></i>'; ?></label>
                    <input type="tel" placeholder="0801 234 5678" <?php if (UH\register_field_requre($field_required, "phone")) echo "required"; ?> name="phone" autocomplete="tel-local" id="phone">
                  </div>
                <?php } ?>
                <br class="c-f">
                <div class="grid-6-tablet">
                  <label class="placeholder" for="password"> <i class="fas fa-key"></i> New password <i class="fas fa-asterisk fa-sm rq-tag color-red"></i></label>
                  <input type="password" placeholder="Password" name="password" autocomplete="off" required id="password">
                </div>
                <div class="grid-6-tablet">
                  <label class="placeholder" for="password-repeat"><i class="fas fa-key"></i> Repeat password <i class="fas fa-asterisk fa-sm rq-tag color-red"></i></label>
                  <input type="password" name="password_repeat" placeholder="Password" autocomplete="off" required id="password-repeat">
                </div>

                <div class="grid-12-tablet">
                  <label><i class="fas fa-asterisk fa-border fa-sm"></i> Kindly read and accept <a href="/app/terms" target="_blank"> <b><i class="fas fa-link"></i> Applicable Terms &amp; Conditions</b></a></label> <br>
                  <input type="checkbox" class="solid" name="accepted_terms" value="1" id="accept-terms" required>
                  <label for="accept-terms" class="bold color-text">I have read and accepted terms.</label>
                </div>
                <div class="grid-8-phone grid-5-tablet">
                  <button type="submit" class="btn face-primary"> <i class="fas fa-check"></i> Sign up</button>
                </div>
                <br class="c-f">
              </form>
            </div>
            <div class="padding -p20 align-c border -bthin -btop">
              I have an account - <a href="<?php echo Generic::setGet("/app/user/sign-in",["rdt"=>$params['rdt']]) ?>"> <i class="fas fa-sign-in-alt"></i> Sign in</a>
            </div>
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
    <script src="/app/tymfrontiers-cdn/user.soswapp/js/user.min.js"></script>
  </body>
</html>
