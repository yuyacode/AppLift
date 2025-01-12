<x-app-layout>
    <form method="post" action="{{ route('company_info.basic_info.update', $company_info) }}">
        @csrf
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg space-y-6">
                    <h2 class="text-lg font-medium text-gray-900">基本情報</h2>

                    <x-error />

                    <p class="max-w-xl">
                        <label class="block font-medium text-sm text-gray-700">企業名</label>
                        <input type="text" name="name" value="{{ data_get($data, 'name') }}" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full">
                    </p>
                    <p class="max-w-xl">
                        <label class="block font-medium text-sm text-gray-700">所在地</label>
                        <input type="text" name="address" value="{{ data_get($data, 'address') }}" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full">
                    </p>
                    <p class="max-w-xl">
                        <label class="block font-medium text-sm text-gray-700">ホームページ</label>
                        <input type="text" name="homepage" value="{{ data_get($data, 'homepage') }}" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full">
                    </p>
                    <div class="flex items-center gap-4">
                        <input type="submit" value="保存" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    </div>
                </div>
            </div>
        </div>
    </form>
</x-app-layout>
