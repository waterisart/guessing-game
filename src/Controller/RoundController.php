<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mercure\Authorization;
use Symfony\Component\Mercure\Discovery;
use App\Repository\RoundRepository;
use App\Repository\PlayerRepository;
use App\Entity\Round;

class RoundController extends AbstractController
{
    private $roundRepository;
    private $playerRepository;

    public function __construct(RoundRepository $roundRepository, PlayerRepository $playerRepository, HubInterface $hub)
    {
        $this->roundRepository = $roundRepository;
        $this->playerRepository = $playerRepository;
        $this->hub = $hub;
    }

    #[Route('/', name:'app_game_index')]
    public function index(): Response
    {
        return $this->render('home.html.twig');
    }
    #[Route('/game', name:'app_game_game')]
    public function game(): Response
    {
        return $this->render('round.html.twig');
    }

    #[Route('/discover')]
    public function discover(Request $request, Discovery $discovery, Authorization $authorization): JsonResponse
    {
        $discovery->addLink($request);
        $username = $this->getUser()->getUsername();

        $authorization->setCookie($request, 
            ['/round', '/number/'.$username, '/initializeRound', '/roundStatus'], 
            ['/round', '/number/'.$username, '/initializeRound', '/roundStatus']
        );
       
        $response = $this->json([$username]);

        return $response;

    }

    #[Route('/join')]
    public function join(HubInterface $hub): JsonResponse
    {
        $username = $this->getUser()->getUsername();

        if(!is_null($username)) {
            $player = $this->playerRepository->findOneByUsername($username);
            $round = $this->roundRepository->findOneByIsFinished(false);
            
            if(is_null($round)) {
                $round = new Round();
                $round->setNumberToGuess(random_int(1, 100));
                $round->setIsFinished(false);
                $this->roundRepository->addOrSave($round, true);
            } 

            $hasPlayerJoined = $round->getPlayers()->contains($player);
            if(!$hasPlayerJoined) {
                $this->roundRepository->addPlayerToRound($player, $round, true);
                $this->playerRepository->addRoundToPlayer($round, $player, true);
            
                $payload = [
                    'playerTemplate' => $this->getPlayerTemplate($username, $player->getId())
                ];
        
                $update = new Update(
                    '/round',
                    json_encode($payload)
                );
                
                $hub->publish($update);
                
                return $this->json('player ' . $username . ' added');
            }/*  else {
                $this->addRoundPlayers();
            } */

            return $this->json('player ' . $username . ' already joined');
        }

        return $this->json('player not loggen in');
    }

    #[Route('/loadJoinedPlayers')]
    public function loadJoinedPlayers(): JsonResponse
    {
        $this->addRoundPlayers();

        return $this->json('players dispatched');
    }

    #[Route('/guess')]
    public function guess(Request $request): JsonResponse
    {
        $numberGuessed = $request->get('_number');
        $username = $this->getUser()->getUsername();
        $round = $this->playerRepository->findOneByUsername($username)->getRounds()->last();

        $payload = ['numberGuessed' => $numberGuessed];
        $payload['username'] = $username;
        if($round->getIsFinished()) {
            $payload = ['status' => 'You lost 🙁, better luck next time! 🙂'];
        } else { 
            $numberToGuess = $round->getNumberToGuess();
            if($numberGuessed > $numberToGuess) {
                $payload['status'] = 'Lower! 🙂';
            } else if ($numberGuessed < $numberToGuess) {
                $payload['status'] = 'Higher! 🙂';
            } else {
                $payload['status'] = 'You win! 🎉';
                $payload['isWinner'] = true;
                $round->setWinner($username);
                $round->setIsFinished(true);
                $this->roundRepository->addOrSave($round, true);

                // send notification to all players except the winner that they lost
                $playersThatLost = $round->getPlayers();
                $playersThatLost->remove($this->playerRepository->findOneByUsername($username)->getId());
                foreach($playersThatLost as $player) {
                    $payloadLost['username'] = $player->getUsername();
                    $payloadLost['status'] = 'You lost 🙁, better luck next time! 🙂';
                    $updateLost = new Update(
                        '/roundStatus',
                        json_encode($payloadLost)
                    );
                    $this->hub->publish($updateLost);
                }
            }
        }

        $updatePlayer = new Update(
            '/number' . '/' . $username,
            json_encode($payload), 
        );
        $this->hub->publish($updatePlayer);

        // send update to all players with status of a specific player
        $update = new Update(
            '/roundStatus',
            json_encode($payload)
        );
        $this->hub->publish($update);

        return $this->json('published');
    }

    /**
     * populate a player template with unique identifiers based on player username
     */
    private function getPlayerTemplate(string $username = null, int $playerNumber): ?string
    {
        if(!is_null($username)){
            $round = $this->roundRepository->findOneByIsFinished(false);
            $playerTemplate = $this->renderView('player.html.twig', ['username'=>$username, 'playerNumber' => $playerNumber]);

            return $playerTemplate;
        }
    }

    /**
     * collect player templates for all players currently joined in the ongoing round
     * dispatch update with the player templates
     */
    private function addRoundPlayers(bool $publish = true): void 
    {
        $username = $this->getUser()->getUsername();
        if(!is_null($username)) {
            $round = $this->roundRepository->findOneByIsFinished(false);

            if(!is_null($round)) {
                $players = $round->getPlayers();

                $playerTemplates = '';
                if(!$players->isEmpty()){
                    while ($player = $players->current()) {
                        $playerTemplate = $this->getPlayerTemplate($player->getUsername(), $player->getId());
                        
                        $playerTemplates .= $playerTemplate;
                        $players->next();
                    }
                    $payload = [
                        'playerTemplate' => $playerTemplates
                    ];
            
                    if($publish) {
                        $update = new Update(
                            '/initializeRound',
                            json_encode($payload)
                        );

                        $this->hub->publish($update);
                    }
                }
            }
        }
    }
}