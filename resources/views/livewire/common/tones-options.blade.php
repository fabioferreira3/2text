@foreach(App\Enums\Tone::cases() as $tone)
<option wire:key="{{$tone->value}}" value={{$tone->value}}>{{$tone->label()}}</option>
@endforeach
