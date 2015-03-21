
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
                    <a href="http://boardgamegeek.com/boardgame/{{ $game->id }}" target="_blank">
                        <img src="http://{{ $game->thumbnail }}" alt="{{{ $game->name }}}" width="180px">
                    </a>
                </td>
                <td class="game-name">
                    <a href="http://boardgamegeek.com/boardgame/{{ $game->id }}">{{{ $game->name }}}</a>
                </td>
                <td class="user-name">
                    @foreach($collections[$game->id] as $stats)
                    <p>
                        @if ($stats->own == '1')
                            {{ $stats->first_name }} {{ $stats->last_name }}
                        @endif
                    </p>
                    @endforeach
                </td>
                <td>
                    @foreach($collections[$game->id] as $stats)
                    <p>
                        @if ($stats->wishlist == '1')
                            {{ $stats->first_name }} {{ $stats->last_name }} ({{ $priorities[$stats->wishlist_priority] }})
                        @endif
                    </p>
                    @endforeach
                </td>
                <td>
                    @foreach($collections[$game->id] as $stats)
                    <p>
                        @if ($stats->for_trade == '1')
                            {{ $stats->first_name }} {{ $stats->last_name }}
                        @endif
                    </p>
                    @endforeach
                </td>
                <td>
                    @foreach($collections[$game->id] as $stats)
                    <p>
                        @if ($stats->want_to_play == '1')
                            {{ $stats->first_name }} {{ $stats->last_name }}
                        @endif
                    </p>
                    @endforeach
                </td>
                <td>
                    @foreach($collections[$game->id] as $stats)
                    <p>
                        @if ($stats->preordered == '1')
                            {{ $stats->first_name }} {{ $stats->last_name }}
                        @endif
                    </p>
                    @endforeach
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="col-md-1"></div>
