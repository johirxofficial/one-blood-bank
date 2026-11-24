@props([
    'divisions' => collect(),
    'districts' => collect(),
    'upazilas' => collect(),
    'unions' => collect(),
    'selected' => [],
    'required' => false,
    'compact' => false,
])

@php
    $selected = (array) $selected;
    $selectedDistrict = old('district_id', $selected['district_id'] ?? null);
    $selectedUpazila = old('upazila_id', $selected['upazila_id'] ?? null);
    $selectedUnion = old('union_id', $selected['union_id'] ?? null);
    $selectedDivision = old('division_id', $selected['division_id'] ?? null);
    $en = app()->getLocale() === 'en';

    $divisionLabel = $en ? 'Division' : 'বিভাগ';
    $districtLabel = $en ? 'District' : 'জেলা';
    $upazilaLabel = $en ? 'Upazila' : 'উপজেলা';
    $unionLabel = $en ? 'Union' : 'ইউনিয়ন';
    $divisionPlaceholder = $en ? 'Select division' : 'বিভাগ নির্বাচন করুন';
    $districtPlaceholder = $en ? 'Select district' : 'জেলা নির্বাচন করুন';
    $upazilaPlaceholder = $en ? 'Select upazila' : 'উপজেলা নির্বাচন করুন';
    $unionPlaceholder = $en ? 'Select union' : 'ইউনিয়ন নির্বাচন করুন';
@endphp

<div data-location-form class="{{ $compact ? 'contents' : 'grid gap-4 sm:grid-cols-2' }}">
    <div>
        <label class="label" for="division_id">{{ $divisionLabel }} @if ($required) <span class="text-brand-600">*</span> @endif</label>
        <select name="division_id" id="division_id" class="input" data-location="division" @required($required)>
            <option value="">{{ $divisionPlaceholder }}</option>
            @foreach ($divisions as $division)
                <option value="{{ $division->id }}" @selected((string) $selectedDivision === (string) $division->id)>
                    {{ $en ? $division->name : $division->name_bn.' ('.$division->name.')' }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="label" for="district_id">{{ $districtLabel }} @if ($required) <span class="text-brand-600">*</span> @endif</label>
        <select name="district_id" id="district_id" class="input" data-location="district"
                data-url-template="{{ route('locations.districts', ['division' => '__ID__']) }}"
                data-placeholder="{{ $districtPlaceholder }}" @required($required)>
            <option value="">{{ $districtPlaceholder }}</option>
            @foreach ($districts as $district)
                <option value="{{ $district->id }}" @selected((string) $selectedDistrict === (string) $district->id)>
                    {{ $en ? $district->name : $district->name_bn.' ('.$district->name.')' }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="label" for="upazila_id">{{ $upazilaLabel }}</label>
        <select name="upazila_id" id="upazila_id" class="input" data-location="upazila"
                data-url-template="{{ route('locations.upazilas', ['district' => '__ID__']) }}"
                data-placeholder="{{ $upazilaPlaceholder }}">
            <option value="">{{ $upazilaPlaceholder }}</option>
            @foreach ($upazilas as $upazila)
                <option value="{{ $upazila->id }}" @selected((string) $selectedUpazila === (string) $upazila->id)>
                    {{ $en ? $upazila->name : $upazila->name_bn.' ('.$upazila->name.')' }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="label" for="union_id">{{ $unionLabel }}</label>
        <select name="union_id" id="union_id" class="input" data-location="union"
                data-url-template="{{ route('locations.unions', ['upazila' => '__ID__']) }}"
                data-placeholder="{{ $unionPlaceholder }}">
            <option value="">{{ $unionPlaceholder }}</option>
            @foreach ($unions as $union)
                <option value="{{ $union->id }}" @selected((string) $selectedUnion === (string) $union->id)>
                    {{ $en ? $union->name : $union->name_bn.' ('.$union->name.')' }}
                </option>
            @endforeach
        </select>
    </div>
</div>
