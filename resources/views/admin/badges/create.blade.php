<x-layout>
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Crea Nuovo Badge</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.badges.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="mb-3">
                                <label for="name" class="form-label">Nome del Badge</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Descrizione</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="icon" class="form-label">Icona</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i id="icon-preview" class="fas fa-award"></i></span>
                                        <input type="text" class="form-control @error('icon') is-invalid @enderror" id="icon" name="icon" value="{{ old('icon', 'fas fa-award') }}" required>
                                    </div>
                                    <small class="form-text text-muted">Utilizza le classi di Font Awesome (es. fas fa-award)</small>
                                    @error('icon')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="color" class="form-label">Colore</label>
                                    <div class="input-group">
                                        <input type="color" class="form-control form-control-color" id="color-picker" value="#007bff">
                                        <input type="text" class="form-control @error('color') is-invalid @enderror" id="color" name="color" value="{{ old('color', '#007bff') }}" required>
                                    </div>
                                    @error('color')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="criteria" class="form-label">Criteri di assegnazione</label>
                                <textarea class="form-control @error('criteria') is-invalid @enderror" id="criteria" name="criteria" rows="3">{{ old('criteria') }}</textarea>
                                <small class="form-text text-muted">Descrivi i criteri necessari per ottenere questo badge</small>
                                @error('criteria')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="is_automatic" name="is_automatic" value="1" {{ old('is_automatic') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_automatic">Assegnazione automatica</label>
                                <small class="form-text text-muted d-block">Se selezionato, il badge verrà assegnato automaticamente quando vengono soddisfatti i criteri</small>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('admin.badges.index') }}" class="btn btn-secondary">Annulla</a>
                                <button type="submit" class="btn btn-primary">Crea Badge</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const iconInput = document.getElementById('icon');
            const iconPreview = document.getElementById('icon-preview');
            const colorPicker = document.getElementById('color-picker');
            const colorInput = document.getElementById('color');

            // Aggiorna l'anteprima dell'icona quando cambia l'input
            iconInput.addEventListener('input', function() {
                iconPreview.className = this.value;
            });

            // Aggiorna l'input del colore quando cambia il color picker
            colorPicker.addEventListener('input', function() {
                colorInput.value = this.value;
            });

            // Aggiorna il color picker quando cambia l'input del colore
            colorInput.addEventListener('input', function() {
                colorPicker.value = this.value;
            });
        });
    </script>
</x-layout>