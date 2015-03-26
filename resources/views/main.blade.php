@extends('layouts.layout')

@section('content')
<div class="container-fluid">
    @include('toolbar', array('geeks' => $geeks))

    <div class="row game-stats">
        <div class="col-md-12">
            <h2>{{ $stats['total'] }} Games owned</h2>
            <h2>{{ $stats['play'] }} Want to be played</h2>
            <h2>{{ $stats['trade'] }} Games for trade</h2>
            <h2>{{ $stats['wishlist'] }} Games being wished for</h2>
        </div>
    </div>

    <div class="row search-results"></div>
</div>
@stop
