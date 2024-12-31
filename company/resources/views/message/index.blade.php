<x-app-layout>
    <x-slot name="header">
        <div class="flex_custom space-around_custom">
            <p class="text-sm"><a href="{{ route('message.index') }}">メッセージ</a></p>
            <p class="text-sm"><a href="{{ route('review.edit') }}">レビュー</a></p>
        </div>
    </x-slot>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6 py-12">
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg space-y-6">
            <div class="flex_custom">
                <div class="w25per h500 y-scroll br-gray">
                    <ul data-bind="foreach: threads">
                        <li class="pt16 pb16 pr8 pl8 pointer" data-bind="css: {'bt-gray': $index() !== 0, 'bg-gray': $root.selectedThreadId() && $data.id === $root.selectedThreadId()}, click: function() {$root.getMessages($data.id, $index());}">
                            <div class="flex_custom space-between_custom mb8">
                                <p data-bind="text: '学生' + $data.student_user_id" class="fz14"></p>
                                <p data-bind="text: $root.datetimeFormat($data.last_activity_at())" class="fz12 text-gray-500"></p>
                            </div>
                            <p data-bind="text: $data.messages.length > 0 ? $root.truncateMessage($data.messages[0].content()) : ''" class="fz14 pr24 text-gray-500"></p>
                        </li>
                    </ul>
                </div>
                <div class="w75per ml24">
                    <!-- ko if: $root.selectedThreadId() -->
                        <!-- ko if: $root.messages().length > 0 -->
                            <div data-bind="foreach: messages" class="h400 y-scroll" id="messages">
                                <div class="mb16 flex_custom direction-column" data-bind="css: {'align-start': $data.is_from_student === 1, 'align-end': $data.is_from_company === 1}">
                                    <div class="max-w70per pt8 pb8 pr12 pl12 mb4 bg-gray radius8">
                                        <p data-bind="text: $data.content" class="fz14"></p>
                                        <div class="flex_custom justify-end">
                                            <p class="fz12 text-gray-500 mr8 pointer">編集</p>
                                            <p class="fz12 text-gray-500 pointer">削除</p>
                                        </div>
                                    </div>
                                    <p class="text-gray-500 fz12" data-bind="text: $root.datetimeFormat($data.sent_at) + ($data.is_sent === 0 ? ' 送信予定' : '')"></p>
                                </div>
                            </div>
                        <!-- /ko -->
                        <!-- ko if: $root.messages().length === 0 -->
                            <div class="h400">
                                <p class="fz14">メッセージがありません</p>
                            </div>
                        <!-- /ko -->
                        <div class="flex_custom">
                            <textarea name="new_message_content" data-bind="value: newMessageContent" class="fz14 w550 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block"></textarea>
                            <div class="ml24">
                                <div>
                                    <input type="checkbox" name="is_reserved_send" id="is_reserved_send" data-bind="checked: $root.isReservedSend" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <label for="is_reserved_send" class="fz14">送信日時を設定する</label>
                                </div>
                                <!-- ko if: $root.isReservedSend() -->
                                    <div class="flex_custom mt8">
                                        <div class="mr8">
                                            <input type="date" name="reserved_send_date" data-bind="value: reservedSendDate" class="fz14 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        </div>
                                        <div>
                                            <select name="reserved_send_time" data-bind="value: reservedSendTime" class="fz14 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                                <option value="09:00">09:00</option>
                                                <option value="10:00">10:00</option>
                                                <option value="11:00">11:00</option>
                                                <option value="12:00">12:00</option>
                                                <option value="13:00">13:00</option>
                                                <option value="14:00">14:00</option>
                                                <option value="15:00">15:00</option>
                                                <option value="16:00">16:00</option>
                                                <option value="17:00">17:00</option>
                                                <option value="18:00">18:00</option>
                                            </select>
                                        </div>
                                    </div>
                                <!-- /ko -->
                                <div data-bind="click: $root.addMessage" class="fz14 w100 h40 p8 mt8 pointer button"><p>送信する</p></div>
                            </div>
                        </div>
                    <!-- /ko -->
                </div>
            </div>
            <p data-bind="click: $root.test">test</p>
        </div>
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

                self.threads = ko.observableArray(
                    @json($threads).map(thread => ({
                        ...thread,
                        last_activity_at: ko.observable(thread.last_activity_at),
                        messages: thread.messages.length > 0
                            ?
                            thread.messages.map(message => ({
                                message_thread_id: message.message_thread_id,
                                content: ko.observable(message.content),
                            }))
                            :
                            [{
                                message_thread_id: thread.id,
                                content: ko.observable(''),
                            }]
                    }))
                );
                self.messages = ko.observableArray();
                self.selectedThreadId = ko.observable();
                self.selectedThreadIndex = ko.observable();
                self.newMessageContent = ko.observable('');
                self.isReservedSend = ko.observable(false);
                self.reservedSendDate = ko.observable(new Date().toISOString().split('T')[0]);
                self.reservedSendTime = ko.observable('09:00');

                self.datetimeFormat = function(datetime, ISO = false) {
                    return ISO ? dayjs(datetime).format('YYYY-MM-DD[T]HH:mm:ss+09:00') : dayjs(datetime).format('YYYY-MM-DD HH:mm:ss');
                }

                self.truncateMessage = function(message) {
                    return message && message.length >= 17 ? message.substring(0, 16) + '...' : message;
                };

                self.getMessages = async function(id, index) {
                    self.messages.removeAll();
                    self.selectedThreadId(undefined);
                    self.selectedThreadIndex(undefined);
                    self.newMessageContent('');
                    self.isReservedSend(false);
                    try {
                        const requestData = {
                            thread_id: id,
                        };
                        const response = await apiGetRequest(`${API_ENDPOINT}`, requestData);
                        self.messages(response);
                        self.selectedThreadId(id);
                        self.selectedThreadIndex(index);
                        if (self.messages().length > 0) {
                            const messagesScroll = document.getElementById('messages');
                            messagesScroll.scrollTop = messagesScroll.scrollHeight;
                        }
                    } catch (error) {
                        console.error(formatErrorInfo(error))
                        showGeneralErrNotification()
                    }
                }

                self.addMessage = async function() {
                    if (self.newMessageContent() === '') {
                        notyf.open({
                            type: 'error',
                            dismissible: true,
                            message: 'メッセージ内容を入力してください'
                        });
                        return;
                    }
                    try {
                        const data = {
                            message_thread_id: self.selectedThreadId(),
                            is_from_company: 1,
                            is_from_student: 0,
                            content: self.newMessageContent(),
                            is_sent: self.isReservedSend() ? 0 : 1,
                            sent_at: self.isReservedSend() ? self.datetimeFormat(self.reservedSendDate() + ' ' + self.reservedSendTime(), true) : self.datetimeFormat(new Date().toLocaleString(), true),
                        };
                        const response = await apiPostRequest(`${API_ENDPOINT}`, data);
                        if (data.is_sent) {
                            self.threads()[self.selectedThreadIndex()].last_activity_at(data.sent_at);
                            self.threads()[self.selectedThreadIndex()].messages[0].content(data.content);
                        }
                        delete data.message_thread_id;
                        data.id = response.id;
                        if (self.messages().length === 0) {
                            self.messages.push(data);
                        } else {
                            for (let i = self.messages().length - 1; i >= 0; i--) {
                                if (new Date(self.messages()[i].sent_at) <= new Date(data.sent_at)) {
                                    self.messages.splice(i + 1, 0, data);
                                    return;
                                }
                            }
                            self.messages.unshift(data);
                        }
                    } catch (error) {
                        console.error(formatErrorInfo(error))
                        showGeneralErrNotification()
                    }
                }

                self.test = async function() {
                    // console.log(self.threads())
                    console.log(self.messages())
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