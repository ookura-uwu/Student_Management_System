<?php
class Hash 
{
    public static function make($string, $salt = '') 
    {
        return hash('sha256', $string . $salt);
    }

    public static function salt($length) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ`-=~!@#$%^&*()_+,./<>?;:[]{}\|';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) 
        {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        
        return $randomString;

        // return openssl_random_pseudo_bytes($length);
    }

    public static function unique() 
    {
        return self::make(uniqid());
    }

    public static function code() 
    {
        return substr(md5(uniqid(rand(1,6))), 0, 8);
    }
}