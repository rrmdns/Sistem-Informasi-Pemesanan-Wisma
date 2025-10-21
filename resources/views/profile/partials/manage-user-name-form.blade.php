<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Kelola Nama Pengguna') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Perbarui nama akun jika terdapat kesalahan penulisan saat registrasi.') }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.update-user-name') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="user_id" value="{{ __('Pilih Pengguna') }}" />
            <select id="user_id" name="user_id" class="mt-1 block w-full border-gray-300 rounded" required>
                <option value="">-- {{ __('Pilih Pengguna') }} --</option>
                @foreach($manageableUsers as $managedUser)
                    <option value="{{ $managedUser->id_user }}" @selected(old('user_id') == $managedUser->id_user)>
                        {{ $managedUser->name }} ({{ $managedUser->email }})
                    </option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('user_id')" />
        </div>

        <div>
            <x-input-label for="new_name" value="{{ __('Nama Baru') }}" />
            <x-text-input id="new_name" name="name" type="text" class="mt-1 block w-full" value="{{ old('name') }}" required />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Simpan Perubahan') }}</x-primary-button>

            @if (session('status') === 'user-name-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-green-600"
                >{{ __('Nama pengguna berhasil diperbarui.') }}</p>
            @endif
        </div>
    </form>
</section>
