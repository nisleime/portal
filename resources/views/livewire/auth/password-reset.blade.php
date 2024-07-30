<div>

    <div class="form-wrap row">

        <div class="title">
            <strong>Nova senha</strong>
        </div>

        @if (session()->has('message-error'))
            <div class="col-100 mb-30">
                <div class="alert alert-red">
                    <p>{!! session('message-error') !!}</p>
                </div>
            </div>
        @endif

        <input type="hidden" name="token" value="{ $token }}">

        <div class="group mb-15 col-100">
            <label>E-mail</label>
            <input type="email" wire:model.defer="email" wire:keydown.enter="submit">
            @error('email') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="group mb-15 col-100">
            <label>Senha</label>
            <input type="password" wire:model.defer="password" wire:keydown.enter="submit">
            @error('password') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="group mb-30 col-100">
            <label>Confirmar senha</label>
            <input type="password" wire:model.defer="password_confirmation" wire:keydown.enter="submit">
            @error('password_confirmation') <span class="error">{{ $message }}</span> @enderror
        </div>

        <div class="col-100">
            <button type="button" class="btn btn-blue btn-block" wire:click.prevent="submit">Enviar</button>
        </div>

    </div>

</div>

@section('title', 'Nova senha')

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
