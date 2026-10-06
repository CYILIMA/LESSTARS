<x-admin.layout title="{{ $form->title }} Applications">
    <div class="admin-heading"><div><p class="eyebrow blue">RECRUITMENT</p><h1>{{ $form->title }}</h1></div><a class="button button-blue" href="{{ route('admin.membership-applications.export') }}">Download approved contacts <span>→</span></a></div>
    <section class="admin-panel">
        <h2>Submitted applications</h2>
        @forelse($applications as $application)<div class="admin-row"><div><b>{{ $application->applicant_name }}</b><span>{{ $application->applicant_email }} · {{ ucfirst($application->status) }} · {{ $application->created_at->format('d M Y') }}</span></div><a href="{{ route('admin.member-applications.show', $application) }}">Review</a></div>@empty<p>No applications submitted yet.</p>@endforelse
    </section>
    {{ $applications->links() }}
</x-admin.layout>
