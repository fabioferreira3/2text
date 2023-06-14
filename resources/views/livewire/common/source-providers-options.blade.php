@foreach(App\Enums\SourceProvider::cases() as $provider)
<option value="{{$provider->value}}">{{$provider->label()}}</option>
@endforeach