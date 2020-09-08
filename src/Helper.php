<?php
namespace TymFrontiers\User\Helper;

function no_ref_qstring() {
  $ret = [];
  $url_parse = \parse_url(THIS_PAGE);
  if (!empty($url_parse["query"])) {
    \parse_str($url_parse["query"], $qs);
    if (isset($qs["referer"])) unset($qs["referer"]);
    $ret = $qs;
  }
  return $ret;
}
function register_field_include (array $fields, string $field_name) {
  return \in_array($field_name, $fields);
}
function register_field_requre (array $fields, string $field_name) {
  return \in_array($field_name, $fields);
}
