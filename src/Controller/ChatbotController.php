<?php

namespace App\Controller;

use Google\Client;
use BotMan\BotMan\BotMan;
use RegisterConversation;
use Google\Service\YouTube;
use BotMan\BotMan\BotManFactory;
use BotMan\Drivers\Web\WebDriver;
use BotMan\BotMan\Cache\SymfonyCache;
use BotMan\BotMan\Drivers\DriverManager;
use App\Chatbot\Middleware\ReceiveMiddleware;
use BotMan\BotMan\Messages\Attachments\Image;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ChatbotController extends AbstractController
{
    #[Route('/chat/message', name: 'chat_message')]
    public function message(): Response
    {
        /*
            Config
        */
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
            Classique
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


        /*
            Youtube : permet de lancer une vidéo youtube dans le chat si la question comporte résumé, match, match de, match entre, résumé de, résumé entre, etc.
        */
        $botman->hears(
            '(.*)(résumé|résumé de|résumé entre|match de|match entre|match) {search}',
            function (BotMan $bot, $search) {
                $youtubeApiKey =$_ENV['YOUTUBE_API_KEY'];
                $googleClient = new Client();
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


        /*
            Giphy
        */
        ///Si la question comporte un mot injurieux, la réponse sera un gif de chat qui fait non de la tête
        $botman->hears(
            '(.*)(pute|connard|salope|enculé|encule|enculer|enculée|enculées|enculés|encule|enculee|connasse|tg)(.*)',
            function (BotMan $bot) {
                $apiGiphyKey = $_ENV['GIPHY_API_KEY'];
                $search = 'don\'t do that';
                $url= "http://api.giphy.com/v1/gifs/search?api_key=".$apiGiphyKey."&q=".urlencode($search)."&limit=20";
                $content = file_get_contents($url);
                $response = json_decode($content);
                ///prend un gif aléatoire dans la liste de résultat
                $gifUrl = $response->data[rand(0, 19)]->images->original->url;
                $attachment = new Image($gifUrl);
                $messageInsulte = OutgoingMessage::create('Surveillez votre langage s\'il vous plaît !')
                        ->withAttachment($attachment);
                $bot->reply($messageInsulte);
            });


            ///Si la question comporte au revoir, ou bye ou good bye, le bot répondra au revoir avec un gif de chat qui fait au revoir
        $botman->hears(
            '(.*)(au revoir|goobye|bye|ciao)(.*)',
            function (BotMan $bot) {
                $apiGiphyKey = $_ENV['GIPHY_API_KEY'];
                $searchAuRevoir = 'cat bye';
                $urlAuRevoir= "http://api.giphy.com/v1/gifs/search?api_key=".$apiGiphyKey."&q=".urlencode($searchAuRevoir)."&limit=10";
                $contentAuRevoir = file_get_contents($urlAuRevoir);
                $responseAuRevoir = json_decode($contentAuRevoir);
                ///prend un gif aléatoire dans la liste de résultat
                $gifUrl = $responseAuRevoir->data[rand(0, 9)]->images->original->url;
                $attachmentAuRevoir = new Image($gifUrl);
                $messageAuRevoir = OutgoingMessage::create('Au revoir !')
                        ->withAttachment($attachmentAuRevoir);
                $bot->reply($messageAuRevoir);
            });


        /*
            Informations sur le mail de l'utilisateur
        */
        function handleMailRequest($botman, $mail) {
            $botman->userStorage()->save(['mail' => $mail]);
            $botman->reply($mail);
        }

        // Define a function to handle mail retrieval requests
        function handleMailRetrievalRequest($botman) {
            $botman->reply('Ton mail est le suivant : ' . $botman->userStorage()->get('mail'));
        }

        $botman->hears('(Mon email est le suivant) {mail}(.*)', function ($botman, $mail) {
            handleMailRequest($botman, 'Salut, je retiens que ton mail est '. $mail);
        });

        $botman->hears('(Quel est mon email ?)', function ($botman) {
            handleMailRetrievalRequest($botman, '');
        });

        /*
            Informations sur le username de l'utilisateur
        */
        function handleUserNameRequest($botman, $pseudo) {
            $botman->userStorage()->save(['pseudo' => $pseudo]);
            $botman->reply($pseudo);
        }

        // Define a function to handle username retrieval requests
        function handleUserNameRetrievalRequest($botman) {
            $botman->reply('Ton pseudo est le suivant : ' . $botman->userStorage()->get('pseudo'));
        }

        $botman->hears('(Mon pseudo est le suivant) {pseudo}(.*)', function ($botman, $pseudo) {
            handleUserNameRequest($botman, 'Salut, je retiens que ton pseudo est '. $pseudo);
        });

        $botman->hears('(Quel est mon pseudo ?)', function ($botman) {
            handleUserNameRetrievalRequest($botman, '');
        });

        /*
            Information sur le site, où s'inscrire
        */
        $botman->hears(
            '(.*)(site|inscription|inscrire|intéressé|interessé|interesse|inscris|inscrire|inscrivez)(.*)',
            function (BotMan $bot) {
                $bot->reply('Vous pouvez vous inscrire sur le lien suivant : http://127.0.0.1:8000/register');
            });



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
        $tableauTeam = ["psg", "madrid", "liverpool", "ol", "om"];
        foreach ($tableauTeam as $team) {
            if (str_contains(strtolower($question), $team)) {
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
