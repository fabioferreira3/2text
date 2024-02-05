@foreach(App\Enums\Style::cases() as $style)
<option wire:key="{{$style->value}}" value={{$style->value}}>{{$style->label()}}</option>
@endforeach
