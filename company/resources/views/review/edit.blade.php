<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            レビュー
        </h2>
    </x-slot>

    <form method="post" action="{{ route('review.update', $review) }}">
        @csrf

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg space-y-6">

                    <x-status.default />
                    <x-error />

                    <p>
                        <label class="fz14 block font-medium text-gray-700">タイトル</label>
                        <input type="text" name="title" value="{{ data_get($data, 'title') }}" class="fz14 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full">
                    </p>

                    <p class="fz14 block font-medium text-gray-700">公開設定</p>
                    <div class="flex_custom">
                        <p class="fz14 mr24">
                            <input type="radio" name="status" value="1" class="mr8" {{ data_get($data, 'status') == '1' ? 'checked' : '' }}>公開
                        </p>
                        <p class="fz14">
                            <input type="radio" name="status" value="0" class="mr8" {{ data_get($data, 'status') == '0' ? 'checked' : '' }}>下書き
                        </p>
                    </div>

                    @foreach ($review->reviewAnswer as $answer)
                        <div style="margin-top: 36px;">
                            <label class="fz14 block font-medium text-gray-700 mb8">{{ $answer->reviewItem->name }}</label>

                            <div class="mb8">
                                <p class="fz12 font-medium text-sm text-gray-700">点数</p>
                                <input type="text"
                                       value="{{ data_get($answer, 'score') }}" 
                                       name="answers[{{ $answer->id }}][score]" 
                                       placeholder="80"
                                       class="fz14 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full">
                            </div>
                            <div>
                                <p class="fz12 font-medium text-sm text-gray-700">コメント</p>
                                <textarea name="answers[{{ $answer->id }}][answer]" class="fz14 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full">{{ data_get($answer, 'answer') }}</textarea>
                            </div>
                        </div>
                    @endforeach

                    <div class="flex items-center gap-4">
                        <input type="submit" value="保存" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    </div>
                </div>
            </div>
        </div>
    </form>
</x-app-layout>
