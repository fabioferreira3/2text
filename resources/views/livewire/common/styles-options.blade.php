@foreach(App\Enums\Style::cases() as $style)
    <option value={{$style->value}}>{{$style->label()}}</option>
@endforeach
