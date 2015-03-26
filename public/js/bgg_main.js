function refreshCollection() {
    $.get('refresh/' + $('select[name="user"]').val());
}

function searchCollection() {
    var searchTerm  = $("input[name='keyword']").val(),
        displayType = $("input[name='format']").val(),
        userVal     = $("select[name='user']").val(),
        sort        = $("input[name='sortBy']").val(),
        order       = $("input[name='sortOrder']").val(),
        ownedVal    = ($("#owned").is(':checked')) ? 1 : 0,
        tradeVal    = ($("#trade").is(':checked')) ? 1 : 0,
        wishlistVal = ($("#wishlist").is(':checked')) ? 1 : 0,
        toPlayVal   = ($("#toplay").is(':checked')) ? 1 : 0,
        preorderedVal = ($("#preordered").is(':checked')) ? 1 : 0;

    $.get('search',
        {
            keyword: searchTerm,
            format: displayType,
            user: userVal,
            sortBy: sort,
            sortOrder: order,
            owned: ownedVal,
            trade: tradeVal,
            wishlist: wishlistVal,
            toplay: toPlayVal,
            preordered: preorderedVal
        },
        function(data) {
            $('.game-stats').hide();
            $('.search-results').html(data);
        }
    );
}

function sortResults(column) {
    var order = $('input[name="sortOrder"]').val(),
        sortBy = $('input[name="sortBy"]').val();

    if (sortBy !== column) {
        $('input[name="sortOrder"]').val('asc');
    } else if (order === 'asc') {
        $('input[name="sortOrder"]').val('desc');
    } else {
        $('input[name="sortOrder"]').val('asc');
    }

    $('input[name="sortBy"]').val(column);
    searchCollection();
}

$('document').ready(function () {
    var thread = null;

    $(".refresh").click(function() { refreshCollection(); });
    $("input[name='search']").click(function() { searchCollection(); });
    $("input[name='keyword']").focus().keyup(function() {
        clearTimeout(thread);
        thread = setTimeout(function() { searchCollection(); }, 400);
    });

    $('.thumbview').click(function() {
        $('input[name="format"]').val('thumbnail');
        searchCollection();
    });

    $('.gridview').click(function() {
        $('input[name="format"]').val('grid');
        searchCollection();
    });

    $('.toolbar-form').on( "submit", function( event ) {
        event.preventDefault();
    });

    $('.toolbar-filters input').click(function() {
        searchCollection();
    });
});
