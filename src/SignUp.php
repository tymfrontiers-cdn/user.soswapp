<?php
namespace TymFrontiers;
use \Michelf\Markdown,
    \Mailgun\Mailgun,
    \SOS\User;
require_once "../.appinit.php";
require_once APP_ROOT . "/src/Helper.php";

$data = new Data;
\header("Content-Type: application/json");
$post = \json_decode( \file_get_contents('php://input'), true); // json data
$post = !empty($post) ? $post : (
  !empty($_POST) ? $_POST : []
);
$gen = new Generic;
$auth = new API\Authentication ($api_sign_patterns);
$http_auth = $auth->validApp ();
if( !$http_auth && ( empty($post['form']) || empty($post['CSRF_token']) ) ){
  HTTP\Header::unauthorized (false,'', Generic::authErrors ($auth,"Request [Auth-App]: Authetication failed.",'self',true));
}
if( !empty($post['phone']) || !empty($post['country_code']) ){
  $post['phone'] = $data->phoneToIntl(\trim($post['phone']),\trim($post['country_code']));
}
$required = ["name", "surname", "email", "country_code", "state_code", "password", "CSRF_token", "form"];
if ($field_required = Helper\setting_get_value("SYSTEM", "USER.REGISTER-REQUIRED-FIELD", PRJ_BASE_DOMAIN)) {
  foreach (\explode(",",$field_required) as $field) {
    if (\in_array($field, $required)) $required[] = $field;
  }
}
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
if ($unilevel_dept = Helper\setting_get_value("SYSTEM", "NET.UNILEVEL-DEPT", PRJ_BASE_DOMAIN)) {
  $unilevel_dept = (int)$unilevel_dept;
} else {
  $unilevel_dept = UNILEVEL_MAX_DEPT;
}

$params = $gen->requestParam(
  [
    "referer" =>["referer","username",3,12],
    "name" =>["name","name"],
    "surname" =>["surname","name"],
    "middle_name" =>["middle_name","name"],
    "email" =>["email","email"],
    "phone" =>["phone","tel"],
    "sex" =>["sex","option", ["MALE", "FEMALE"]],
    "dob" =>[
      "dob",
      "date",
      \strftime("%Y-%m-%d",\strtotime("- {$user_max_age} Years")),
      \strftime("%Y-%m-%d",\strtotime("- {$user_min_age} Years"))
    ],

    "country_code" =>["country_code","username",2,2],
    "state_code" =>["state_code","username",5,8],
    "city_code" =>["city_code","username",8,12],
    "zip_code" =>["zip_code","text",3,16],
    "address" =>["address","text",5,128],

    "password" =>["password","text",6,32],
    "password_repeat" =>["password_repeat","text",6,32],
    "accepted_terms" =>["accepted_terms","boolean"],

    "rdt" => ["rdt","url"],
    "form" => ["form","text",2,55],
    "CSRF_token" => ["CSRF_token","text",5,500]
  ],
  $post,  $required);
if (!$params || !empty($gen->errors)) {
  $errors = (new InstanceError($gen,true))->get("requestParam",true);
  echo \json_encode([
    "status" => "3." . \count($errors),
    "errors" => $errors,
    "message" => "Request failed",
    "rdt" => ""
  ]);
  exit;
}

