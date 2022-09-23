<?php

namespace App\Controller;

use App\Entity\Employe;
use App\Form\EmployeType;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class EmployeController extends AbstractController
{
    /**
     * @Route("/addEmploye", name="addemploye")
     */
    public function addEmploye(ObjectManager $objetManager, Request $requete, SluggerInterface $slugger): Response
    {
        $employe = new Employe();
        $form = $this->createForm(EmployeType::class,$employe);
        $form->handleRequest($requete);
        if($form->isSubmitted() && $form->isValid()){
            // ajouter un fichier 
            // ajouter dans le fichier "services.yaml" dans parameters :
            // "brochures_directory: '%kernel.project_dir%/public/uploads/dossier_images'"
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();
            if($imageFile){
                $origineFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFileName = $slugger->slug($origineFilename);
                $newFileName = $safeFileName . '-' . uniqid() . '.' . 
                $imageFile->guessExtension();
                try{
                    $imageFile->move(
                        $this->getParameter('dossier_images'),
                        $newFileName
                    );
                } catch (FileException $e){
                    $e->getMessage();
                }
            $employe->setImage($newFileName);
            }
            $objetManager->persist($employe);
            // fin du code d'ajout d'un fichier via un formulaire
            $objetManager->flush();
        return $this->redirectToRoute('app_home');
        }
        return $this->render('employe/addEmploye.html.twig', [
            'formulaire' => $form->createView()
        ]);
    }
}
