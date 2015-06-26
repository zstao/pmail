<?php
/**
 * 所有控制器的基类，负责公用任务处理
 */
class Base extends \Phalcon\Mvc\Controller
{
    public function initialize()
    {
        Phalcon\Tag::prependTitle('Hello | ');
    }
}

