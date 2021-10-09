<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Client;

use Telegram;
use Telegram\Bot\Keyboard\Keyboard;
use Log;

class WebHookController extends Controller
{
    private $update;
    private $client;

    public function update($coin) {
        Telegram::commandsHandler(true);

        $this->initial();

        return 'ok';
    }

    public function initial() {
        $update = Telegram::getWebhookUpdates();
        Log::info($update);
        $this->update = $update;

        $type = $update->detectType();
        if(!$type) {
            Log::info($update);
            return;
        }
        $tg_id = $update->{$type}->from->id;

        $client = Client::where('tg_id', $tg_id)->first();

        if(!$client) {
            Log::info('no client yet');
            return;
        }

        $this->client = $client;

        if($type == 'callback_query') {
            $this->callbacks();
        } else if($type == 'message') {
            $this->messages();
        }

        $this->required_settings();
    }

    public function required_settings() {
        if(!$this->client->lang) {
            $this->langSetter();
            return;
        } else if(!$this->client->login) {
            $this->loginSetter();
            return;
        } else if(!$this->client->password) {
            $this->passwordSetter();
            return;
        }  else {
            $this->menu();
        }

    }

    public function callbacks() {
        $data_string = $this->update->callback_query->data;

        $data = explode('--', $data_string);
        $type = $data[0];
        $value = $data[1];

        if($type === 'set_lang') {
            $this->clientUpdate('lang', $value);
        }

        Log::info($this->update);
    }

    public function messages() {
        $text = $this->update->message->text;

        switch($this->client->last_position) {
            case 'login':
                $this->clientUpdate('login', $text, true);
                break;
            case 'password':
                $this->clientUpdate('password', $text);
                break;
            default:
                $this->menuSwitch($text);
                break;
        }

        Log::info($this->update->message->text);
    }

    public function menuSwitch($action) {
        $actions = [
            '🛒 Магазин' => [
                'name' => 'exchange',
                'text' => 'Предложения',
                'keyboard' => [
                    ['text' => 'Выбор предложения', 'callback_data' => 'exchange--select'],
                    ['text' => 'Создать предложение', 'callback_data' => 'exchange--create'],
                ]
            ],
            'ℹ Информация' => [
                'name' => 'info',
                'text' => 'Информация о боте',
                'keyboard' => [
                    ['text' => 'О проекте', 'callback_data' => 'info--about'],
                    ['text' => 'F.A.Q.', 'callback_data' => 'info--faq'],
                    ['text' => 'Условия, комиссии, лимиты', 'callback_data' => 'info--limits'],
                    ['text' => 'Поддержка', 'callback_data' => 'info--support'],
                ]
            ],
            '🤵 Аккаунт' => [
                'name' => 'account',
                'text' => 'Об аккаунте',
                'keyboard' => [
                    ['text' => 'Настройки', 'callback_data' => 'account--settings'],
                    ['text' => 'Рефералка', 'callback_data' => 'account--referals'],
                    ['text' => 'История сделок', 'callback_data' => 'account--history'],
                ]
            ],
        ];

        $current_action = isset($actions[$action]) ? $actions[$action] : null;

        if(!$current_action) {
            return;
        }

        $current_position = $current_action['name'];

        $this->client->last_position = $current_position;
        $this->client->save();

        $keyboard = Keyboard::make()->inline();


        foreach($current_action['keyboard'] as $keyboard_element) {
            $keyboard->row(Keyboard::inlineButton($keyboard_element));
        }

        Telegram::sendMessage([
            'chat_id'      => $this->client->tg_id,
            'text'         => $current_action['text'],
            'reply_markup' => $keyboard
        ]);
    }

    public function langSetter() {
        $this->client->last_position = 'language';
        $this->client->save();

        $keyboard = Keyboard::make()
            ->inline()
            ->row(
                Keyboard::inlineButton(['text' => 'RU', 'callback_data' => 'set_lang--ru']),
                Keyboard::inlineButton(['text' => 'EN', 'callback_data' => 'set_lang--en']),
                Keyboard::inlineButton(['text' => 'UZ', 'callback_data' => 'set_lang--uz']),
            );

        Telegram::sendMessage([
            'chat_id'      => $this->client->tg_id,
            'text'         => 'Language',
            'reply_markup' => $keyboard
        ]);
    }

    public function loginSetter() {
        $this->client->last_position = 'login';
        $this->client->save();

        Telegram::sendMessage([
            'chat_id'      => $this->client->tg_id,
            'text'         => 'Введите логин',
        ]);
    }

    public function passwordSetter() {
        $this->client->last_position = 'password';
        $this->client->save();

        Telegram::sendMessage([
            'chat_id'      => $this->client->tg_id,
            'text'         => 'Введите пароль',
        ]);
    }

    public function clientUpdate($param, $value, $unique = false) {
        if($unique) {
            if(Client::where($param, $value)->exists()) {
                Telegram::sendMessage([
                    'chat_id'      => $this->client->tg_id,
                    'text'         => 'Значение занято!',
                ]);
                return;
            }
        }
        $this->client->{$param} = $value;
        $this->client->save();
    }

    public function menu() {
        $this->client->last_position = 'menu';
        $this->client->save();

        $keyboard = [
            ['🛒 Магазин', 'ℹ Информация', '🤵 Аккаунт'],
        ];

        $r_k = Keyboard::make([
            'keyboard' => $keyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ]);

        Telegram::sendMessage([
            'chat_id' => $this->client->tg_id,
            'text'         =>
            'Меню:

👍 Lorem ipsum dolor sit amet, consectetur adipiscing elit.

🎁 Ut egestas suscipit pellentesque. Aenean laoreet, mi aliquam tristique maximus, nisi nunc convallis risus, eu ornare sapien enim id nibh.',
            'reply_markup' => $r_k
        ]);
    }
}
