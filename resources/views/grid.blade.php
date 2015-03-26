
    <div class="col-md-1"></div>
    <div class="col-md-10">
        <p class="search-count">{{ $count }}</p>
        @if ($count > 0)
        <table class="table table-bordered search-grid">
            <thead>
                <th></th>
                <th><span class="sortable" data-sort="name">Name</span></th>
                <th><span data-sort="own">Owned</span></th>
                <th><span data-sort="wishlist">Wishlist</span></th>
                <th><span data-sort="for_trade">For Trade</span></th>
                <th><span data-sort="want_to_play">Want to Play</span></th>
                <th><span data-sort="preordered">Preordered</span></th>
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
        @endif
    </div>
    <div class="col-md-1"></div>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.sortable').click(function() {
                sortResults($(this).attr('data-sort'));
            });

        })
    </script>
