<?php

namespace ES\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Form\Extension\Core\Type\FormType;

class UserController extends Controller
{
	public function indexAction()
	{
		$listUsers = $this->getDoctrine()->getManager()->getRepository('ESUserBundle:User')->FindAll();
		return $this->render('ESUserBundle:User:index.html.twig', array('listUsers' => $listUsers));
	}

	public function viewAction($id)
	{
		$repository = $this->getDoctrine()->getManager()->getRepository('ESUserBundle:User');
		$user = $repository->find($id);

		if(null === $user) {
			throw new NotFoundHttpException("L'utilisateur n°".$id." n'existe pas.");
		}

		return $this->render('ESUserBundle:User:view.html.twig', array('user' => $user));
	}

	public function becomeMembreAction($id, Request $request)
  	{
		$em = $this->getDoctrine()->getManager();

		$user = $em->getRepository('ESUserBundle:User')->find($id);

		if(null === $user) {
		 throw new NotFoundHttpException("l'utilisateur n°".$id." n'existe pas.");
		}

		$user->addRole("ROLE_MEMBRE");
		$em->flush();

		$referer = $request->headers->get('referer');
    	if ($referer == NULL) {
	        $url = $this->router->generate('fallback_url');
	    } else {
	        $url = $referer;
	    }
	    return $this->redirect($url);
	}

	public function becomeAuthorAction($id, Request $request)
  	{
		$em = $this->getDoctrine()->getManager();

		$user = $em->getRepository('ESUserBundle:User')->find($id);

		if(null === $user) {
		 throw new NotFoundHttpException("l'utilisateur n°".$id." n'existe pas.");
		}

		$user->addRole("ROLE_AUTEUR");
		$em->flush();

		$referer = $request->headers->get('referer');
    	if ($referer == NULL) {
	        $url = $this->router->generate('fallback_url');
	    } else {
	        $url = $referer;
	    }
	    return $this->redirect($url);
	}

	public function deleteMembreAction($id, Request $request)
  	{
		$em = $this->getDoctrine()->getManager();

		$user = $em->getRepository('ESUserBundle:User')->find($id);

		if(null === $user) {
		 throw new NotFoundHttpException("l'utilisateur n°".$id." n'existe pas.");
		}

		$user->removeRole("ROLE_MEMBRE");
		$em->flush();

		$referer = $request->headers->get('referer');
    	if ($referer == NULL) {
	        $url = $this->router->generate('fallback_url');
	    } else {
	        $url = $referer;
	    }
	    return $this->redirect($url);
	}

	public function deleteAuthorAction($id, Request $request)
  	{
		$em = $this->getDoctrine()->getManager();

		$user = $em->getRepository('ESUserBundle:User')->find($id);

		if(null === $user) {
		 throw new NotFoundHttpException("l'utilisateur n°".$id." n'existe pas.");
		}

		$user->removeRole("ROLE_AUTEUR");
		$em->flush();

		$referer = $request->headers->get('referer');
    	if ($referer == NULL) {
	        $url = $this->router->generate('fallback_url');
	    } else {
	        $url = $referer;
	    }
	    return $this->redirect($url);
	}
}