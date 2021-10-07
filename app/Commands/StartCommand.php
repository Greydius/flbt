<?php

namespace App\Commands;

use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;

class StartCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = "start";

    /**
     * @var string Command Description
     */
    protected $description = "Start Command to get you started";

    /**
     * @inheritdoc
     */
    public function handle()
    {
        // This will send a message using `sendMessage` method behind the scenes to
        // the user/chat id who triggered this command.
        // `replyWith<Message|Photo|Audio|Video|Voice|Document|Sticker|Location|ChatAction>()` all the available methods are dynamically
        // handled when you replace `send<Method>` with `replyWith` and use the same parameters - except chat_id does NOT need to be included in the array.
        $this->replyWithMessage(['text' => 'Добро пожаловать!']);

        $keyboard = [
            ['🛒 Магазин', 'ℹ Информация', '🤵 Аккаунт'],
        ];

        // $reply_markup = Telegram::replyKeyboardMarkup([
        //     'keyboard' => $keyboard,
        //     'resize_keyboard' => true,
        //     'one_time_keyboard' => true
        // ]);

        $r_k = Keyboard::make([
            'keyboard' => $keyboard, 
            'resize_keyboard' => true, 
            'one_time_keyboard' => true
        ]); 

        $this->replyWithMessage([
            'text'         => 
            'Меню:

👍 Lorem ipsum dolor sit amet, consectetur adipiscing elit.

🎁 Ut egestas suscipit pellentesque. Aenean laoreet, mi aliquam tristique maximus, nisi nunc convallis risus, eu ornare sapien enim id nibh.',
            'reply_markup' => $r_k
        ]);
    }
}
