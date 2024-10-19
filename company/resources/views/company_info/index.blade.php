<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            企業情報
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">

                <!-- 一旦、同じページにリダイレクト -->
                <a href="{{ route('company_info.index') }}">編集</a>

                <p>企業名：{{ $company_info->name }}</p>
                <p>所在地：{{ $company_info->address }}</p>
                <p>HP：<a href="{{ $company_info->homepage }}">{{ $company_info->homepage }}</a></p>

            </div>
        </div>
    </div>
</x-app-layout>
