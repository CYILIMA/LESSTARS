<x-admin.layout title="Organisation settings">
    <div class="admin-heading"><div><p class="eyebrow blue">ORGANISATION</p><h1>Settings</h1></div></div>
    <form class="admin-form" method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        <label>Organisation email<input type="email" name="organisation_email" value="{{ $settings['organisation_email'] ?? '' }}"></label>
        <div class="form-grid"><label>Instagram URL<input type="url" name="instagram_url" value="{{ $settings['instagram_url'] ?? '' }}"></label><label>Facebook URL<input type="url" name="facebook_url" value="{{ $settings['facebook_url'] ?? '' }}"></label></div>
        <label>Organisation logo<input type="file" name="organisation_logo" accept="image/png,image/jpeg,image/webp"></label>
        @if(!empty($settings['organisation_logo_path']))<p class="form-hint">Current logo is stored and will appear in the header, footer, and other branded areas.</p>@endif
        <label>Website background image<input type="file" name="site_background" accept="image/png,image/jpeg,image/webp"><small>JPEG, PNG or WebP; maximum 6 MB. This image is shown behind all public website pages.</small></label>
        @if(!empty($settings['site_background_path']))<p class="form-hint">A background image is currently active. Upload a new one to replace it.</p>@endif
        <label>About Les Stars<textarea name="about_content" maxlength="5000" placeholder="Tell visitors about the organisation.">{{ $settings['about_content'] ?? '' }}</textarea></label>
        <label>Our mission<textarea name="mission_statement" maxlength="2000" placeholder="Describe what the organisation does and why.">{{ $settings['mission_statement'] ?? '' }}</textarea></label>
        <label>Our vision<textarea name="vision_statement" maxlength="2000" placeholder="Describe the future you want to help create.">{{ $settings['vision_statement'] ?? '' }}</textarea></label>
        <label>Donation message<textarea name="donation_message">{{ $settings['donation_message'] ?? '' }}</textarea></label>
        <button class="button button-blue" type="submit">Save settings <span>→</span></button>
    </form>
</x-admin.layout>
