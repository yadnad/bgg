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
        Log::useFiles(storage_path().'/laravel.log');

        $wishlistPriorities = array(
            1 => "Must have",
            2 => "Love to have",
            3 => "Like to have",
            4 => "Thinking about it",
            5 => "Don't buy this"
        );

        $keyword    = Request::input('keyword');
        $display    = Request::input('format');
        $user       = Request::input('user');
        $owned      = Request::input('owned');
        $trade      = Request::input('trade');
        $wishlist   = Request::input('wishlist');
        $toPlay     = Request::input('toplay');
        $preordered = Request::input('preordered');

        $collectionQuery = Collection::select(
                                'collections.own',
                                'collections.wishlist',
                                'collections.want_to_play',
                                'collections.for_trade',
                                'collections.preordered',
                                'games.id',
                                'games.name',
                                'games.thumbnail',
                                DB::raw('CONCAT(first_Name, " ", last_Name) AS full_name')
                            )
                            ->join('games', 'games.id', '=', 'collections.game_id')
                            ->join('geeks', 'geeks.id', '=', 'collections.geek_id');

        if ($keyword != '') $collectionQuery->where('name', 'like', "%$keyword%");

        if ($user != 'all') $collectionQuery->where('geek_id', $user);

        if ($owned || $trade || $wishlist || $toPlay || $preordered) {
            $collectionQuery->where(function($query) use ($owned, $trade, $wishlist, $toPlay, $preordered) {
                if ($owned) $query->orWhere('own', '=', 1);
                if ($trade) $query->orWhere('for_trade', '=', 1);
                if ($wishlist) $query->orWhere('wishlist', '=', 1);
                if ($toPlay) $query->orWhere('want_to_play', '=', 1);
                if ($preordered) $query->orWhere('preordered', '=', 1);
            });
        }

        $collections = $collectionQuery->orderBy('name')->get();

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
                    'preordered' => array()
                );
            }
            if ($game->own) $games[$game->id]['owned'][] = $game->full_name;
            if ($game->wishlist) $games[$game->id]['wishlist'][] = $game->full_name;
            if ($game->for_trade) $games[$game->id]['for_trade'][] = $game->full_name;
            if ($game->want_to_play) $games[$game->id]['want_to_play'][] = $game->full_name;
            if ($game->preordered) $games[$game->id]['preordered'][] = $game->full_name;
        }

        $viewType = ($display == 'grid') ? 'grid' : 'thumbnail';
        $view = view($viewType)
                    ->with('games', $games)
                    ->with('priorities', $wishlistPriorities);

        return $view;
    }


}
