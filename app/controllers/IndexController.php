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
	}