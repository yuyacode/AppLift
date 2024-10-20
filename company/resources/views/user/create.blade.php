<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            企業情報
        </h2>
    </x-slot>

    <form method="post" action="{{ route('company_info.member.store') }}">
        @csrf
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg space-y-6">
                    <h2 class="text-lg font-medium text-gray-900">メンバー追加</h2>
                    <x-error />
                    <p class="max-w-xl">
                        <label class="block font-medium text-sm text-gray-700">名前</label>
                        <input type="text" name="name" value="{{ data_get($data, 'name') }}" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full">
                    </p>
                    <p class="max-w-xl">
                        <label class="block font-medium text-sm text-gray-700">メールアドレス</label>
                        <input type="text" name="email" value="{{ data_get($data, 'email') }}" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full">
                    </p>
                    <p class="max-w-xl">
                        <label class="block font-medium text-sm text-gray-700">パスワード</label>
                        <input type="text" name="password" value="{{ data_get($data, 'password') }}" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full">
                    </p>
                    <div class="flex items-center gap-4">
                        <input type="submit" value="登録" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    </div>
                </div>
            </div>
        </div>
    </form>
</x-app-layout>
