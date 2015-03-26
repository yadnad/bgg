    <div class="row toolbar">
        <div class="col-md-2"></div>

        <div class="col-md-8">
            <form class="toolbar-form">
                <label class="hidden-xs">Search: </label>

                <select name="user" class="toolbar-users">
                    <option value="all">All Users</option>
                    @foreach ($geeks as $geek)
                            <option value="{{ $geek->id }}">{{ $geek->first_name }} {{ $geek->last_name }}</option>
                    @endforeach
                </select>

                <input type="text" name="keyword" id="keyword" placeholder="game name" />

                <input type="submit" name="search" class="search-button hidden-xs" value="Go" />

                <button type="button" class="btn btn-default gridview" title="Grid View" aria-label="Left Align">
                    <span class="glyphicon glyphicon-th-list" aria-hidden="true"></span>
                </button>

                <button type="button" class="btn btn-default thumbview" title="Thumbnail View" aria-label="Left Align">
                    <span class="glyphicon glyphicon-th" aria-hidden="true"></span>
                </button>

                <button type="button" class="btn btn-default refresh" title="Refresh User Collection" aria-label="Left Align">
                    <span class="glyphicon glyphicon-refresh" aria-hidden="true"></span>
                </button>

                <input type="hidden" name="format" value="grid" />
                <input type="hidden" name="sortBy" value="name" />
                <input type="hidden" name="sortOrder" value="asc" />

                <div class="toolbar-filters">
                    <ul class="list-inline">
                        <li>
                            <input type="checkbox" name="owned" value="owned" id="owned" checked="checked" />
                            <label for="owned">Owned</label>
                        </li>
                        <li>
                            <input type="checkbox" name="wishlist" value="wishlist" id="wishlist" checked="checked" />
                            <label for="wishlist">Wishlist</label>
                        </li>
                        <li>
                            <input type="checkbox" name="trade" value="trade" id="trade" checked="checked" />
                            <label for="trade">Trade</label>
                        </li>
                        <li>
                            <input type="checkbox" name="toplay" value="toplay" id="toplay" checked="checked" />
                            <label for="toplay">Want to play</label>
                        </li>
                        <li>
                            <input type="checkbox" name="preordered" value="preordered" id="preordered" checked="checked" />
                            <label for="preordered">Preordered</label>
                        </li>
                    </ul>
                </div>
            </form>
        </div>

        <div class="col-md-2"></div>
    </div>
