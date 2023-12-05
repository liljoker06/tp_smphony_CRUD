<?php

namespace App\Controller;

use App\Entity\Brand;
use App\Form\BrandType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/brand')]
class BrandController extends AbstractController
{
    #[Route('/', name: 'app_brand')]
    public function index(EntityManagerInterface $em, Request $request): Response
    {
        $brand = new Brand();
        $form = $this->createForm(BrandType::class, $brand);

        // Demander d'analyser la requete HTTP
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $imageFile = $form->get('logo')->getData();

            if ($imageFile) {
                $newFilename =uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('upload_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Impossible d\'ajouter l\'image');
                }
                $brand->setLogo($newFilename);
            }
            // Le formulaire a été sumis et est validé
            //Prépare la sauvegarde
            $em->persist($brand);
            //Envoie la sauvegarde
            $em->flush();

            $this->addFlash('success', 'brand ajouté');
        }

        //Récupérer des Brands (SELECT *)
        $brands = $em->getRepository(Brand::class)->findAll();


        return $this->render('brand/index.html.twig', [
            'brands' => $brands,
            'ajout' => $form->createView(), //Envoi de la version HTML du formulaire
        ]);
    }

    #[Route('/{id}', name: 'brand')]
    public function brand(Brand $brand = null, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(BrandType::class, $brand);
        // Demander d'analyser la requete HTTP
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $imageFile = $form->get('logo')->getData();

            if ($imageFile) {
                $newFilename =uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('upload_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('danger', 'Impossible d\'ajouter l\'image');
                }
                $brand->setLogo($newFilename);
            }
            // Le formulaire a été soumis et est validé
            //Prépare la sauvegarde
            $em->persist($brand);
            //Envoie la sauvegarde
            $em->flush();

            $this->addFlash('success', 'brand mise à jour');
        }
        //Si la Brand est introuvable
        if ($brand == null) {
            $this->addFlash('error', 'brand introuvable');
            return $this->redirectToRoute('app_brand');
        }

        return $this->render('brand/show.html.twig', [
            'brand' => $brand,
            'edit' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id}', name:'delete_brand')]
    public function delete(Brand $brand = null, EntityManagerInterface $em, Request $request) 
    {
        if ($brand == null) {
            $this->addFlash('warning','brand introuvable');
            return $this->redirectToRoute('app_brand');
        }

        $em->remove($brand);
        $em->flush();

        $this->addFlash('success','brand supprimée');
            return $this->redirectToRoute('app_brand');
        
    }
}
