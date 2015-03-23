$('document').ready(function () {
    var thread = null;

    $("input[name='refresh']").click(function() { refreshCollection(); });
    $("input[name='search']").click(function() { searchCollection(); });
    $("input[name='keyword']").focus().keyup(function() {
        clearTimeout(thread);
        var target = $(this);
        thread = setTimeout(function() { searchCollection(); }, 400);
    });

    $('.thumbview').click(function() {
        $('input[name="format"]').val('thumbnail');
        searchCollection();
    })

    $('.gridview').click(function() {
        $('input[name="format"]').val('grid');
        searchCollection();
    })

    $('.toolbar-form').on( "submit", function( event ) {
        event.preventDefault();
    })

})


function refreshCollection() {
    $.get('refresh/' + $('select[name="user"]').val());
}

function searchCollection() {
    var searchTerm  = $("input[name='keyword']").val(),
        displayType = $("input[name='format']").val(),
        userVal     = $("select[name='user']").val(),
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
