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
use Google\Client as GoogleClient;
use Google\Service\YouTube;

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

        ///Youtube : permet de lancer une vidéo youtube dans le chat si la question comporte résumé 
        
        // Youtube
        // --------------------------------

        $botman->hears(
            '(.*)(résumé|résumé de|match de|match entre|match) {search}',
            function (BotMan $bot, $search) {
                $youtubeApiKey =$_ENV['YOUTUBE_API_KEY'];
                $googleClient = new GoogleClient();
                $googleClient->setDeveloperKey($youtubeApiKey);
        
                $youtubeService = new Youtube($googleClient);
        
                ///renvoit une liste de résultat, on prend le premier
                $searchResponse = $youtubeService->search->listSearch('id,snippet', [
                    'q' => $search,
                    'type' => 'video',
                    'maxResults' => 1
                ]);
        
                ///Si le search ne contient pas un nom d'équipe (faire cela en regex), on renvoit une erreur
                if (!$this->checkQuestionTeam($search)) {
                    $bot->reply('Mmmh cela ne paraît pas être un match de foot, veuillez réessayer.');
                }
                ///Si on a trouvé une vidéo, on renvoit l'iframe de la vidéo
                else if (count($searchResponse['items']) > 0) {
                    $videoId = $searchResponse['items'][0]['id']['videoId'];
                    $videoUrl = $this->iFrameYoutubeVideo($videoId);
                    $bot->reply($videoUrl);
                } else {
                    $bot->reply('Sorry, I did not find any video on this match. Désolé, je n\'ai pas trouvé de vidéo sur ce match.');
                }

                
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

    private function iFrameYoutubeVideo(string $videoId): ?string
    {
        /*
            Méthode pour récupérer une vidéo youtube en iframe
            La méthode prend un string en paramètre qui est l'id de la vidéo
            La méthode renvoit un string qui est le code html de l'iframe
            On utilise l'url https://www.youtube.com/embed/ qui permet d'obtenir une vidéo embed
        */
        $urlToEmbed =  'https://www.youtube.com/embed/' . $videoId;
        $iframe = '<iframe width="260" height="315" src="' . $urlToEmbed . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
        return $iframe;

    }

    private function checkQuestionTeam(string $question): bool
    {
        /*
            Méthode pour vérifier si la question contient un nom d'équipe
            La méthode prend un string en paramètre qui est la question
            La méthode renvoit un booléen
            On utilise la méthode str_contains qui permet de vérifier si un string contient un autre string
        */
        $tableauTeam = ["psg", "madrid", "liverpool", "OL", "OM"];
        foreach ($tableauTeam as $team) {
            if (str_contains($question,$team) || str_contains($question,$team)) {
                return true;
            }
        }
        return false;
    }
    
    #[Route('/chat/frame', name: 'chat_frame')]
    public function chatframe(): Response
    {
        return $this->render('chatbot/chatbot.html.twig');
    }
}
