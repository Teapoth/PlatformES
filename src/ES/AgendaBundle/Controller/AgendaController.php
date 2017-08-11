<?php

namespace ES\AgendaBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use ES\AgendaBundle\Entity\Event;
use ES\AgendaBundle\Form\EventType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Form\Extension\Core\Type\FormType;

class AgendaController extends Controller
{
	public function indexAction($month)
	{
		$listEvents = $this->getDoctrine()->getManager()->getRepository('ESAgendaBundle:Event')->GetByMonth(intval($month));
		return $this->render('ESAgendaBundle:Agenda:index.html.twig', array(
			'listEvents' => $listEvents,
			'month' => intval($month)));
	}

	public function addAction(Request $request)
  	{
		$event = new Event();

		$form = $this->get('form.factory')->create(EventType::class, $event);

		if ($request->isMethod('POST')) {
			$form->handleRequest($request);
			if ($form->isValid()){

				$em = $this->getDoctrine()->getManager();
				$em->persist($event);
				$em->flush();

				return $this->redirectToRoute('es_agenda_view', array('id' => $event->getId()));
			}
		}

		return $this->render('ESAgendaBundle:Agenda:add.html.twig', array('form' => $form->createView()));
  }

	public function viewAction($id)
	{
		$repository = $this->getDoctrine()->getManager()->getRepository('ESAgendaBundle:Event');
		$event = $repository->find($id);

		if(null === $event) {
			throw new NotFoundHttpException("L'évènement n°".$id." n'existe pas.");
		}

		return $this->render('ESAgendaBundle:Agenda:view.html.twig', array('event' => $event));
	}

	public function editAction($id, Request $request)
  	{
		$em = $this->getDoctrine()->getManager();

		$event = $em->getRepository('ESAgendaBundle:Event')->find($id);

		if(null === $event) {
		 throw new NotFoundHttpException("l'évènement n°".$id." n'existe pas.");
		}

		$formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $event);

		$form = $this->get('form.factory')->create(EventType::class, $event);

		if ($request->isMethod('POST')) {
			$form->handleRequest($request);
			if ($form->isValid()){
				$em->flush();

				return $this->redirectToRoute('es_agenda_view', array('id' => $event->getId()));
			}
		}

		return $this->render('ESAgendaBundle:Agenda:edit.html.twig', array('form' => $form->createView(), 'event' => $event));
	}

  	public function deleteAction($id, Request $request)
  	{
		$em = $this->getDoctrine()->getManager();
		$event = $em->getRepository('ESAgendaBundle:Event')->find($id);
		if (null === $event) {
			throw new NotFoundHttpException("L'évènement d'id ".$id." n'existe pas.");
		}

		$form = $this->get('form.factory')->create();

		if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) 
		{
			$em->remove($event);
			$em->flush();

			return $this->redirectToRoute('es_agenda_index');
		}

		return $this->render('ESAgendaBundle:Agenda:delete.html.twig', array('event' => $event, 'form' => $form->createView()));
    }
}