if( !$http_auth ){
  if ( !$gen->checkCSRF($params["form"],$params["CSRF_token"]) ) {
    $errors = (new InstanceError($gen,true))->get("checkCSRF",true);
    echo \json_encode([
      "status" => "3." . \count($errors),
      "errors" => $errors,
      "message" => "Request failed.",
      "rdt" => ""
    ]);
    exit;
  }
}
//
// echo " <tt> <pre>";
// print_r($params);
// echo "</pre></tt>";
// exit;
if( $params['password'] !== $params['password_repeat'] ){
  echo \json_encode([
    "status" => "3.1",
    "errors" => ["[password]: does not match [password_repeat]"],
    "message" => "Request halted.",
    "rdt" => ""
  ]);
  exit;
}
if( !(bool)$params['accepted_terms'] ){
  echo \json_encode([
    "status" => "3.1",
    "errors" => ["You must accept Terms and Conditions"],
    "message" => "Request halted.",
    "rdt" => ""
  ]);
  exit;
}
if( User::valExist($params['email'],"email") ){
  echo \json_encode([
    "status" => "3.1",
    "errors" => ["Email: [{$params['email']}] is not available"],
    "message" => "Request halted.",
    "rdt" => ""
  ]);
  exit;
}
if (!empty($params['phone'])) {
  if( User::valExist($params['phone'],'phone') ){
    echo \json_encode([
      "status" => "3.1",
      "errors" => ["Phone: [{$params['phone']}] is not available"],
      "message" => "Request halted.",
      "rdt" => ""
    ]);
    exit;
  }
}
$referer = false;
if (!empty($params['referer'])) {
  if( !$referer = User::findBySql("SELECT u._id AS id, u.email, up.name, up.surname  FROM :db:.:tbl: AS u LEFT JOIN :db:.user_profile AS up ON up.user = u._id WHERE u._id='{$params['referer']}' AND u.status NOT IN ('BANNED', 'DISABLED') LIMIT 1") ){
    echo \json_encode([
      "status" => "3.1",
      "errors" => ["No valid account found for [referer]!"],
      "message" => "Request halted.",
      "rdt" => ""
    ]);
    exit;
  }
  $referer = $referer[0];
}

// create the record
$country_code = $params['country_code'];
$rdt = $params['rdt'];

unset($params['form']);
unset($params['CSRF_token']);
unset($params['password_repeat']);
unset($params['rdt']);
unset($params['accepted_terms']);

// welcome email
$otp_code = Data::uniqueRand('', 12, Data::RAND_MIXED, false);
$otp_ref = Data::uniqueRand('', 16, Data::RAND_MIXED_LOWER);
$otp_qid = NULL;
// $otp_expiry = NULL;
$otp_expiry = \strtotime("+1 Month", \time());
$otp_expiry = \strftime("%Y-%m-%d %H:%M:%S",$otp_expiry);

$auth_param = [
  "user" => $data->encodeEncrypt($params['email']),
  "token" => $data->encodeEncrypt($otp_code),
  "reference" => $data->encodeEncrypt($otp_ref),
  "rdt" => $rdt,
];
$whost = WHOST;
$auth_link = Generic::setGet(WHOST . "/app/tymfrontiers-cdn/user.soswapp/service/verify-email.php", $auth_param);
$subject = "Welcome to " . PRJ_TITLE;
$prj_title = PRJ_TITLE;
$prj_icon = \defined("PRJ_EMAIL_ICON") ? PRJ_EMAIL_ICON : PRJ_ICON_150X150;
$prj_icon = WHOST .  $prj_icon;
$prj_color_primary = PRJ_PRIMARY_COLOUR;
// OTP

$verify_msg = <<<VMSG
<h3>You need to verify your email</h3>
<p>Please follow the link below to verify your email for a smooth communication experience.</p>
<p>
  <a href="{$auth_link}" style="font-weight:bold; display:inline-block; padding:12px 15px; background-color:#e4e4e4; color:black; border:solid 3px #cbcbcb; text-decoration:none; -webkit-border-radius:5px; -ms-border-radius:5px; -moz-border-radius:5px; border-radius:5px;">
    Verify your email
  </a>
