<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\BinaryFileResponse;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\BookingRequest;
use App\Entity\Room;

class MainController extends AbstractController
{
    /**
     * @Route("/test", name="test")
     */
    public function test(): Response
    {
//        $fileContent = file_get_contents($this->getParameter('kernel.project_dir').'/var/test.xls');
//        $response = new Response($fileContent);
//
//        $disposition = HeaderUtils::makeDisposition(
//            HeaderUtils::DISPOSITION_ATTACHMENT,
//            'test.xls'
//        );
//        $response->headers->set('Content-Disposition', $disposition);
        $file = $this->getParameter('kernel.project_dir').'/var/test.xls';
        $response = new BinaryFileResponse($file);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        return $response;
    }

    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        return $this->render('main/index.html.twig', [
            "capatchaSiteKey" => $_ENV['CAPTCHA_SITE_KEY'],
        ]);
    }

    /**
     * @Route("/about", name="about")
     */
    public function about(): Response
    {
        return $this->render('main/about.html.twig', []);
    }

    /**
     * @Route("/rooms", name="rooms")
     */
    public function rooms(): Response
    {
        return $this->render('main/rooms.html.twig', []);
    }

    /**
     * @Route("/gallery", name="gallery")
     */
    public function gallery(): Response
    {
        return $this->render('main/gallery.html.twig', []);
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contact(): Response
    {
        return $this->render('main/contact.html.twig', []);
    }

    /**
     * @Route("/articles", name="articles")
     */
    public function articles(): Response
    {
        return $this->render('main/articles.html.twig', []);
    }

    /**
     * @Route("/article/1", name="article1")
     */
    public function article1(): Response
    {
        return $this->render('main/article/article1.html.twig', []);
    }

    /**
     * @Route("/article/2", name="article2")
     */
    public function article2(): Response
    {
        return $this->render('main/article/article2.html.twig', []);
    }

    /**
     * @Route("/article/3", name="article3")
     */
    public function article3(): Response
    {
        return $this->render('main/article/article3.html.twig', []);
    }

    /**
     * @Route("/article/4", name="article4")
     */
    public function article4(): Response
    {
        return $this->render('main/article/article4.html.twig', []);
    }

    /**
     * @Route("/article/5", name="article5")
     */
    public function article5(): Response
    {
        return $this->render('main/article/article5.html.twig', []);
    }

    /**
     * @Route("/ajax_booking", name="ajax_booking")
     */
    public function ajaxBooking(): Response
    {
        $gCaptchaSiteKey = $_ENV['CAPTCHA_SITE_KEY'];
        $gCaptchaSecretKey = $_ENV['CAPTCHA_SECRET_KEY'];
        $telegramBotId = $_ENV['TELEGRAM_BOT_ID'];
        $telegramChatId = $_ENV['TELEGRAM_CHAT_ID'];

        $request = Request::createFromGlobals();
        $data = $request->request->all();
        if ($this->isCsrfTokenValid('csrf_token', $data['token']) && preg_match("/^\d{2}\.\d{2}\.\d{4}\s-\s\d{2}\.\d{2}\.\d{4}$/", $data["daterange"]) && preg_match("/^((8|\+7)[\- ]?)?(\(?\d{3,4}\)?[\- ]?)?[\d\- ]{6,7}$/", $data["phonenumber"]) && preg_match("/^[A-Za-zА-Яа-я0-9 ]+$/u", $data["name"]) && $data["rooms"]) {
            $capatcha_response = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . $gCaptchaSecretKey . "&response={$data['g-recaptcha-response']}"), true);

            if ($capatcha_response['score'] && $capatcha_response['score'] > 0.5) {
                $entityManager = $this->getDoctrine()->getManager();

                $room = $this->getDoctrine()->getRepository(Room::class)->find($data["rooms"]);
                $bookingRequest = new BookingRequest();
                $bookingRequest->setName($data["name"]);
                $bookingRequest->setPhonenumber($data["phonenumber"]);
                $bookingRequest->setRoom($room);
                $bookingRequest->setDaterange($data["daterange"]);
                $entityManager->persist($bookingRequest);
                $entityManager->flush();

                $bot_answer = json_decode(file_get_contents("https://api.telegram.org/bot{$telegramBotId}/sendMessage?chat_id={$telegramChatId}&text=Имя : " . $data["name"] . "%0aЗаезд-отъезд : " . $data["daterange"] . "%0aНомер : " . $room->getName() . "%0aНомер телефона : " . $data["phonenumber"]));
                if ($bookingRequest->getId()) {
                    return new Response(1);
                } else {
                    return new Response(0);
                }
                return new Response(0);
            }
        } else {
            return new Response(0);
        }
    }
}
