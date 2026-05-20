@extends('index')

@section('body')

<div class="container py-4">

    <div class="row mx-0">

        @foreach($pokemon as $pokemon)

        <div class="col-lg-4 col-md-6 mb-4">

            <div class="card h-100 shadow-sm border-0">

                <div class="card-header text-white" style="background-color: gray">

                    <h5 class="mb-0 fw-bold">
                        {{ $pokemon->name }}
                    </h5>

                </div>

                <div class="card-body d-flex flex-column">

                    <p class="text-muted mb-2">
                        Abilities
                    </p>

                    <ul>
                        @foreach($pokemon->abilities as $ability)
                        <li>{{ $ability->name }}</li>
                        @endforeach
                    </ul>

                    <!-- BAN/UNBAN BUTTON -->
                    <form method="POST" action="/pokemon/{{ $pokemon->id }}/toggle-ban">
                        @csrf
                        <button class="btn btn-sm {{ $pokemon->if_banned ? 'btn-success' : 'btn-danger' }}">
                            {{ $pokemon->if_banned ? 'Unban' : 'Ban' }}
                        </button>
                    </form>

                </div>

            </div>

        </div>

        @endforeach

    </div>

</div>

@endsection