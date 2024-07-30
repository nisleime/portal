<div>

    <div class="form-wrap row">

        <div class="title">
            <strong>Logar no Sistema</strong>
        </div>

        @if (session()->has('message-password-reseted'))
            <div class="col-100 mb-30">
                <div class="alert alert-green">
                    <p>{!! session('message-password-reseted') !!}</p>
                </div>
            </div>
        @endif

        @if (session()->has('message-success'))
            <div class="col-100 mb-30">
                <div class="alert alert-green">
                    <p>{{ session('message-success') }}</p>
                </div>
            </div>
        @endif

        @if (session()->has('message-warning'))
            <div class="col-100 mb-30">
                <div class="alert alert-yellow">
                    <p>{{ session('message-warning') }}</p>
                </div>
            </div>
        @endif

        <div class="group mb-15 col-100">
            <label for="email">E-mail</label>
            <input type="email" id="email" wire:model.defer="email" wire:keydown.enter="submit">
            @error('email') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="group mb-30 col-100">
            <label for="password">Senha</label>
            <input type="password" id="password" wire:model.defer="password" wire:keydown.enter="submit">
            @error('password') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="col-50 v-align-middle">
            <a href="{{ route('auth.forgot.password') }}" class="text-dark-gray">Recuperar senha</a>
        </div>

        <div class="col-50 v-align-middle text-right">
            <button type="button" class="btn btn-blue btn-block" wire:click.prevent="submit">ENTRAR</button>
        </div>

    </div>

</div>

@section('title', 'Login')

@push('plugins-styles')

@endpush

@push('plugins-scripts')

@endpush

@push('component-styles')

@endpush

@push('component-scripts')

    <script>
        document.addEventListener('livewire:load', function() {

            (function($) {

                //

            })(jQuery);

        });
    </script>

@endpush
