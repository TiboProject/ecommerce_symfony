<?php

namespace App\Controller;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\BotManFactory;
use BotMan\Drivers\Web\WebDriver;
use BotMan\BotMan\Cache\SymfonyCache;
use BotMan\BotMan\Drivers\DriverManager;
use App\Chatbot\Middleware\ReceiveMiddleware;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ChatbotController extends AbstractController
{
    #[Route('/chat/message', name: 'chat_message')]
    public function message(): Response
    {
        $config= [
            'web' => [
                'matchingData' => [
                    'driver' => 'web',
                ],
            ],
        ];
        DriverManager::loadDriver(WebDriver::class);
        $adapter = new FilesystemAdapter();
        $botman = BotManFactory::create($config, new SymfonyCache($adapter));
        $botman->middleware->received(new ReceiveMiddleware());
        /*
            Classic
        */
        
        $botman->hears(
            'hi(.*)',
            function (BotMan $bot) {
                $bot->reply('Hello, I am a chatBot ! Ask me questions about the site, or about products, I will try to answer them !');
            }
        );

        $botman->hears(
            'Salut(.*)',
            function (BotMan $bot) {
                $bot->reply('Salut, je suis un robot conversationnel ! Pose moi des question sur le site, ou sur des produits, j\'essayerai d\'y répondre !');
            }
        );

        ///Si la question comporte un mot injurieux, la réponse sera un gif de chat qui fait non de la tête


        ///Conversation
        ///Si la question comporte le mot "enregistrement", le bot va lancer une conversation (si le user n'est pas déjà connecté)

        ///Stopper 
        $botman->hears(
            'Stop conversation(.*)',
            function (BotMan $bot) {
                $bot->reply('I will stop our conversation.');
            }
        )->stopsConversation();

        $botman->hears(
            'Stopper la conversation(.*)',
            function (BotMan $bot) {
                $bot->reply('la conversation s\'est correctement terminée.');
            }
        )->stopsConversation();

        

        ///Fallback, si aucune réponse n'est trouvée ou que la question ne matche aucun des cas au dessus
        $botman->fallback(
            function (BotMan $bot) {
                $bot->reply('Sorry, I did not understand your question, please try again. Désole, je n\'ai pas compris votre question, veuillez réessayer.');
            }
        );

        $botman->listen();

        return new Response();
    }

    
    #[Route('/chat/frame', name: 'chat_frame')]
    public function chatframe(): Response
    {
        return $this->render('chatbot/chatbot.html.twig');
    }
}
