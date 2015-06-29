<?php
/**
 * Created by PhpStorm.
 * User: sify
 * Date: 15-6-29
 * Time: 上午4:49
 */
class DecodeUtils
{
    //163邮箱Subject: = ?GBK?B?...?=
    static public function decode163Subject($subject){
        $s=imap_mime_header_decode($subject)[0]->text;
        return iconv("GB2312","UTF-8//IGNORE",$s);
    }
}