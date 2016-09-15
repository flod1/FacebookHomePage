<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

return array(
    'router' => array(
        'router_class' => 'Zend\Mvc\Router\Http\TranslatorAwareTreeRouteStack',
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'application' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/application',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
            'graphnode' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/graph[/:id]',
                    'defaults' => array(
                        'controller' => 'graph_controller',
                        'action'     => 'node',
                    ),
                ),
            ),
            'graphedge' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/graph/[:id]/[:edge]',
                    'defaults' => array(
                        'controller' => 'graph_controller',
                        'action'     => 'edge',
                    ),
                ),
            ),
            'examples' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/examples',
                    'defaults' => array(
                        'controller' => 'example_controller',
                        'action'     => 'index',
                    ),
                ),
            ),
            'widgets' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/widgets',
                    'defaults' => array(
                        'controller' => 'example_controller',
                        'action'     => 'widgets',
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'factories' => array(
            'translator' => 'Zend\Mvc\Service\TranslatorServiceFactory',
            //Navigtion
            'navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
        ),
        //'aliases' => array(
        //    'logger' => 'jhu.zdt_logger',
        //),
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'example_controller' => 'Application\Controller\ExampleController',
            'Application\Controller\Index' => Controller\IndexController::class
        ),
        'factories' => array(
            'graph_controller' => 'Application\Factory\Controller\GraphControllerFactory'
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),
    // Navigation
    'navigation' => array(
        'default' => array(
            array(
                'label' => 'Dashboard',
                'route' => 'fbpage_dashboard',
            ),
            array(
                'label' => 'Widgets',
                'route' => 'widgets',
            ),
            array(
                'label' => 'Examples',
                'route' => 'examples',
            ),
            array(
                'label' => 'Albums',
                'route' => 'fbpage_albums',
                'pages' => array(
                    array(
                        'label' => 'Album',
                        'route' => 'fbpage_album',
                    ),
                )
            ),
            array(
                'label' => 'Events',
                'route' => 'fbpage_events',
                'pages' => array(
                    array(
                        'label' => 'Event',
                        'route' => 'fbpage_event',
                    ),
                )
            ),
            array(
                'label' => 'Posts',
                'route' => 'fbpage_posts',
                'pages' => array(
                    array(
                        'label' => 'Post',
                        'route' => 'fbpage_post',
                    ),
                )
            ),
        )
    )
);
