<x-layouts::app :title="__('Projects')">
    <div class="mx-auto flex w-full max-w-6xl flex-col gap-6">
        <x-flash-messages />

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="grid gap-1">
                <h1 class="text-2xl font-semibold tracking-tight text-zinc-900 dark:text-white">Projects</h1>
                <p class="text-sm text-zinc-600 dark:text-zinc-400">Plan your work and keep every task in one place.</p>
            </div>
            <a href="{{ route('projects.create') }}" class="inline-flex items-center justify-center rounded-lg bg-zinc-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-zinc-700 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200">
                New project
            </a>
        </div>

        @if ($projects->isEmpty())
            <div class="rounded-xl border border-dashed border-zinc-300 bg-zinc-50 px-6 py-14 text-center dark:border-zinc-700 dark:bg-zinc-900">
                <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">No projects yet</h2>
                <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">Create your first project to start organizing tasks.</p>
                <a href="{{ route('projects.create') }}" class="mt-5 inline-flex rounded-lg bg-zinc-900 px-4 py-2 text-sm font-semibold text-white dark:bg-white dark:text-zinc-900">Create a project</a>
            </div>
        @else
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($projects as $project)
                    <article class="flex min-h-52 flex-col justify-between gap-6 rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                        <div class="grid gap-3">
                            <div class="flex items-start justify-between gap-3">
                                <h2 class="text-lg font-semibold text-zinc-900 dark:text-white">
                                    <a href="{{ route('projects.show', $project) }}" class="hover:underline">{{ $project->title }}</a>
                                </h2>
                                <span class="shrink-0 rounded-full bg-zinc-100 px-2.5 py-1 text-xs font-medium text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                                    {{ $project->tasks_count }} {{ Str::plural('task', $project->tasks_count) }}
                                </span>
                            </div>
                            <p class="text-sm leading-6 text-zinc-600 dark:text-zinc-400">
                                {{ $project->description ? Str::limit($project->description, 150) : 'No description provided.' }}
                            </p>
                        </div>
                        <a href="{{ route('projects.show', $project) }}" class="text-sm font-semibold text-zinc-900 hover:underline dark:text-zinc-100">View project →</a>
                    </article>
                @endforeach
            </div>

            {{ $projects->links() }}
        @endif
    </div>
</x-layouts::app>
