<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MyUtils
 *
 * @author sergeykargopolov
 */
class MyUtils {
 
    static function generateTokenString($length = 10) {
      return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
    }
 
}

?>
