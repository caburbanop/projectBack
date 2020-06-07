<?php

namespace App\Controller;

use App\Entity\Post;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
//use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Service\Validate;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class PostController extends AbstractController
{
    protected $encoders;
    protected $normalizers;
    protected $serializer;

    public function __construct() {
        $this->encoders = [new XmlEncoder(), new JsonEncoder()];
        $this->normalizers = [new ObjectNormalizer()];
        $this->serializer = new Serializer($this->normalizers, $this->encoders);
    }

    /**
     * @Route("/post/list", name="list_post")
     * @Method({"GET"})
     */
    public function listPost() {
        $posts = $this->getDoctrine()->getRepository(Post::class)->findAll();

        if (count($posts) == 0) {
            $response = [
                'code'=>1,
                'message'=>'No posts found!',
                'error'=>null,
                'result'=>null
            ];
            return new JsonResponse($response, Response::HTTP_NOT_FOUND);
        }

        $data = $this->serializer->serialize($posts, 'json');

        $response = [
            'code'=>0,
            'message'=>'success',
            'error'=>null,
            'result'=>json_decode($data)
        ];

        return new JsonResponse($response, 200);
    }

    /**
     * @Route("/post/show/{id}", name="show_post")
     * @Method({"GET"})
     */
    public function showPost($id) {
        $post = $this->getDoctrine()->getRepository(Post::class)->find($id);

        if (empty($post)) {
            $response = [
                'code'=>1,
                'message'=>'Post not found',
                'error'=>null,
                'result'=>null
            ];

            return new JsonResponse($response, Response::HTTP_NOT_FOUND);
        }

        $data = $this->serializer->serialize($post, 'json');

        $response = [
            'code'=>0,
            'message'=>'success',
            'error'=>null,
            'result'=>json_decode($data)
        ];

        return new JsonResponse($response, 200);
    }

    /**
     * @param Request $request
     * @param Validate $validate
     * @return JsonResponse
     * @Route("/post/create", name="create_post")
     * @Method({"POST"})
     */
    public function createPost(Request $request, Validate $validate) {
        $em = $this->getDoctrine()->getManager();
        $data = $request->getContent();
        $post = $this->serializer->deserialize($data, Post::class, 'json');

        $reponse=$validate->validateRequest($post);

        if (!empty($reponse)) {
            return new JsonResponse($reponse, Response::HTTP_BAD_REQUEST);
        }

        $em->persist($post);
        $em->flush();

        $response = [
            'code'=>0,
            'message'=>'Post created!',
            'errors'=>null,
            'result'=>null
        ];

        return new JsonResponse($response, 200);
    }

    /**
     * @param Request $request
     * @param $id
     * @Route("/post/update/{id}",name="update_post")
     * @Method({"PUT"})
     * @return JsonResponse
     */
    public function updatePost(Request $request, $id, Validate $validate) {
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository(Post::class)->find($id);

        if (empty($post)) {
            $response = [
                'code'=>1,
                'message'=>'Post Not found !',
                'errors'=>null,
                'result'=>null
            ];
            return new JsonResponse($response, Response::HTTP_NOT_FOUND);
        }

        $body = $request->getContent();
        $data = $this->serializer->deserialize($body,Post::class,'json');

        $reponse= $validate->validateRequest($data);

        if (!empty($reponse)) {
            return new JsonResponse($reponse, Response::HTTP_BAD_REQUEST);
        }

        $post->setTitle($data->getTitle());
        $post->setDescription($data->getDescription());

        $em->persist($post);
        $em->flush();

        $response = [
            'code'=>0,
            'message'=>'Post updated!',
            'errors'=>null,
            'result'=>null
        ];

        return new JsonResponse($response,200);
    }

    /**
     * @Route("/post/delete/{id}",name="delete_post")
     * @Method({"DELETE"})
     */
    public function deletePost($id) {
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository(Post::class)->find($id);

        if (empty($post)) {
            $response = [
                'code'=>1,
                'message'=>'post Not found !',
                'errors'=>null,
                'result'=>null
            ];
            return new JsonResponse($response, Response::HTTP_NOT_FOUND);
        }

        $em->remove($post);
        $em->flush();
        $response = [
            'code'=>0,
            'message'=>'Post deleted!',
            'errors'=>null,
            'result'=>null
        ];

        return new JsonResponse($response,200);
    }
}
