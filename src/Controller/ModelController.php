<?php

namespace App\Controller;

use App\Entity\Model;
use App\Form\ModelType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/model')]
class ModelController extends AbstractController
{
    #[Route('/', name: 'app_model')]
    public function index(EntityManagerInterface $em, Request $request): Response
    {
        $model = new Model();
        $form = $this->createForm(ModelType::class, $model);

        // Demander d'analyser la requete HTTP
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Le formulaire a été sumis et est validé
            //Prépare la sauvegarde
            $em->persist($model);
            //Envoie la sauvegarde
            $em->flush();

            $this->addFlash('success', 'Modèle ajoutée');
        }

        //Récupérer des catégories (SELECT *)
        $models = $em->getRepository(Model::class)->findAll();


        return $this->render('model/index.html.twig', [
            'models' => $models,
            'ajout' => $form->createView(), //Envoi de la version HTML du formulaire
        ]);
    }

    #[Route('/{id}', name: 'model')]
    public function model(Model $model = null, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ModelType::class, $model);
        // Demander d'analyser la requete HTTP
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Le formulaire a été sumis et est validé
            //Prépare la sauvegarde
            $em->persist($model);
            //Envoie la sauvegarde
            $em->flush();

            $this->addFlash('success', 'Modèle mis à jour');
        }
        //Si la model est introuvable
        if ($model == null) {
            $this->addFlash('error', 'Modèle introuvable');
            return $this->redirectToRoute('app_category');
        }

        return $this->render('category/show.html.twig', [
            'model' => $model,
            'edit' => $form->createView(),
        ]);
    }


    #[Route('/delete/{id}', name:'delete_model')]
    public function delete(Model $model = null, EntityManagerInterface $em, Request $request) 
    {
        if ($model == null) {
            $this->addFlash('warning','model introuvable');
            return $this->redirectToRoute('app_model');
        }

        $em->remove($model);
        $em->flush();

        $this->addFlash('success','model supprimée');
            return $this->redirectToRoute('app_model');
        
    }
}
