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
                    <h2 data-bind="text: $root.isShowingSearchResults() ? '検索結果' : '最近閲覧した企業'" class="text-lg font-medium text-gray-900"></h2>
                    <div>
                        <div class="flex_custom">
                            <p><input type="text" data-bind="value: searchKeyword" class="fz14 w250 h40 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mr8" placeholder="企業名で検索"></p>
                            <div data-bind="click: $root.searchCompany" class="fz14 w100 h40 p8 pointer button">検索</div>
                        </div>
                        <p data-bind="visible: $root.isShowingSearchResults(), click: $root.clearSearchResults" class="font-medium text-sm text-gray-700 mt8 pointer">× 検索結果をクリア</p>
                    </div>
                </div>
                <!-- ko if: !$root.isShowingSearchResults() -->
                    <div>
                        @foreach ($recent_viewed_companies as $company)
                            <div class="mt24">
                                <p class="mt-1"><a href="{{ route('company_info.show', $company->id) }}">{{ $company->name }}</a></p>
                                <p class="block font-medium text-sm text-gray-700 mt8">所在地：{{ $company->address }}</p>
                                <p class="block font-medium text-sm text-gray-700 mt8">ホームページ：{{ $company->homepage }}</p>
                            </div>
                        @endforeach
                    </div>
                <!-- /ko -->
                <!-- ko if: $root.isShowingSearchResults() -->
                    <div data-bind="foreach: searchResults">
                        <div class="mt24">
                            <p class="mt-1"><a data-bind="attr: {href: '/student/company_info/' + $data.id}, text: $data.name"></a></p>
                            <p data-bind="text: '住所：' + ($data.address || '')" class="block font-medium text-sm text-gray-700 mt8"></p>
                            <p data-bind="text: 'ホームページ：' + ($data.homepage || '')" class="block font-medium text-sm text-gray-700 mt8"></p>
                        </div>
                    </div>
                <!-- /ko -->
            </div>
        </div>
    </div>
    <script>
        const notyf = new Notyf({
            position: {
                x: 'right',
                y: 'bottom'
            },
            ripple: true
        });

        document.addEventListener('DOMContentLoaded', function() {
            function ViewModel() {
                const self = this;
                self.isShowingSearchResults = ko.observable(false);
                self.searchKeyword = ko.observable('');
                self.searchResults = ko.observableArray();

                self.searchCompany = async function() {
                    if (self.searchKeyword() === '') {
                        notyf.open({
                            type: 'error',
                            dismissible: true,
                            message: '検索キーワードを入力してください'
                        });
                        return;
                    }
                    try {
                        const params = {
                            keyword: self.searchKeyword()
                        }
                        const queryParams = new URLSearchParams(params).toString();
                        const response = await fetch(`/student/company_info/search?${queryParams}`, {
                            method: 'GET',
                            headers: {
                                'Accept': 'application/json',
                            }
                        });
                        const data = await response.json();
                        if (!response.ok) {
                            if (response.status === 401) {
                                window.location.href = '/student/login';
                            }
                            throw buildErrorObject(data.message, data.detail, response.status);
                        }
                        self.isShowingSearchResults(true);
                        self.searchResults(data);
                    } catch (error) {
                        console.error(formatErrorInfo(error))
                        showGeneralErrNotification()
                    }
                }

                self.clearSearchResults = function() {
                    self.isShowingSearchResults(false);
                    self.searchKeyword('');
                    self.searchResults.removeAll();
                }
            }
            ko.applyBindings(new ViewModel());

            function buildErrorObject(message, detail, status) {
                const error = new Error(message);
                error.detail = detail ?? undefined;
                error.status = status;
                return error;
            }

            function formatErrorInfo(error) {
                return {
                    message: error.message,
                    ...(error.detail && { detail: error.detail }),
                    status: error.status,
                };
            }

            function showGeneralErrNotification() {
                notyf.open({
                    type: 'error',
                    dismissible: true,
                    message: '<b>エラーが発生しました</b><br />もう一度お試しください'
                });
            }
        });
    </script>
</x-app-layout>
