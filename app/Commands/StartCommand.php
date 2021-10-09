<?php

namespace App\Commands;

use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;

use App\Models\Client;

use App\Http\Controllers\WebHookController;

use Telegram;
use Log;

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
        $updates = Telegram::getWebhookUpdates();
        $tg_id = $updates->message->from->id;

        $client = Client::firstOrCreate(
            ['tg_id' => $tg_id],
            ['last_position' => 'language']
        );

        Log::info($client->id);

        // This will send a message using `sendMessage` method behind the scenes to
        // the user/chat id who triggered this command.
        // `replyWith<Message|Photo|Audio|Video|Voice|Document|Sticker|Location|ChatAction>()` all the available methods are dynamically
        // handled when you replace `send<Method>` with `replyWith` and use the same parameters - except chat_id does NOT need to be included in the array.
        $this->replyWithMessage(['text' => 'Добро пожаловать!']);
    }
}
