<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name') }} - Authorization</title>

    <!-- Styles -->
    <link href="{{ asset('/css/app.css') }}" rel="stylesheet">
</head>

<body>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="p-6 px-10 rounded-xl shadow-xl bg-white w-fit mx-auto">
            {{-- image --}}
            <div class="flex items-center justify-center mb-4">
                <img src="{{ asset('assets/images/logo.png') }}" alt="logo" class="w-20 h-20">
            </div>

            <div class="pb-3">
                <h3 class="text-xl font-bold text-gray-700">
                    Authorization Request
                </h3>
            </div>

            <hr class="pb-3">

            <!-- Introduction -->
            <p class="text-gray-700"><strong>{{ $client->name }}</strong> is requesting permission to access your account.</p>

            <!-- Scope List -->
            @if (count($scopes) > 0)
            <div class="scopes mt-3">
                <p class="text-gray-700"><strong>This application will be able to:</strong></p>

                <ul class="text-gray-700 list-disc pl-8">
                    @foreach ($scopes as $scope)
                    <li>{{ $scope->description }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="flex flex-row-reverse items-center justify-start gap-3 mt-8">
                <!-- Authorize Button -->
                <form method="post" action="{{ route('passport.authorizations.approve') }}">
                    @csrf

                    <input type="hidden" name="state" value="{{ $request->state }}">
                    <input type="hidden" name="client_id" value="{{ $client->id }}">
                    <input type="hidden" name="auth_token" value="{{ $authToken }}">
                    <button type="submit" class="btn btn-primary btn-approve">Authorize</button>
                </form>

                <!-- Cancel Button -->
                <form method="post" action="{{ route('passport.authorizations.deny') }}">
                    @csrf
                    @method('DELETE')

                    <input type="hidden" name="state" value="{{ $request->state }}">
                    <input type="hidden" name="client_id" value="{{ $client->id }}">
                    <input type="hidden" name="auth_token" value="{{ $authToken }}">
                    <button class="btn btn-ghost">Cancel</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>