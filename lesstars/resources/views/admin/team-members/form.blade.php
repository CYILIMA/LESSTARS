<x-admin.layout title="{{ $teamMember->exists ? 'Edit team member' : 'Add team member' }}">
    <div class="admin-heading"><div><p class="eyebrow blue">OUR TEAM</p><h1>{{ $teamMember->exists ? 'Edit member' : 'Add member' }}</h1></div></div>
    <form class="admin-form" method="POST" enctype="multipart/form-data" action="{{ $teamMember->exists ? route('admin.team-members.update', $teamMember) : route('admin.team-members.store') }}">
        @csrf
        @if($teamMember->exists) @method('PUT') @endif
        <label>Full name<input name="name" value="{{ old('name', $teamMember->name) }}" required></label>
        <div class="form-grid"><label>Role or title<input name="role" value="{{ old('role', $teamMember->role) }}" required></label><label>Display order<input type="number" min="0" name="display_order" value="{{ old('display_order', $teamMember->display_order ?? 0) }}"></label></div>
        <label>Team category<select name="category" required>@foreach (App\Models\TeamMember::CATEGORIES as $category => $label)<option value="{{ $category }}" @selected(old('category', $teamMember->category ?? 'current_stars') === $category)>{{ $label }}</option>@endforeach</select><small>This determines which Our Team page displays this member.</small></label>
        <label>Biography<textarea name="bio" required>{{ old('bio', $teamMember->bio) }}</textarea></label>
        <div class="form-grid"><label>Email address<input type="email" name="email" value="{{ old('email', $teamMember->email) }}"></label><label>Phone number<input name="phone" value="{{ old('phone', $teamMember->phone) }}"></label></div>
        <label>Profile image<input type="file" name="image" accept="image/jpeg,image/png,image/webp"><small>JPEG, PNG or WebP; maximum 4 MB.</small>@if($teamMember->profile_image_url)<small>Current image: <a href="{{ $teamMember->profile_image_url }}" target="_blank" rel="noopener">view image</a></small>@endif</label>
        <div class="form-grid"><label>Instagram URL<input type="url" name="instagram_url" value="{{ old('instagram_url', $teamMember->instagram_url) }}"></label><label>LinkedIn URL<input type="url" name="linkedin_url" value="{{ old('linkedin_url', $teamMember->linkedin_url) }}"></label></div>
        <label>Visibility<select name="status"><option value="draft" @selected(old('status', $teamMember->status) === 'draft')>Draft — hidden from the website</option><option value="published" @selected(old('status', $teamMember->status) === 'published')>Published — visible on the website</option></select></label>
        <button class="button button-blue" type="submit">{{ $teamMember->exists ? 'Save changes' : 'Add team member' }} <span>→</span></button>
    </form>
</x-admin.layout>
