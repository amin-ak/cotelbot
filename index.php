<?php
// Import File
require('core.php');
require('db.php');

$content = file_get_contents("php://input");
$update = json_decode($content, true);

$hr = "\n -------------------";
//
    if (isset($update["message"])) {
        processMessage($update);
    } elseif (isset($update["callback_query"])) {
        processCallback($update);

    // $query = $update['callback_query'];
        // $query_id = $query['id'];
        // $query_userID = $query['from']['id'];
        // $query_data = $query['data'];
        // $agg = json_encode($update, JSON_PRETTY_PRINT);
        // if ($query_data == 'befahm2') {
        //     // bot('sendmessage',['chat_id'=>$query_userID,'text'=>'chi migiiiiiiiiii?']);
        // }
        // bot('sendmessage', ['chat_id'=>'@codeup','text'=>$agg]);
        // processCallback($update);
        // bot('sendmessage', ['chat_id'=>'@codeup','text'=>'processCallback']);
    } elseif (isset($update["inline_query"])) {
        inlineMessage($update);
        bot('sendmessage', ['chat_id'=>'@Molkabadi','text'=>'processCallback']);
    }


// bot('sendmessage', [

//     'chat_id'=>'@Molkabadi',
//     'text'=>$update

//     ]);

    $agg = json_encode($update, JSON_PRETTY_PRINT);
    bot('sendmessage', ['chat_id'=>'@Molkabadi','text'=>$agg]);

