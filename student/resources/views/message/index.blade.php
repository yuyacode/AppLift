<x-app-layout>
    <x-slot name="header">
        <div class="flex_custom space-around_custom">
            <p class="text-sm"><a href="{{ route('message.index') }}">メッセージ</a></p>
            <p class="text-sm"><a href="{{ route('company_info.index') }}">企業情報</a></p>
        </div>
    </x-slot>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6 py-12">
        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg space-y-6">
            <div class="flex_custom">
                <div class="w25per h530 y-scroll br-gray">
                    <ul data-bind="foreach: threads">
                        <li class="pt16 pb16 pr8 pl8" data-bind="css: {'bt-gray': $index() !== 0, 'bg-gray': $root.selectedThreadId() && $data.id === $root.selectedThreadId()}">
                            <div class="flex_custom space-between_custom mb8">
                                <p class="fz14 underline"><a href="#" data-bind="text: $data.company_name"></a></p>
                            </div>
                            <div class="flex_custom space-between_custom mb8">
                                <p class="fz14 underline"><a href="#" data-bind="text: $data.company_user_name"></a></p>
                                <p data-bind="text: $root.datetimeFormat($data.last_activity_at())" class="fz12 text-gray-500"></p>
                            </div>
                            <div data-bind="click: function() {$root.getMessages($data.id, $index());}" class="fz14 text-gray-500 pointer">
                                <!-- ko if: $data.messages[0].content() !== '' -->
                                    <p data-bind="text: $root.truncateMessage($data.messages[0].content())"></p>
                                <!-- /ko -->
                                <!-- ko if: $data.messages[0].content() === '' -->
                                    <p>&nbsp;</p>
                                <!-- /ko -->
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="w75per ml24">
                    <!-- ko if: $root.selectedThreadId() -->
                        <!-- ko if: $root.messages().length > 0 -->
                            <div data-bind="foreach: messages" class="h450 y-scroll" id="messages">
                                <div class="mb16 flex_custom direction-column" data-bind="css: {'align-start': $data.is_from_company === 1, 'align-end': $data.is_from_student === 1}">
                                    <div class="max-w70per pt8 pb8 pr12 pl12 mb4 bg-gray radius8">
                                        <p data-bind="visible: $root.showMsgContent($data.id), text: $data.content()" class="fz14"></p>
                                        <!-- ko if: $data.is_from_student === 1 -->
                                            <div data-bind="visible: $root.showMsgContent($data.id)" class="flex_custom justify-end mt-1">
                                                <p data-bind="click: function() {$root.editingMessageId($data.id)}" class="fz12 text-gray-500 mr8 pointer">編集</p>
                                                <p data-bind="click: function() {$root.deleteMessage($data.id, $index())}" class="fz12 text-gray-500 pointer">削除</p>
                                            </div>
                                        <!-- /ko -->
                                        <div data-bind="visible: $root.showMsgEditForm($data.id)">
                                            <textarea data-bind="attr: {name: 'edit_message_content[' + $index() + ']'}, value: $data.content()" class="fz14 w550 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block"></textarea>
                                        </div>
                                        <div data-bind="visible: $root.showMsgEditForm($data.id)" class="flex_custom justify-end mt-1">
                                            <p data-bind="click: function() {$root.editMessage($data.id, $index())}" class="fz12 text-gray-500 mr8 pointer">保存</p>
                                            <p data-bind="click: function() {$root.editingMessageId(undefined)}" class="fz12 text-gray-500 pointer">やめる</p>
                                        </div>
                                    </div>
                                    <p class="text-gray-500 fz12" data-bind="text: $root.datetimeFormat($data.sent_at) + ($data.is_sent === 0 ? ' 送信予定' : '')"></p>
                                </div>
                            </div>
                        <!-- /ko -->
                        <!-- ko if: $root.messages().length === 0 -->
                            <div class="h450">
                                <p class="fz14">メッセージがありません</p>
                            </div>
                        <!-- /ko -->
                        <div class="flex_custom mt8">
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
                self.threads = ko.observableArray(
                    @json($threads).map(thread => ({
                        ...thread,
                        last_activity_at: ko.observable(thread.last_activity_at),
                        messages: thread.messages.length > 0
                            ?
                            thread.messages.map(message => ({
                                id: message.id,
                                message_thread_id: message.message_thread_id,
                                content: ko.observable(message.content),
                            }))
                            :
                            [{
                                id: null,
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
                self.editingMessageId = ko.observable();

                self.datetimeFormat = function(datetime, ISO = false) {
                    return ISO ? dayjs(datetime).format('YYYY-MM-DD[T]HH:mm:ss+09:00') : dayjs(datetime).format('YYYY-MM-DD HH:mm:ss');
                }

                self.truncateMessage = function(message) {
                    return message && message.length >= 19 ? message.substring(0, 18) + '...' : message;
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
                        self.selectedThreadId(id);
                        self.selectedThreadIndex(index);
                        self.messages(self.observeMessageContent(response));
                        if (self.messages().length > 0) {
                            for (let i = self.messages().length - 1; i >= 0; i--) {
                                if (self.messages()[i].is_sent === 1) {
                                    self.threads()[index].last_activity_at(self.messages()[i].sent_at);
                                    self.threads()[index].messages[0].id = self.messages()[i].id;
                                    self.threads()[index].messages[0].content(self.messages()[i].content());
                                    break;
                                }
                            }
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
                            is_from_company: 0,
                            is_from_student: 1,
                            content: self.newMessageContent(),
                            is_sent: self.isReservedSend() ? 0 : 1,
                            sent_at: self.isReservedSend()
                                        ? self.datetimeFormat(self.reservedSendDate() + ' ' + self.reservedSendTime(), true)
                                        : self.datetimeFormat(new Date().toLocaleString(), true),
                        };
                        const response = await apiPostRequest(`${API_ENDPOINT}`, data);
                        self.newMessageContent('');
                        self.isReservedSend(false);
                        if (data.is_sent) {
                            self.threads()[self.selectedThreadIndex()].last_activity_at(data.sent_at);
                            self.threads()[self.selectedThreadIndex()].messages[0].id = response.id;
                            self.threads()[self.selectedThreadIndex()].messages[0].content(data.content);
                        }
                        delete data.message_thread_id;
                        data.id = response.id;
                        data.content = ko.observable(data.content)
                        if (self.messages().length === 0) {
                            self.messages.push(data);
                        } else {
                            let sorted = false;
                            for (let i = self.messages().length - 1; i >= 0; i--) {
                                if (new Date(self.messages()[i].sent_at) <= new Date(data.sent_at)) {
                                    self.messages.splice(i + 1, 0, data);
                                    sorted = true;
                                }
                            }
                            if (!sorted) {
                                self.messages.unshift(data);
                            }
                        }
                        const messagesScroll = document.getElementById('messages');
                        messagesScroll.scrollTop = messagesScroll.scrollHeight;
                    } catch (error) {
                        console.error(formatErrorInfo(error))
                        showGeneralErrNotification()
                    }
                }

                self.editMessage = async function(id, index) {
                    content = document.querySelector('textarea[name="edit_message_content[' + index + ']"').value;
                    if (content === '') {
                        notyf.open({
                            type: 'error',
                            dismissible: true,
                            message: 'メッセージ内容を入力してください'
                        });
                        return;
                    }
                    try {
                        const data = {
                            content: content,
                        };
                        await apiPatchRequest(`${API_ENDPOINT}/${id}`, data);
                        self.messages()[index].content(content);
                        if (self.threads()[self.selectedThreadIndex()].messages[0].id === id) {
                            self.threads()[self.selectedThreadIndex()].messages[0].content(content);
                        }
                    } catch (error) {
                        console.error(formatErrorInfo(error))
                        showGeneralErrNotification()
                    }
                    self.editingMessageId(undefined);
                }

                self.deleteMessage = async function(id, index) {
                    try {
                        await apiDeleteRequest(`${API_ENDPOINT}/${id}`);
                        self.messages.splice(index, 1);
                        if (self.threads()[self.selectedThreadIndex()].messages[0].id === id) {
                            let sentMessageExist = false;
                            if (self.messages().length > 0) {
                                for (let i = self.messages().length - 1; i >= 0; i--) {
                                    if (self.messages()[i].is_sent === 1) {
                                        sentMessageExist = true;
                                        self.threads()[self.selectedThreadIndex()].last_activity_at(self.messages()[i].sent_at);
                                        self.threads()[self.selectedThreadIndex()].messages[0].id = self.messages()[i].id;
                                        self.threads()[self.selectedThreadIndex()].messages[0].content(self.messages()[i].content());
                                        break;
                                    }
                                }
                            }
                            if (self.messages().length === 0 || !sentMessageExist) {
                                self.threads()[self.selectedThreadIndex()].last_activity_at(self.threads()[self.selectedThreadIndex()].created_at);
                                self.threads()[self.selectedThreadIndex()].messages[0].id = null;
                                self.threads()[self.selectedThreadIndex()].messages[0].content('');
                            }
                        }
                    } catch (error) {
                        console.error(formatErrorInfo(error))
                        showGeneralErrNotification()
                    }
                }

                self.observeMessageContent = function(data) {
                    return data.map(item => ({
                        id:              item.id,
                        is_from_company: item.is_from_company,
                        is_from_student: item.is_from_student,
                        content:         ko.observable(item.content),
                        is_sent:         item.is_sent,
                        sent_at:         item.sent_at,
                    }));
                }

                self.showMsgContent = function(id) {
                    return ko.computed(function() {
                        return !self.editingMessageId() || self.editingMessageId() !== id;
                    });
                };

                self.showMsgEditForm = function(id) {
                    return ko.computed(function() {
                        return self.editingMessageId() && self.editingMessageId() === id;
                    });
                };
            }
            ko.applyBindings(new ViewModel());

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

            async function apiPatchRequest(endpoint, body = {}) {
                try {
                    const accessToken = await fetchAccessToken();
                    const response = await fetch(endpoint, {
                        method: 'PATCH',
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
                            data = await apiPatchRequest(endpoint, body);
                        } else {
                            throw buildErrorObject(data.message, data.detail, response.status);
                        }
                    }
                    return data;
                } catch (error) {
                    throw error;
                }
            }

            async function apiDeleteRequest(endpoint) {
                try {
                    const accessToken = await fetchAccessToken();
                    const response = await fetch(endpoint, {
                        method: 'DELETE',
                        headers: {
                            'Authorization': `Bearer ${accessToken}`,
                            'Accept': 'application/json',
                        },
                    });
                    let data = await response.json();
                    if (!response.ok) {
                        if (response.status === 401 && data.message === 'token_expired') {
                            await refreshAccessToken();
                            data = await apiDeleteRequest(endpoint);
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
                    const response = await fetch('/student/message/access-token/', {
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
                    return data.access_token;
                } catch (error) {
                    throw error;
                }
            }

            async function refreshAccessToken() {
                try {
                    const response = await fetch('/student/message/access-token/refresh', {
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