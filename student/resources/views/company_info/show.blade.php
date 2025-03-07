<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg space-y-6">
                <div>
                    <p class="mt-1">{{ $company_info->name }}</a></p>
                    <p class="block font-medium text-sm text-gray-700 mt8">所在地：{{ $company_info->address }}</p>
                    <p class="block font-medium text-sm text-gray-700 mt8">ホームページ：{{ $company_info->homepage }}</p>
                </div>
            </div>
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg space-y-6">
                <p>メンバー</p>
                <div class="flex_custom wrap gap4per flex-start">
                    @foreach ($members as $member)
                        <div class="w22per mb24 border-gray rounded-md shadow-sm border-box pointer">
                            <a href="{{ route('company_info.member', ['company_info' => $member->company_info_id, 'company_user' => $member->id]) }}">
                                <div class="p8">
                                    <p>{{ $member->name }}</p>
                                    <p class="block font-medium text-sm text-gray-700 mt8">{{ $member->department }}、{{ $member->occupation }}、{{ $member->position }}</p>
                                </div>
                                <div class="p8 bt-gray">
                                    <p class="introduction font-medium text-sm text-gray-700">{{ $member->introduction }}</p>
                                </div>
                                <!-- <div class="p8 bt-gray">
                                    <p class="font-medium text-sm text-gray-700">レビュー</p>
                                    @if($member->review->status)
                                        <p class="review font-medium text-sm text-gray-700">{{ $member->review->title }}</p>
                                    @else
                                        <p class="font-medium text-sm text-gray-700">非公開</p>
                                    @endif
                                </div> -->
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const introductions = document.querySelectorAll('.introduction');
            introductions.forEach((element) => {
                const maxLength = 80;
                const text = element.textContent.trim();
                if (text.length >= maxLength) {
                    element.textContent = text.slice(0, maxLength-1) + '...';
                }
            });

            // const reviews = document.querySelectorAll('.review');
            // reviews.forEach((element) => {
            //     const maxLength = 21;
            //     const text = element.textContent.trim();
            //     if (text.length >= maxLength) {
            //         element.textContent = text.slice(0, maxLength-1) + '...';
            //     }
            // });
        });
    </script>
</x-app-layout>
