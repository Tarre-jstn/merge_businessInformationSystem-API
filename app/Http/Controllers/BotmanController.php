<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;
use Illuminate\Http\Request;

class BotmanController extends Controller
{
    public function handle(Request $request)
    {
        $botman = app('botman');

        $botman->hears('{message}', function (BotMan $bot, $message) {
            if ($message == 'Option 1') {
                $bot->reply('You chose Option 1!');
            } elseif ($message == 'Option 2') {
                $bot->reply('You chose Option 2!');
            } else {
                $bot->reply('Option not recognized.');
            }
        });

        $botman->listen();
    }
}
