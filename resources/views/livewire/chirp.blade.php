<?php

use App\Models\Chirp;
use function Livewire\Volt\{state, mount, rules};

state(['chirp' => null, 'message' => '', 'editing' => false]);

rules([
    'message' => 'required|string|min:3|max:255',
]);

mount(function (Chirp $chirp): void {
    $this->chirp = $chirp;
    $this->message = $chirp->message;
});

$update = function () {
    $this->validate();

    $this->authorize('update', $this->chirp);

    $this->chirp->update([
        'message' => $this->message,
    ]);

    $this->editing = false;
};

$delete = function () {
    $this->authorize('update', $this->chirp);

    $this->chirp->delete();
};
?>

<div class="p-6 flex space-x-2">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600 -scale-x-100" fill="none" viewBox="0 0 24 24"
        stroke="currentColor" strokeWidth="2">
        <path strokeLinecap="round" strokeLinejoin="round"
            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
    </svg>
    <div class="flex-1">
        <div class="flex justify-between items-center">
            <div>
                <span class="text-gray-800">{{ $chirp->user->name }}</span>
                <small class="ml-2 text-sm text-gray-600">{{ $chirp->created_at->diffForHumans() }}</small>
                @if ($chirp->created_at !== $chirp->updated_at)
                    <small class="text-sm text-gray-600"> &middot; edited</small>
                @endif
            </div>
        </div>
        @if ($editing)
            <form wire:submit="update">
                <textarea wire:model="message"
                    class="mt-4 w-full text-gray-900 border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"></textarea>
                <x-input-error :messages="$errors->get('message')" class="mt-2" />
                <div class="space-x-2">
                    <x-primary-button class="mt-4">Save</x-primary-button>
                    <button type="button" class="mt-4" wire:click="$set('editing', false)">
                        Cancel
                    </button>
                </div>
            </form>
        @else
            <p class="mt-4 text-lg text-gray-900">{{ $chirp->message }}</p>
        @endif
    </div>
    @if ($chirp->user->id === auth()->user()->id)
        <x-dropdown>
            <x-slot name="trigger">
                <button>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path
                            d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                    </svg>
                </button>
            </x-slot>
            <x-slot name="content">
                <x-dropdown-link button="true" wire:click="$set('editing', true)">
                    {{ __('Edit') }}
                </x-dropdown-link>
                <x-dropdown-link button="true" wire:click="delete">
                    {{ __('Delete') }}
                </x-dropdown-link>
            </x-slot>
        </x-dropdown>
    @endif
</div>
