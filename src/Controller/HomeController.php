<?php

namespace App\Controller;

use App\Entity\Admin\Messages;
use App\Entity\Blog;
use App\Entity\Setting;
use App\Form\Admin\MessagesType;
use App\Repository\Admin\CommentRepository;
use App\Repository\BlogRepository;
use App\Repository\ImageRepository;
use App\Repository\SettingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Bridge\Google\Smtp\GmailTransport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class
HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(SettingRepository $settingRepository, BlogRepository $blogRepository)
    {
        $setting = $settingRepository->findAll();
        $slider = $blogRepository->findBy([],['title'=>'ASC'],10);
        $blogs=$blogRepository->findBy([],['title'=>'DESC'],10);
        $newblogs=$blogRepository->findBy([],['title'=>'DESC'],10);



        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'setting' => $setting,
            'slider' => $slider,
            'blogs' => $blogs,
            'newblogs' => $newblogs,

        ]);
    }

    /**
     * @Route("/blog/{id}", name="home_blog_show", methods={"GET"})
     */
    public function show(Blog $blog,$id,ImageRepository $imageRepository,CommentRepository $commentRepository): Response
    {


        $images = $imageRepository->findBy(['blog'=>$id]);
        $comments=$commentRepository->findBy(['blogId'=>$id,'status'=>'True']);
        //dump($comments);
       // die();
        return $this->render('home/blogshow.html.twig', [
            'blog' => $blog,
            'images' => $images,
            'comments' => $comments,
        ]);
    }
    /**
     * @Route("/about", name="home_about")
     */
    public function about(SettingRepository $settingRepository): Response
    {
        $setting = $settingRepository->findAll();
        return $this->render('home/aboutus.html.twig', [
            'setting' => $setting,
        ]);
    }

    /**
     * @Route("/contact", name="home_contact", methods={"GET","POST"})
     */
    public function contact(SettingRepository $settingRepository, Request $request): Response
    {
        $message = new Messages();
        $form = $this->createForm(MessagesType::class, $message);
        $form->handleRequest($request);
        $submittedToken=$request->get('token');

        if ($form->isSubmitted()) {
            if ($this->isCsrfTokenValid('form-message',$submittedToken)){
                $entityManager = $this->getDoctrine()->getManager();
                $message->setStatus('New');
                $message->setIp($_SERVER['REMOTE_ADDR']);
                $entityManager->persist($message);
                $entityManager->flush();
                $this->addFlash('success','Your message has been sent succesful');


                $setting=$settingRepository->findBy(['id'=>1]);
                $email = (new Email())
                    ->from($setting[0]->getSmtpemail())
                    ->to($form['email']->getData())
                    //->cc('cc@example.com')
                    //->bcc('bcc@example.com')
                    //->replyTo('fabien@example.com')
                    //->priority(Email::PRIORITY_HIGH)
                    ->subject('Blog Your Request!')
                    //->text('Sending emails is fun again!')
                    ->html("Dear ".$form['name']->getData() ."<br>
                         <p>We will evaluate your requests and contact you as soon as possible</p>
                         Thank You for your message <br>
                         
                         <br>".$setting[0]->getTitle()."  <br>
                         "
                    
               
                    );
                    
                    $transport= new GmailTransport($setting[0]->getSmtpemail(),$setting[0]->getSmtppassword());
                    $mailer=new Mailer($transport);
                    $mailer->send($email);

                return $this->redirectToRoute('home_contact');
            }
        }
        $setting = $settingRepository->findAll();
        return $this->render('home/contact.html.twig', [
            'setting' => $setting,
            'form' => $form->createView(),
        ]);
    }


}

