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
                        <li class="pt16 pb16 pointer" data-bind="css: { 'bt-gray': $index() !== 0 }, click: $root.getMessages">
                            <div class="flex_custom space-between_custom mb8 pr24">
                                <p data-bind="text: '学生' + $data.student_user_id"></p>
                                <p data-bind="text: $root.datetimeFormat($data.updated_at)" class="text-gray-500"></p>
                            </div>
                            <p data-bind="text: $data.messages.length > 0 ? $root.truncateMessage($data.messages[0].content) : '（メッセージがありません）'" class="pr24 text-gray-500"></p>
                        </li>
                    </ul>
                </div>
                <div class="ml24" data-bind="foreach: messages">
                    <div>
                        <p data-bind="text: $data.content"></p>
                        <!-- <p data-bind="text: $data.send_date"></p> -->
                    </div>
                </div>
            </div>
        </div>
        <p data-bind="click: $root.test">test</p>
    </div>
    <script>
        const API_ENDPOINT = "{{ config('api.message_api_base_url_frontend') }}";
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
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

                // self.sample = ko.observable(1);
                // self.sampleArray = ko.observableArray();
                // self.sampleJson = ko.mapping.fromJS({name: 'Scot', children: [{ id : 1, name : 'Alicw' }]});

                self.threads = ko.observableArray(@json($threads));
                self.messages = ko.observableArray();

                self.datetimeFormat = function(datetime) {
                    return dayjs(datetime).format('YYYY/MM/DD HH:mm');
                }

                self.truncateMessage = function(message) {
                    return message && message.length >= 20 ? message.substring(0, 19) + '...' : message;
                };

                self.getMessages = async function(data) {
                    self.messages.removeAll();
                    try {
                        const requestData = {
                            thread_id: data.id,
                        };
                        const response = await apiGetRequest(`${API_ENDPOINT}`, requestData);
                        self.messages(response);
                    } catch (error) {
                        console.error(formatErrorInfo(error))
                        showErrNotification()
                    }
                }
            }
            ko.applyBindings(new ViewModel());

            // const users = await apiGetRequest('https://api.example.com/users', { search: 'John', page: 2 });
            // // 実際のリクエストURL: https://api.example.com/users?search=John&page=2
            async function apiGetRequest(endpoint, params = {}) {
                try {
                    const accessToken = await fetchAccessToken();
                    const queryParams = new URLSearchParams(params).toString();
                    const url = queryParams ? `${endpoint}?${queryParams}` : endpoint;                
                    const response = await fetch(url, {
                        method: 'GET',
                        headers: {
                            'Authorization': `Bearer ${accessToken}`,
                            'Accept': 'application/json',
                        },
                    });
                    let data = await response.json();
                    if (!response.ok) {
                        if (response.status === 401 && data.message === 'token_expired') {
                            await refreshAccessToken();
                            data = await apiGetRequest(endpoint, params);
                        } else {
                            throw buildErrorObject(data.message, data.detail, response.status);
                        }
                    }
                    return data;
                } catch (error) {
                    throw error;
                }
            }

            // const newUser = await apiPostRequest('https://api.example.com/users', { name: 'John', age: 30 });
            // 実際のリクエストURL: https://api.example.com/users
            // リクエストボディ: { "name": "John", "age": 30 }
            async function apiPostRequest(endpoint, body = {}) {
                try {
                    const accessToken = await fetchAccessToken();
                    const response = await fetch(endpoint, {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${accessToken}`,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(body),
                    });
                    let data = await response.json();
                    if (!response.ok) {
                        if (response.status === 401 && data.message === 'token_expired') {
                            await refreshAccessToken();
                            data = await apiPostRequest(endpoint, body);
                        } else {
                            throw buildErrorObject(data.message, data.detail, response.status);
                        }
                    }
                    return data;
                } catch (error) {
                    throw error;
                }
            }

            async function fetchAccessToken() {
                try {
                    const response = await fetch('/company/message/access-token/', {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                        }
                    });
                    const data = await response.json();
                    if (!response.ok) {
                        if (response.status === 401) {
                            window.location.href = '/company/login';
                        }
                        throw buildErrorObject(data.message, data.detail, response.status);
                    }
                    return data.access_token;
                } catch (error) {
                    throw error;
                }
            }

            async function refreshAccessToken() {
                try {
                    const response = await fetch('/company/message/access-token/refresh', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        }
                    });
                    if (!response.ok) {
                        const data = await response.json();
                        throw buildErrorObject(data.message, data.detail, response.status);
                    }
                    return;
                } catch (error) {
                    throw error;
                }
            }

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

            function showErrNotification() {
                notyf.open({
                    type: 'error',
                    dismissible: true,
                    message: '<b>エラーが発生しました</b><br />もう一度お試しください'
                });
            }
        });
    </script>
</x-app-layout>