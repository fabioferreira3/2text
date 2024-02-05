@foreach(App\Enums\SourceProvider::cases() as $provider)
<option wire:key="{{$provider->value}}" value="{{$provider->value}}">{{$provider->label()}}</option>
@endforeach
