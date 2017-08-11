<?php

namespace ES\FileBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use ES\FileBundle\Entity\File;
use ES\FileBundle\Form\MyfileType;
use ES\FileBundle\Form\MyfileRenameType;
use ES\FileBundle\Entity\Directory;
use ES\FileBundle\Form\DirectoryType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class FileController extends Controller
{
    public function indexAction($directoryId)
    {
      $em = $this->getDoctrine()->getManager();

      if ($directoryId == 0) {
        $listFiles = $em->getRepository('ESFileBundle:File')->findByDirectory(null);
        $listDirectories = $em->getRepository('ESFileBundle:Directory')->findByDirectory(null);
        return $this->render('ESFileBundle:File:index.html.twig', array(
          'listFiles' => $listFiles,
          'listDirectories' => $listDirectories,
          'idParentDirectory' => null,
          'directory' => null));
      }

      $directory = $em->getRepository('ESFileBundle:Directory')->findOneById($directoryId);
      $listFiles = $em->getRepository('ESFileBundle:File')->findByDirectory($directory);
      $listDirectories = $em->getRepository('ESFileBundle:Directory')->findByDirectory($directory);
      $ParentDirectory = $directory->getDirectory();
      if ($ParentDirectory == null) {
        $idParentDirectory = 0;
      }
      else
      {
        $idParentDirectory = $ParentDirectory->getId();
      }
      return $this->render('ESFileBundle:File:index.html.twig', array(
          'listFiles' => $listFiles,
          'listDirectories' => $listDirectories,
          'idParentDirectory' => $idParentDirectory,
          'directory' => $directory));
    }

    public function addAction(Request $request, $directoryId)
  	{
      	$file = new File();

        $em = $this->getDoctrine()->getManager();
        $directory = $em->getRepository('ESFileBundle:Directory')->findOneById($directoryId);

      	$form = $this->get('form.factory')->create(MyfileType::class, $file);

      if ($request->isMethod('POST')) {
        $form->handleRequest($request);
        if ($form->isValid()){

          $file->setDirectory($directory);

          $em->persist($file);
          $em->flush();

          $request->getSession()->getFlashBag()->add('notice', 'Fichier bien enregistrée.');

          return $this->redirectToRoute('es_file_index', array('directoryId' => $directoryId));
        }
      }

      return $this->render('ESFileBundle:File:add.html.twig', array(
        'form' => $form->createView(),
        'directory' => $directory));
    }

    public function addDirectoryAction(Request $request, $directoryId)
    {
        $directory = new Directory();

        $em = $this->getDoctrine()->getManager();
        $inDirectory = $em->getRepository('ESFileBundle:Directory')->findOneById($directoryId);

        $form = $this->get('form.factory')->create(DirectoryType::class, $directory);

      if ($request->isMethod('POST')) {
        $form->handleRequest($request);
        if ($form->isValid()){

          $directory->setDirectory($inDirectory);

          $em->persist($directory);
          $em->flush();

          return $this->redirectToRoute('es_file_index', array('directoryId' => $directoryId));
        }
      }

      return $this->render('ESFileBundle:File:addDirectory.html.twig', array(
        'form' => $form->createView(),
        'directory' => $inDirectory));
  }

  public function renameDirectoryAction($id, Request $request)
  {
      $em = $this->getDoctrine()->getManager();

      $directory = $em->getRepository('ESFileBundle:Directory')->find($id);

      if(null === $directory) {
         throw new NotFoundHttpException("le dossier n°".$id." n'existe pas.");
      }

      $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $directory);

      $form = $this->get('form.factory')->create(DirectoryType::class, $directory);

      if ($request->isMethod('POST')) {
          $form->handleRequest($request);
          if ($form->isValid()){
            $em->flush();

            $ParentDirectory = $directory->getDirectory();
            if ($ParentDirectory == null){
              return $this->redirectToRoute('es_file_index');
            }
            return $this->redirectToRoute('es_file_index', array('directoryId' => $ParentDirectory->getId()));
        }
      }

      return $this->render('ESFileBundle:File:renameDirectory.html.twig', array('form' => $form->createView(), 'directory' => $directory));
  }

  public function renameFileAction($id, Request $request)
  {
      $em = $this->getDoctrine()->getManager();

      $file = $em->getRepository('ESFileBundle:File')->find($id);

      if(null === $file) {
         throw new NotFoundHttpException("le fichier n°".$id." n'existe pas.");
      }

      $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $file);

      $form = $this->get('form.factory')->create(MyfileRenameType::class, $file);

      if ($request->isMethod('POST')) {
          $form->handleRequest($request);
          if ($form->isValid()){
            $em->flush();

            $ParentDirectory = $file->getDirectory();
            if ($ParentDirectory == null){
              return $this->redirectToRoute('es_file_index');
            }
            return $this->redirectToRoute('es_file_index', array('directoryId' => $ParentDirectory->getId()));
        }
      }

      return $this->render('ESFileBundle:File:renameFile.html.twig', array('form' => $form->createView(), 'file' => $file));
  }

  public function deleteFileAction($id, Request $request)
  {
      $em = $this->getDoctrine()->getManager();
      $file = $em->getRepository('ESFileBundle:File')->find($id);
      if (null === $file) {
          throw new NotFoundHttpException("Le fichier d'id ".$id." n'existe pas.");
      }

      $form = $this->get('form.factory')->create();

      if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) 
      {
        $em->remove($file);
        $em->flush();

        $ParentDirectory = $file->getDirectory();
        if ($ParentDirectory == null){
          return $this->redirectToRoute('es_file_index');
        }
        return $this->redirectToRoute('es_file_index', array('directoryId' => $ParentDirectory->getId()));
      }

      return $this->render('ESFileBundle:File:deleteFile.html.twig', array('file' => $file, 'form'   => $form->createView()));
  }

  public function deleteDirectoryAction($id, Request $request)
  {
      $em = $this->getDoctrine()->getManager();
      $directory = $em->getRepository('ESFileBundle:Directory')->find($id);
      if (null === $directory) {
          throw new NotFoundHttpException("Le dossier d'id ".$id." n'existe pas.");
      }

      $form = $this->get('form.factory')->create();

      if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) 
      {
        $em->remove($directory);
        $em->flush();

        $ParentDirectory = $directory->getDirectory();
        if ($ParentDirectory == null){
          return $this->redirectToRoute('es_file_index');
        }
        return $this->redirectToRoute('es_file_index', array('directoryId' => $ParentDirectory->getId()));
      }

      return $this->render('ESFileBundle:File:deleteDirectory.html.twig', array('directory' => $directory, 'form'   => $form->createView()));
  }

  public function viewAction($id, $extension){
    $em = $this->getDoctrine()->getManager();
    $file = $em->getRepository('ESFileBundle:File')->find($id);
    $response = new Response();
    $response->setContent(file_get_contents("uploads/file/".$id.".".$extension));
    $response->headers->set('Content-Type', 'application/force-download');
    $response->headers->set('Content-disposition', 'filename='.$file->getName().".".$extension);
         
    return $response;
  }
}
