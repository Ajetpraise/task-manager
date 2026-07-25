<x-layouts::app :title="$task->title">
    <div class="mx-auto flex w-full max-w-3xl flex-col gap-6">
        <x-flash-messages />

        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div class="grid gap-2">
                <a href="{{ route('projects.show', $project) }}" class="text-sm font-medium text-zinc-500 hover:text-zinc-900 dark:hover:text-white">← {{ $project->title }}</a>
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="text-2xl font-semibold tracking-tight text-zinc-900 dark:text-white">{{ $task->title }}</h1>
                    <span @class([
                        'rounded-full px-3 py-1 text-xs font-semibold',
                        'bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300' => $task->status === 'todo',
                        'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-200' => $task->status === 'in_progress',
                        'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-200' => $task->status === 'done',
                    ])>{{ $task->statusLabel() }}</span>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('projects.tasks.edit', [$project, $task]) }}" class="inline-flex rounded-lg border border-zinc-300 px-3 py-2 text-sm font-semibold text-zinc-800 hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-800">Edit</a>
                <form method="POST" action="{{ route('projects.tasks.destroy', [$project, $task]) }}" data-confirm="Delete this task?">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex rounded-lg border border-red-300 px-3 py-2 text-sm font-semibold text-red-700 hover:bg-red-50 dark:border-red-800 dark:text-red-300 dark:hover:bg-red-950">Delete</button>
                </form>
            </div>
        </div>

        <article class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Description</h2>
            <p class="mt-3 whitespace-pre-line text-sm leading-7 text-zinc-700 dark:text-zinc-300">{{ $task->description ?: 'No description provided.' }}</p>
        </article>
    </div>
</x-layouts::app>
