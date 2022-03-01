@extends('layouts.app')

@section('page_title')
    Create New Luxury Car
@endsection

@section('main_content')
    <div class="new-car">
        <div class="container">

            <div class="title">
                Aggiungi la tua macchina di lusso
            </div>

            <div class="validation">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <form action="{{ route('admin.cars.store') }}" method="post">

                @csrf
                @method('POST')

                <input type="text" name="brand" placeholder="Brand dell'auto?" value="{{ old('brand') }}">

                <input type="text" name="src" placeholder="Inserisci la copertina della macchina" value="{{ old('src') }}">

                <input type="text" name="price" placeholder="Quanto costa la tua macchina?" value="{{ old('price') }}">

                <input type="text" name="model" placeholder="Che modello è?" value="{{ old('model') }}">

                <label for="category_id">Categoria</label>

                <select name="category_id" id="category_id">

                    <option value="">Nessuna</option>

                    @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                
                    @endforeach

                </select>

                <input type="text" name="cc" placeholder="Che cilindrata ha?" value="{{ old('cc') }}">

                <textarea name="description" id="" cols="30" rows="10" placeholder="Forniscici una descrizione della macchina">{{ old('description') }}</textarea>

                <input type="submit">
            </form>
        </div>
    </div>
@endsection