<?php 
    require APPPATH . '/libraries/JWT.php';

    class CreatorJwt
    {
       

        /*************This function generate token private key**************/ 

        PRIVATE $key = "13aa537c-2963-49e9-83d3-d81224f661a8"; 
        public function GenerateToken($data)
        {          
            $jwt = JWT::encode($data, $this->key);
            return $jwt;
        }
        

       /*************This function DecodeToken token **************/

        public function DecodeToken($token)
        {          
            $decoded = JWT::decode($token, $this->key, array('HS256'));
            $decodedData = (array) $decoded;
            return $decodedData;
        }
    }
