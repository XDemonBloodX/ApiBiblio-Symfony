<?php

namespace App\Controller;

use App\Entity\Auteur;
use App\Repository\AuteurRepository;
use App\Repository\NationaliteRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/apiM")
 */
class ApiAuteurController extends AbstractController
{
    /**
     * @Route("/auteurs", name="lstAuteur", methods={"GET"})
     */
    public function lst(AuteurRepository $repo, SerializerInterface $serializer)
    {
        $Auteur=$repo->findAll();
        $result=$serializer->serialize($Auteur, 'json', [
            'groups'=>['lstAuteurFull']
        ]);

        return new JsonResponse($result, 200, [], true);
    }

    /**
     * @Route("/auteur/{id}", name="showAuteurId", methods={"GET"})
     */
    public function showAuteurById(Auteur $Auteur, SerializerInterface $serializer)
    {

        $result=$serializer->serialize($Auteur, 'json', [
            'groups'=>['lstAuteur']
        ]);

        return new JsonResponse($result,Response::HTTP_OK, [], true);
    }

     /**
     * @Route("/auteurAdd", name="auteurCreate", methods={"POST"})
     */
    public function createAuteur(Request $request, NationaliteRepository  $nation, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator)
    {
        $data=$request->getContent();
        $dataTab=$serializer->decode($data, 'json');
        
        $Auteur=new Auteur();

        $nationalite=$nation->find($dataTab['nationalite']['id']);
        $serializer->deserialize($data, Auteur::class, 'json', ['object_to_populate'=>$Auteur]);

        $Auteur->setNationalites($nationalite);

        $errors=$validator->validate($Auteur);
        if (count($errors)) {
            $errorsJson=$serializer->serialize($errors, 'json');
            return new JsonResponse($errorsJson, Response::HTTP_BAD_REQUEST, [], true);
        }
        $em->persist($Auteur);
        $em->flush();

        return new JsonResponse("Le Auteur a bien ete cree",
        Response::HTTP_CREATED, [
            ["location" => $this->generateUrl(
                'showAuteurId',
                ["id"=>$Auteur->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL
                )
            ]
        ], true);
    }

    /**
    * @Route("/auteurUp/{id}", name="auteurUpdate", methods={"PUT"})
    */
    public function updateAuteur(Auteur $Auteur, NationaliteRepository  $nation, Request $request, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator)
    {
        $data=$request->getContent();
 
        $dataTab=$serializer->decode($data, 'json');
        $nationalite=$nation->find($dataTab['nationalite']['id']);
        
        $serializer->deserialize($data, Auteur::class, 'json', ['object_to_populate'=>$Auteur]);
        $Auteur->setNationalites($nationalite);

        $errors=$validator->validate($Auteur);
        if (count($errors)) {
            $errorsJson=$serializer->serialize($errors, 'json');
            return new JsonResponse($errorsJson, Response::HTTP_BAD_REQUEST, [], true);
        }

        $em->persist($Auteur);
        $em->flush();
 
        return new JsonResponse("Le Auteur a bien ete modifie",
        Response::HTTP_OK, [], true);
    }

    /**
    * @Route("/auteurDel/{id}", name="auteurDelete", methods={"DELETE"})
    */
   public function deleteAuteur(Auteur $Auteur, EntityManagerInterface $em)
   {
       $em->remove($Auteur);
       $em->flush();

       return new JsonResponse("Le Auteur a bien ete supprime",
       Response::HTTP_OK, []);
   }
}