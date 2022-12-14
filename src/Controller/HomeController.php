<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Produit;
use App\Form\ItemType;
use Doctrine\Persistence\ManagerRegistry;
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
        return $this->render('home/index.html.twig', []);
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
    // /**
    //  * @Route("/addItem", name="ajoutProduit")
    //  */
    // public function ajoutItem(ObjectManager $objetManager, Request $requete){
    //     $produit = new produit();
    //     $form = $this->createForm(ItemType::class, $produit);
    //     $form->handleRequest($requete);
    //     // dd($form);
    //     if($form->isSubmitted() && $form->isValid()){
    //         $objetManager->persist($produit);
    //         $objetManager->flush();
    //        return $this->redirectToRoute('app_home');
    //     }
    //     $formMode = false;
    //     // if ($produit->getId() !== null){
    //     //     $formMode = true;
    //     // }
    //     return $this->render('home/addItem.html.twig',[
    //         'formulaire' => $form->createView(),
    //         'mode' => $formMode
    //     ]);
    // }
    /**
     * @Route("/addItem", name="ajoutProduit") // permet de remplacer toute la fonction ajoutItem
     * @Route("/editItem/{id}", name="modifProduit")
     */
    public function modifItem(ManagerRegistry $doctrine, Request $requete, Produit $produit = null){
        if(!$produit){
            $produit = new Produit();
        }
        $form = $this->createForm(ItemType::class, $produit);
        $form->handleRequest($requete); dd($produit);
        if($form->isSubmitted() && $form->isValid()){  /* dd($produit); ne veut pas entrer dans ma condition ! */
            $produit->setCreatedAt(new \DateTimeImmutable());
            $om = $doctrine->getManager();
            // $category = new Category();
            // $category->addProduit($produit);
            // $produit->addCategory($category);
            // $om->persist($category);
            $om->persist($produit);
            $om->flush();
            return $this->redirectToRoute('showone', ['id' => $produit->getId()]);// pour renvoyer sur une page utilisant un id
        } /* dd($produit); ici ??a veut bien passer, mais pas entrer non plus dans la condition suivante */
        $formMode = false;
        if($produit->getId() !== null){
            $formMode = true; 
        }
        return $this->render('home/addItem.html.twig',[
            'formulaire' => $form->createView(),
            'mode' => $formMode
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
     /**
     * @Route("/catProduit/{id}",name="app_catProduit")
     */
    public function catProduit(ManagerRegistry $doctrine, $id ,Category $category){ 
        $om = $doctrine->getRepository(Category::class);
        $catProduit = $om->findProduitByCategory($id);
        // dd($catProduit);
        return $this->render("home/catProduit.html.twig",[
            'catProduit' => $catProduit,
            "category" => $category,
        ]);
    }
}
