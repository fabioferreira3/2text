@foreach(App\Enums\Language::voiceEnabled() as $language)
<option value={{$language->value}}>{{$language->label()}}</option>
@endforeach
