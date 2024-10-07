@props(['channel' => 'App.Models.User'])

<div x-data="initPushNotification('{{ auth()->id() }}', `{{ $channel ?? 'App.Models.User' }}`)" class="fixed top-10 right-5 w-96 z-[1055]">
    <template x-for="(notification, index) in notifications" :key="index">
        <div x-show="visible[index]" x-transition.opacity.duration.500ms
            class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative shadow mb-4">
            <span class="block sm:inline" x-text="notification.message"></span>
            <button @click="closeNotification(index)"
                class="absolute top-0 bottom-0 right-0 px-4 py-3 text-blue-700">
                <svg class="fill-current h-6 w-6 text-blue-700" role="button" xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20">
                    <path
                        d="M14.348 5.652a.5.5 0 0 0-.707 0L10 9.293 6.36 5.652a.5.5 0 1 0-.707.707L9.293 10l-3.64 3.64a.5.5 0 0 0 .707.707L10 10.707l3.64 3.64a.5.5 0 0 0 .707-.707L10.707 10l3.64-3.64a.5.5 0 0 0 0-.707z" />
                </svg>
            </button>
        </div>
    </template>
</div>
