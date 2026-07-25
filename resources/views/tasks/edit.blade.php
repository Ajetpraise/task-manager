<x-layouts::app :title="__('Edit task')">
    <div class="mx-auto flex w-full max-w-2xl flex-col gap-6">
        <x-flash-messages />

        <div class="grid gap-1">
            <a href="{{ route('projects.tasks.show', [$project, $task]) }}" class="text-sm font-medium text-zinc-500 hover:text-zinc-900 dark:hover:text-white">← {{ $task->title }}</a>
            <h1 class="text-2xl font-semibold tracking-tight text-zinc-900 dark:text-white">Edit task</h1>
            <p class="text-sm text-zinc-600 dark:text-zinc-400">Update the task details or status.</p>
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <x-task-form :action="route('projects.tasks.update', [$project, $task])" method="PUT" :task="$task" submit-label="Save changes">
                <x-slot:cancel>
                    <a href="{{ route('projects.tasks.show', [$project, $task]) }}" class="text-sm font-medium text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white">Cancel</a>
                </x-slot:cancel>
            </x-task-form>
        </div>
    </div>
</x-layouts::app>
