$('document').ready(function () {
    var thread = null;

    $("input[name='refresh']").click(function() { refreshCollection(); });
    $("input[name='search']").click(function() { searchCollection(); });
    $("input[name='keyword']").focus().keyup(function() {
        clearTimeout(thread);
        var target = $(this);
        thread = setTimeout(function() { searchCollection(target.val()); }, 400);
    });

    $('.thumbview').click(function() {
        $('input[name="format"]').val('thumbnail');
        searchCollection();
    })

    $('.gridview').click(function() {
        $('input[name="format"]').val('grid');
        searchCollection();
    })

})


function refreshCollection() {
    $.get('/refresh/' + $('select[name="user"]').val());
}

function searchCollection() {
    var searchTerm = $("input[name='keyword']").val(),
        displayType = $("input[name='format']").val();

    if (searchTerm.length == 0) return;

    $.get('/search',
        { keyword: searchTerm, format: displayType },
        function(data) {
            $('.game-stats').hide();
            $('.search-results').html(data);
        }
    );
}
