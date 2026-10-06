<x-admin.layout title="Member Applications">
    <div class="admin-heading"><div><p class="eyebrow blue">RECRUITMENT</p><h1>Member applications</h1></div><a class="button button-blue" href="{{ route('admin.membership-applications.create') }}">Create application link <span>→</span></a></div>
    <section class="admin-panel">
        <h2>Application links</h2>
        @forelse($forms as $form)
            <div class="admin-row"><div><b>{{ $form->title }}</b><span>{{ $form->applications_count }} applications · {{ $form->is_open ? 'Open' : 'Closed' }}</span><small class="application-link">{{ route('membership-applications.create', $form) }}</small></div><div class="row-actions"><a href="{{ route('admin.membership-applications.submissions', $form) }}">Review applications</a><form method="POST" action="{{ route('admin.membership-applications.toggle', $form) }}">@csrf @method('PATCH')<button>{{ $form->is_open ? 'Close link' : 'Open link' }}</button></form></div></div>
        @empty
            <p>Create a form to receive applications from future Member Stars.</p>
        @endforelse
    </section>
</x-admin.layout>
