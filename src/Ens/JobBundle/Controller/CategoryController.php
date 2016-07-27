<?php

namespace Ens\JobBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Ens\JobBundle\Entity\Job;
use Ens\JobBundle\Form\JobType;

/**
 * Category controller.
 *
 */
class CategoryController extends Controller
{

/**
     * Finds and displays a Category entity.
     *
     */
    public function showAction($slug, $page)
    {
    	$em = $this->getDoctrine()->getManager();
    	$category = $em->getRepository('EnsJobBundle:Category')->findOneBySlug($slug);

    	if(!$category){
    		throw $this->createNotFoundException('Unable to find Category '.$slug);
    	}

    	$total_jobs 	= $em->getRepository('EnsJobBundle:Job')->countActiveJobs($category->getId());
    	$jobs_per_page 	= $this->container->getParameter('max_jobs_on_category'); 
    	$last_page   	= ceil($total_jobs / $jobs_per_page);
    	$previous_page  = $page > 1 ? $page -1 : 1;
    	$next_page		= $page < $last_page ? $page + 1 : $last_page;

    	$category->setActiveJobs($em->getRepository('EnsJobBundle:Job')
    								->getActiveJobs(
    													$category->getId(),
    													$jobs_per_page,
    													($page -1) * $jobs_per_page
    												)
    							);

        return $this->render('EnsJobBundle:Category:show.html.twig', array(
            																'category' 		=> $category,
            																'page'			=> $page,
            																'last_page'		=> $last_page,
            																'previous_page' => $previous_page,
            																'next_page'		=> $next_page,
            																'total_jobs'	=> $total_jobs
        																)
        					);
    }

}