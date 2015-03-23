
    <div class="col-md-1"></div>
    <div class="col-md-10">
        <table class="table table-bordered search-grid">
            <thead>
                <th></th>
                <th>Name</th>
                <th>Owned</th>
                <th>Wishlist</th>
                <th>For Trade</th>
                <th>Want to Play</th>
                <th>Preordered</th>
            </thead>
            <tbody>
            @foreach($games as $game)
            <tr>
                <td class="search-image">
                    <a href="http://boardgamegeek.com/boardgame/{{ $game['id'] }}" target="_blank">
                        <img src="http://{{ $game['thumbnail'] }}" alt="{{{ $game['name'] }}}" width="180px">
                    </a>
                </td>
                <td class="game-name">
                    <a href="http://boardgamegeek.com/boardgame/{{ $game['id'] }}">{{{ $game['name'] }}}</a>
                </td>
                <td class="user-name">
                    @foreach($game['owned'] as $owned)
                        <p>{{ $owned }}</p>
                    @endforeach
                </td>
                <td class="user-name">
                    @foreach($game['wishlist'] as $wishlist)
                        <p>{{ $wishlist }}</p>
                    @endforeach
                </td>
                <td class="user-name">
                    @foreach($game['for_trade'] as $trade)
                        <p>{{ $trade }}</p>
                    @endforeach
                </td>
                <td class="user-name">
                    @foreach($game['to_play'] as $toPlay)
                        <p>{{ $toPlay }}</p>
                    @endforeach
                </td>
                <td class="user-name">
                    @foreach($game['preordered'] as $preordered)
                        <p>{{ $preordered }}</p>
                    @endforeach
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="col-md-1"></div>
