<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

$config['socket_type'] = 'tcp'; //`tcp` or `unix`
$config['socket'] = '/var/run/redis.sock'; // in case of `unix` socket type
$config['host'] = '192.168.86.50';
// $config['host'] = '192.168.53.99';
$config['password'] = 'business_card';
$config['port'] = 6379;
$config['isopen']   = true;
$config['timeout'] = 0;