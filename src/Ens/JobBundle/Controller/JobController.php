<?php

namespace Ens\JobBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Ens\JobBundle\Entity\Job;
use Ens\JobBundle\Form\JobType;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;

/**
 * Job controller.
 *
 */
class JobController extends Controller
{
    /**
     * Lists all Job entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository('EnsJobBundle:Category')->getWithJobs();

        foreach($categories as $category){
            $category->setActiveJobs($em->getRepository('EnsJobBundle:Job')->getActiveJobs($category->getId()));
            $category->setMoreJobs(
                            $em->getRepository('EnsJobBundle:Job')->countActiveJobs($category->getId())
                            -
                            $this->container->getParameter('max_jobs_on_homepage')
                        );
        }

        return $this->render('EnsJobBundle:Job:index.html.twig',array(
                                                                        'categories' => $categories
                            ));
    }

    /**
     * Creates a new Job entity.
     *
     */
    public function newAction(Request $request)
    {
        $job = new Job();
        $form = $this->createForm('Ens\JobBundle\Form\JobType', $job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($job);
            $em->flush();

            return $this->redirectToRoute('ens_job_preview', array(
                                                                  'company' => $job->getCompanySlug(),
                                                                  'location' => $job->getLocationSlug(),
                                                                  'token' => $job->getToken(),
                                                                  'position' => $job->getPositionSlug()
                                                            ));
        }

        return $this->render('EnsJobBundle::job/new.html.twig', array(
            'job' => $job,
            'form' => $form->createView(),
        ));
    }

    public function previewAction(Request $request, $token)
    {
        $form = $this->createPublishForm($token);
        $form->handleRequest($request);
     
        $em  = $this->getDoctrine()->getManager();
        $job = $em->getRepository('EnsJobBundle:Job')->findOneByToken($token);


        if (!$job) 
        {
            throw $this->createNotFoundException('Unable to find Job entity.');
        }
        
        $deleteForm = $this->createDeleteForm($job->getId());
        
        
        return $this->render('EnsJobBundle:Job:show.html.twig', array(
                                                           'job' => $job,
                                                           'delete_form' => $deleteForm->createView(),
                                                            ));
    }

    /**
     * Finds and displays a Job entity.
     *
     */
    public function showAction($id)
    {
        if($id == NULL){
            return $this->redirectToRoute('ens_job_index');
        }
        
        $rep = $this->getDoctrine()->getRepository('EnsJobBundle:Job');
        $job = $rep->getActiveJob($id);

        if(!$job){
            throw $this->createNotFoundException('Unable to find Job entity.');
        }

        $deleteForm = $this->createDeleteForm($job->getToken());

        return $this->render('EnsJobBundle::job/show.html.twig', array(
            'job' => $job,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Job entity.
     *
     */
    public function editAction(Request $request, $token)
    {
        $em  = $this->getDoctrine()->getManager();
        $job = $em->getRepository('EnsJobBundle:Job')->findOneByToken($token);

        if(!$job){
            throw $this->createNotFoundException('Unable to find Job entity.');
        } 


        $editForm = $this->createForm(new JobType(), $job);
        $deleteForm = $this->createDeleteForm($token);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            
            $em->persist($job);
            $em->flush();

            return $this->redirectToRoute('ens_job_edit', array('token' => $token));
        }

        return $this->render('job/edit.html.twig', array(
            'job' => $job,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }


    public function updateAction($token)
    {
      $em = $this->getDoctrine()->getEntityManager();
     
      $entity = $em->getRepository('EnsJobBundle:Job')->findOneByToken($token);
     
      if (!$entity) {
        throw $this->createNotFoundException('Unable to find Job entity.');
      }
     
      $editForm   = $this->createForm(new JobType(), $entity);
      $deleteForm = $this->createDeleteForm($token);
     
      $request = $this->getRequest();
     
      $editForm->bindRequest($request);
     
      if ($editForm->isValid()) {
        $em->persist($entity);
        $em->flush();
     
        return $this->redirect($this->generateUrl('ens_job_edit', array('token' => $token)));
      }
     
      return $this->render('EnsJobeetBundle:Job:edit.html.twig', array(
        'entity'      => $entity,
        'edit_form'   => $editForm->createView(),
        'delete_form' => $deleteForm->createView(),
      ));
    }
    
    /**
     * Deletes a Job entity.
     *
     */
    public function deleteAction($token)
    {
      $form = $this->createDeleteForm($token);
      $request = $this->getRequest();
     
      $form->bindRequest($request);
     
      if ($form->isValid()) {
        $em = $this->getDoctrine()->getEntityManager();
        $entity = $em->getRepository('EnsJobeetBundle:Job')->findOneByToken($token);
     
        if (!$entity) {
          throw $this->createNotFoundException('Unable to find Job entity.');
        }
     
        $em->remove($entity);
        $em->flush();
      }
     
      return $this->redirect($this->generateUrl('ens_job'));
    }
    
    /**
     * Creates a form to delete a Job entity.
     *
     * @param String $token The token of job
     *
     * @return \Symfony\Component\Form\Form The form
     */ 
    private function createDeleteForm($token)
    {
      return $this->createFormBuilder(array('token' => $token))
        ->add('token', HiddenType::class)
        ->getForm()
      ;
    }

    private function createPublishForm($token)
    {
      return $this->createFormBuilder(array('token' => $token))
        ->add('token', HiddenType::class)
        ->getForm();
    }

}
