@props([
    'title',
    'description',
])

<div class="flex w-full flex-col text-center mb-2">
    <flux:heading size="xl" class="text-blue-600 font-extrabold">{{ $title }}</flux:heading>
    <flux:subheading class="text-gray-500 font-medium">{{ $description }}</flux:subheading>
</div>
