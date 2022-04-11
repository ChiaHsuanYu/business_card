<?php
class MY_Service
{
    public function __construct()
    {
        log_message('debug',"Service Class Initialize");
    }
    function __get($key)
    {
        $CI = & get_instance();
        return $CI->$key;
    }
}