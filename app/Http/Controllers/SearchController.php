<?php namespace App\Http\Controllers;

use \App\Games as Game;
use \Request as Request;
use \App\Collection as Collection;
use \Log as Log;

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

        $name    = Request::input('keyword');
        $display = Request::input('format');
        $games   = Game::select('id', 'name', 'thumbnail')->where('name', 'like', "%$name%")->get();
        $collections = Collection::whereIn('game_id', $games->lists('id'))
                            ->join('geeks', 'geeks.id', '=', 'collections.geek_id')
                            ->get()->groupBy('game_id');

        $viewType = ($display == 'grid') ? 'grid' : 'thumbnail';
        $view = view($viewType)
                    ->with('games', $games)
                    ->with('collections', $collections)
                    ->with('priorities', $wishlistPriorities);

        return $view;
    }


}
