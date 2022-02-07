<?php
class MY_Input extends CI_Input {
function _clean_input_keys($str, $fatal = true)
{
if ( ! preg_match("/^[a-z0-9:_\/-]+$/i", $str) )
{
$str = preg_replace("/^[^a-z0-9:_\/-]+$/i", '', $str);
}
// Clean UTF-8 if supported
if (UTF8_ENABLED === TRUE)
{
$str = $this->uni->clean_string($str);
}
return $str;
}
}