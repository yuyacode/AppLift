<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            企業情報
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg space-y-6">
                <h2 class="text-lg font-medium text-gray-900">基本情報</h2>

                <x-status />

                <div class="max-w-xl">
                    <p class="block font-medium text-sm text-gray-700">企業名</p>
                    <p class="mt-1">{{ $company_info->name }}</p>
                </div>
                <div class="max-w-xl">
                    <p class="block font-medium text-sm text-gray-700">所在地</p>
                    <p class="mt-1">{{ $company_info->address }}</p>
                </div>
                <div class="max-w-xl">
                    <p class="block font-medium text-sm text-gray-700">ホームページ</p>
                    <p class="mt-1"><a href="{{ $company_info->homepage }}">{{ $company_info->homepage }}</a></p>
                </div>
                <div class="max-w-xl">
                    <p class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <a href="{{ route('company_info.basic_info.edit', $company_info) }}">編集</a>
                    </p>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg space-y-6">
                <h2 class="text-lg font-medium text-gray-900">メンバー</h2>
                <table>
                    <thead>
                        <tr>
                            <th class="font-medium text-sm text-gray-700 pr24">名前</th>
                            <th class="font-medium text-sm text-gray-700">メールアドレス</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($members as $member)
                            <tr>
                                <td class="pr24">{{ $member->name }}</td>
                                <td class="pr32">{{ $member->email }}</td>
                                <td class="pt4 pb4">
                                    <p class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        <a href="{{ route('company_info.basic_info.edit', $company_info) }}">詳細</a>
                                    </p>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
