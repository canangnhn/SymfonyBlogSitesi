<?php

namespace App\Controller;


use App\Entity\User;
use App\Form\Admin\CommentType;
use App\Form\Admin\MessagesType;
use App\Form\UserType;
use App\Repository\Admin\CommentRepository;
use App\Repository\UserRepository;
use App\Security\AppCustomAuthenticator;
use phpDocumentor\Reflection\File;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use App\Entity\Admin\Comment;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(): Response
    {
        //echo "user list";
        //die();
        return $this->render('user/show.html.twig', [

        ]);
    }
    /**
     * @Route("/blog", name="user_blog", methods={"GET"})
     */
    public function blog(): Response
    {
        return $this->render('user/blog.html.twig', [

        ]);
    }
    /**
     * @Route("/comments", name="user_comments", methods={"GET"})
     */
    public function comments(CommentRepository $commentRepository): Response
    {
        $user=$this->getUser();
        $id=$user->getId();
        $comments=$commentRepository->getAllComments2($id);


        return $this->render('user/comments.html.twig', [
            'comments'=>$comments

        ]);
    }

    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request,UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, AppCustomAuthenticator $authenticator): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            /***file upload***********/
            /** @var file $file */
            $file =$form['image']->getData();

            if ($file) {
                    $fileName = $this->generateUniqueFileName() . '+' . $file->guessExtension();

                    try {
                        $file->move(
                            $this->getParameter('images_directory'),
                            $fileName
                        );
                    } catch (FileException $exception) {

                    }
                    $user->setImage($fileName);

            }

            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request,$id, User $user,UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user=$this->getUser();
        if($user->getId() != $id){
            return $this->redirectToRoute('home');
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /***file upload***********/
            /** @var file $file */
            $file =$form['image']->getData();
            if ($file) {
                $fileName = $this->generateUniqueFileName() . '+' . $file->guessExtension();

                try {
                    $file->move(
                        $this->getParameter('images_directory'),
                        $fileName
                    );
                } catch (FileException $exception) {

                }
                $user->setImage($fileName);

            }

            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @return string
     */
    private function generateUniqueFileName(){
        return md5(uniqid());
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index');
    }





    /**
     * @Route("/newcomment/{id}", name="user_new_comment", methods={"GET","POST"})
     */
    public function newcomment(Request $request,$id): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        $submittedToken=$request->request->get('token');

        if ($form->isSubmitted()) {
            if ($this->isCsrfTokenValid('comment',$submittedToken)) {
                $entityManager = $this->getDoctrine()->getManager();

                $comment->setStatus('New');
                $comment->setIp($_SERVER['REMOTE_ADDR']);
                $comment->setBlogId($id);
                $user=$this->getUser();
                $comment->setUserid($user->getId());

                $entityManager->persist($comment);
                $entityManager->flush();
                $this->addFlash('success','Your comment has been sent successfuly');

                return $this->redirectToRoute('home_blog_show', ['id' => $id]);
            }
        }

        return $this->redirectToRoute('home_blog_show', ['id'=>$id]);
    }
}
