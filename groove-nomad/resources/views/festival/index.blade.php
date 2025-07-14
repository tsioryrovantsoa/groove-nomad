@extends('layouts.app')

@section('title', 'Festival')

@section('content')
    <div class="row">
        @foreach ($festivals as $festival)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card h-100 shadow-sm">
                    <img src="https://picsum.photos/seed/festival{{ $festival->id }}/600/400" class="card-img-top"
                        alt="Image Festival">

                    <div class="discography__item__text">
                        <span>{{ $festival->name }}</span>
                        <p class="card-text">
                            {{ $festival->description }}
                        </p>
                        <p class="mb-1"><strong>Dates:</strong>
                            {{ \Carbon\Carbon::parse($festival->startDate)->format('d M Y') }} -
                            {{ \Carbon\Carbon::parse($festival->endDate)->format('d M Y') }}</p>
                        <p class="mb-1"><strong>Lieu:</strong> {{ $festival->location }}, {{ $festival->city }},
                            {{ $festival->region }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $festivals->links() }}
    </div>
@endsection
