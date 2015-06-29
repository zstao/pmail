<?php
    class IndexController
        extends Base
    {

        public function initialize()
        {
            Phalcon\Tag::setTitle('Index');
            parent::initialize();
        }

        public function indexAction()
        {
        }

        public function helloAction()
        {
            require __DIR__.'/../vendor/autoload.php';
            require __DIR__ . '/DecodeUtils.php';
            $server = new \Fetch\Server('pop.163.com',110,'pop3');
            $server->setAuthentication('softpioneers@163.com', 'npcxcizgalswoafa');

            $messages = $server->getMessages();
            foreach ($messages as $message) {
                $title=DecodeUtils::decode163Subject($message->getSubject());
                echo "Subject: {$title}<br/>Body: {$message->getMessageBody(true)}<br/>";
            }
        }
    }