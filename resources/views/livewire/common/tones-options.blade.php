@foreach(App\Enums\Tone::cases() as $tone)
    <option value={{$tone->value}}>{{$tone->label()}}</option>
@endforeach
