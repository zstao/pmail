<?php
    /**
     * Created by PhpStorm.
     * User: XYN
     * Date: 10/14/14
     * Time: 11:40 AM
     */
    use Phalcon\Events\Event, Phalcon\Mvc\User\Plugin, Phalcon\Mvc\Dispatcher, Phalcon\Acl;

    class Security
        extends Plugin
    {
        public function __construct($dependencyInjector)
        {
            $this->_dependencyInjector = $dependencyInjector;
        }

        public function getAcl()
        {
            if (!isset($this->persistent->acl))
            {
                $acl = new Phalcon\Acl\Adapter\Memory();
                $acl->setDefaultAction(Phalcon\Acl::DENY);
                //Register roles
                $roles = array(
                    'guests'   => new Phalcon\Acl\Role('Guests'));
                foreach ($roles as $role)
                {
                    $acl->addRole($role);
                }
                //Public area resources
                $publicResources = array(
                     'index' => array('*'),
					 'admin' => array('login')
                );
                foreach ($publicResources as $resource => $actions)
                {
                    $acl->addResource(new Phalcon\Acl\Resource($resource), $actions);
                }
                //Grant access to public areas to both users and guests
                foreach ($roles as $role)
                {
                    foreach ($publicResources as $resource => $actions)
                    {
						foreach ($actions as $action)
						{
							$acl->allow($role->getName(), $resource, $action);
						}
                    }
                }
				
                $this->persistent->acl = $acl;
            }

            return $this->persistent->acl;
        }

        public function beforeDispatch(Event $event, Dispatcher $dispatcher)
        {
            $role = $this->session->get("role");
            if ($role == null )
            {
                $role = "Guests";
            }
            $controller = $dispatcher->getControllerName();
            $action = $dispatcher->getActionName();
            $acl = $this->getAcl();
            $allowed = $acl->isAllowed($role, $controller, $action);
            if ($allowed != Acl::ALLOW)
            {
                $dispatcher->forward(array('controller' => 'admin',
                                           'action' => 'login'));
                return false;
            }
        }
    }