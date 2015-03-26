    <p class="search-count">{{ $count }}</p>
    @foreach(array_chunk($games, 5) as $gamesRow)
    <div class="row thumbnail-row">
        <div class="col-md-1"></div>
        @foreach ($gamesRow as $game)
            <div class="col-md-2">
                <a href="http://boardgamegeek.com/boardgame/{{ $game['id'] }}" target="_blank" class="thumbnail">
                    <img src="http://{{ $game['thumbnail'] }}" alt="{{{ $game['name'] }}}" width="180px">
                </a>
                <p>
                    <a href="http://boardgamegeek.com/boardgame/{{ $game['id'] }}">{{{ $game['name'] }}}</a>
                </p>
            </div>
        @endforeach
        <div class="col-md-1"></div>
    </div>
    @endforeach
