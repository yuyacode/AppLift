<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            プロフィール情報
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            プロフィール情報を更新できます
        </p>
    </header>


    <form method="post" action="{{ route('profile.update_profile_info', $user) }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="department" value="部署、部門" />
            <x-text-input id="department" name="department" type="text" class="mt-1 block w-full" :value="old('department', $user->department)" autofocus autocomplete="department" />
            <x-input-error class="mt-2" :messages="$errors->get('department')" />
        </div>

        <div>
            <x-input-label for="occupation" value="職種、職業" />
            <x-text-input id="occupation" name="occupation" type="text" class="mt-1 block w-full" :value="old('occupation', $user->occupation)" autofocus autocomplete="occupation" />
            <x-input-error class="mt-2" :messages="$errors->get('occupation')" />
        </div>

        <div>
            <x-input-label for="position" value="役職、ポジション" />
            <x-text-input id="position" name="position" type="text" class="mt-1 block w-full" :value="old('position', $user->position)" autofocus autocomplete="position" />
            <x-input-error class="mt-2" :messages="$errors->get('position')" />
        </div>

        <div>
            <x-input-label for="join_date" value="入社時期" />
            <x-text-input id="join_date" name="join_date" type="text" class="mt-1 block w-full" :value="old('join_date', $user->join_date)" autofocus autocomplete="join_date" />
            <x-input-error class="mt-2" :messages="$errors->get('join_date')" />
        </div>

        <div>
            <x-input-label for="introduction" value="自己紹介" />
            <textarea id="introduction" name="introduction" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" value="old('introduction', $user->introduction)" autofocus autocomplete="introduction">{{ old('introduction', $user->introduction) }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('introduction')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile_info-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>

    </form>
</section>
