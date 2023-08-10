<?php

use App\Models\Chirp;
use function Laravel\Folio\{middleware};
use function Livewire\Volt\{state, rules, computed, on, usesPagination};

usesPagination();

middleware(['auth', 'verified']);

state(['message' => '']);

rules([
    'message' => 'required|string|min:3|max:255',
]);

$chirps = computed(function () {
    return Chirp::with('user')
        ->latest()
        ->simplePaginate(5);
});

$submit = function (): void {
    $this->validate();

    $user = auth()->user();

    $user->chirps()->create([
        'message' => $this->message,
    ]);

    $this->reset('message');
};
?>

<x-app-layout>
    @volt('chirps.index')
        <div class="max-w-2xl mx-auto p-4 sm:p-6 lg:p-8 space-y-4">
            <form wire:submit="submit">
                <textarea wire:model="message" name="message" placeholder="{{ __('What\'s on your mind?') }}"
                    class="block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"></textarea>
                <x-input-error :messages="$errors->get('message')" class="mt-2" />
                <x-primary-button class="mt-4">{{ __('Chirp') }}</x-primary-button>
            </form>
            <div class="bg-white shadow-sm rounded-lg divide-y">
                @foreach ($this->chirps as $chirp)
                    <livewire:chirp :$chirp :wire:key="$chirp->id" />
                @endforeach
            </div>
            <div>
                {{ $this->chirps->links() }}
            </div>
        </div>
    @endvolt
</x-app-layout>
