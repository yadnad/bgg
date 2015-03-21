    <div class="row toolbar">
        <div class="col-md-2"></div>

        <div class="col-md-8">
            <label for="keyword">Search: </label>
            <input type="text" name="keyword" id="keyword" />
            <input type="submit" name="search" class="search-button" value="Go" />

            <button type="button" class="btn btn-default gridview" aria-label="Left Align">
                <span class="glyphicon glyphicon-th-list" aria-hidden="true"></span>
            </button>
            <button type="button" class="btn btn-default thumbview" aria-label="Left Align">
                <span class="glyphicon glyphicon-th" aria-hidden="true"></span>
            </button>
            <input type="hidden" name="format" value="grid" />

            <select name="user" class="toolbar-refresh">
                @foreach ($geeks as $geek)
                        <option value="{{ $geek->id }}">{{ $geek->first_name }} {{ substr($geek->last_name,0,1) }}.</option>
                @endforeach
            </select>
            <input type="button" name="refresh" value="Refresh" />
        </div>

        <div class="col-md-2"></div>
    </div>
