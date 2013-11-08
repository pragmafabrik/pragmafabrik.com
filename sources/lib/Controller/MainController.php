<?php
namespace Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;

use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;

class MainController implements ControllerProviderInterface
{
    protected $app;

    public function connect(Application $app)
    {
        $this->app = $app;
        $controller_collection = $app['controllers_factory'];
        $controller_collection->get('/', array($this, 'index'))->bind('index');

        return $controller_collection;
    }

    public function index()
    {
        $this->app['pomm.connection']
            ->getMapFor('\Model\Pragmafabrik\Pragmafabrik\News')
            ->findAll();

        return $this->app['twig']->render('index.html.twig');
    }
}