function processMessage($update)
{
    if (isset($update['message']['text'])) {
        $text = $update['message']['text'];
    } else {
        $text = null;
    }

    $chat_id = $update['message']['chat']['id'];
    $db = Db::getInstance();
    if ($chat_id == 412213803) {
        if ($text == '/start') {
            // bot('sendmessage', [
            //         'chat_id'=>$chat_id,
            //         'text'=>'welcpme    ',
            //         'reply_markup'=>[
            //             'inline_keyboard'=>[

            //                 [['text'=>'Go','url'=>'https://t.me/utubebot?start=1&rate=4'],['text'=>'gotoGoogle','url'=>'https://t.me/utubebot?start=1&rate=4']],
            //                 [['text'=>'inline','url'=>'https://t.me/Codentobot?start=1']],
            //                 [['text'=>'befahm3','callback_data'=>'befahm2']]
            //         ],
            //             'resize_keyboard' => true,
            //             'one_time_keyboard' => true

            //         ]
            //     ]);
            bot('sendmessage', [
                'chat_id'=>$chat_id,
                'text'=>'hi guys',
                'reply_markup'=>[
                    'keyboard'=>[
                        ['📊 Statistics','📝 Create New Post'],
                        ['buy','SendMessage'],
                    ],
                    'resize_keyboard' => true,
                    'one_time_keyboard' => true
                ]
            ]);
        } elseif ($text == 'sendpm') {
            bot('sendmessage', [
                'chat_id'=>$chat_id,
                'text'=>'Welcome 11to Teest Admin panel',
                'reply_markup'=>[
                    'keyboard'=>[
                        ['📊 Statistics','📝 Create New Post'],
                        ['buy','SendMessage'],
                        ['Contact Us'],
                    ],
                    'resize_keyboard' => true,
                    'one_time_keyboard' => true
                ]
            ]);
        }
        // Start : Clicker
        elseif ($text == '📝 Create New Post') {
            $click_id = uniqid("click_");
            // ---------------------
            if ($db->modify('UPDATE admin SET status=:status,status_click_id=:click_id WHERE user_id=:chat_id', ['chat_id'=>$chat_id,'click_id'=>$click_id,'status'=>'sendMedia'])) {
                // bot('sendmessage',['chat_id'=>412213803,'text'=>'clicker is enable']);
            } else {
                bot('sendmessage', ['chat_id'=>412213803,'text'=>'clicker is failed']);
            }
            if ($db->insert('INSERT INTO clicks (click_id) VALUES (:click_id)', ['click_id'=>$click_id])) {
                // bot('sendmessage',['chat_id'=>412213803,'text'=>'ok']);
            } else {
                // bot('sendmessage',['chat_id'=>412213803,'text'=>'no']);
            }
            // ---------------------
            bot('sendmessage', [
                'chat_id'=>$chat_id,
                'text'=>"🏙 Send Post Media\n\nInclude ➜ Image / Video / Gif",
                'reply_markup'=>[
                    'keyboard'=>[
                        ['◀ Back to Menu']
                    ],
                    'resize_keyboard' => true,
                    'one_time_keyboard' => true
                ]
            ]);
        }
        // END : Clicker
        // ========================================
        // START : SendMessage
        elseif ($text == 'SendMessage') {
            bot('sendmessage', [
                'chat_id'=>$chat_id,
                'text'=>'send message',
                'reply_markup'=>[
                'keyboard'=>[
                    ['◀ Back to Menu']
                ],
                'resize_keyboard' => true]


            ]);
        }
        // END : SendMessage
        // ========================================

        // Start : Amar
        elseif ($text == '📊 Statistics') {
            $member = $db->query('SELECT DISTINCT user_id FROM users');
            $count = count($member);
            bot('sendmessage', [
                'chat_id'=>$chat_id,
                'text'=>'تعداد کاربران ربات : '.$count
                ]);
        }
        // END : Amar

        // =======================================
        // Start : back button
        elseif ($text == '◀ Back to Menu') {
            $result = $db->query('SELECT status,status_click_id FROM admin');
            if ($result[0]['status_click_id'] == 0) {
                $db->modify('UPDATE admin SET status=:status WHERE user_id=:chat_id', ['status'=>0,'chat_id'=>$chat_id]);
            } else {
                $db->modify('UPDATE admin SET status=:status,status_click_id=:click_id  WHERE user_id=:chat_id', ['status'=>0,'click_id'=>0,'chat_id'=>$chat_id]);
                $db->query('DELETE FROM clicks WHERE click_id=:click_id', ['click_id'=>$result[0]['status_click_id']]);
            }
            bot('sendmessage', [
                'chat_id'=>$chat_id,
                'text'=>'Back To MainMenu',
            ]);
            bot('sendmessage', [
                'chat_id'=>$chat_id,
                'text'=>'Choose a choice ⬇',
                'reply_markup'=>[
                    'keyboard'=>[
                        ['📊 Statistics','📝 Create New Post']
                    ],
                    'resize_keyboard' => true,
                    'one_time_keyboard' => true

                ]
            ]);
        }
        // END : back button
        // ===================================
        // START : sendMedia
        else {
            $query = $db->query('SELECT * FROM admin');
            $status = $query[0]['status'];
            $click_id = $query[0]['status_click_id'];
            // bot('sendmessage',['chat_id'=>412213803,'text'=>'hiiiii']);
            // bot('sendmessage',['chat_id'=>412213803,'text'=>$status]);
            // bot('sendmessage',['chat_id'=>412213803,'text'=>$click_id]);
            if ($status == 'sendMedia') {
                if (isset($update['message']['photo'][2]['file_id'])) {
                    $file_id = $update['message']['photo'][2]['file_id'];

                    // bot('sendmessage',['chat_id'=>412213803,'text'=>$file_id]); // test

                    $db->modify('UPDATE admin SET status=:status WHERE user_id=:chat_id', ['chat_id'=>$chat_id,'status'=>'text_des']);
                    // bot('sendmessage',['chat_id'=>412213803,'text'=>'update admin ok']);
                    $db->modify('UPDATE clicks SET file_id=:file_id,type=:type WHERE click_id=:click_id', ['file_id'=>$file_id,'type'=>'photo','click_id'=>$click_id]);
                    bot('sendmessage', [
                        'chat_id'=>$chat_id,
                        'text'=>'📝 Send Caption ',
                        'reply_markup'=>[
                            'keyboard'=>[
                                ['◀ Back to Menu']
                            ],'resize_keyboard' => true,
                            'one_time_keyboard' => true
                        ]
                    ]);
                } elseif (isset($update['message']['animation']['file_id'])) {
                    $file_id = $update['message']['animation']['file_id'];
                    $db->modify('UPDATE admin SET status=:status WHERE user_id=:chat_id', ['chat_id'=>$chat_id,'status'=>'text_des']);
                    $db->modify('UPDATE clicks SET file_id=:file_id,type=:type WHERE click_id=:click_id', ['file_id'=>$file_id,'type'=>'gif','click_id'=>$click_id]);
                    bot('sendmessage', [
                        'chat_id'=>$chat_id,
                        'text'=>'📝 Send Caption ',
                        'reply_markup'=>[
                            'keyboard'=>[
                                ['◀ Back to Menu']
                            ],'resize_keyboard' => true,
                            'one_time_keyboard' => true
                        ]
                    ]);
                } elseif (isset($update['message']['video']['file_id'])) {
                    $file_id = $update['message']['video']['file_id'];
                    $db->modify('UPDATE admin SET status=:status WHERE user_id=:chat_id', ['chat_id'=>$chat_id,'status'=>'text_des']);
                    $db->modify('UPDATE clicks SET file_id=:file_id,type=:type WHERE click_id=:click_id', ['file_id'=>$file_id,'type'=>'video','click_id'=>$click_id]);
                    bot('sendmessage', [
                        'chat_id'=>$chat_id,
                        'text'=>'📝 Send Caption ',
                        'reply_markup'=>[
                            'keyboard'=>[
                                ['◀ Back to Menu']
                            ],'resize_keyboard' => true,
                        ]
                    ]);
                }
            }
            // Start : Description
            elseif ($status == 'text_des') {
                if (strlen($text) <= 4000) {
                    $db->modify('UPDATE admin SET status=:status WHERE user_id=:chat_id', ['chat_id'=>$chat_id,'status'=>'count_click']);
                    $db->modify('UPDATE clicks SET text_des=:text_des WHERE click_id=:click_id', ['text_des'=>$text,'click_id'=>$click_id]);
                    bot('sendmessage', [
                        'chat_id'=>$chat_id,
                        'text'=>'حالا تعداد کلیک رو بفرست #️⃣ ',
                        'reply_markup'=>[
                            'keyboard'=>[
                                ['◀ Back to Menu']
                            ],'resize_keyboard' => true,
                        ]
                    ]);
                } else {
                    bot('sendmessage', [
                        'chat_id'=>$chat_id,
                        'text'=>'تعداد کاراکتر ها باید کمتر از ۲۰۰ باشه دوباره امتحان کن',
                        'reply_markup'=>[
                            'keyboard'=>[
                                ['◀ Back to Menu']
                            ],'resize_keyboard' => true,
                        ]
                    ]);
                }
            }
            // End : Description
            // ========================================
            // Start : Click count
            elseif ($status == 'count_click') {
                $db->modify('UPDATE admin SET status=:status WHERE user_id=:chat_id', ['chat_id'=>$chat_id,'status'=>'btn_name']);
                $db->modify('UPDATE clicks SET count_click=:count_click WHERE click_id=:click_id', ['count_click'=>$text,'click_id'=>$click_id]);
                bot('sendmessage', [
                    'chat_id'=>$chat_id,
                    'text'=>'حالا اسم دکمه رو بفرست',
                    'reply_markup'=>[
                        'keyboard'=>[
                            ['◀ Back to Menu']
                        ],'resize_keyboard' => true,
                    ]
                ]);
            }

            // End : Click count

            // =========================================

            // Start : button Name
            elseif ($status == 'btn_name') {
                $db->modify('UPDATE admin SET status=:status WHERE user_id=:chat_id', ['chat_id'=>$chat_id,'status'=>'award']);
                $db->modify('UPDATE clicks SET btn_name=:btn_name WHERE click_id=:click_id', ['btn_name'=>$text,'click_id'=>$click_id]);
                bot('sendmessage', [
                    'chat_id'=>$chat_id,
                    'text'=>'حالا لینک یا فایل جایزه رو بفرست',
                    'reply_markup'=>[
                        'keyboard'=>[
                            ['◀ Back to Menu']
                        ],'resize_keyboard' => true,
                    ]
                ]);
            }
            // END : button Name
            // ===============================================
            // Start : Award (file-link ...)
            elseif ($status == 'award') {
                if (isset($update['message']['document'])) {
                    $file_id_award = $update['message']['document']['file_id'];
                    $db->modify('UPDATE admin SET status=:status WHERE user_id=:chat_id', ['chat_id'=>$chat_id,'status'=>'text_award_after']);
                    $db->modify('UPDATE clicks SET file_id_award=:award_id WHERE click_id=:click_id', ['award_id'=>$file_id_award,'click_id'=>$click_id]);
                    bot('sendmessage', [
                        'chat_id'=>$chat_id,
                        'text'=>'حالا متن بعدی جایزه رو بفرست',
                        'reply_markup'=>[
                            'keyboard'=>[
                                ['◀ Back to Menu'],'resize_keyboard' => true,
                            ]
                        ]
                    ]);
                } else {
                    $db->modify('UPDATE admin SET status=:status WHERE user_id=:chat_id', ['chat_id'=>$chat_id,'status'=>'text_award_after']);
                    $db->modify('UPDATE clicks SET text_award=:text_award WHERE click_id=:click_id', ['text_award'=>$text,'click_id'=>$click_id]);
                    bot('sendmessage', [
                        'chat_id'=>$chat_id,
                        'text'=>'حالا متن بعدی جایزه رو بفرست',
                        'reply_markup'=>[
                            'keyboard'=>[
                                ['◀ Back to Menu']
                            ],'resize_keyboard' => true,
                        ]
                    ]);
                }
            }
            // End : Award (file-link ...)
            // =====================================
            // Start : After Award Text (file-link )
            elseif ($status == 'text_award_after') {
                $db->modify('UPDATE admin SET status=:status WHERE user_id=:chat_id', ['chat_id'=>$chat_id,'status'=>'send_id']);
                $db->modify('UPDATE clicks SET text_award_after=:text_after WHERE click_id=:click_id', ['text_after'=>$text,'click_id'=>$click_id]);
                bot('sendmessage', [
                    'chat_id'=>$chat_id,
                    'text'=>'حالا ایدی چنلی که میخوای توش اینو بفرسی بفرست(حتما باید ربات توش ادمین باشه',
                    'reply_markup'=>[
                        'keyboard'=>[
                            ['Codeup'],
                            ['◀ Back to Menu']
                        ],'resize_keyboard' => true,
                    ]
                ]);
            }
            // End : After Award Text (file-link )
            elseif ($status == 'send_id') {
                $db->modify('UPDATE admin SET status=:status,status_click_id=:click_id WHERE user_id=:chat_id', ['chat_id'=>$chat_id,'click_id'=>'0','status'=>'0']);
                $query = $db->query('SELECT * FROM clicks WHERE click_id=:click_id', ['click_id'=>$click_id]);
                if ($query[0]['type'] == 'photo') {
                    $send = bot('sendphoto', [
                    'chat_id'=>'@'.$text,
                    'photo'=>$query[0]['file_id'],
                    'caption'=>$query[0]['text_des'],
                    'reply_markup'=>[
                        'inline_keyboard'=>[

                            [['text'=>'📬 '.$query[0]['btn_name'],'url'=>'http://t.me/Codentobot?start='.$click_id]],
                            // [['text'=>'befahm','callback_data'=>'befahm']],
                            [['text'=>'📥 تعداد دانلود : '.$query[0]['count_click_use']. ' از '.$query[0]['count_click'],'callback_data'=>'null']]
                        ]
                    ]
                    ]);
                } elseif ($query[0]['type'] == 'gif') {
                    $send = bot('sendAnimation', [
                    'chat_id'=>'@'.$text,
                    'animation'=>$query[0]['file_id'],
                    'caption'=>$query[0]['text_des'],
                    'reply_markup'=>[
                        'inline_keyboard'=>[
                            [['text'=>'📬 '.$query[0]['btn_name'],'url'=>'http://t.me/Codentobot?start='.$click_id]],
                            [['text'=>'📥 تعداد دانلود : '.$query[0]['count_click_use']. ' از '.$query[0]['count_click'],'callback_data'=>'null']]
                        ]
                    ]
                    ]);
                } elseif ($query[0]['type'] == 'video') {
                    $send = bot('sendVideo', [
                    'chat_id'=>'@'.$text,
                    'video'=>$query[0]['file_id'],
                    'caption'=>$query[0]['text_des'],
                    'reply_markup'=>[
                        'inline_keyboard'=>[
                            [['text'=>'📬 '.$query[0]['btn_name'],'url'=>'http://t.me/Codentobot?start='.$click_id]],
                            [['text'=>'📥 تعداد دانلود : '.$query[0]['count_click_use']. ' از '.$query[0]['count_click'],'callback_data'=>'null']]
                        ]
                    ]
                    ]);
                }
                $result = json_decode($send, true);
                $db->modify('UPDATE clicks SET message_id=:message_id, chat_id=:chat_id WHERE click_id=:click_id', ['message_id'=>$result['result']['message_id'],'chat_id'=>$result['result']['chat']['id'],'click_id'=>$click_id]);
                bot('sendmessage', [
                    'chat_id'=>$chat_id,
                    'text'=>'Send file is complete',
                    'reply_markup'=>[
                        'keyboard'=>[
                            ['📊 Statistics','📝 Create New Post']
                        ],
                    'resize_keyboard' => true,
                    ]
                ]);
            }
        }
        // END : sendMedia
        // ================================
        // Start : check channel
    } elseif ($chat_id == "22606659jj") {
        if ($text == '/start') {
            bot('sendmessage', [
                    'chat_id'=>$chat_id,
                    'text'=>'کاربر به جز ادمین',
                    'reply_markup'=>[
                        'inline_keyboard'=>[
                                [
                                ['text'=>'📪 به پایان رسید','callback_data'=>'end'],
                                ['text'=>'befahm','callback_data'=>'befahm']
                                ],
                        ],
                        'resize_keyboard' => true
                    ]
                ]);
        }
    } else {
        $channel_id_1 = '@ProjeYaab';

        $getchannel1 = bot('getChatMember', [
            'chat_id'=>$channel_id_1,
            'user_id'=>$chat_id
        ]);
        $getchannel1 = json_decode($getchannel1, true);
        $channel_id_2 = '@Qbyte';
        $getchannel2 = bot('getChatMember', [
            'chat_id'=>$channel_id_2,
            'user_id'=>$chat_id
        ]);
        $getchannel2 = json_decode($getchannel2, true);
        $channel_id_3 = '@Codento';
        $getchannel3 = bot('getChatMember', [
            'chat_id'=>$channel_id_3,
            'user_id'=>$chat_id
        ]);
        $getchannel3 = json_decode($getchannel3, true);
        $channel_id_4 = '@FullPackage';
        $getchannel4 = bot('getChatMember', [
              'chat_id'=>$channel_id_4,
              'user_id'=>$chat_id
          ]);
        $getchannel4 = json_decode($getchannel4, true);
        $text2 = explode(' ', $text);
        $text2 = $text2[1];
        $text3 = $text2[2];
        if ($text == '/start') {
            // bot('sendmessage',[
            //   'chat_id'=>$chat_id,س
            //   'text' => 'از کانال اقدام کنید.'
            // ]);
            bot('sendmessage', [
                    'chat_id'=>$chat_id,
                    'text'=>"از کانال اقدام کنید.",
                    'reply_markup'=>[
                        'inline_keyboard'=>[
                            [['text'=>'برگشت به کانال','url'=>'https://t.me/Codento']]
                            // [['text'=>'inline','url'=>'https://t.me/Codentobot?start=1']],
                            // [['text'=>'befahm3','callback_data'=>'befahm2']]
                        ],
                    'resize_keyboard' => true,
                ]
            ]);
        }

        if ($getchannel1['result']['status'] != 'left' && $getchannel2['result']['status'] != 'left' && $getchannel3['result']['status'] != 'left' && $getchannel4['result']['status'] != 'left') {
            // Start : if text2 == isset
            if (isset($text2)) {

                // user id for avoid duplicate request

                $result = $db->query('SELECT * FROM clicks WHERE click_id=:click_id', ['click_id'=>$text2]);
                
                $result1 = $db->query('select * from users where user_id=:user_id and click_id=:click_id', ['user_id'=>$chat_id,'click_id' => $result[0]['click_id']]);
                $user_count_id = count($result1);

                if ($user_count_id == 0) {
                    bot('sendmessage', [
                    'chat_id'=>$chat_id,
                    'text'=> "
                    تبریک میگم رفیق! 👏
                    "
                    ]);

                    $db->insert('INSERT INTO users (user_id,click_id) VALUES (:user_id,:click_id)', ['user_id'=> $chat_id,'click_id'=> $result[0]['click_id']]);
                    $click_use = $result[0]['count_click_use'] +1;
                    bot('editMessageReplyMarkup', [
                    'chat_id'=>$result[0]['chat_id'],
                    'message_id'=>$result[0]['message_id'],
                    'reply_markup'=>[
                            'inline_keyboard'=>[
                                [['text'=>'📬 '.$result[0]['btn_name'],'url'=>'http://t.me/Codentobot?start='.$text2]],

                                [['text'=>'📥 تعداد دانلود  : '.$click_use.' از '.$result[0]['count_click'],'callback_data'=>'null']]
                            ]
                        ]
                ]);
                    $db->modify('UPDATE clicks SET count_click_use=:click_use WHERE click_id=:click_id', ['click_use'=>$click_use,'click_id'=>$text2]);
                    
                    
                    if ($result[0]['file_id_award'] != null) {
                        
                            bot('sendDocument', [
                        'chat_id'=>$chat_id,
                        'document'=>$result[0]['file_id_award']
                    ]);
                    }else{
                        bot('sendmessage',[
                            'chat_id'=>$chat_id,
                            'text'=>$result[0]['text_award']
                            ]);
                    }

                    // Text Award After
                    bot('sendmessage', [
                    'chat_id'=>$chat_id,
                    'text'=>$result[0]['text_award_after']
                    ]);

                    // bot('sendmessage', [
                    //       'chat_id'=>$chat_id,
                    //       'text'=>$text3
                    //   ]);
                    if ($click_use >= $result[0]['count_click']) {
                        bot('editMessageReplyMarkup', [
                    'chat_id'=>$result[0]['chat_id'],
                    'message_id'=>$result[0]['message_id'],
                    'reply_markup'=>[
                            'inline_keyboard'=>[
                                [['text'=>'📪 به پایان رسید','callback_data'=>'end']],
                                [['text'=>'📥 تعداد دانلود  : '.$result[0]['count_click'].' از '.$result[0]['count_click'],'callback_data'=>'null']]
                            ]
                        ]
                    ]);
                    }
                } else {
                    bot('sendmessage', [
                        'chat_id'=>$chat_id,
                        'text'=> 'یک بار این فایل رو دریافت کردید 😉'
                    ]);
                }
            }
            // END : if text2 == isset
        }
        // START : have to Join
        else {
            bot('sendmessage', ['chat_id'=>$chat_id,'text'=>'برای دریافت ( فایل | لینک | محصول ) این مراحل رو انجام بدید 👇👇

        1⃣ در همه‌ی کانال های زیر عضو شوید.

        @ProjeYaab
        @Qbyte
        @FullPackage

        2⃣ بعد از عضویت در کانالهای ذکر شده "حتما" دوباره به کانال @Codento برید و مجددا روی دریافت فایل بزنید.

        📛 اگر مجدد از داخل کانال اقدام نکنید از ربات هیچ پاسخی دریافت نمیکنید.']);
        }
        // END : have to Join
    }
    // END : check channel
}

function processCallback($update)
{
    $data = $update['callback_query']['data'];
    $id = $update['callback_query']['id'];
    $inline_message_id = $update['callback_query']['inline_message_id'];
    $firstname = $update['callback_query']['from']['first_name'];
    $chat_id = $update['message']['chat']['id'];
    //     $inline_message_id = $update['callback_query']['inline_message_id'];
    //     $firstname = $update['callback_query']['from']['first_name'];
    //     bot('editMessageText',[
    //         'inline_message_id'=>$inline_message_id,
    //         'text'=>'کاربر زیر برنده شد
    // '.$firstname
    //     ]);

    if ($data == 'null') {
        bot('answerCallbackQuery', [
            'callback_query_id'=>$id,
            'text'=>"کُدِنتو |‌ حمایت شما به ما انگیزه میده 🍊 "
        ]);
    } elseif ($data == 'end') {
        bot('answerCallbackQuery', [
            'callback_query_id'=>$id,
            'text'=>'❌ظرفیت دانلود فایل پر شده و شما نمیتونید فایل رو دریافت کنید ❌'
        ]);
    } elseif ($data == 'befahm2') {
        $query = $update['callback_query'];
        $query_id = $query['id'];
        $query_userID = $query['from']['id'];
        $query_data = $query['data'];
        bot('sendmessage', ['chat_id'=>$query_userID,'text'=>'https://google.com']);

        bot('answerCallbackQuery', [
          'callback_query_id'=>$id,
          'text'=>'❌AAA ❌'
      ]);

        bot('sendmessage', [
          'chat_id'=>$query_userID,
          'text'=>'Welcome to Teest Admin panel',
          'reply_markup'=>[
              'keyboard'=>[
                  ['📊 Statistics','📝 Create New Post'],
                  ['buy','SendMessage'],
                  ['Contact Us'],
              ],
              'resize_keyboard' => true,

          ]
      ]);
    }
}
