@if (session('success'))
    <div role="alert" class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 dark:border-emerald-800 dark:bg-emerald-950 dark:text-emerald-200">
        {{ session('success') }}
    </div>
@endif

@if (session('completed'))
    <div role="alert" class="rounded-lg border border-emerald-300 bg-emerald-100 px-4 py-3 text-sm font-semibold text-emerald-900 dark:border-emerald-700 dark:bg-emerald-900 dark:text-emerald-100">
        {{ session('completed') }}
    </div>
@endif

@if (session('error'))
    <div role="alert" class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800 dark:border-red-800 dark:bg-red-950 dark:text-red-200">
        {{ session('error') }}
    </div>
@endif
