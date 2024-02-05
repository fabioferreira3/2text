@foreach(App\Enums\Language::voiceEnabled() as $language)
<option wire:key="{{$language->value}}" value={{$language->value}}>{{$language->label()}}</option>
@endforeach
