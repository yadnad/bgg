<?php namespace App\Http\Controllers;

use \App\Games as Game;
use \Request as Request;
use \App\Collection as Collection;
use \Log as Log;
use \DB as DB;

class SearchController extends Controller {

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
     * Search for a game by name
     *
     */
    public function index()
    {
        // Array of wishlist priorities
        $wishlistPriorities = array(
            1 => "Must have",
            2 => "Love to have",
            3 => "Like to have",
            4 => "Thinking about it",
            5 => "Don't buy this"
        );

        // Get all search form inputs
        $keyword    = Request::input('keyword');
        $display    = Request::input('format');
        $user       = (int) Request::input('user');
        $owned      = (int) Request::input('owned');
        $trade      = (int) Request::input('trade');
        $wishlist   = (int) Request::input('wishlist');
        $toPlay     = (int) Request::input('toplay');
        $preordered = (int) Request::input('preordered');
        $sortBy     = Request::get('sortBy');
        $order      = Request::get('sortOrder');

        // Build up the base query
        $collectionQuery = Collection::select(
                                'collections.own',
                                'collections.wishlist',
                                'collections.wishlist_priority',
                                'collections.want_to_play',
                                'collections.for_trade',
                                'collections.preordered',
                                'collections.num_plays',
                                'games.id',
                                'games.name',
                                'games.thumbnail',
                                DB::raw('CONCAT(first_Name, " ", last_Name) AS full_name')
                            )
                            ->join('games', 'games.id', '=', 'collections.game_id')
                            ->join('geeks', 'geeks.id', '=', 'collections.geek_id');

        // Add a search term if something was entered
        if ($keyword != '') $collectionQuery->where('name', 'like', "%$keyword%");

        // Add user ID if a specific user was selected
        if ($user != 'all') $collectionQuery->where('geek_id', $user);

        // Add additional filters for games owned, for trade, etc.
        if ($owned || $trade || $wishlist || $toPlay || $preordered) {
            $collectionQuery->where(function($query) use ($owned, $trade, $wishlist, $toPlay, $preordered) {
                if ($owned) $query->orWhere('own', '=', 1);
                if ($trade) $query->orWhere('for_trade', '=', 1);
                if ($wishlist) $query->orWhere('wishlist', '=', 1);
                if ($toPlay) $query->orWhere('want_to_play', '=', 1);
                if ($preordered) $query->orWhere('preordered', '=', 1);
            });
        }

        // Sort the collection if a table header was clicked
        $allowedSorts = array('name');
        if ($sortBy && $order && in_array($sortBy, $allowedSorts)) {
            $collections = $collectionQuery->orderBy($sortBy, $order)->get();
        } else {
            $collections = $collectionQuery->orderBy('name')->get();
        }

        // Create the final array of games to be sent to the view
        $games = array();
        foreach ($collections as $game) {
            if (!array_key_exists($game->id, $games)) {
                $games[$game->id] = array(
                    'id' => $game->id,
                    'thumbnail' => $game->thumbnail,
                    'name' => $game->name,
                    'owned' => array(),
                    'wishlist' => array(),
                    'for_trade' => array(),
                    'to_play' => array(),
                    'preordered' => array(),
                    'played' => array()
                );
            }
            if ($game->own) $games[$game->id]['owned'][] = $game->full_name;
            if ($game->wishlist) $games[$game->id]['wishlist'][] = $game->full_name . " ({$wishlistPriorities[$game->wishlist_priority]})";
            if ($game->for_trade) $games[$game->id]['for_trade'][] = $game->full_name;
            if ($game->want_to_play) $games[$game->id]['want_to_play'][] = $game->full_name;
            if ($game->preordered) $games[$game->id]['preordered'][] = $game->full_name;
            if ($game->num_plays) $games[$game->id]['played'][] = $game->full_name;
        }

        // Sort the user names for each category for each game
        foreach ($games as $id => $game) {
            asort($games[$id]['owned']);
            asort($games[$id]['wishlist']);
            asort($games[$id]['for_trade']);
            asort($games[$id]['to_play']);
            asort($games[$id]['preordered']);
            asort($games[$id]['played']);
        }

        // Get the total to display
        $total = number_format(count($games));
        $plural = ($total == 1) ? "game" : "games";
        $count = "$total $plural found";

        $viewType = ($display == 'grid') ? 'grid' : 'thumbnail';
        $view = view($viewType)
                    ->with('games', $games)
                    ->with('priorities', $wishlistPriorities)
                    ->with('count', $count);

        return $view;
    }


}
