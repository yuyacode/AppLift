<x-app-layout>
    <x-slot name="header">
        <div class="flex_custom space-around_custom">
            <p class="text-sm"><a href="#">メッセージ</a></p>
            <p class="text-sm"><a href="#">レビュー</a></p>
        </div>
    </x-slot>
    <div id="message">
        <div class="flex_custom space-around_custom">
            <div data-bind="event: {scroll: threadLoad}">
                <!-- スレッド一覧 -->
                <!-- スレッド一覧 -->
                <div data-bind="visible: $root.threadLoading" style="text-align: center"><img src="/company/build/assets/loading_small.gif"></div>
            </div>
            <div>
                <p>メッセージ詳細</p>
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
                self.hasMoreThreads = ko.observable(false);
                self.threadLoading = ko.observable(false);

                self.threadLoad = function() {
                    if (!self.hasMoreThreads() || self.threadLoading()) return;
                    if ($('.thread-wrapper').scrollTop() > ($('.thread-wrapper > ul').height() - $('.thread-wrapper').height() - 200)) {
                        self.threadLoading(true);
                        var url = 'https://sample.com/endpoint.json'
                        $.ajax({
                            type: "POST",
                            dataType: "json",
                            url: url,
                            data: {
                                // トークン
                            }
                        }).done(function (res) {
                            if (res.thread) {
                                self.threads(self.threads().concat(res.thread));
                                self.hasMoreThreads(res.has_more_threads);
                                self.lastLoadedMessageId(res.thread.at(-1)['message_id']);
                            } else if (res.error) {
                                $('.thread-wrapper > ul').append('<li>'+res.error+'</li>');
                            }
                            self.threadLoading(false);
                        }).fail(function (ex) {
                            $('.thread-wrapper > ul').append('<li>エラーが発生しました。</li>');
                            self.threadLoading(false);
                        });
                    }
                }
            }
            // ko.applyBindings(new ViewModel(), document.getElementById('message'));
            ko.applyBindings(new ViewModel());
        });
    </script>
</x-app-layout>