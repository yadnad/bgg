    <div class="row toolbar">
        <div class="col-md-2"></div>

        <div class="col-md-8">
            <form class="toolbar-form">
                <label>Search: </label>

                <select name="user" class="toolbar-refresh hidden-xs">
                    <option value="all">All Users</option>
                    @foreach ($geeks as $geek)
                            <option value="{{ $geek->id }}">{{ $geek->first_name }} {{ $geek->last_name }}</option>
                    @endforeach
                </select>

                <input type="text" name="keyword" id="keyword" />

                <input type="submit" name="search" class="search-button hidden-xs" value="Go" />

                <button type="button" class="btn btn-default gridview hidden-xs" aria-label="Left Align">
                    <span class="glyphicon glyphicon-th-list" aria-hidden="true"></span>
                </button>
                <button type="button" class="btn btn-default thumbview hidden-xs" aria-label="Left Align">
                    <span class="glyphicon glyphicon-th" aria-hidden="true"></span>
                </button>

                <input type="button" name="refresh" value="Refresh" class="hidden-xs" />

                <input type="hidden" name="format" value="grid" />

                <div class="toolbar-filters">
                    <input type="checkbox" name="owned" value="owned" id="owned" checked="checked" />
                    <label for="owned">Owned</label>
                    <input type="checkbox" name="wishlist" value="wishlist" id="wishlist" checked="checked" />
                    <label for="wishlist">Wishlist</label>
                    <input type="checkbox" name="trade" value="trade" id="trade" checked="checked" />
                    <label for="trade">Trade</label>
                    <input type="checkbox" name="toplay" value="toplay" id="toplay" checked="checked" />
                    <label for="toplay">Want to play</label>
                    <input type="checkbox" name="preordered" value="preordered" id="preordered" checked="checked" />
                    <label for="preordered">Preordered</label>
                </div>
            </form>
        </div>

        <div class="col-md-2"></div>
    </div>
