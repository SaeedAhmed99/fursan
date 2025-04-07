<form method="GET" action="{{ route('universities.index01') }}">
    <label for="country">اختر الدولة:</label>
    <select name="country" id="country" onchange="this.form.submit()">
        @foreach ($availableCountries as $country)
            <option value="{{ $country }}" {{ $selectedCountry == $country ? 'selected' : '' }}>
                {{ $country }}
            </option>
        @endforeach
    </select>
</form>

<h2>الجامعات في {{ $selectedCountry }}:</h2>

 @if($universities->isEmpty())
    <p>لا توجد جامعات متاحة لهذه الدولة.</p>
@else
    <ul>
        @foreach ($universities as $university)
            <li>{{ $university['name'] }}</li>
        @endforeach
    </ul>
@endif 