@extends('layouts.layout')

@section('content')
<div class="container">
    @include('toolbar', array('geeks' => $geeks))

    <div class="row game-stats">
        <div class="col-md-12">
            <h1>{{ $stats['total'] }} Games owned</h1>
            <h1>{{ $stats['play'] }} Want to be played</h1>
            <h1>{{ $stats['trade'] }} Games for trade</h1>
            <h1>{{ $stats['wishlist'] }} Games being wished for</h1>
        </div>
    </div>

    <div class="row search-results"></div>
</div>
@stop
