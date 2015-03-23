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
    public function showCollection()
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
        Log::useFiles(storage_path().'/laravel.log');

        // Get the user info
        $geek = Geek::find($geekId);
        if (empty($geek)) return;
        $userName = $geek->first_name . ' ' . $geek->last_name;
        Log::info("Fetching {$userName}'s collection");

        // Retrieve a list of all games in our database
        $games = Game::select('id', 'name', 'thumbnail')->get();

        // Retrieve the users currently stored collection
        $userGames = Collection::where('geek_id', $geekId)->lists('bgg_collection_id', 'game_id');

        $bggXML = $this->fetchGames($geek);

        if ($bggXML === false) {
            echo "Error retrieving user's collection :(";
            return;
        }

        // Create an array to hold all the game IDs in the latest collection
        $newCollection = array();

        foreach ($bggXML as $gameInfo) {
            $attributes = $gameInfo->attributes();
            $newCollection[] = (int) $attributes->collid;
            $gameId = (int) $attributes->objectid;

            // Check if we've already saved this game before
            if (!$games->contains($gameId)) {
                $game = new Game();
                $game->id        = $gameId;
                $game->name      = $gameInfo->name;
                $game->published = $gameInfo->yearpublished;
                $game->thumbnail = str_replace('//', '', $gameInfo->thumbnail);
                $game->image     = str_replace('//', '', $gameInfo->image);
                $game->save();
                $games->push($game);
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
                $toDelete->delete();
            }
        }

        Log::info("Finished updating {$userName}'s collection");
    }


    public function fetchGames($geek)
    {
        try {
            // Retrieve the users collection from BGG
            $url = self::API_URL . 'collection?username=' . $geek->bgg_username;
            $client = new \GuzzleHttp\Client();
            $response = $client->get($url);
            $statusCode = $response->getStatusCode();

            if ($statusCode == 503) {
                return false;
            }

            // TODO Add error handling for guzzle request and 404 for bad user name

            /**
             * If we get a 202 from BGG our request was queued.
             * Wait a second and then try again for up to 10 seconds.
             */
            $count = 0;
            if ($statusCode == 202) {
                while ($statusCode == 202 || $count == 10) {
                    sleep(1);
                    $response = $client->get($url);
                    $statusCode == $response->getStatusCode();
                    $count++;
                }
            }

            // TODO Add error message if we didn't get a response after 10 seconds

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

}
