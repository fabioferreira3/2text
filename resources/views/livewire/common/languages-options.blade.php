@foreach(App\Enums\Language::cases() as $language)
<option value={{$language->value}}>{{$language->label()}}</option>
@endforeach