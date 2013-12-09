<?php
namespace Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;

use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;

class BlogController implements ControllerProviderInterface
{
    protected $app;

    public function connect(Application $app)
    {
        $this->app = $app;
        $controller_collection = $app['controllers_factory'];
        $controller_collection->get('/', array($this, 'index'))->bind('blog_index')->value('lang', 'fr');

        return $controller_collection;
    }

    public function index()
    {
        $posts = $this->app['pomm.connection']
            ->getMapFor('')
            ->findLastShortPosts();

        return $this->app["twig"]->render(sprintf("blog/list_%s.html.twig", $this->getLang()), [ "posts" => $posts ]);
    }
}

