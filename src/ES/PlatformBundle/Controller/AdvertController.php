<?php

namespace ES\PlatformBundle\Controller;

use ES\PlatformBundle\Entity\Advert;
use ES\PlatformBundle\Form\AdvertType;
use ES\PlatformBundle\Form\AdvertEditType;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class AdvertController extends Controller
{
	public function indexAction($page)
  {
    	if ($page < 1) {
      		throw new NotFoundHttpException('Page "'.$page.'" inexistante.');
    	}

      $nbPerPages = $this->container->getParameter('nb_per_page');

      $listAdverts = $this->getDoctrine()->getManager()->getRepository('ESPlatformBundle:Advert')->getAdverts($page, $nbPerPages);
      $nbPages = ceil(count($listAdverts)/$nbPerPages);

      if ($page > $nbPages) {
        throw $this->createNotFoundException("La page ".$page." n'existe pas.");
      }

	    return $this->render('ESPlatformBundle:Advert:index.html.twig', array(
        'listAdverts' => $listAdverts,
        'nbPages'     => $nbPages,
        'page'        => $page
        ));
	}


	public function viewAction($id)
  {
      $repository = $this->getDoctrine()->getManager()->getRepository('ESPlatformBundle:Advert');
      $advert = $repository->find($id);

      if(null === $advert) {
          throw new NotFoundHttpException("l'annonce n°".$id." n'existe pas.");
      }

      $this->denyAccessUnlessGranted('view', $advert);

      return $this->render('ESPlatformBundle:Advert:view.html.twig', array('advert' => $advert));
  }

  /**
   * @Security("has_role('ROLE_AUTEUR')")
   */
  public function addAction(Request $request)
  {
      $advert = new Advert();

      $form = $this->get('form.factory')->create(AdvertType::class, $advert);

      if ($request->isMethod('POST')) {
        $form->handleRequest($request);
        if ($form->isValid()){

          $advert->setAuthor($this->getUser());

          $em = $this->getDoctrine()->getManager();
          $em->persist($advert);
          $em->flush();

          $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

          return $this->redirectToRoute('es_platform_view', array('id' => $advert->getId()));
        }
      }

      return $this->render('ESPlatformBundle:Advert:add.html.twig', array('form' => $form->createView()));
  }

/**
 * @Security("has_role('ROLE_AUTEUR') or has_role('ROLE_MODERATEUR')")
 */
  public function editAction($id, Request $request)
  {
    	$em = $this->getDoctrine()->getManager();

      $advert = $em->getRepository('ESPlatformBundle:Advert')->find($id);

      if(null === $advert) {
         throw new NotFoundHttpException("l'annonce n°".$id." n'existe pas.");
      }
      $this->denyAccessUnlessGranted('edit', $advert);

      $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $advert);

      $form = $this->get('form.factory')->create(AdvertEditType::class, $advert);

      if ($request->isMethod('POST')) {
          $form->handleRequest($request);
          if ($form->isValid()){
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien modifiée.');

            return $this->redirectToRoute('es_platform_view', array('id' => $advert->getId()));
        }
      }

      return $this->render('ESPlatformBundle:Advert:edit.html.twig', array('form' => $form->createView(), 'advert' => $advert));
	}

/**
 * @Security("has_role('ROLE_AUTEUR') or has_role('ROLE_MODERATEUR')")
 */
  public function deleteAction($id, Request $request)
  {
      $em = $this->getDoctrine()->getManager();
      $advert = $em->getRepository('ESPlatformBundle:Advert')->find($id);
      if (null === $advert) {
          throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
      }

      $this->denyAccessUnlessGranted('edit', $advert);

      $form = $this->get('form.factory')->create();

      if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) 
      {
        $em->remove($advert);
        $em->flush();

        $request->getSession()->getFlashBag()->add('info', "L'annonce a bien été supprimée.");

        return $this->redirectToRoute('es_platform_home');
      }

    	return $this->render('ESPlatformBundle:Advert:delete.html.twig', array('advert' => $advert, 'form'   => $form->createView()));
  }

  public function menuAction($limit)
  {
      $listAdverts = $this->getDoctrine()->getManager()->getRepository('ESPlatformBundle:Advert')->findBy(
          array('published' => 1),
          array('date' => 'desc'),
          $limit,
          0
        );

      return $this->render('ESPlatformBundle:Advert:menu.html.twig', array('listAdverts' => $listAdverts));
  }

  public function selfadvertsAction()
  {
      $listAdverts = $this->getDoctrine()->getManager()->getRepository('ESPlatformBundle:Advert')->findBy(
          array('author' => $this->getUser()),
          array('date' => 'desc')
        );

      return $this->render('ESPlatformBundle:Advert:selfadverts.html.twig', array(
        'listAdverts' => $listAdverts,
        ));
  }

}

?>
