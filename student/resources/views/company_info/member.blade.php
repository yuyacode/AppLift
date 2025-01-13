<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg space-y-6">
                <div class="flex_custom space-between_custom">
                    <p>{{ $company_user['name'] }}</p>
                    <div>
                        <form id="add_thread_form" method="POST" action="{{ route('message.thread.store') }}">
                            @csrf
                            <input type="hidden" name="company_user_id" value="{{ $company_user['id'] }}">
                            <button type="submit" id="add_thread_submit_btn" class="fz14 w100 h40 p8 pointer button">話しかける</button>
                        </form>
                    </div>
                </div>
                <div>
                    <p class="block font-medium text-sm text-gray-700 mb4">在籍企業</p>
                    <p class="fz14 underline"><a href="{{ route('company_info.show', $company_info['id']) }}">{{ $company_info['name'] }}</a></p>
                </div>
                <div>
                    <p class="block font-medium text-sm text-gray-700 mb4">部署、部門</p>
                    <p class="fz14">{{ $company_user['department'] }}</p>
                </div>
                <div>
                    <p class="block font-medium text-sm text-gray-700 mb4">職種、職業</p>
                    <p class="fz14">{{ $company_user['occupation'] }}</p>
                </div>
                <div>
                    <p class="block font-medium text-sm text-gray-700 mb4">役職、ポジション</p>
                    <p class="fz14">{{ $company_user['position'] }}</p>
                </div>
                <div>
                    <p class="block font-medium text-sm text-gray-700 mb4">入社時期</p>
                    <p class="fz14">{{ $company_user['join_date'] }}</p>
                </div>
                <div>
                    <p class="block font-medium text-sm text-gray-700 mb4">自己紹介</p>
                    <p class="fz14">{{ $company_user['introduction'] }}</p>
                </div>
            </div>
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg space-y-6">
                <p>レビュー</p>
                @if($review['status'])
                    <p>{{ $review['title'] }}</p>
                    @foreach ($review->reviewAnswers as $answer)
                        <div style="margin-top: 48px;">
                            <p class="block font-medium text-sm text-gray-700 mb4">{{ $answer->reviewItem->name }}（{{ $answer->score ?? ' -' }} / 100）</p>
                            <p class="fz14">{{ $answer->answer ?? '未入力' }}</p>
                        </div>
                    @endforeach
                @else
                    <p>非公開</p>
                @endif
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('add_thread_form').addEventListener('submit', function (event) {
                document.getElementById('add_thread_submit_btn').disabled = true;
            });
        });
    </script>
</x-app-layout>