</p>
VMSG;
if (!\file_exists(PRJ_ROOT . "/src/prj-user-welcome.md")) {
$message = <<<WELCOME
<html lang="en" dir="ltr">
<body>
  <header style="border-bottom: solid 5px {$prj_color_primary}; padding: 12px; margin-bottom: 8px;">
    <p style="text-align:right; margin: 5px 0;">
      <a href="{$whost}/app/user">
        <img style="max-width:85%; max-height:72px" src="{$prj_icon}" alt="Logo" />
      </a>
    </p>
    <h1 style="margin: 5px 0;">{$subject}</h1>
  </header>
  <section>
    <p>Hi {$params['name']}, <br> <br> We are excited to welcome you onboard.
    {$verify_msg}
    <p>
      You can learn more about us at <a href="{$whost}">{$whost}</a>,
      <br /> If your require any help; don't hesitate to <a href="{$whost}/app/support">visit our support</a> page.
    </p>
    <p>Have a wonderful time.</p>
  </section>
</body>
WELCOME;
} else {
  $message = "<html lang=\"en\" dir=\"ltr\"> <body>";
  $message .= Markdown::defaultTransform(\file_get_contents(PRJ_ROOT . "/src/prj-user-welcome.md"));
  $message .= $verify_msg;
  $message .= "</body></html>";
}
$message_text = "Hi {$params['name']}, Welcome to {$prj_title} \r\n Kindly follow link: {$auth_link} to verify your email.";
// send message right away
if (empty($mailgun_api_key) || empty($mailgun_api_domain)) {
  echo \json_encode([
    "status" => "5.1",
    "errors" => ["Please contact developer to create Mailgun API and assign \$mailgun_api_domain and \$mailgun_api_key respectively."],
    "message" => "Variable error.",
    "rdt" => ""
  ]);
  exit;
}
try {
  $mgClient = Mailgun::create($mailgun_api_key);
  $result = $mgClient->messages()->send($mailgun_api_domain, [
    'from' => PRJ_SUPPORT_EMAIL,
    'to' => "{$params['name']} {$params['surname']} <{$params['email']}>",
    'subject' => $subject,
    'text' => $message_text,
    'html' => $message
  ]);
  if(
    \is_object($result) &&
    !empty($result->getId()) &&
    \strpos($result->getId(), $mailgun_api_domain) !== false
  ){
    $otp_qid = $result->getId();
  }
} catch (\Exception $e) {
  echo \json_encode([
    "status" => "5.2",
    "errors" => ["We were unable to email you, confirm that you have entered a correct receiving email and try again.", $e->getMessage()],
    "message" => "Communication error.",
    "rdt" => ""
  ]);
  exit;
}
// save detail
$otp = new MultiForm(MYSQL_LOG_DB, 'otp_email', 'id');
$otp->ref = $otp_ref;
$otp->user = $params['email'];
$otp->qid = $otp_qid;
$otp->code = $otp_code;
$otp->expiry = $otp_expiry;
$otp->subject = $subject;
$otp->message = $message;
$otp->message_text = $message_text;
$otp->sender = PRJ_SUPPORT_EMAIL;
$otp->receiver = "{$params['name']} {$params['surname']} <{$params['email']}>";
if (!$otp->create()) {
  if (!empty($otp->errors['query'])) {
    $errs = (new InstanceError($otp, true))->get("query",true);
    echo \json_encode([
      "status" => "4." . \count($errs),
      "errors" => $errs,
      "message" => "Communication error.",
      "rdt" => ""
    ]);
    exit;
  }
}
// register user
$user = new User($params);
if ( empty($user->id()) ) {
  $do_errors = [];
  $user->mergeErrors();
  $more_errors = (new InstanceError($user))->get('',true);
  if (!empty($more_errors)) {
    foreach ($more_errors as $method=>$errs) {
      foreach ($errs as $err){
        $do_errors[] = $err;
      }
    }
    echo \json_encode([
      "status" => "4." . \count($do_errors),
      "errors" => $do_errors,
      "message" => "Failed to create account.",
      "rdt" => ""
    ]);
    exit;
  } else {
    echo \json_encode([
      "status" => "4.1",
      "errors" => ["Failed to create account, try again later."],
      "message" => "Request failed",
      "rdt" => ""
    ]);
    exit;
  }
}
// delete ref cookie
\TymFrontiers\Helper\destroy_cookie("_TFUSRREF");
$tym = new BetaTym;
// save referer
if ($referer) {
  // create new referer record
  $base_db = MYSQL_BASE_DB;
  if (
    $database->query("INSERT INTO `{$base_db}`.user_referer (`user`, `parent`, `level`) VALUES ('{$user->id()}', '{$referer->id}', 1)")
    && $database->query("INSERT INTO `{$base_db}`.user_follower (`user`, `follower`) VALUES ('{$referer->id}', '{$user->id()}')")
  ) {
    // send followership notice
    $country_name = ($country_name = (new MultiForm(MYSQL_DATA_DB, "country", "code"))->findById($user->country_code)) ? $country_name->name : NULL;
    $reg_datetym = \strftime(BetaTym::MYSQL_DATETYM_STRING, \time());
    $f_subject = "You have a new follower";
$f_message = <<<FMSG
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8" />
    <style type="text/css">
      table th{ font-weight:bold; padding:8px; text-align:left; }
      table td{ padding:8px; }
      table tr{ border-bottom: solid 1px grey; }
      table tr:nth-child(odd){ background-color:#cbcbcb; }
    </style>
  </head>
  <body style="max-width:580px; padding:12px; background-color:#e4e4e4; color:black;">
    <header style="border-bottom: solid 5px {$prj_color_primary}; padding: 12px; margin-bottom: 8px;">
      <p style="margin: 5 0; text-align:right;">
        <a href="{$whost}/app/user">
          <img style="max-width:40%; max-height:72px; margin:0 0 3px 3px;" src="{$prj_icon}" alt="Logo" />
        </a>
      </p>
      <h1 style="margin: 5px 0">New follower notice</h1>
    </header>
    <p> Hello {$referer->name}</p>
    <p>A new member is now following you on our platform, the following are the user's details</p>
    <table>
      <tr>
        <th>Name</th>
        <td>{$user->name} {$user->surname}</td>
      </tr>
      <tr>
        <th>Email</th>
        <td>{$user->email}</td>
      </tr>
      <tr>
        <th>Phone</th>
        <td>{$data->phoneToLocal($user->phone)}</td>
      </tr>
      <tr>
        <th>Country/Region</th>
        <td>{$user->country_code} - {$country_name}</td>
      </tr>
      <tr>
        <th>Joined</th>
        <td>{$tym->dateTym($reg_datetym)}</td>
      </tr>
    </table>
    <p>Kindly follow up with your new downline to ensure maximal collective benefit.</p>
    <hr />
    <p style="font-size:0.75em; text-align:center"> <a href="{$whost}/app/newsletter/unsubscribe">Unsubscribe</a> to this notification</p>
  </body>
</html>
FMSG;
    $f_message_text = "{$user->name} {$user->surname} - {$user->email} is now following you";
    // queue message
    $f_queue = new \SOS\EMailer([
      "sender" => PRJ_SUPPORT_EMAIL,
      "receiver" => "{$referer->name} {$referer->surname} <{$referer->email}>",
      "subject" => $f_subject,
      "msg_html" => $f_message,
      "msg_text" => "$f_message_text",
    ],3);
    if (!$f_queue->queue(3)) {
      $do_errors = [];
      $f_queue->mergeErrors();
      $more_errors = (new InstanceError($f_queue, true))->get('',true);
      if (!empty($more_errors)) {
        foreach ($more_errors as $method=>$errs) {
          foreach ($errs as $err){
            $do_errors[] = $err;
          }
        }
        echo \json_encode([
          "status" => "4." . \count($do_errors),
          "errors" => $do_errors,
          "message" => "Failed to complete request.",
          "rdt" => ""
        ]);
        exit;
      } else {
        echo \json_encode([
          "status" => "4.1",
          "errors" => ["Failed to queue/send referal alert."],
          "message" => "Request incomplete",
          "rdt" => ""
        ]);
        exit;
      }
    }
  } else {
    echo \json_encode([
      "status" => "3.1",
      "errors" => ["Failed to update network referal, contact admin."],
      "message" => "Request incomplete",
      "rdt" => ""
    ]);
    exit;
  }
}
$user = User::authenticate($params['email'],$params["password"],$country_code);
if( !$user ){
  echo \json_encode([
    "status" => "3.1",
    "errors" => ["Automatic login failed, contact Admin."],
    "message" => "Login failed",
    "rdt" => ""
  ]);
  exit;
}
$remember = \strtotime("+ 1 Hour");
$session->login($user,$remember);
$rdt = empty($rdt)
  ? WHOST . "/app/user"
  : $rdt;
$rdt = Generic::setGet( WHOST . "/app/tymfrontiers-cdn/user.soswapp/service/email-verification.php", ["rdt" => $rdt, "reference"=>$data->encodeEncrypt($otp_ref)]);
echo \json_encode([
  "status" => "0.0",
  "errors" => [],
  "message" => "Your account was created, you are now logged in.",
  "rdt" => $rdt
]);
exit;
