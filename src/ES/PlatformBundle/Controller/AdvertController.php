<?php

namespace ES\PlatformBundle\Controller;

use ES\PlatformBundle\Entity\Advert;
use ES\PlatformBundle\Form\AdvertType;
use ES\PlatformBundle\Form\AdvertEditType;
use ES\FileBundle\Entity\File;
use ES\FileBundle\Form\MyfileType;
use ES\FileBundle\Entity\Directory;
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
      $nbPages = ($nbPages == 0 ? 1 : $nbPages);

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
          array('projectAccepted' => 1),
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

  public function acceptAction()
  {
      $listAdverts = $this->getDoctrine()->getManager()->getRepository('ESPlatformBundle:Advert')->findBy(
          array('projectAccepted' => 0),
          array('date' => 'desc')
        );

      return $this->render('ESPlatformBundle:Advert:accept.html.twig', array(
        'listAdverts' => $listAdverts,
        ));
  }

  public function acceptDescriptionAction($id, Request $request)
  {
    $em = $this->getDoctrine()->getManager();

    $advert = $em->getRepository('ESPlatformBundle:Advert')->find($id);

    if(null === $advert) {
      throw new NotFoundHttpException("L'annonce n°".$id." n'existe pas.");
    }

    $advert->setDescriptionAccepted(1);
    $em->flush();

    $referer = $request->headers->get('referer');
      if ($referer == NULL) {
          $url = $this->router->generate('fallback_url');
      } else {
          $url = $referer;
      }
      return $this->redirect($url);
  }

  public function acceptBudgetAction($id, Request $request)
  {
    $em = $this->getDoctrine()->getManager();

    $advert = $em->getRepository('ESPlatformBundle:Advert')->find($id);

    if(null === $advert) {
      throw new NotFoundHttpException("L'annonce n°".$id." n'existe pas.");
    }

    $advert->setBudgetAccepted(1);
    $em->flush();

    $referer = $request->headers->get('referer');
      if ($referer == NULL) {
          $url = $this->router->generate('fallback_url');
      } else {
          $url = $referer;
      }
      return $this->redirect($url);
  }

  public function acceptProjectAction($id, Request $request)
  {
    $em = $this->getDoctrine()->getManager();

    $advert = $em->getRepository('ESPlatformBundle:Advert')->find($id);

    if(null === $advert) {
      throw new NotFoundHttpException("L'annonce n°".$id." n'existe pas.");
    }

    $advert->setProjectAccepted(1);
    $em->flush();

    $referer = $request->headers->get('referer');
      if ($referer == NULL) {
          $url = $this->router->generate('fallback_url');
      } else {
          $url = $referer;
      }
      return $this->redirect($url);
  }

  public function addBudgetAction($id, Request $request)
  {
    $file = new File();

    $em = $this->getDoctrine()->getManager();

    $advert = $em->getRepository('ESPlatformBundle:Advert')->find($id);

    if(null === $advert) {
      throw new NotFoundHttpException("L'annonce n°".$id." n'existe pas.");
    }

    $directory = $em->getRepository('ESFileBundle:Directory')->findOneById(1);

    if(null === $directory) {
      $directory = new Directory();
      $directory->setName("Budgets");
      $directory->setId(1);
    }

    $form = $this->get('form.factory')->create(MyfileType::class, $file);

    if ($request->isMethod('POST')) {
      $form->handleRequest($request);
      if ($form->isValid()){

        $file->setDirectory($directory);

        $advert->setBudget($file);

        $em->persist($file);
        $em->flush();

        return $this->redirectToRoute('es_platform_view', array('id' => $advert->getId()));
      }
    }

    return $this->render('ESFileBundle:File:add.html.twig', array(
        'form' => $form->createView(),
        'directory' => $directory));
  }

  public function editBudgetAction($id, Request $request)
  {
    $file = new File();

    $em = $this->getDoctrine()->getManager();

    $advert = $em->getRepository('ESPlatformBundle:Advert')->find($id);

    if(null === $advert) {
      throw new NotFoundHttpException("L'annonce n°".$id." n'existe pas.");
    }

    $previousBudget = $advert->getBudget();

    $directory = $em->getRepository('ESFileBundle:Directory')->findOneById(1);

    if(null === $directory) {
      $directory = new Directory();
      $directory->setName("Budgets");
      $directory->setId(1);
    }

    $form = $this->get('form.factory')->create(MyfileType::class, $file);

    if ($request->isMethod('POST')) {
      $form->handleRequest($request);
      if ($form->isValid()){

        $file->setDirectory($directory);
        $em->remove($previousBudget);
        $advert->setBudget($file);

        $em->persist($file);
        $em->flush();

        return $this->redirectToRoute('es_platform_view', array('id' => $advert->getId()));
      }
    }

    return $this->render('ESFileBundle:File:add.html.twig', array(
        'form' => $form->createView(),
        'directory' => $directory));
  }

  public function viewBudgetAction($id)
  {
    $em = $this->getDoctrine()->getManager();

    $advert = $em->getRepository('ESPlatformBundle:Advert')->find($id);

    if(null === $advert) {
      throw new NotFoundHttpException("L'annonce n°".$id." n'existe pas.");
    }

    $budget = $advert->getBudget();
    return $this->redirectToRoute('es_file_view', array(
      'id' => $budget->getId(), 
      'extension' => $budget->getExtension()));
  }

  public function participeAction($id, Request $request)
  {
    $em = $this->getDoctrine()->getManager();

    $advert = $em->getRepository('ESPlatformBundle:Advert')->find($id);

    if(null === $advert) {
      throw new NotFoundHttpException("L'annonce n°".$id." n'existe pas.");
    }

    $advert->addParticipant($this->getUser());
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

?>
