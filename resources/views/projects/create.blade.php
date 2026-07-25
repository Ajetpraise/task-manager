<x-layouts::app :title="__('Create project')">
    <div class="mx-auto flex w-full max-w-2xl flex-col gap-6">
        <x-flash-messages />

        <div class="grid gap-1">
            <h1 class="text-2xl font-semibold tracking-tight text-zinc-900 dark:text-white">Create project</h1>
            <p class="text-sm text-zinc-600 dark:text-zinc-400">Add a title and an optional description.</p>
        </div>

        <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <x-project-form :action="route('projects.store')" submit-label="Create project">
                <x-slot:cancel>
                    <a href="{{ route('projects.index') }}" class="text-sm font-medium text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white">Cancel</a>
                </x-slot:cancel>
            </x-project-form>
        </div>
    </div>
</x-layouts::app>
