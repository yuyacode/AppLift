<x-app-layout>
    <x-slot name="header">
        <div class="flex_custom space-around_custom">
            <p class="text-sm"><a href="{{ route('message.index') }}">メッセージ</a></p>
            <p class="text-sm"><a href="{{ route('company_info.index') }}">企業情報</a></p>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg space-y-6">
                <div class="flex_custom space-between_custom">
                    <h2 class="text-lg font-medium text-gray-900">最近閲覧した企業</h2>
                    <div class="flex_custom">
                        <p><input type="text" class="fz14 w250 h40 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mr8"></p>
                        <div class="fz14 w100 h40 p8 pointer button">検索</div>
                    </div>
                </div>
                @foreach ($recent_viewed_companies as $company)
                    <div class="max-w-xl">
                        <p class="mt-1"><a href="{{ route('company_info.show', $company->id) }}">{{ $company->name }}</a></p>
                        <p class="block font-medium text-sm text-gray-700 mt8">所在地：{{ $company->address }}</p>
                        <p class="block font-medium text-sm text-gray-700 mt8">ホームページ：{{ $company->homepage }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
