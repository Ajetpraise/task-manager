<x-layouts::app :title="$project->title">
    <div class="mx-auto flex w-full max-w-6xl flex-col gap-6">
        <x-flash-messages />

        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div class="grid gap-2">
                <a href="{{ route('projects.index') }}" class="text-sm font-medium text-zinc-500 hover:text-zinc-900 dark:hover:text-white">← All projects</a>
                <h1 class="text-2xl font-semibold tracking-tight text-zinc-900 dark:text-white">{{ $project->title }}</h1>
                <p class="max-w-3xl whitespace-pre-line text-sm leading-6 text-zinc-600 dark:text-zinc-400">{{ $project->description ?: 'No description provided.' }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('projects.edit', $project) }}" class="inline-flex rounded-lg border border-zinc-300 px-3 py-2 text-sm font-semibold text-zinc-800 hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-800">Edit</a>
                <form method="POST" action="{{ route('projects.destroy', $project) }}" data-confirm="Delete this project and all of its tasks?">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex rounded-lg border border-red-300 px-3 py-2 text-sm font-semibold text-red-700 hover:bg-red-50 dark:border-red-800 dark:text-red-300 dark:hover:bg-red-950">Delete</button>
                </form>
            </div>
        </div>

        <section class="grid gap-4">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-zinc-900 dark:text-white">Tasks</h2>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">Track the work required for this project.</p>
                </div>
                <a href="{{ route('projects.tasks.create', $project) }}" class="inline-flex items-center justify-center rounded-lg bg-zinc-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-zinc-700 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200">New task</a>
            </div>

            <nav aria-label="Filter tasks by status" class="flex flex-wrap gap-2">
                @foreach ([
                    ['value' => null, 'label' => 'All'],
                    ['value' => 'todo', 'label' => 'To Do'],
                    ['value' => 'in_progress', 'label' => 'In Progress'],
                    ['value' => 'done', 'label' => 'Done'],
                ] as $filter)
                    <a
                        href="{{ $filter['value'] ? route('projects.show', ['project' => $project, 'status' => $filter['value']]) : route('projects.show', $project) }}"
                        @class([
                            'rounded-full px-3 py-1.5 text-sm font-medium transition',
                            'bg-zinc-900 text-white dark:bg-white dark:text-zinc-900' => $status === $filter['value'],
                            'bg-zinc-100 text-zinc-700 hover:bg-zinc-200 dark:bg-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-700' => $status !== $filter['value'],
                        ])
                    >
                        {{ $filter['label'] }}
                    </a>
                @endforeach
            </nav>

            @if ($tasks->isEmpty())
                <div class="rounded-xl border border-dashed border-zinc-300 bg-zinc-50 px-6 py-12 text-center dark:border-zinc-700 dark:bg-zinc-900">
                    <h3 class="font-semibold text-zinc-900 dark:text-white">{{ $status ? 'No tasks match this filter' : 'No tasks yet' }}</h3>
                    <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">{{ $status ? 'Choose another status or view all tasks.' : 'Add the first task for this project.' }}</p>
                </div>
            @else
                <div class="grid gap-3">
                    @foreach ($tasks as $task)
                        <article class="flex flex-col gap-4 rounded-xl border border-zinc-200 bg-white p-5 shadow-sm sm:flex-row sm:items-center sm:justify-between dark:border-zinc-700 dark:bg-zinc-900">
                            <div class="min-w-0">
                                <h3 class="font-semibold text-zinc-900 dark:text-white">
                                    <a href="{{ route('projects.tasks.show', [$project, $task]) }}" class="hover:underline">{{ $task->title }}</a>
                                </h3>
                                <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">{{ $task->description ? Str::limit($task->description, 140) : 'No description provided.' }}</p>
                            </div>
                            <span @class([
                                'shrink-0 rounded-full px-3 py-1 text-xs font-semibold',
                                'bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300' => $task->status === 'todo',
                                'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-200' => $task->status === 'in_progress',
                                'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-200' => $task->status === 'done',
                            ])>{{ $task->statusLabel() }}</span>
                        </article>
                    @endforeach
                </div>

                {{ $tasks->links() }}
            @endif
        </section>
    </div>
</x-layouts::app>
