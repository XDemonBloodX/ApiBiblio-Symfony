<?php

namespace App\Controller;

use App\Entity\Genre;
use App\Repository\GenreRepository;
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
class ApiGenreController extends AbstractController
{
    /**
     * @Route("/genres", name="lstGenre", methods={"GET"})
     */
    public function lst(GenreRepository $repo, SerializerInterface $serializer)
    {
        $genre=$repo->findAll();
        $result=$serializer->serialize($genre, 'json', [
            'groups'=>['lstGenreFull']
        ]);

        return new JsonResponse($result, 200, [], true);
    }

    /**
     * @Route("/genres/{id}", name="showGenreId", methods={"GET"})
     */
    public function showGenreById(Genre $genre, SerializerInterface $serializer)
    {

        $result=$serializer->serialize($genre, 'json', [
            'groups'=>['lstGenre']
        ]);

        return new JsonResponse($result,Response::HTTP_OK, [], true);
    }

     /**
     * @Route("/genreAdd", name="genreCreate", methods={"POST"})
     */
    public function createGenre(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator)
    {
        $data=$request->getContent();

        $genre=$serializer->deserialize($data, Genre::class, 'json');
        $errors=$validator->validate($genre);
        if (count($errors)) {
            $errorsJson=$serializer->serialize($errors, 'json');
            return new JsonResponse($errorsJson, Response::HTTP_BAD_REQUEST, [], true);
        }
        $em->persist($genre);
        $em->flush();

        return new JsonResponse("Le genre a bien ete cree",
        Response::HTTP_CREATED, [
            ["location" => $this->generateUrl(
                'showGenreId',
                ["id"=>$genre->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL
                )
            ]
        ], true);
    }

    /**
    * @Route("/genreUp/{id}", name="genreUpdate", methods={"PUT"})
    */
    public function updateGenre(Genre $genre, Request $request, SerializerInterface $serializer, EntityManagerInterface $em)
    {
        $data=$request->getContent();
 
        $genre=$serializer->deserialize($data, Genre::class, 'json', ['object_to_populate'=>$genre]);
        $em->persist($genre);
        $em->flush();
 
        return new JsonResponse("Le genre a bien ete modifie",
        Response::HTTP_OK, [], true);
    }

    /**
    * @Route("/genreDel/{id}", name="genreDelete", methods={"DELETE"})
    */
   public function deleteGenre(Genre $genre, EntityManagerInterface $em)
   {
       $em->remove($genre);
       $em->flush();

       return new JsonResponse("Le genre a bien ete supprime",
       Response::HTTP_OK, []);
   }
}