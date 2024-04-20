@extends('layouts.app')

@section('content')

@if ($errorMessage)
<div class="alert alert-danger">
    {{ $errorMessage }}
</div>
@endif

<div class="panel">
    <div class="panel-heading">
        <span class="glyphicon glyphicon-plus"></span>
        Create short URL
    </div>
    <div class="panel-body container" id="url-shortener-form">

        <div>
            <label class="form-label">
                Insert full URL here
                <input type="text" class="form-control" id="url-shortener-form-url" />
            </label>
        </div>

        <button class="btn btn-primary" id="url-shortener-form-button">Go</button>

        <div id="url-shortener-form-warning"></div>

    </div>
</div>

<div class="panel">
    <div class="panel-heading">
        <span class="glyphicon glyphicon-list"></span>
        Recently added URLs
    </div>
    <div class="panel-body" id="url-shortener-list">
        No data
    </div>
</div>

@endsection
