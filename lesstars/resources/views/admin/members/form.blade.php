<x-admin.layout title="{{ $member->exists ? 'Edit Member Star' : 'Add Member Star' }}">
    <div class="admin-heading"><div><p class="eyebrow blue">MEMBER ACCESS</p><h1>{{ $member->exists ? 'Edit Member Star' : 'Add Member Star' }}</h1></div></div>
    <form class="admin-form" method="POST" action="{{ $member->exists ? route('admin.members.update', $member) : route('admin.members.store') }}">
        @csrf @if($member->exists) @method('PUT') @endif
        <label>Full name<input name="name" value="{{ old('name', $member->name) }}" required></label>
        <label>Email address<input type="email" name="email" value="{{ old('email', $member->email) }}" required></label>
        <label>Registration number<input name="registration_number" value="{{ old('registration_number', $member->registration_number) }}" required></label>
        <label class="remember"><input type="checkbox" name="is_member_approved" value="1" @checked(old('is_member_approved', $member->is_member_approved))> Approved current Member Star</label>
        @if($errors->any())<p class="form-error">{{ $errors->first() }}</p>@endif
        <button class="button button-blue" type="submit">{{ $member->exists ? 'Save member' : 'Add member' }} <span>→</span></button>
    </form>
</x-admin.layout>
