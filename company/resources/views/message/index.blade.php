<x-app-layout>
    <x-slot name="header">
        <div class="flex_custom space-around_custom">
            <p class="text-sm"><a href="{{ route('message.index') }}">メッセージ</a></p>
            <p class="text-sm"><a href="{{ route('review.edit') }}">レビュー</a></p>
        </div>
    </x-slot>
    <div id="message" class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6 py-12">
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg space-y-6">
            <div class="flex_custom">
                <div class="w30per br-gray">
                    <ul data-bind="foreach: threads">
                        <li class="pt16 pb16 pointer" data-bind="css: { 'bt-gray': $index() !== 0 }">
                            <div class="flex_custom space-between_custom mb8 pr24">
                                <p data-bind="text: '学生' + $data.student_user_id"></p>
                                <p data-bind="text: $root.datetimeFormat($data.updated_at)" class="text-gray-500"></p>
                            </div>
                            <p data-bind="text: $data.messages.length > 0 ? $root.truncateMessage($data.messages[0].content) : '（メッセージがありません）'" class="pr24 text-gray-500"></p>
                        </li>
                    </ul>
                </div>
                <div class="ml24">
                    <p>メッセージ詳細</p>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function ViewModel() {
                var self = this;
                // self.sample = ko.observable(1);
                // self.sampleArray = ko.observableArray();
                // self.sampleJson = ko.mapping.fromJS({name: 'Scot', children: [{ id : 1, name : 'Alicw' }]});
                self.threads = ko.observableArray(@json($threads));

                self.datetimeFormat = function(datetime) {
                    return dayjs(datetime).format('YYYY/MM/DD HH:mm');
                }

                self.truncateMessage = function(message) {
                    return message && message.length >= 20 ? message.substring(0, 19) + '...' : message;
                };
            }
            // ko.applyBindings(new ViewModel(), document.getElementById('message'));
            ko.applyBindings(new ViewModel());
        });
    </script>
</x-app-layout>