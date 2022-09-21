<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ItemType;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\RedirectController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index()
    {
        // $repository = $this->getDoctrine()->getRepository(Produit::class);
        // $produits = $repository->findAll();
        // // dd($employes);
        return $this->render('home/index.html.twig', [
            // "produits" => $produits
        ]);
    }
    /**
     * @Route("/showItems", name="showitems")
     */
    public function showItems(){
        $repo = $this->getDoctrine()->getRepository(Produit::class);
        $produit = $repo->findAll();
        // dd($produit);
        return $this->render('home/showItems.html.twig',[
            'produit' => $produit
        ]);
    }
    /**
     * @Route("/addItem", name="ajoutProduit")
     */
    public function ajoutItem(ObjectManager $objetManager, Request $requete){
        $produit = new produit();
        $form = $this->createForm(ItemType::class, $produit);
        $form->handleRequest($requete);
        // dd($form);
        if($form->isSubmitted() && $form->isValid()){
            $objetManager->persist($produit);
            $objetManager->flush();
           return $this->redirectToRoute('app_home');
        }
        return $this->render('home/addItem.html.twig',[
            'formulaire' => $form->createView()
        ]);
    }
    /**
     * @Route("/editItem/{id}", name="modifProduit")
     */
    public function modifItem(ObjectManager $objetManager, Request $requete, Produit $produit = null){
        if(!$produit){
            $produit = new Produit();
        }
        $form = $this->createForm(ItemType::class, $produit);
        $form->handleRequest($requete);
        if($form->isSubmitted() && $form->isValid()){
            $objetManager->persist($produit);
            $objetManager->flush();
           return $this->redirectToRoute('showitems');
        }
        return $this->render('home/addItem.html.twig',[
            'formulaire' => $form->createView()
        ]);
    }
    /**
     * @Route("/deleteItem/{id}", name="supprimProduit")
     */
    public function removeItem(Produit $produit){
        $objetManager = $this->getDoctrine()->getManager();
        $objetManager->remove($produit);
        $objetManager->flush();
        return $this->redirectToRoute('showitems');
    }
    /**
     * @Route("/showOne/{id}", name="showone")
     */
    public function showOneItem($id){
        $repo = $this->getDoctrine()->getRepository(Produit::class);
        $produit = $repo->find($id);
        // dd($produit);
        return $this->render('home/showOne.html.twig',[
            'produit' => $produit
        ]);
    }
}
