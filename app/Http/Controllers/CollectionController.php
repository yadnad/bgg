<?php namespace App\Http\Controllers;

use \Log as Log;
use \App\Games as Game;
use \App\Geeks as Geek;
use \App\Collection as Collection;
use HTML;


class CollectionController extends Controller {

    /*
    |--------------------------------------------------------------------------
    | User Controller
    |--------------------------------------------------------------------------
    |
    | This controller user collection page
    |
    */

    const API_URL = 'http://www.boardgamegeek.com/xmlapi2/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Main page with search options
     *
     * @return Response
     */
    public function index()
    {
        $geeks = Geek::orderBy('first_name')->get();
        $gameStats = $this->gameStats();
        $view = view('main')
                    ->with('geeks', $geeks)
                    ->with('stats', $gameStats);
        return $view;
    }

    /**
     * Refresh a users collection of games
     *
     * @param int $id users.bgg_username
     * @return Response
     */
    public function refreshCollection($geekId)
    {
        // Get the user info
        $geek = Geek::find($geekId);
        if (empty($geek)) return;
        $userName = $geek->first_name . ' ' . $geek->last_name;
        Log::info("Fetching {$userName}'s collection");

        // Retrieve a list IDs for all the games in our database
        $games = Game::all()->modelKeys();

        // Retrieve the users currently stored collection
        $userGames = Collection::where('geek_id', $geekId)->lists('bgg_collection_id', 'game_id');

        // Get the collection XML from the BGG API
        $bggXML = $this->fetchGames($geek, $userName);

        if ($bggXML === false) {
            echo "Error retrieving user's collection :(";
            return;
        }

        // Create an array to hold all the collection IDs from the latest collection
        $newCollection = array();

        foreach ($bggXML as $gameInfo) {
            $attributes = $gameInfo->attributes();
            $newCollection[] = (int) $attributes->collid;
            $gameId = (int) $attributes->objectid;

            // Check if this game is in our games database
            if (!in_array($gameId, $games)) {
                $game = new Game();
                $game->id        = $gameId;
                $game->name      = $gameInfo->name;
                $game->published = $gameInfo->yearpublished;
                $game->thumbnail = str_replace('//', '', $gameInfo->thumbnail);
                $game->image     = str_replace('//', '', $gameInfo->image);
                $game->save();
                $games[] = $gameId;
                $allGames[$game->id] = $gameId;
            }

            // Get user stats for the game (owned, for trade, etc.)
            $statusAttributes = $gameInfo->status->attributes();

            // Check if the game is already in the users collection
            if (!array_key_exists($gameId, $userGames)) {
                $collection = new Collection();
                $collection->bgg_collection_id = $attributes->collid;
                $collection->game_id           = $attributes->objectid;
                $collection->geek_id           = $geekId;
                Log::info('Adding ' . $gameInfo->name . ' to ' . $userName . "'s collection");
            } else {
                $collection = Collection::where('geek_id', $geekId)->where('bgg_collection_id', $userGames[$gameId])->first();
                Log::info('Updating ' . $gameInfo->name . ' in ' . $userName . "'s collection");
            }

            // Add or update fields that can change
            $collection->own               = $statusAttributes->own;
            $collection->prev_owned        = $statusAttributes->prevowned;
            $collection->for_trade         = $statusAttributes->fortrade;
            $collection->want              = $statusAttributes->want;
            $collection->want_to_play      = $statusAttributes->wanttoplay;
            $collection->want_to_buy       = $statusAttributes->wanttobuy;
            $collection->wishlist          = $statusAttributes->wishlist;
            $collection->preordered        = $statusAttributes->preordered;
            $collection->last_modified     = $statusAttributes->lastmodified;
            $collection->num_plays         = $gameInfo->numplays;

            if (isset($statusAttributes->wishlistpriority)) {
                $collection->wishlist_priority = $statusAttributes->wishlistpriority;
            }

            $collection->save();
        }

        // Remove any games in the collection database that no longer exist in BGG
        foreach ($userGames as $gameId => $collectionId) {
            if (!in_array($collectionId, $newCollection)) {
                Log::info('Deleting ' . $gameId);
                $toDelete = Collection::find($gameId);
                if ($toDelete) $toDelete->delete();
            }
        }

        Log::info("Finished updating {$userName}'s collection");
    }


    public function fetchGames($geek, $userName)
    {
        try {
            // Retrieve the users collection from BGG
            $url = self::API_URL . 'collection?username=' . $geek->bgg_username;
            $client = new \GuzzleHttp\Client();
            $response = $client->get($url, ['timeout' => 10, 'exceptions' => false]);
            $statusCode = $response->getStatusCode();

            if ($statusCode == 503) {
                return false;
            }

            // TODO Add error handling for guzzle request and 404 for bad user name

            /**
             * If we get a 202 from BGG our request was queued.
             * Wait a few seconds then try again.
             */
            $count = 1;
            if ($statusCode == 202) {
                while ($count <= 15) {
                    Log::info("Status 202 ($count) when fetching collection for $userName");
                    sleep(3);
                    $response = $client->get($url, ['timeout' => 10, 'exceptions' => false]);
                    $statusCode == $response->getStatusCode();
                    if ($statusCode == 200) break;
                    $count++;
                }
            }

            if ($statusCode != 200) {
                Log::error("Failed to fetch collection for $userName: $statusCode");
                return false;
            }

            return new \SimpleXMLElement($response->getBody());

        } catch (Exception $e) {
            return false;
        }
    }


    /**
     * Get Game Stats
     *
     * @return array
     */
    public function gameStats()
    {
        $totalGames = Collection::owned()->get()->count();
        $toPlay = Collection::toPlay()->get()->count();
        $forTrade = Collection::forTrade()->get()->count();
        $wishlist = Collection::wishlist()->get()->count();

        return array(
            'total'    => number_format($totalGames, 0),
            'play'     => number_format($toPlay, 0),
            'trade'    => number_format($forTrade, 0),
            'wishlist' => number_format($wishlist, 0)
        );

    }

    /**
     * Get game info
     *
     */
    public function getGameInfo($gameId)
    {
        try {
            // Retrieve the game data from BGG
            $url = self::API_URL . 'things?stats=1&id=' . $gameId;
            $client = new \GuzzleHttp\Client();
            $response = $client->get($url, ['timeout' => 10, 'exceptions' => false]);
            $statusCode = $response->getStatusCode();

            if ($statusCode == 503) {
                return false;
            }

            $gameInfo = new \SimpleXMLElement($response->getBody());
Log::info(print_r(array_keys($gameInfo->item),true));
        } catch (Exception $e) {
            Log::info(print_r($e->getMessage(),true));
        }
    }
}
