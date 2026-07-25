@props([
    'action',
    'method' => 'POST',
    'task' => null,
    'submitLabel',
])

<form method="POST" action="{{ $action }}" class="grid gap-6">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <div class="grid gap-2">
        <label for="title" class="text-sm font-medium text-zinc-800 dark:text-zinc-200">Title</label>
        <input
            id="title"
            name="title"
            type="text"
            maxlength="255"
            required
            autofocus
            value="{{ old('title', $task?->title) }}"
            class="w-full rounded-lg border bg-white px-3 py-2 text-zinc-900 shadow-sm outline-none transition focus:border-zinc-500 focus:ring-2 focus:ring-zinc-500/20 dark:bg-zinc-900 dark:text-zinc-100 @error('title') border-red-500 @else border-zinc-300 dark:border-zinc-700 @enderror"
        >
        @error('title')
            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid gap-2">
        <label for="description" class="text-sm font-medium text-zinc-800 dark:text-zinc-200">Description <span class="font-normal text-zinc-500">(optional)</span></label>
        <textarea
            id="description"
            name="description"
            rows="6"
            maxlength="2000"
            class="w-full rounded-lg border bg-white px-3 py-2 text-zinc-900 shadow-sm outline-none transition focus:border-zinc-500 focus:ring-2 focus:ring-zinc-500/20 dark:bg-zinc-900 dark:text-zinc-100 @error('description') border-red-500 @else border-zinc-300 dark:border-zinc-700 @enderror"
        >{{ old('description', $task?->description) }}</textarea>
        @error('description')
            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid gap-2">
        <label for="status" class="text-sm font-medium text-zinc-800 dark:text-zinc-200">Status</label>
        <select
            id="status"
            name="status"
            required
            class="w-full rounded-lg border bg-white px-3 py-2 text-zinc-900 shadow-sm outline-none transition focus:border-zinc-500 focus:ring-2 focus:ring-zinc-500/20 dark:bg-zinc-900 dark:text-zinc-100 @error('status') border-red-500 @else border-zinc-300 dark:border-zinc-700 @enderror"
        >
            @foreach (['todo' => 'To Do', 'in_progress' => 'In Progress', 'done' => 'Done'] as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $task?->status ?? 'todo') === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('status')
            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex flex-wrap items-center gap-3">
        <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-zinc-900 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-zinc-700 focus:outline-none focus:ring-2 focus:ring-zinc-500 focus:ring-offset-2 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200">
            {{ $submitLabel }}
        </button>
        {{ $cancel }}
    </div>
</form>
