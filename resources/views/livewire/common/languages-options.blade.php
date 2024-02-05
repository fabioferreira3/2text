@foreach(App\Enums\Language::cases() as $language)
<option wire:key="{{$language->value}}" value={{$language->value}}>{{$language->label()}}</option>
@endforeach
