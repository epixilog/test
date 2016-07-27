<?php

namespace Ens\JobBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('EnsJobBundle:Default:index.html.twig');
    }
}
