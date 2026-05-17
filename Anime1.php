<?php
ob_start();
error_reporting(0);
date_Default_timezone_set('Asia/Tashkent');
define('API_KEY',"8376174975:AAFlCdQMEioAf4cyVhfkTCrJI0J6jZjYtMg");
$Axmedovs = "5775346497";

/*
@AniFineBot kodi!

Manba: @DavlatyorUz & @axmedovs01 (Chopmanglar ancha mehnat ketgan)
Tarqatildi: @TexnoPHPuz kanalida
*/




$admins = file_get_contents("tizim/admins.txt");
$admin = explode("\n", $admins);
array_push($admin,$Axmedovs);
$bot = bot('getme',['bot'])->result->username;

function getAdmin($chat){
$url = "https://api.telegram.org/bot".API_KEY."/getChatAdministrators?chat_id=@".$chat;
$result = file_get_contents($url);
$result = json_decode ($result);
return $result->ok;
}

function bot($method,$datas=[]){
$url = "https://api.telegram.org/bot".API_KEY."/".$method;
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch,CURLOPT_POSTFIELDS,$datas);
$res = curl_exec($ch);
if(curl_error($ch)){
var_dump(curl_error($ch));
}else{
return json_decode($res);
}}

function addstat($id){
    $dir = "users"; // Papka nomi
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true); // Agar yo‘q bo‘lsa, papkani yaratadi
    }

    $file = "$dir/$id.txt"; // ID ga mos fayl
    $sana = date("d.m.Y"); // Hozirgi sana

    if (!file_exists($file)) {
        file_put_contents($file, $sana); // Yangi fayl yaratib, sanani yozish
    }
}

function addblock($id){
$stat=file_get_contents("block");
$check=explode("\n",$stat);
if(!in_array($id,$check)){
file_put_contents("block","\n".$id,8);
}
}

function joinchat($id){
    global $bot;

    $needJoin = [];   // faqat majburiy kanallar
    $free = [];       // oddiy url tugmalar

    /* ========= 1. OMMAVIY MAJBURIY KANALLAR ========= */
    if (file_exists("channel.txt")) {
        $rows = explode("\n", trim(file_get_contents("channel.txt")));
        foreach ($rows as $row) {
            if (!$row) continue;
            $username = str_replace("@","",trim($row));

            $res = bot("getChatMember",[
                "chat_id"=>"@$username",
                "user_id"=>$id
            ]);

            $status = $res->result->status ?? "left";
            if (!in_array($status,["creator","administrator","member"])) {
                $needJoin[] = [[
                    'text'=>"❌ @$username",
                    'url'=>"https://t.me/$username"
                ]];
            }
        }
    }

    /* ========= 2. MAXFIY MAJBURIY KANALLAR ========= */
    if (file_exists("channel2.txt")) {
        $rows = explode("\n", trim(file_get_contents("channel2.txt")));
        for ($i=0;$i<count($rows);$i+=2){
            if (!isset($rows[$i+1])) continue;
            $link = trim($rows[$i]);
            $kid  = trim($rows[$i+1]);
            $f = "tizim/$kid.txt";

            if (!file_exists($f) || !in_array($id, explode("\n", trim(file_get_contents($f))))) {
                $needJoin[] = [[
                    'text'=>"❌ Maxfiy kanal",
                    'url'=>$link
                ]];
            }
        }
    }

    /* ========= 3. ODDIY URL (TEKSHIRILMAYDI) ========= */
    if (file_exists("channel3.txt")) {
        $rows = explode("\n", trim(file_get_contents("channel3.txt")));
        foreach ($rows as $l) {
            if ($l) {
                $free[] = [[
                    'text'=>"📎 Kanal",
                    'url'=>trim($l)
                ]];
            }
        }
    }

    /* ========= 4. QAROR ========= */
    if (!empty($needJoin)) {
        $kb = array_merge($needJoin, $free);
        $kb[] = [['text'=>"🔄 Tekshirish",'callback_data'=>"checksuv"]];

        bot("sendMessage",[
            "chat_id"=>$id,
            "text"=>"⚠️ <b>Botdan foydalanish uchun majburiy kanallarga obuna bo‘ling!</b>",
            "parse_mode"=>"html",
            "reply_markup"=>json_encode(["inline_keyboard"=>$kb])
        ]);
        return false;
    }

    return true; // ❗ faqat majburiy kanal yo‘q bo‘lsa
}

$Axmedov = json_decode(file_get_contents('php://input'));
$message = $Axmedov->message;
$cid = $message->chat->id;
$name = $message->chat->first_name;
$tx = $message->text;
$step = file_get_contents("step/$cid.step");
$mid = $message->message_id;
$type = $message->chat->type;
$text = $message->text;
$premium = $message->from->is_premium;
$bio = $message->from->about;
$username = $message->from->username;
$chat_id = $message->chat->id;
$message_id = $message->message_id;
$reply = $message->reply_to_message->text;
$uid = $message->from->id;
$name = $message->from->first_name;
$familya = $message->from->last_name;
$nameru = "<a href='tg://user?id=$uid'>$name $familya</a>";

$contact = $message->contact;
$contact_id = $contact->user_id;
$contact_user = $contact->username;
$contact_name = $contact->first_name;
$phone = $contact->phone_number;

$doc = $Axmedov->message->document;
$doc_id = $doc->file_id;


$call = $Axmedov->callback_query;
$mes = $call->message;
$username = $mes->chat->username;
$fristname = $call->from->first_name;

//Mahalliy metodlar
$botdel = $Axmedov->my_chat_member->new_chat_member;
$botdelid = $Axmedov->my_chat_member->from->id;
$doc = $Axmedov->message->document;
$doc_id = $doc->file_id;
$userstatus= $botdel->status;
$mes = $call->message;
$callmid = $mes->message_id;

//inline uchun metodlar
$callback = $Axmedov->callback_query;
$data = $Axmedov->callback_query->data;
$qid = $Axmedov->callback_query->id;
$id = $Axmedov->inline_query->id;
$query = $Axmedov->inline_query->query;
$query_id = $Axmedov->inline_query->from->id;
$cid2 = $Axmedov->callback_query->message->chat->id;
$mid2 = $Axmedov->callback_query->message->message_id;
$callfrid = $Axmedov->callback_query->from->id;
$callname = $Axmedov->callback_query->from->first_name;
$calluser = $Axmedov->callback_query->from->username;
$surname = $Axmedov->callback_query->from->last_name;
$about = $Axmedov->callback_query->from->about;
$frid= $Axmedov->callback_query->from->id;
$nameuz = "<a href='tg://user?id=$callfrid'>$callname $surname</a>";


$chat_join_request = $Axmedov->chat_join_request;
$join_chat_id = $chat_join_request->chat->id;
$join_user_id = $chat_join_request->from->id;

// 📌 Fayl va katalogni yaratamiz
if (!is_dir("tizim")) {
    mkdir("tizim", 0777, true);
}

// 📌 Faylni o‘qiymiz va mavjud IDlarni massivga ajratamiz
$fayl_nomi = "tizim/$join_chat_id.txt";
$ids = file_exists($fayl_nomi) ? explode("\n", trim(file_get_contents($fayl_nomi))) : [];

// 📌 Agar ID allaqachon mavjud bo‘lmasa, yangi IDni qo‘shamiz
if (!in_array($join_user_id, $ids)) {
    $ids[] = $join_user_id;
    file_put_contents($fayl_nomi, implode("\n", $ids) . "\n"); // ✅ Faqat kerakli ID-larni yozamiz

    bot('SendMessage',[
        'chat_id' => $join_user_id,
        'text' => "<b>/start - bosing va kino kodini yuboring!</b>",
        'parse_mode' => 'html'
    ]);
}

$caption = $message->caption;
$photo = $message->photo;
$video = $message->video;
$file_id = $video->file_id;
$file_name = $video->file_name;
$file_size = $video->file_size;
$size = $file_size/100000;
$dtype = $video->mime_type;


$photo = $message->photo;
$file = $photo[count($photo)-1]->file_id;

mkdir("step");
mkdir("admin");
mkdir("kino");
mkdir("tizim");


if(file_get_contents("admin/user.txt")){
	}else{
		if(file_put_contents("admin/user.txt","Kiritilmagan"));
}
if(file_get_contents("admin/admins.txt")){
	}else{
if(file_put_contents("admin/admins.txt","$Axmedovs"));
}
if(file_get_contents("kino/son.txt")){
	}else{
if(file_put_contents("kino/son.txt","0"));
}
if(file_get_contents("kino/kodi.txt")){
	}else{
if(file_put_contents("kino/kodi.txt","0"));
}
if(file_get_contents("egaa.txt")){
	}else{
if(file_put_contents("egaa.txt","$Axmedovs"));
}

$photo = $message->photo;
$file = $photo[count($photo)-1]->file_id;


mkdir("step");
mkdir("kino");

if(file_get_contents("kino/id.txt")==null){
file_put_contents("kino/id.txt",0);
}

$test1 = file_get_contents("step/test1.txt");
$test2 = file_get_contents("step/test2.txt");
$test3 = file_get_contents("step/test3.txt");
$test4 = file_get_contents("step/test4.txt");
$test5 = file_get_contents("step/test5.txt");
$test6 = file_get_contents("step/test6.txt");
$test7 = file_get_contents("step/test7.txt");
$test8 = file_get_contents("step/test8.txt");
$last_kino = file_get_contents("kino/id.txt");
$step = file_get_contents("step/$cid.step");



if(file_get_contents("holat.txt")){
	}else{
if(file_put_contents("holat.txt","Yoqilgan"));
}

if($botdel){ 
if($userstatus=="kicked"){ 
addblock($cid);
}}
if(isset($message)){
$block=file_get_contents("block");
$block=str_replace("\n".$cid,"",$block);
file_put_contents("block",$block);
}



$umum_d = date("d.m.Y H:i");
if(isset($message)){
addstat($cid);
}

if(isset($message)){
$baza = file_get_contents("azo.dat");
if(mb_stripos($baza,$chat_id) !==false){
}else{
$txt="\n$chat_id";
$file=fopen("azo.dat","a");
fwrite($file,$txt);
fclose($file);
bot('sendMessage',[
'chat_id'=>$Axmedovs,
'text'=>"<b>👤 Yangi obunachi qo'shildi!

👤 Ism: $name  
🆔 ID: <code>$cid</code>  
🔗 Telegram: $username 
🕒 Vaqt: " . date("d.m.Y | H:i") . "</b>",
'parse_mode'=>"html",
'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => "👀 Ko'rish", 'url' => "tg://user?id=$cid"]],
                ]
            ])
]);
}}

if(isset($callback)){
$baza = file_get_contents("azo.dat");
if(mb_stripos($baza,$callfrid) !==false){
}else{
$txt="\n$callfrid";
$file=fopen("azo.dat","a");
fwrite($file,$txt);
fclose($file);
bot('sendMessage',[
'chat_id'=>$Axmedovs,
'text'=>"<b>👤 Yangi obunachi qo'shildi!

👤 Ism: $callname  
🆔 ID: <code>$cid2</code>  
🔗 Telegram: $calluser 
🕒 Vaqt: " . date("d.m.Y | H:i") . "</b>",
'parse_mode'=>"html",
'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => "👀 Ko'rish", 'url' => "tg://user?id=$cid2"]],
                ]
            ])
]);
}}

$kanal_uz = file_get_contents("step/kanal.txt");
$kanalcha = file_get_contents("kino_ch.txt");
$holat = file_get_contents("holat.txt");

$panel = json_encode([
'resize_keyboard'=>true,
'keyboard'=>[
[['text'=>"📢 Kanallar"],['text'=>"📥 kino Yuklash"]],
[['text'=>"✉ Xabarnoma"],['text'=>"📊 Statistika"]],
[['text'=>"🤖 Bot holati"],['text'=>"👥 Adminlar"]],
[['text'=>"◀️ Orqaga"]],
]
]);

$orqa = json_encode([
'resize_keyboard'=>true,
'keyboard'=>[
[['text'=>"◀️ Orqaga"]],
]
]);

$bosh = json_encode([
'resize_keyboard'=>true,
'keyboard'=>[
[['text'=>"🗄 Boshqaruv paneli"]],
]
]);

if($data == "yopish"){
	bot('deleteMessage',[
'chat_id'=>$cid2,
'message_id'=>$mid2,
]);
}

$rpl = json_encode([
            'resize_keyboard'=>false,
            'force_reply'=>true,
            'selective'=>true
        ]);


$holat = file_get_contents("holat.txt");
if($text){
 if($holat == "O'chirilgan"){
	if(in_array($cid,$admin)){
}else{
	bot('sendMessage',[
	'chat_id'=>$cid,
	'text'=>"⛔️ <b>Bot vaqtinchalik o'chirilgan!</b>

<i>Botda ta'mirlash ishlari olib borilayotgan bo'lishi mumkin!</i>",
'parse_mode'=>'html',
]);
exit();
}
}else{
}
}

if($data){
 if($holat == "O'chirilgan"){
	if(in_array($cid2,$admin)){
}else{
	bot('answerCallbackQuery',[
		'callback_query_id'=>$qid,
		'text'=>"⛔️ Bot vaqtinchalik o'chirilgan!

Botda ta'mirlash ishlari olib borilayotgan bo'lishi mumkin!",
		'show_alert'=>true,
		]);
exit();
}
}else{
}
}


if ($text == "/start" and joinchat($cid) == true) {
    if (in_array($cid, $admin)) {
        $boshqar = "🗄 Boshqaruv paneli";
    } else {
        $boshqar = ""; 
    }

    bot('SendPhoto', [
        'chat_id' => $cid,
        'photo' => 'https://t.me/Rasmlarkod/37  ', // Rasm URL yoki file_id
        'caption' => "😊Assalomu Alaykum! $nameru\n<blockquote>Agar foydalanishda qiyinchilikka duch kelsangiz yoki administrator bilan aloqaga chiqmoqchi bo'lsangiz — /help buyrug'ini yuboring.</blockquote>\n\n<b>🎬 Iltimos, kino kodingizni yuboring:</b>",
        'parse_mode' => 'html',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [['text' => "🔎 kino kodlari", 'url' => "https://t.me/  " . str_ireplace("@", null, $kanalcha)]],
                ($boshqar ? [['text' => "$boshqar", 'callback_data' => "boshqar"]] : [])
            ]
        ])
    ]);

    exit();
}




if($text == "◀️ Orqaga" and joinchat($cid) == true){        
bot('SendMessage',[
'chat_id'=>$cid,
'text'=>"Assalomu Alaykum $nameru
kino Kodini Yuboring:",
'parse_mode'=>'html',
'disable_web_page_preview'=>true,
'reply_markup'=>$rpl,
]);
unlink("step/$cid.step");
exit();
}

if ($text == "/help" and joinchat($cid) == true) {    
    bot('SendPhoto', [
        'chat_id' => $cid,
        'photo' => 'https://t.me/Rasmlarkod/37', 
        'caption' => "<blockquote> 💻 <b>Savol va Takliflaringiz bolsa pastdagi manzilimizga murojaat qiling!</b>\n❗<b>Botga faqat Kino kodini kiriting ! </b></blockquote> ",
        'parse_mode' => 'html',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [['text' => "☎️ Qo'llab-quvvatlash", 'url' => "https://t.me/DavlatyorUZ"]]
            ]
        ])
    ]);
    exit();
}
if($data=="checksuv"){
bot('deleteMessage',[
	'chat_id'=>$cid2,
	'message_id'=>$mid2,
	]);
	if(joinchat($cid2) == true){
	bot('SendMessage',[
	'chat_id'=>$cid2,
	'text'=>"<b>✅ Obunangiz tasdiqlandi!</b>",
	'parse_mode'=>'html'
	]);
	if (in_array($cid, $admin)) {
            $boshqar = "🗄 Boshqaruv paneli";
        }
	bot('SendMessage',[
	'chat_id'=>$cid2,
	'text'=>"Assalomu Alaykum $nameru
kino Kodini Yuboring:",
	'parse_mode'=>'html',
	'disable_web_page_preview'=>true,
	'reply_markup' => json_encode([
'inline_keyboard'=>[
[['text'=>"🔎 kino kodlari",'url'=>"https://t.me/".str_ireplace("@",null,$kanalcha)]],
[['text' => "$boshqar", 'callback_data' => "boshqar"]]
]
])
	]);
	exit();
}}

// --- ID BO'YICHA QIDIRISH ---
if (is_numeric($text) == true and empty($step)) {
    $kino_id = $text;

    if (joinchat($cid) == 1) {
        // Tekshirish: Bunday ID li katalog (kino) mavjudmi?
        if (!is_dir("kino/$kino_id")) {
            bot('sendMessage', [
                'chat_id' => $cid,
                'text' => "<b>❌ Uzr, <code>$kino_id</code> ID raqamli kino topilmadi.</b>",
                'parse_mode' => 'html',
            ]);
            exit();
        }

        // Asosiy ma'lumotlarni olish
        $main_caption = file_get_contents("kino/$kino_id/nomi.txt");
        $rasm_id = file_get_contents("kino/$kino_id/rasm.txt");

        // --- QISMLARNI ANIQLASH ---
        $parts_dir = "kino/$kino_id/parts/";
        $inline_parts = [];
        $temp_row = [];
        $part_numbers = [];

        if (is_dir($parts_dir)) {
            $parts = array_filter(glob($parts_dir . '*'), 'is_dir');
            foreach ($parts as $part_path) {
                $part_numbers[] = basename($part_path);
            }
            sort($part_numbers, SORT_NUMERIC);
        }

        $part1_malumot = file_get_contents("kino/$kino_id/parts/1/malumot.txt") ?: "Ma'lumot topilmadi.";
        $part1_downcount = file_get_contents("kino/$kino_id/parts/1/downcount.txt") ?: 0;
        
        // --- CHIQARISH ---

        if (count($part_numbers) == 1) {
            // Faqat 1-qism bo'lsa, videoni yuborish
            $video_id = file_get_contents("kino/$kino_id/parts/1/film.txt");
            
            // Yuklash sonini oshirish
            file_put_contents("kino/$kino_id/parts/1/downcount.txt", $part1_downcount + 1);

            bot('sendVideo',[
                'chat_id'=>$cid,
                'video'=>$video_id,
                'caption'=>"<b>🍿 kino nomi: $main_caption (1-qism)</b>\n\nkino haqida: <blockquote>$part1_malumot</blockquote>\n\nId: <code>$kino_id</code>\n#️⃣ Qism: <code>1</code>\n🗂 Yuklashlar soni: ".($part1_downcount + 1)."\n\n🤖 Bizning bot: @$bot",
                'parse_mode'=>'html',
                'reply_markup'=>json_encode([
                    'inline_keyboard'=>[
                        [['text'=>"📋 Ulashish",'url'=>"https://t.me/share/url?url=https://t.me/$bot?start=$kino_id"]],
                    ]
                ])
            ]);
            
        } elseif (count($part_numbers) > 1) {
            // 1 tadan ko'p qism bo'lsa, rasm va qismlar tugmasini yuborish
            
            // Tugmalarni yaratish (har 5 ta qismdan keyin yangi qator)
            $row_counter = 0;
            foreach ($part_numbers as $part_num) {
                $temp_row[] = ['text' => $part_num, 'callback_data' => "get_part|$kino_id|$part_num"];
                $row_counter++;
                
                if ($row_counter % 5 == 0) {
                    $inline_parts[] = $temp_row;
                    $temp_row = [];
                }
            }
            if (!empty($temp_row)) {
                $inline_parts[] = $temp_row;
            }

            $caption_output = "<b>🍿 kino nomi: $main_caption</b>\n\n";
            $caption_output .= "kino haqida: <blockquote>$part1_malumot</blockquote>\n\n";
            $caption_output .= "Id: <code>$kino_id</code>\n";
            $caption_output .= "<b>Qismlari:</b>";
            
            bot('sendPhoto',[
                'chat_id'=>$cid,
                'photo'=>$rasm_id,
                'caption'=>$caption_output,
                'parse_mode'=>'html',
                'reply_markup'=>json_encode([
                    'inline_keyboard'=> $inline_parts
                ])
            ]);
        } else {
             // Hech qanday qism topilmasa, xato
             bot('sendMessage',[
                'chat_id'=>$cid,
                'text'=>"<b>❌ Uzr, bu kinoga hali hech qanday qism joylanmagan.</b>",
                'parse_mode'=>'html',
            ]);
        }
    } else {
        // A'zo bo'lmasa kanalga a'zolikni tekshirish logikasi (avvalgi kodingizdagi kabi)
        file_put_contents("step/$cid.kino_ids", $kino_id);
    }
    exit();
}

if (mb_stripos($data, "get_part|") !== false) {

    if(joinchat($cid2)==1){ 
        
        $exp = explode("|", $data);
        $kino_id = $exp[1]; // Asosiy kino ID
        $part_num = $exp[2]; // Qism raqami (1, 2, 3...)
        
        $part_dir = "kino/$kino_id/parts/$part_num";
        
        if (is_dir($part_dir)) {
            $nomi=file_get_contents("$part_dir/nomi.txt");
            $video_id=file_get_contents("$part_dir/film.txt"); // Video file_id
            $malumot=file_get_contents("$part_dir/malumot.txt");
            
            // Yuklashlar sonini oshirish
            $downcount=file_get_contents("$part_dir/downcount.txt") ?: 0;
            $downcountt = $downcount + 1;
            file_put_contents("$part_dir/downcount.txt", $downcountt); 

            // 1. Videoni foydalanuvchiga yuborish
            bot('sendVideo',[
                'chat_id'=>$cid2, // Callback querydagi chat ID
                'video'=>$video_id,
                'caption'=>"<b>🍿 kino nomi: $nomi</b>\n\nkino haqida: <blockquote>$malumot</blockquote>\n\nId: <code>$kino_id</code>\n#️⃣ Qism: <code>$part_num</code>\n🗂 Yuklashlar soni: $downcountt\n\n🤖 Bizning bot: @$bot",
                'parse_mode'=>'html',
                'reply_markup'=>json_encode([
                    'inline_keyboard'=>[
                        [['text'=>"📋 Ulashish",'url'=>"https://t.me/share/url?url=https://t.me/$bot?start=$kino_id"]],
                    ]
                ])
            ]);
            
            // 2. Callback bildirishnomasini yuborish (muvaffaqiyatli)
            bot('answerCallbackQuery', [
                'callback_query_id' => $qid,
                'text' => "$part_num-qism yuborildi.",
                'show_alert' => false
            ]);
            
        } else {
            // 3. Agar katalog topilmasa
             bot('answerCallbackQuery', [
                'callback_query_id' => $qid,
                'text' => "❌ Uzr, $part_num-qism topilmadi!",
                'show_alert' => true
            ]);
        }
    } else {
        // A'zo bo'lmasa, ogohlantirish
        bot('answerCallbackQuery', [
            'callback_query_id' => $qid,
            'text' => "⚠️ kinoni olish uchun kanalga a'zo bo'lishingiz kerak!",
            'show_alert' => true
        ]);
    }
    exit();
}


if(mb_stripos($text,"/start")!==false){
    $exp=explode(" ",$text);
    $kino_id=$exp[1]; 

    if(joinchat($cid)==1){
        
        $main_caption = file_get_contents("kino/$kino_id/nomi.txt");
        $rasm_id=file_get_contents("kino/$kino_id/rasm.txt"); 

       
        $parts_dir = "kino/$kino_id/parts/";
        $inline_parts = [];
        $temp_row = [];
        $part_numbers = [];

        if (is_dir($parts_dir)) {
            $parts = array_filter(glob($parts_dir . '*'), 'is_dir');
            foreach ($parts as $part_path) {
                $part_numbers[] = basename($part_path);
            }
            sort($part_numbers, SORT_NUMERIC); 
        }
        
        if (count($part_numbers) > 1) {
            $row_counter = 0;
            foreach ($part_numbers as $part_num) {
                $temp_row[] = ['text' => $part_num, 'callback_data' => "get_part|$kino_id|$part_num"];
                $row_counter++;
                
                if ($row_counter % 5 == 0) { // Har 5 ta qismdan keyin yangi qator
                    $inline_parts[] = $temp_row;
                    $temp_row = [];
                }
            }
            if (!empty($temp_row)) {
                $inline_parts[] = $temp_row;
            }
        } elseif (count($part_numbers) == 1) {
            // Agar faqat 1-qism mavjud bo'lsa, tugma ko'rsatilmaydi (Talabga binoan)
        }

        $part1_malumot = file_get_contents("kino/$kino_id/parts/1/malumot.txt") ?: "Ma'lumot topilmadi.";
        $part1_downcount = file_get_contents("kino/$kino_id/parts/1/downcount.txt") ?: 0;
        
        $caption_output = "<b>🍿 kino nomi: $main_caption</b>\n\n";
        $caption_output .= "kino haqida: <blockquote>$part1_malumot</blockquote>\n\n";
        $caption_output .= "Id: <code>$kino_id</code>\n";
        
        // Agar 1-qismdan ko'p qism bo'lsa, qismlar ro'yxatini ko'rsatish
        if (count($part_numbers) > 1) {
            $caption_output .= "<b>Qismlari:</b>";
        } elseif (count($part_numbers) == 1) {
            // Faqat 1-qism bo'lsa, uni yuborish
            $video_id = file_get_contents("kino/$kino_id/parts/1/film.txt");
            
            file_put_contents("kino/$kino_id/parts/1/downcount.txt", $part1_downcount + 1);

            bot('sendVideo',[
                'chat_id'=>$cid,
                'video'=>$video_id,
                'caption'=>"<b>🍿 kino nomi: $main_caption (1-qism)</b>\n\nkino haqida: <blockquote>$part1_malumot</blockquote>\n\nId: <code>$kino_id</code>\n#️⃣ Qism: <code>1</code>\n🗂 Yuklashlar soni: ".($part1_downcount + 1)."\n\n🤖 Bizning bot: @$bot",
                'parse_mode'=>'html',
                'reply_markup'=>json_encode([
                    'inline_keyboard'=>[
                        [['text'=>"📋 Ulashish",'url'=>"https://t.me/share/url?url=https://t.me/$bot?start=$kino_id"]],
                    ]
                ])
            ]);
            exit(); 
        } else {
             $caption_output .= "<b>❌ Uzr, bu kino qismi topilmadi.</b>";
        }
        
        
        if (!empty($rasm_id) && count($part_numbers) > 1) {
            bot('sendPhoto',[
                'chat_id'=>$cid,
                'photo'=>$rasm_id,
                'caption'=>$caption_output,
                'parse_mode'=>'html',
                'reply_markup'=>json_encode([
                    'inline_keyboard'=> $inline_parts
                ])
            ]);
        }
        
        exit();
    }else{
        file_put_contents("step/$cid.kino_ids",$kino_id);
        
    }
}


if($text == "🗄 Boshqaruv paneli" or $text=="/panel"){
	if(in_array($cid,$admin)){
	bot('SendMessage',[
	'chat_id'=>$cid,
	'text'=>"<b>Admin paneliga xush kelibsiz!</b>",
	'parse_mode'=>'html',
	'reply_markup'=>$panel,
	]);
	unlink("step/$cid.step");
   unlink("step/test.txt");
   unlink("step/$cid.txt");
	exit();
}
}

if($data == "boshqar"){
bot('deleteMessage',[
'chat_id'=>$cid2,
'message_id'=>$mid2,
]);
bot('sendMessage',[
'chat_id'=>$cid2,
'text'=>"<b>🖥️ Boshqaruv panelidasiz!</b>",
'parse_mode'=>'html',
'reply_markup'=>$panel,
]);
}


if($data == "bosh"){
	bot('deleteMessage',[
	'chat_id'=>$cid2,
	'message_id'=>$mid2,
	]);
	bot('SendMessage',[
	'chat_id'=>$cid2,
	'text'=>"<b>Admin paneliga xush kelibsiz!</b>",
	'parse_mode'=>'html',
	'reply_markup'=>$panel,
	]);
	exit();
}
if ($text == "👥 Adminlar") {
    if (in_array($cid, $admin)) {
        if ($cid == $Axmedovs) {
            bot('SendMessage', [
                'chat_id' => $Axmedovs,
                'text' => "🔰 <b>Quyidagilardan birini tanlang:</b>",
                'parse_mode' => 'html',
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [['text' => "➕ Yangi admin qo‘shish", 'callback_data' => "add"]],
                        [['text' => "📑 Ro‘yxat", 'callback_data' => "list"], ['text' => "🗑 O‘chirish", 'callback_data' => "remove"]],
                    ]
                ])
            ]);
            exit();
        } else {
            bot('SendMessage', [
                'chat_id' => $cid,
                'text' => "🔰 <b>Quyidagilardan birini tanlang:</b>",
                'parse_mode' => 'html',
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [['text' => "📑 Ro‘yxat", 'callback_data' => "list"]],
                    ]
                ])
            ]);
            exit();
        }
    }
}

if ($data == "admins") {
    if (in_array($cid2, $admin)) {
        if ($cid2 == $Axmedovs) {
            bot('editMessageText', [
                'chat_id' => $Axmedovs,
                'message_id'=>$mid2,
                'text' => "🔰 <b>Quyidagilardan birini tanlang:</b>",
                'parse_mode' => 'html',
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [['text' => "➕ Yangi admin qo‘shish", 'callback_data' => "add"]],
                        [['text' => "📑 Ro‘yxat", 'callback_data' => "list"], ['text' => "🗑 O‘chirish", 'callback_data' => "remove"]],
                    ]
                ])
            ]);
            exit();
        } else {
            bot('editMessageText', [
                'chat_id' => $cid2,
                'message_id'=>$mid2,
                'text' => "🔰 <b>Quyidagilardan birini tanlang:</b>",
                'parse_mode' => 'html',
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [['text' => "📑 Ro‘yxat", 'callback_data' => "list"]],
                    ]
                ])
            ]);
            exit();
        }
    }
}

if ($data == "list") {
    $admins = file_get_contents("tizim/admins.txt");
    if (empty(trim($admins))) {
        $text = "🚫 <b>Yordamchi adminlar topilmadi!</b>";
    } else {
        $text = "👮‍♂️ <b>Adminlar ro‘yxati:</b>\n" . $admins;
    }
    bot('editMessageText', [
        'chat_id' => $cid2,
        'message_id' => $mid2,
        'text' => $text,
        'parse_mode' => 'html',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [['text' => "🔙 Orqaga", 'callback_data' => "admins"]]
            ]
        ])
    ]);
}

if ($data == "add") {
    if ($cid2 == $Axmedovs) {
        bot('deleteMessage', [
            'chat_id' => $cid2,
            'message_id' => $mid2,
        ]);
        bot('SendMessage', [
            'chat_id' => $Axmedovs,
            'text' => "🔢 <b>Kerakli foydalanuvchi ID raqamini yuboring:</b>",
            'parse_mode' => 'html',
            'reply_markup' => $bosh
        ]);
        file_put_contents("step/$cid2.step", "add-admin");
        exit();
    }
}

if ($step == "add-admin" and $cid == $Axmedovs) {
    $users = file_get_contents("azo.dat"); // Foydalanuvchi borligini tekshirish
    if (!mb_stripos($users, $text)) {
        bot('SendMessage', [
            'chat_id' => $cid,
            'text' => "🚫 <b>Ushbu foydalanuvchi botdan foydalanmaydi!</b>\n\n🔢 Boshqa ID raqamni kiriting:",
            'parse_mode' => 'html',
        ]);
        exit();
    }

    $admins = file_get_contents("tizim/admins.txt");
    if (mb_stripos($admins, $text) !== false) {
        bot('SendMessage', [
            'chat_id' => $cid,
            'text' => "🚫 <b>Ushbu foydalanuvchi allaqachon admin!</b>\n\n🔢 Boshqa ID raqamni kiriting:",
            'parse_mode' => 'html',
        ]);
    }

  $file = "tizim/admins.txt";
$text = trim($text); // Bo'sh joylarni olib tashlash

$old_data = file_get_contents($file); // Eski ma'lumotlarni olish
$new_data = $text . "\n" . $old_data; // Yangi ma'lumotni boshiga qo'shish

file_put_contents($file, $new_data); // Yangi ma'lumotni yozish
    bot('SendMessage', [
        'chat_id' => $Axmedovs,
        'text' => "✅ <code>$text</code> <b>adminlar ro‘yxatiga qo‘shildi!</b>",
        'parse_mode' => 'html',
        'reply_markup' => $panel
    ]);
    unlink("step/$cid.step");
    exit();
}

if ($data == "remove") {
    bot('deleteMessage', [
        'chat_id' => $cid2,
        'message_id' => $mid2,
    ]);
    bot('SendMessage', [
        'chat_id' => $Axmedovs,
        'text' => "🔢 <b>Kerakli foydalanuvchi ID raqamini yuboring:</b>",
        'parse_mode' => 'html',
        'reply_markup' => $bosh
    ]);
    file_put_contents("step/$cid2.step", "remove-admin");
    exit();
}

if ($step == "remove-admin" and $cid == $Axmedovs) {
    $admins = file_get_contents("tizim/admins.txt");
    if (!mb_stripos($admins, $text)) {
        bot('SendMessage', [
            'chat_id' => $cid,
            'text' => "🚫 <b>Ushbu foydalanuvchi adminlar ro‘yxatida mavjud emas!</b>\n\n🔢 Boshqa ID raqamni kiriting:",
            'parse_mode' => 'html',
        ]);
        exit();
    }

    $newAdmins = str_replace($text . "\n", "", $admins);
    file_put_contents("tizim/admins.txt", $newAdmins);
    bot('SendMessage', [
        'chat_id' => $Axmedovs,
        'text' => "✅ <code>$text</code> <b>adminlar ro‘yxatidan olib tashlandi!</b>",
        'parse_mode' => 'html',
        'reply_markup' => $panel
    ]);
    unlink("step/$cid.step");
    exit();
}

if($text == "📢 Kanallar"){
	if(in_array($cid,$admin)){
	bot('SendMessage',[
	'chat_id'=>$cid,
	'text'=>"<b>Majburiy obunalarni sozlash bo'limidasiz:</b>",
'parse_mode'=>'html',
'reply_markup'=>json_encode([
	'inline_keyboard'=>[
	[['text'=>"💎 Majburiy obunalar",'callback_data'=>"majburiy"]],
	[['text'=>"🎥 kino kanal",'callback_data'=>"qoshimcha"],['text'=>"❌ Yopish",'callback_data'=>"bosh"]]
	]
	])
	]);
	exit();
}
}

if($data == "kanallar"){
	bot('editMessageText',[
        'chat_id'=>$cid2,
       'message_id'=>$mid2,
	'text'=>"<b>⬇️ Quyidagilardan birini tanlang:</b>",
	'parse_mode'=>'html',
	'reply_markup'=>json_encode([
	'inline_keyboard'=>[
	[['text'=>"💎 Majburiy obunalar",'callback_data'=>"majburiy"]],
	[['text'=>"🎥 kino kanal",'callback_data'=>"qoshimcha"],['text'=>"❌ Yopish",'callback_data'=>"bosh"]]
	]
	])
	]);
	exit();
}

if($data == "majburiy"){	
     bot('editMessageText',[
        'chat_id'=>$cid2,
       'message_id'=>$mid2,
'text'=>"<b>⁉️ Qaysi turda kanal qo'shmoqchisiz!</b>",
'parse_mode'=>'html',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"👥 Ommaviy",'callback_data'=>"ommav"],['text'=>"🔐 Maxfiy",'callback_data'=>"maxfiy"]],
[['text'=>"📎 Link",'callback_data'=>"oddiyurl"],
['text'=>"◀️ Orqaga",'callback_data'=>"kanallar"]],
]
])
]);
}

if($data == "ommav"){	
     bot('editMessageText',[
        'chat_id'=>$cid2,
       'message_id'=>$mid2,
'text'=>"<b>✅ Ommaviy kanallarni sozlash bo'limidasiz:</b>",
'parse_mode'=>'html',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"➕ Qo'shish",'callback_data'=>"qoshish"]],
[['text'=>"📑 Ro'yxat",'callback_data'=>"royxati"],['text'=>"🗑 O'chirish",'callback_data'=>"ochirish"]],
[['text'=>"◀️ Orqaga",'callback_data'=>"majburiy"]],
]
])
]);
}

if($data == "maxfiy"){	
     bot('editMessageText',[
        'chat_id'=>$cid2,
       'message_id'=>$mid2,
'text'=>"<b>✅ Maxfiy kanallarni sozlash bo'limidasiz:</b>",
'parse_mode'=>'html',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"➕ Qo'shish",'callback_data'=>"qosh"]],
[['text'=>"📑 Ro'yxat",'callback_data'=>"roy"],['text'=>"🗑 O'chirish",'callback_data'=>"ochir"]],
[['text'=>"◀️ Orqaga",'callback_data'=>"majburiy"]],
]
])
]);
}

if($data == "oddiyurl"){	
     bot('editMessageText',[
        'chat_id'=>$cid2,
       'message_id'=>$mid2,
'text'=>"<b>✅ url linklarini sozlash bo'limidasiz:</b>",
'parse_mode'=>'html',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"➕ Qo'shish",'callback_data'=>"c3_add"]],
[['text'=>"📑 Ro'yxat",'callback_data'=>"c3_list"],['text'=>"🗑 O'chirish",'callback_data'=>"c3_del"]],
[['text'=>"◀️ Orqaga",'callback_data'=>"majburiy"]],
]
])
]);
}

if ($data == "qosh") {
    bot('deleteMessage', [
        'chat_id' => $cid2,
        'message_id' => $mid2,
    ]);
    bot('SendMessage', [
        'chat_id' => $cid2,
        'text' => "<i>⚠️ Kanalingiz manzilini yuborishdan avval botni kanalingizga admin qilib olishingiz kerak! Aks holda xatoliklar yuzaga keladi!</i>

📢 <b>Maxfiy kanalni quyidagicha yuboring:</b>

🆔️ id olish: @UserX_RoBot

📄 <b>Namuna:</b> <code>https://t.me/+ZEcQiRY_pRphZTdi
-100326189432</code>",
        'parse_mode' => 'html',
        'reply_markup' => $bosh,
    ]);
    file_put_contents("step/$cid2.step", "add-chanel");
    exit();
}

if ($step == "add-chanel") {
    if (in_array($cid, $admin)) {
        if (!empty(trim($text))) {
            if (mb_stripos($text, "https://t.me/+") !== false) {
                // Kanal ID ni ajratib olish
                preg_match('/-100\d+/', $text, $matches);
                if (!empty($matches[0])) {
                    $kanalid = $matches[0];

                    // Foyl tizimini yaratish
                    if (!file_exists("tizim")) {
                        mkdir("tizim", 0777, true);
                    }

                    // Kanal ID uchun fayl yaratish
                    file_put_contents("tizim/$kanalid.txt", "");

                    // `channel2.txt` faylini tekshirish va kanalni qo'shish
                    $kanallar = trim(file_get_contents("channel2.txt"));

                    if (empty($kanallar)) {
                        file_put_contents("channel2.txt", $text);
                    } else {
                        file_put_contents("channel2.txt", $kanallar . "\n" . $text);
                    }

                    bot('SendMessage', [
                        'chat_id' => $cid,
                        'text' => "<b>✅ $text - kanal muvaffaqiyatli qo'shildi.</b>",
                        'parse_mode' => 'html',
                        'reply_markup' => $panel
                    ]);

                    unlink("step/$cid.step");
                    exit();
                }
            }
        }

        bot('SendMessage', [
            'chat_id' => $cid,
            'text' => "<b>Kanal manzilini to'g'ri yuboring:</b>\n\n
📄 <b>Namuna:</b> <code>https://t.me/+ZEcQiRY_pRphZTdi
-100326189432</code>",
            'parse_mode' => 'html',
        ]);
        exit();
    }
}

if($data == "ochir"){
    bot('deleteMessage',[
        'chat_id'=>$cid2,
        'message_id'=>$mid2,
    ]);
    bot('SendMessage',[
        'chat_id'=>$cid2,
        'text'=>"<b>📝 O‘chirilishi kerak bo‘lgan maxfiy kanalning manzilini va ID sini yuboring:</b>\n\n
📄 <b>Namuna:</b> \n<code>https://t.me/+ZEcQiRY_pRphZTdiHs
-1001234567890</code>",
        'parse_mode'=>'html',
        'reply_markup'=>$bosh,
    ]);
    file_put_contents("step/$cid2.step","remove-secret-channel");
    exit();
}

if($step == "remove-secret-channel"){
    if(in_array($cid,$admin)){
        if(isset($text)){    
            $lines = explode("\n", $text);
            if(count($lines) == 2 && mb_stripos($lines[0], "https://t.me/+") !== false && mb_stripos($lines[1], "-100") !== false){
                $url = trim($lines[0]); // Kanal havolasi
                $chat_id = trim($lines[1]); // Kanal ID

                $kanallar = file("channel2.txt", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                $new_kanallar = [];
                $found = false;

                for($i = 0; $i < count($kanallar); $i+=2){
                    if($kanallar[$i] == $url && isset($kanallar[$i+1]) && $kanallar[$i+1] == $chat_id){
                        $found = true; // O'chirilishi kerak bo'lgan kanal topildi
                    } else {
                        $new_kanallar[] = $kanallar[$i];
                        if(isset($kanallar[$i+1])) $new_kanallar[] = $kanallar[$i+1];
                    }
                }

                if($found){
                    file_put_contents("channel2.txt", implode("\n", $new_kanallar) . "\n");

                    // Kanalga mos .txt faylni o‘chiramiz
                    $fayl_nomi = "tizim/$chat_id.txt";
                    if(file_exists($fayl_nomi)){
                        unlink($fayl_nomi);
                    }

                    bot('SendMessage',[
                        'chat_id'=>$cid,
                        'text'=>"<b>✅ $url nomli maxfiy kanal muvaffaqiyatli o‘chirildi.</b>",
                        'parse_mode'=>'html',
                        'reply_markup'=>$panel
                    ]);
                    unlink("step/$cid.step");
                    exit();
                } else {
                    bot('SendMessage',[
                        'chat_id'=>$cid,
                        'text'=>"<b>❗ $url ro‘yxatdan topilmadi!</b>\n\n<i>🆙 Qayta urinib ko‘ring!</i>",
                        'parse_mode'=>'html',
                    ]);
                    exit();
                }
            } else {
                bot('SendMessage',[
                    'chat_id'=>$cid,
                    'text'=>"<b>Kanal manzilini va ID sini to‘g‘ri yuboring:</b>\n\n
📄 <b>Namuna:</b> \n<code>https://t.me/+ZEcQiRY_pRphZTdiHs
-1001234567890</code>",
                    'parse_mode'=>'html',
                ]);
                exit();
            }
        }
    }
}

if($data == "roy"){
    // Ommaviy kanallarni olish
    $kanallar = file_get_contents("channel.txt");
    $soni = substr_count($kanallar, "@");
    if($kanallar == null){
        $kanallar_text = "<b>Ommaviy kanallar ulanmagan!</b>";
    }else{
        $kanallar_text = "<b>📢 Ommaviy kanallar:</b>\n\n$kanallar\n\n<b>Ulangan ommaviy kanallar soni:</b> $soni ta";
    }

    // Maxfiy kanallarni olish
    $maxfiy_kanallar = file_get_contents("channel2.txt");
    if($maxfiy_kanallar == null){
        $maxfiy_text = "<b>Maxfiy kanallar ulanmagan!</b>";
    }else{
        $maxfiy_text = "<b>🔒 Maxfiy kanallar:</b>\n\n";
        $ex = explode("\n", $maxfiy_kanallar);
        for($i=0; $i<count($ex); $i+=2){
            if(isset($ex[$i]) && isset($ex[$i+1])){
                $maxfiy_text .= "🔹 <code>".$ex[$i]."</code>\n";
            }
        }
        $maxfiy_text .= "\n<b>Ulangan maxfiy kanallar soni:</b> ".(count($ex)/2)." ta";
    }

    // Yakuniy xabarni shakllantirish
    $text = "$kanallar_text\n\n$maxfiy_text";

    bot('editMessageText',[
        'chat_id'=>$cid2,
        'message_id'=>$mid2,
        'text'=>$text,
        'parse_mode'=>'html',
        'reply_markup'=>json_encode([
            'inline_keyboard'=>[
                [['text'=>"🔙 Orqaga",'callback_data'=>"maxfiy"]],
            ]
        ])
    ]);
}

if ($data == "c3_add") {
    file_put_contents("step/$cid2.txt", "c3_add");
    bot('sendMessage', [
        'chat_id' => $cid2,
        'text' => "🔗 <b>channel3.txt ga qo‘shish uchun link yuboring</b>\n\nMisol:\nhttps://t.me/kanal",
        'parse_mode' => 'html'
    ]);
}

if (file_exists("step/$cid.txt") and file_get_contents("step/$cid.txt") == "c3_add" and strpos($text, "https://") !== false) {
    unlink("step/$cid.txt");
    file_put_contents("channel3.txt", trim($text)."\n", FILE_APPEND);
    bot('sendMessage', [
        'chat_id' => $cid,
        'text' => "✅ Link muvaffaqiyatli qo‘shildi"
    ]);
}

if ($data == "c3_list") {
    $links = file_get_contents("channel3.txt");
    if (!$links) {
        $links = "❌ Hozircha linklar yo‘q";
    }
    bot('sendMessage', [
        'chat_id' => $cid2,
        'text' => "📄 <b>channel3.txt dagi linklar:</b>\n\n$links",
        'parse_mode' => 'html'
    ]);
}

if ($data == "c3_del") {
    file_put_contents("step/$cid2.txt", "c3_del");
    bot('sendMessage', [
        'chat_id' => $cid2,
        'text' => "❌ <b>O‘chirmoqchi bo‘lgan linkni yuboring</b>",
        'parse_mode' => 'html'
    ]);
}

if (file_exists("step/$cid.txt") and file_get_contents("step/$cid.txt") == "c3_del") {
    unlink("step/$cid.txt");
    $links = explode("\n", file_get_contents("channel3.txt"));
    $new = "";

    foreach ($links as $l) {
        if (trim($l) != trim($text) and $l != "") {
            $new .= $l."\n";
        }
    }

    file_put_contents("channel3.txt", $new);
    bot('sendMessage', [
        'chat_id' => $cid,
        'text' => "🗑 Link o‘chirildi (agar mavjud bo‘lsa)"
    ]);
}

if($data=="qoshimcha"){
	$kino = file_get_contents("kino_ch.txt");
bot('editMessageText',[
'chat_id'=>$cid2,
'message_id'=>$mid2,
'text'=>"<b>?? Hozirgi kino kanal:</b> $kino",
'parse_mode'=>'html',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"📝 Kino kanalni o'zgartirish",'callback_data'=>"kinokanal"]],
[['text'=>"◀️ Orqaga",'callback_data'=>"kanallar"]],
]
])
]);
exit();
}

if($data == "kinokanal"){
	bot('deleteMessage',[
	'chat_id'=>$cid2,
	'message_id'=>$mid2,
	]);
	bot('SendMessage',[
	'chat_id'=>$cid2,
'text'=>"<blockquote>⚠️ Kanalingiz manzilini yuborishdan avval botni kanalingizga admin qilib olishingiz kerak!</blockquote>

📢 <b>Kerakli kanalni manzilini yuboring:

📄 Namuna:</b> <code>@axmedovs_01</code>",
'parse_mode'=>'html',
'reply_markup'=>$bosh,
]);
file_put_contents("step/$cid2.step","add-channl");
exit();
}

if($step == "add-channl"){
if(in_array($cid,$admin)){
if(isset($text)){		
if(mb_stripos($text, "@")!==false){
$get = bot('getChat',[
'chat_id'=>$text
]);
$types = $get->result->type;
$ch_name = $get->result->title;
$ch_user = $get->result->username;
if(getAdmin($ch_user)== true){
file_put_contents("kino_ch.txt",$text);
	bot('SendMessage',[
	'chat_id'=>$cid,
	'text'=>"<b>✅ $text nomli kanal muvaffaqiyatli qo'shildi.</b>",
	'parse_mode'=>'html',
	'reply_markup'=>$panel
]);
unlink("step/$cid.step");
exit();
}else{
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>⚠️ Bot ushbu kanalda admin emas!</b>

<i>🆙️ Qayta urinib ko'ring:</i>",
'parse_mode'=>'html',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text' => "🛡 Kanalga admin qilish", 'url' => "https://t.me/$bot?startchannel=on"]]
]
])
]);
exit();
}
}else{
bot('SendMessage',[
'chat_id'=>$cid,
'text'=>"<b>⛔ Kanal manzilini to'g'ri yuboring:</b>\n\n
<b>📄 Namuna:</b> <code>@axmedovs_01</code>",
'parse_mode'=>'html',
]);
exit();
}
}
}
}

if($data == "qoshish"){
	bot('deleteMessage',[
	'chat_id'=>$cid2,
	'message_id'=>$mid2,
	]);
	bot('SendMessage',[
	'chat_id'=>$cid2,
'text'=>"<i>⚠️ Kanalingiz manzilini yuborishdan avval botni kanalingizga admin qilib olishingiz kerak!</i>

📢 <b>Kerakli kanalni manzilini yuboring:

📄 Namuna:</b> <code>@axmedovs_01</code>",
'parse_mode'=>'html',
'reply_markup'=>$bosh,
]);
file_put_contents("step/$cid2.step","add-channel");
exit();
}

if($step == "add-channel"){
if(in_array($cid,$admin)){
if(isset($text)){		
if(mb_stripos($text, "@")!==false){
$get = bot('getChat',[
'chat_id'=>$text
]);
$types = $get->result->type;
$ch_name = $get->result->title;
$ch_user = $get->result->username;
if(getAdmin($ch_user)== true){
if($kanallar == null){
file_put_contents("channel.txt",$text);
}else{
file_put_contents("channel.txt","\n".$text,FILE_APPEND);
}
	bot('SendMessage',[
	'chat_id'=>$cid,
	'text'=>"<b>✅ $text nomli kanal muvaffaqiyatli qo'shildi.</b>",
	'parse_mode'=>'html',
	'reply_markup'=>$panel
]);
unlink("step/$cid.step");
exit();
}else{
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"<b>⚠️ Bot ushbu kanalda admin emas!</b>

<i>🆙️ Qayta urinib ko'ring:</i>",
'parse_mode'=>'html',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text' => "🛡 Kanalga admin qilish", 'url' => "https://t.me/$bot?startchannel=on"]]
]
])
]);
exit();
}
}else{
bot('SendMessage',[
'chat_id'=>$cid,
'text'=>"<b>Kanal manzilini to'g'ri yuboring:</b>\n\n
<b>📄 Namuna:</b> <code>@axmedovs_01</code>",
'parse_mode'=>'html',
]);
exit();
}
}
}
}

if($data == "ochirish"){
	bot('deleteMessage',[
	'chat_id'=>$cid2,
	'message_id'=>$mid2,
	]);
	bot('SendMessage',[
	'chat_id'=>$cid2,
'text'=>"<b>📝 O'chirilishi kerak bo'lgan kanalning manzilini yuboring:

📄 Namuna:</b> <code>@axmedovs_01</code>",
'parse_mode'=>'html',
'reply_markup'=>$bosh,
]);
file_put_contents("step/$cid2.step","remove-channel");
exit();
}

if($step == "remove-channel"){
if(in_array($cid,$admin)){
if(isset($text)){	
if(mb_stripos($text, "@") !== false){

if(!file_exists("channel.txt")){
bot('sendMessage',[
'chat_id'=>$cid,
'text'=>"❌ Kanal ro'yxati bo‘sh!",
'parse_mode'=>'html',
]);
exit();
}

$kanal = file_get_contents("channel.txt");

if(mb_stripos($kanal, $text) !== false){

$kanal = str_replace("\n".$text, "", $kanal);
$kanal = str_replace($text."\n", "", $kanal);
$kanal = str_replace($text, "", $kanal);

$kanal = trim($kanal);

if($kanal == ""){
unlink("channel.txt");
}else{
file_put_contents("channel.txt", $kanal);
}

bot('SendMessage',[
'chat_id'=>$cid,
'text'=>"<b>✅ $text nomli kanal muvaffaqiyatli o‘chirildi.</b>",
'parse_mode'=>'html',
'reply_markup'=>$panel
]);

unlink("step/$cid.step");
exit();

}else{
bot('SendMessage',[
'chat_id'=>$cid,
'text'=>"❗ <b>$text ro‘yxatdan topilmadi!</b>",
'parse_mode'=>'html',
]);
exit();
}

}else{
bot('SendMessage',[
'chat_id'=>$cid,
'text'=>"<b>Kanal manzilini to‘g‘ri yuboring!</b>\n\n📄 Namuna: <code>@axmedovs_01</code>",
'parse_mode'=>'html',
]);
exit();
}
}
}
}

if($data == "royxati"){
$soni = substr_count($kanallar,"@");
if($kanallar == null){
$text = "<b>Hech qanday kanallar ulanmagan!</b>";
}else{
$text = "<b>📢 Kanallar ro'yxati:</b>

$kanallar 

<b>Ulangan kanallar soni:</b> $soni ta";
}
     bot('editMessageText',[
        'chat_id'=>$cid2,
       'message_id'=>$mid2,
       'text'=>$text,
'parse_mode'=>'html',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"🔙 Orqaga",'callback_data'=>"ommav"]],
]
])
]);
}

$logFile = "log.txt"; // Log fayli nomi
$time = date("Y-m-d H:i:s"); // Joriy vaqt

$logMessage = "[$time]"; // Log formati

if (isset($text) && !empty($text)) { 
    $logMessage .= " TEXT: " . $text; 
}

if (isset($data) && !empty($data)) { 
    $logMessage .= " DATA: " . $data; 
}

if ($logMessage !== "[$time]") { // Faqat ma'lumot bo‘lsa yozadi
    file_put_contents($logFile, $logMessage . "\n", FILE_APPEND);
}

if ($text == "📊 Statistika") {
	
	$statt = file_get_contents("azo.dat");  
	$stat = substr_count($statt, "\n");
    $keyboard = [
        "inline_keyboard" => [
            [["text" => "📅 Kunlik", "callback_data" => "kunlik"],
            ["text" => "📆 Haftalik", "callback_data" => "haftalik"],
            ["text" => "📊 Oylik", "callback_data" => "oylik"]]
        ]
    ];

    bot("sendMessage", [
        "chat_id" => $cid,
        "text" => "<b>📊 Qaysi statistikani ko'rmoqchisiz?

✅ Jami Foydalanuvchilar: $stat ta</b>",
        'parse_mode'=>'html',
        "reply_markup" => json_encode($keyboard)
    ]);
}

if ($data == "stat") {
	$statt = file_get_contents("azo.dat");  
	$stat = substr_count($statt, "\n");
     $keyboard = [
        "inline_keyboard" => [
            [["text" => "📅 Kunlik", "callback_data" => "kunlik"],
            ["text" => "📆 Haftalik", "callback_data" => "haftalik"],
            ["text" => "📊 Oylik", "callback_data" => "oylik"]]
        ]
    ];

    bot("editMessageText", [
        "chat_id" => $cid2,
        'message_id'=>$mid2,
        "text" => "<b>📊 Qaysi statistikani ko'rmoqchisiz?

✅ Jami Foydalanuvchilar: $stat ta</b>",
        'parse_mode'=>'html',
        "reply_markup" => json_encode($keyboard)
    ]);
}

if ($data == "kunlik") {
    $users_dir = "users";
    $bugun = date("d.m.Y");
    $kecha = date("d.m.Y", strtotime("-1 day"));
    $oldin_2 = date("d.m.Y", strtotime("-2 days"));
    $oldin_3 = date("d.m.Y", strtotime("-3 days"));
    $oldin_4 = date("d.m.Y", strtotime("-4 days"));
    $oldin_5 = date("d.m.Y", strtotime("-5 days"));

    $kunlik = ["bugun" => 0, "kecha" => 0, "2kun" => 0, "3kun" => 0, "4kun" => 0, "5kun" => 0];

    if (is_dir($users_dir)) {
        $files = scandir($users_dir);
        foreach ($files as $file) {
            if ($file == "." || $file == "..") continue;
            $sana = file_get_contents("$users_dir/$file");

            if ($sana == $bugun) $kunlik["bugun"]++;
            elseif ($sana == $kecha) $kunlik["kecha"]++;
            elseif ($sana == $oldin_2) $kunlik["2kun"]++;
            elseif ($sana == $oldin_3) $kunlik["3kun"]++;
            elseif ($sana == $oldin_4) $kunlik["4kun"]++;
            elseif ($sana == $oldin_5) $kunlik["5kun"]++;
        }
    }

    $stat_msg = "
<b>📅 Kunlik statistika:</b>
<blockquote>🔹 Bugun: {$kunlik["bugun"]} ta  
🔹 Kecha: {$kunlik["kecha"]} ta  
🔹 2 kun oldin: {$kunlik["2kun"]} ta  
🔹 3 kun oldin: {$kunlik["3kun"]} ta  
🔹 4 kun oldin: {$kunlik["4kun"]} ta  
🔹 5 kun oldin: {$kunlik["5kun"]} ta</blockquote>";

    bot("editMessageText", [
        "chat_id" => $cid2,
        "message_id" => $mid2,
        "text" => $stat_msg,
        "parse_mode" => "html",
        'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => "⬅️ Ortga qaytish", 'callback_data' => "stat"]],
                ]
            ])
    ]);
}

if ($data == "haftalik") {
    $users_dir = "users";
    $joriy_hafta = date("W");
    $oldin_hafta = date("W", strtotime("-7 days"));
    $oldin_2hafta = date("W", strtotime("-14 days"));

    $haftalik = ["shu_hafta" => 0, "oldin_hafta" => 0, "oldin_2hafta" => 0];

    if (is_dir($users_dir)) {
        $files = scandir($users_dir);
        foreach ($files as $file) {
            if ($file == "." || $file == "..") continue;
            $sana = file_get_contents("$users_dir/$file");

            $file_hafta = date("W", strtotime(str_replace(".", "-", $sana)));
            if ($file_hafta == $joriy_hafta) $haftalik["shu_hafta"]++;
            elseif ($file_hafta == $oldin_hafta) $haftalik["oldin_hafta"]++;
            elseif ($file_hafta == $oldin_2hafta) $haftalik["oldin_2hafta"]++;
        }
    }

    $stat_msg = "
<b>📆 Haftalik statistika:</b>
<blockquote>🔹 Shu hafta: {$haftalik["shu_hafta"]} ta  
🔹 O‘tgan hafta: {$haftalik["oldin_hafta"]} ta  
🔹 2 hafta oldin: {$haftalik["oldin_2hafta"]} ta</blockquote>";

    bot("editMessageText", [
        "chat_id" => $cid2,
        "message_id" => $mid2,
        "text" => $stat_msg,
        "parse_mode" => "html",
        'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => "⬅️ Ortga qaytish", 'callback_data' => "stat"]],
                ]
            ])
    ]);
}

if ($data == "oylik") {
    $users_dir = "users";
    $joriy_oy = date("m.Y");
    $oldin_oy = date("m.Y", strtotime("-1 month"));
    $oldin_2oy = date("m.Y", strtotime("-2 months"));

    $oylik = ["shu_oy" => 0, "oldin_oy" => 0, "oldin_2oy" => 0];

    if (is_dir($users_dir)) {
        $files = scandir($users_dir);
        foreach ($files as $file) {
            if ($file == "." || $file == "..") continue;
            $sana = file_get_contents("$users_dir/$file");

            $file_oy = date("m.Y", strtotime(str_replace(".", "-", $sana)));
            if ($file_oy == $joriy_oy) $oylik["shu_oy"]++;
            elseif ($file_oy == $oldin_oy) $oylik["oldin_oy"]++;
            elseif ($file_oy == $oldin_2oy) $oylik["oldin_2oy"]++;
        }
    }

    $stat_msg = "
<b>📊 Oylik statistika:</b>
<blockquote>🔹 Shu oy: {$oylik["shu_oy"]} ta  
🔹 O‘tgan oy: {$oylik["oldin_oy"]} ta  
🔹 2 oy oldin: {$oylik["oldin_2oy"]} ta</blockquote>";

    bot("editMessageText", [
        "chat_id" => $cid2,
        "message_id" => $mid2,
        "text" => $stat_msg,
        "parse_mode" => "html",
        'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => "⬅️ Ortga qaytish", 'callback_data' => "stat"]],
                ]
            ])
    ]);
}

if ($text == "✉ Xabarnoma") {
    if (in_array($cid, $admin)) {
        bot('SendMessage', [
            'chat_id' => $cid,
            'text' => "<b>❗ Yuboriladigan xabar turini tanlang:</b>",
            'parse_mode' => 'html',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => "💠 Oddiy xabar", 'callback_data' => "send"],['text'=>"💠 Userga xabar",'callback_data'=>"user"]],
[['text'=>"❌ Yopish",'callback_data'=>"bosh"],['text' => "💠 Forward xabar", 'callback_data' => "send2"]],
                ]
            ])
        ]);
    }
}

if ($data == "user") {
    bot('deleteMessage', [
        'chat_id' => $cid2,
        'message_id' => $mid2,
    ]);
    bot('sendMessage', [
        'chat_id' => $cid2,
        'text' => "<b>📝 Foydalanuvchi ID raqamini kiriting:</b>",
        'parse_mode' => 'html',
        'reply_markup' => $bosh,
    ]);
    file_put_contents("step/$cid2.step", 'user');
    exit();
}

// Agar user ID kiritayotgan bo‘lsa
if ($step == "user") {
    if (in_array($cid, $admin)) {
        if (is_numeric($text)) {
            file_put_contents("step/cid.txt", $text);
            bot('SendMessage', [
                'chat_id' => $cid,
                'text' => "<b>📝 Yubormoqchi bo'lgan xabaringizni kiriting:</b>",
                'parse_mode' => 'html',
                'reply_markup' => $bosh,
            ]);
            file_put_contents("step/$cid.step", 'xabar');
            exit();
        } else {
            bot('SendMessage', [
                'chat_id' => $cid,
                'text' => "<b>Faqat raqamlardan foydalaning!</b>",
                'parse_mode' => 'html',
            ]);
            exit();
        }
    }
}

// Agar admin xabar yuborayotgan bo‘lsa
if ($step == "xabar") {
    if (in_array($cid, $admin)) {
        $user_id = file_get_contents("step/cid.txt"); // Oldin kiritilgan foydalanuvchi ID
        bot('SendMessage', [
            'chat_id' => $user_id,
            'text' => "<b>📩 Sizga yangi xabar keldi:</b>\n\n" . $text,
            'parse_mode' => 'html',
        ]);

        bot('SendMessage', [
            'chat_id' => $cid,
            'text' => "<b>✅ Xabaringiz foydalanuvchiga yetkazildi!</b>",
            'parse_mode' => 'html',
            'reply_markup' => $panel, // Boshqaruv panelni ochish
        ]);

        unlink("step/$cid.step"); // Stepni tozalash
        unlink("step/$cid.step"); // ID-ni o‘chirish
        exit();
    }
}

if ($data == "send") {
    $users = file('azo.dat', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $user_count = count($users);

    bot('deleteMessage', [
        'chat_id' => $cid2,
        'message_id' => $mid2,
    ]);
    
    bot('sendMessage', [
        'chat_id' => $cid2,
        'text' => "<b><u>📝 $user_count ta foydalanuvchiga yuboriladigan xabarni botga yuboring.</u>

⚠️<i>Oddiy ko'rinishda yuboring!</i></b>",
        'parse_mode' => 'html',
        'reply_markup' => $bosh,
    ]);
    
    file_put_contents("step/$cid2.step", "sendpost");
    exit();
}

if ($step == "sendpost") {
    if (in_array($cid, $admin)) {
        unlink("step/$cid.step");

        bot('sendMessage', [
            'chat_id' => $cid,
            'text' => "🔄 <b>Xabar yuborish boshlandi!</b>",
            'parse_mode' => 'html',
        ]);

        $x = 0; // Yuborilgan xabarlar soni
        $y = 0; // Yuborilmagan xabarlar soni
        $users = file('azo.dat', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $user_count = count($users);

        foreach ($users as $id) {
            $ok = bot('copyMessage', [
                'from_chat_id' => $cid,
                'chat_id' => $id,
                'message_id' => $mid,
            ])->ok;

            if ($ok) $x++; else $y++;
        }

        bot('sendMessage', [
            'chat_id' => $cid,
            'text' => "<b>✅ Xabar yuborildi!</b>

📨 Jami foydalanuvchilar: <b>$user_count</b>  
✅ Yuborildi: <b>$x</b>  
❌ Yuborilmadi: <b>$y</b>",
            'parse_mode' => 'html',
            'reply_markup' => $panel,
        ]);
    }
    exit();
}

if ($data == "send2") {
    $users = file('azo.dat', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $user_count = count($users);

    bot('deleteMessage', [
        'chat_id' => $cid2,
        'message_id' => $mid2,
    ]);

    bot('sendMessage', [
        'chat_id' => $cid2,
        'text' => "<b><u>📝 $user_count ta foydalanuvchiga yuboriladigan xabarni botga yuboring.</u>

⚠️<i>Forward ko'rinishda yuboring!</i></b>",
        'parse_mode' => 'html',
        'reply_markup' => $bosh,
    ]);
    
    file_put_contents("step/$cid2.step", "sendfwrd");
    exit();
}

if ($step == "sendfwrd") {
    if (in_array($cid, $admin)) {
        unlink("step/$cid.step");

        bot('sendMessage', [
            'chat_id' => $cid,
            'text' => "🔄 <b>Xabar yuborish boshlandi!</b>",
            'parse_mode' => 'html',
        ]);

        $x = 0; // Yuborilgan xabarlar soni
        $y = 0; // Yuborilmagan xabarlar soni
        $users = file('azo.dat', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $user_count = count($users);

        foreach ($users as $id) {
            $ok = bot('ForwardMessage', [
                'from_chat_id' => $cid,
                'chat_id' => $id,
                'message_id' => $mid,
            ])->ok;

            if ($ok) $x++; else $y++;
        }

        bot('sendMessage', [
            'chat_id' => $cid,
            'text' => "<b>✅ Xabar yuborildi!</b>

📨 Jami foydalanuvchilar: <b>$user_count</b>  
✅ Yuborildi: <b>$x</b>  
❌ Yuborilmadi: <b>$y</b>",
            'parse_mode' => 'html',
            'reply_markup' => $panel,
        ]);
    }
    exit();
}

if($text == "🤖 Bot holati"){
	if(in_array($cid,$admin)){
	if($holat == "✅ Yoqilgan"){
		$xolat = "❌ O'chirish";
	}
	if($holat == "❌ O'chirilgan"){
		$xolat = "✅ Yoqish";
	}
	bot('sendMessage',[
	'chat_id'=>$cid,
	'message_id'=>$mid,
	'text'=>"<b>📄 Hozirgi holat:</b> $holat",
	'parse_mode'=>'html',
	'reply_markup'=>json_encode([
	'inline_keyboard'=>[
[['text'=>"$xolat",'callback_data'=>"bot"]],
]
])
]);
exit();
}
}

if($data == "xolat"){
	if($holat == "✅ Yoqilgan"){
		$xolat = "❌ O'chirish";
	}
	if($holat == "❌ O'chirilgan"){
		$xolat = "✅ Yoqish";
	}
	bot('editMessageText',[
	'chat_id'=>$cid2,
	'message_id'=>$mid2,
	'text'=>"<b>📄 Hozirgi holat:</b> $holat",
	'parse_mode'=>'html',
	'reply_markup'=>json_encode([
	'inline_keyboard'=>[
[['text'=>"$xolat",'callback_data'=>"bot"]],
]
])
]);
exit();
}

if($data == "bot"){
if($holat == "Yoqilgan"){
file_put_contents("holat.txt","O'chirilgan");
     bot('editMessageText',[
        'chat_id'=>$cid2,
       'message_id'=>$mid2,
       'text'=>"<b>✅ Bot holati muvaffaqiyatli o'zgartirildi!</b>",
'parse_mode'=>'html',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"◀️ Orqaga",'callback_data'=>"xolat"]],
]
])
]);
}else{
file_put_contents("holat.txt","Yoqilgan");
     bot('editMessageText',[
        'chat_id'=>$cid2,
       'message_id'=>$mid2,
       'text'=>"<b>✅ Bot holati muvaffaqiyatli o'zgartirildi!</b>",
'parse_mode'=>'html',
'reply_markup'=>json_encode([
'inline_keyboard'=>[
[['text'=>"◀️ Orqaga",'callback_data'=>"xolat"]],
]
])
]);
}
}


if ($text == "📥 kino Yuklash") {
    bot('SendMessage', [
        'chat_id' => $cid,
        'text' => "<b>⁉️ Qaysi usulda kino yuklaysiz?</b>",
        'parse_mode' => 'html',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [['text' => "✅ kino joylash" ,'callback_data' => "oddiyk"]
                , ['text' => "➕ Qism qo'shish",'callback_data'=>"sseriya"]],
                [['text'=>"🗑 kinoni ochirish",'callback_data'=>"deletekino"]]
            ]
        ])
    ]);
    exit();
}


if ($data == "sseriya" and in_array($cid, $admin)) {
    bot('SendMessage', [
        'chat_id' => $cid2,
        'text' => "<b>➕ Qaysi kinoga nechanchi qismni qo'shmoqchisiz?</b>\n\n<i>Kino ID sini (masalan: <code>2</code>) kiriting.</i>",
        'parse_mode' => 'html',
        'reply_markup' => $bosh,
    ]);
    file_put_contents("step/$cid2.step", "part_id_sorash");
    exit();
}

// 2. Kino ID si kiritilgandan so'ng, qism raqamini so'rash
if ($step == "part_id_sorash" and is_numeric($text)) {
    if (in_array($cid, $admin)) {
        $kino_id = $text;
        
        // Bu ID li kino mavjudligini tekshirish (ixtiyoriy, ammo tavsiya etiladi)
        if (!is_dir("kino/$kino_id")) {
             bot('SendMessage', [
                'chat_id' => $cid,
                'text' => "<b>⚠️ Uzr, $kino_id ID raqamli asosiy kino topilmadi.</b>\n\n<i>Boshqa ID kiriting yoki bekor qiling.</i>",
                'parse_mode' => 'html',
                'reply_markup' => $bosh,
            ]);
            exit();
        }

        // Keyingi bosqich uchun ID ni saqlab qolamiz
        file_put_contents("step/$cid.part_kino_id", $kino_id);
        file_put_contents("step/$cid.step", "part_number_sorash");
        
        bot('SendMessage', [
            'chat_id' => $cid,
            'text' => "<b>🎬 Siz $kino_id ID lik kinoga nechinchi qismni qo'shmoqchisiz.</b>",
            'parse_mode' => 'html',
            'reply_markup' => $bosh,
        ]);
        exit();
    }
}

// 3. Qism raqami kiritilgandan so'ng, film nomini so'rash
if ($step == "part_number_sorash" and is_numeric($text)) {
    if (in_array($cid, $admin)) {
        $qism_raqami = $text;
        $kino_id = file_get_contents("step/$cid.part_kino_id");
        
        // Qism uchun katalog yaratish
        $part_dir = "kino/$kino_id/parts/$qism_raqami";
        mkdir($part_dir, 0777, true);

        // Qism raqamini saqlash
        file_put_contents("step/$cid.part_number", $qism_raqami);
        file_put_contents("step/$cid.step", "part_name_sorash");
        
        bot('SendMessage', [
            'chat_id' => $cid,
            'text' => "<b>📄 $kino_id ID lik kinoning $qism_raqami-qismini yuklaymiz.</b>\n\n<i>Endi qismning nomini kiriting. (Masalan: <code>Naruto. 2-qism</code>)</i>",
            'parse_mode' => 'html',
            'reply_markup' => $bosh,
        ]);
        exit();
    }
}

// 4. Film nomi kiritilgandan so'ng, rasm so'rash
if ($step == "part_name_sorash") {
    if (in_array($cid, $admin)) {
        $qism_nomi = $text;
        file_put_contents("step/$cid.part_name", $qism_nomi);
        file_put_contents("step/$cid.step", "part_rasm_sorash");
        
        bot('SendMessage', [
            'chat_id' => $cid,
            'text' => "<b>🖼️ Nom saqlandi.</b>\n\n<i>Endi qism uchun rasm yuboring.</i>",
            'parse_mode' => 'html',
            'reply_markup' => $bosh,
        ]);
        exit();
    }
}


// 5. Rasm qabul qilingandan so'ng, video (kino) so'rash
if ($step == "part_rasm_sorash" and isset($message->photo)) {
    if (in_array($cid, $admin)) {
        $photo_id = $message->photo[count($message->photo) - 1]->file_id;
        file_put_contents("step/$cid.part_rasm", $photo_id);
        file_put_contents("step/$cid.step", "part_video_sorash");
        
        bot('SendMessage', [
            'chat_id' => $cid,
            'text' => "<b>🎬 Rasm saqlandi.</b>\n\n<i>Endi esa kinoni yuboring.</i>",
            'parse_mode' => 'html',
            'reply_markup' => $bosh,
        ]);
        exit();
    }
}

// 6. Video (kino) kiritilgandan so'ng, ma'lumot (caption) so'rash
if ($step == "part_video_sorash" and isset($message->video)) {
    if (in_array($cid, $admin)) {
        $file_id = $message->video->file_id;
        file_put_contents("step/$cid.part_video", $file_id);
        file_put_contents("step/$cid.step", "part_malumot_sorash");
        
        bot('SendMessage', [
            'chat_id' => $cid,
            'text' => "<b>📝 kino saqlandi.</b>\n\n<i>Endi kino haqidagi ma'lumotni to'liq kiriting.</i>",
            'parse_mode' => 'html',
            'reply_markup' => $bosh,
        ]);
        exit();
    }
}

// 7. Ma'lumot (caption) kiritilgandan so'ng, yakuniy saqlash va kanalga yuborish
if ($step == "part_malumot_sorash") {
    if (in_array($cid, $admin)) {
        
        // Barcha ma'lumotlarni o'qish
        $kino_id = file_get_contents("step/$cid.part_kino_id");
        $qism_raqami = file_get_contents("step/$cid.part_number");
        $qism_nomi = file_get_contents("step/$cid.part_name");
        $part_rasm = file_get_contents("step/$cid.part_rasm");
        $part_video = file_get_contents("step/$cid.part_video");
        $part_malumot = $text; // Yakuniy kiritilgan ma'lumot
        
        // Fayllarga saqlash
        $part_dir = "kino/$kino_id/parts/$qism_raqami";
        file_put_contents("$part_dir/nomi.txt", $qism_nomi);
        file_put_contents("$part_dir/film.txt", $part_video);
        file_put_contents("$part_dir/rasm.txt", $part_rasm);
        file_put_contents("$part_dir/malumot.txt", $part_malumot);
        file_put_contents("$part_dir/downcount.txt", 0); // Yuklash hisoblagichini boshlash

        // --------- Kanalga Xabar Yuborish ---------
        $caption_to_channel = "<b>🍿 Botga yangi kino joylandi!</b>

🎬 kino nomi: <b>$qism_nomi</b>

📄 kino haqida: 
<blockquote>$part_malumot</blockquote>

🔢 kino ID: <code>$kino_id</code>
#️⃣Qism raqami: <code>$qism_raqami</code>

‼️ Bot manzili: @$bot

<i>❗ Diqqat quyidagi tugmani bosish orqali kinoni tomosha qiling.</i>";
        
        $msg = bot('sendPhoto', [
            'chat_id' => $kanalchaaaaaa,
            'photo' => $part_rasm,
            'caption' => $caption_to_channel,
            'parse_mode' => 'html',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    // Start buyrug'iga kino ID sini yuboramiz
                    [['text' => "✨️ kinoni tomosha qilish", 'url' => "https://t.me/$bot?start=$kino_id"]], 
                ]
            ])
        ])->result->message_id;

        // Adminni xabardor qilish
        bot('sendMessage', [
            'chat_id' => $cid,
            'text' => "<blockquote>✅ kino bazaga muvaffaqiyatli joylandi!</blockquote> 

🔄 kino ID: <code>$kino_id</code>
#️⃣ Qism raqami: <code>$qism_raqami</code>",
            'parse_mode' => 'html',
            'reply_to_message_id' => $mid,
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [['text' => "📢 kinoni Ko'rish", 'url' => "https://t.me/" . str_replace("@", "", $kanalcha) . "/$msg"]]
                ]
            ])
        ]);
        
        // Qadamlarni va vaqtinchalik ma'lumotlarni tozalash
        unlink("step/$cid.step");
        unlink("step/$cid.part_kino_id");
        unlink("step/$cid.part_number");
        unlink("step/$cid.part_name");
        unlink("step/$cid.part_rasm");
        unlink("step/$cid.part_video");

        bot('sendMessage', [
            'chat_id' => $cid,
            'text' => "<b>✅ Admin paneliga qaytdingiz!</b>",
            'parse_mode' => 'html',
            'reply_markup' => $panel,
        ]);
        exit();
    }
}

if ($data == "oddiyk" and in_array($cid2, $admin)) {
	if(!empty($kanalcha)){
        
        bot('deleteMessage', [
            'chat_id' => $cid2,
            'message_id' => $mid2,
        ]);
        
        bot('SendMessage', [
            'chat_id' => $cid2,
            'text' => "<b>🔢 kino uchun ID raqamini kiriting:</b>",
            'parse_mode' => 'html',
            'reply_markup' => $bosh,
        ]);
        file_put_contents("step/$cid2.step", "kino_id_kiriting");
    }else{
        bot('SendMessage',[
            'chat_id'=>$cid2,
            'text'=>"<b>⚠️ kino yuboriladigan kanal qo'shilmagan!</b>",
            'parse_mode'=>'html',
        ]);
    }
    exit();
}

// 1. kino ID ni qabul qilish va tekshirish
if ($step == "kino_id_kiriting" and is_numeric($text)) {
    if (in_array($cid, $admin)) {
        $kino_id = $text;

        // Bandlikni tekshirish
        if (is_dir("kino/$kino_id")) {
            bot('SendMessage', [
                'chat_id' => $cid,
                'text' => "<b>❌ Uzr, $kino_id ID raqami band!</b>\n\n<i>Boshqa ID raqamini kiriting.</i>",
                'parse_mode' => 'html',
                'reply_markup' => $bosh,
            ]);
            // Qadamni o'zgartirmaymiz, foydalanuvchi qayta kiritadi
            exit();
        }

        // Agar ID band bo'lmasa:
        
        // 2. Yangi ID ni saqlash va 1-qism raqamini o'rnatish
        file_put_contents("step/$cid.part_kino_id", $kino_id); // Asosiy ID ni saqlash
        file_put_contents("step/$cid.part_number", 1); // 1-qism raqamini o'rnatish
        file_put_contents("step/$cid.step", "kino_name_sorash"); // Nom so'rash bosqichiga o'tish

        bot('SendMessage', [
            'chat_id' => $cid,
            'text' => "<b>✅ $kino_id ID raqami muvaffaqiyatli saqlandi!</b>\n\n<i>Bu avtomatik ravishda 1-qism deb hisoblanadi.</i>\n\n<b>1️⃣. Endi kinonning nomini kiriting:</b>",
            'parse_mode' => 'html',
            'reply_markup' => $bosh,
        ]);
        exit();
    }
}

if ($step == "kino_name_sorash" and in_array($cid, $admin)) {
    $qism_nomi = $text;
    
    // Qism nomini va asosiy katalog ma'lumotlarini saqlash
    $kino_id = file_get_contents("step/$cid.part_kino_id");
    
    // Asosiy katalog yaratish (yangi kino uchun)
    mkdir("kino/$kino_id"); 
    file_put_contents("kino/$kino_id/nomi.txt", $qism_nomi); // Asosiy nom sifatida saqlash
    
    file_put_contents("step/$cid.part_name", $qism_nomi);
    file_put_contents("step/$cid.step", "kino_rasm_sorash");
    
    bot('SendMessage', [
        'chat_id' => $cid,
        'text' => "<b>🖼️ Nom muvaffaqiyatli saqlandi.</b>\n\n<i>Endi kino posterini (rasm) yuboring.</i>",
        'parse_mode' => 'html',
        'reply_markup' => $bosh,
    ]);
    exit();
}

if ($step == "kino_rasm_sorash" and isset($message->photo)) {
    if (in_array($cid, $admin)) {
        $photo_id = $message->photo[count($message->photo) - 1]->file_id;
        $kino_id = file_get_contents("step/$cid.part_kino_id");
        
        // Asosiy kino katalogiga rasm ID ni saqlash
        file_put_contents("kino/$kino_id/rasm.txt", $photo_id); 
        
        // Vaqtinchalik faylga ham saqlash (qism yuklash uchun)
        file_put_contents("step/$cid.part_rasm", $photo_id);
        file_put_contents("step/$cid.step", "kino_video_sorash");
        
        bot('SendMessage', [
            'chat_id' => $cid,
            'text' => "<b>🎬 Rasm muvaffaqiyatli saqlandi.</b>\n\n<i>Endi 1-qismning mp4 ni yuboring.</i>",
            'parse_mode' => 'html',
            'reply_markup' => $bosh,
        ]);
        exit();
    }
}

if ($step == "kino_video_sorash" and isset($message->video)) {
    if (in_array($cid, $admin)) {
        $file_id = $message->video->file_id;
        file_put_contents("step/$cid.part_video", $file_id);
        file_put_contents("step/$cid.step", "kino_malumot_sorash");
        
        bot('SendMessage', [
            'chat_id' => $cid,
            'text' => "<b>📝 Video saqlandi.</b>\n\n<i>Endi kino (1-qism) haqidagi ma'lumotni (caption) to'liq kiriting.</i>",
            'parse_mode' => 'html',
            'reply_markup' => $bosh,
        ]);
        exit();
    }
}

if ($step == "kino_malumot_sorash" and in_array($cid, $admin)) {
    
    // Ma'lumotlarni o'qish
    $kino_id = file_get_contents("step/$cid.part_kino_id");
    $qism_raqami = file_get_contents("step/$cid.part_number"); // 1
    $qism_nomi = file_get_contents("step/$cid.part_name");
    $part_rasm = file_get_contents("step/$cid.part_rasm");
    $part_video = file_get_contents("step/$cid.part_video");
    $part_malumot = $text; // Yakuniy kiritilgan ma'lumot
    
    // Qism (Part) katalogini yaratish va fayllarni saqlash
    $part_dir = "kino/$kino_id/parts/$qism_raqami";
    mkdir($part_dir, 0777, true);
    
    file_put_contents("$part_dir/nomi.txt", $qism_nomi);
    file_put_contents("$part_dir/film.txt", $part_video);
    file_put_contents("$part_dir/rasm.txt", $part_rasm);
    file_put_contents("$part_dir/malumot.txt", $part_malumot);
    file_put_contents("$part_dir/downcount.txt", 0); 
    
    // --------- Kanalga Xabar Yuborish ---------
    $caption_to_channel = "<b>🍿 Botga yangi kino joylandi!</b>

🎬 kino nomi: <b>$qism_nomi (1-qism)</b>

📄 kino haqida: 
<blockquote>$part_malumot</blockquote>

🔢 kino ID: <code>$kino_id</code>

‼️ Bot manzili: @$bot

<i>❗ Diqqat quyidagi tugmani bosish orqali kinoni tomosha qiling.</i>";
    
    $msg = bot('sendPhoto', [
        'chat_id' => $kanalcha,
        'photo' => $part_rasm,
        'caption' => $caption_to_channel,
        'parse_mode' => 'html',
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                // Start buyrug'iga kino ID sini yuboramiz
                [['text' => "✨️ kinoni tomosha qilish", 'url' => "https://t.me/$bot?start=$kino_id"]], 
            ]
        ])
    ])->result->message_id;

    // Adminni xabardor qilish
    bot('sendMessage', [
        'chat_id' => $cid,
        'text' => "<blockquote>✅ kino (1-qism) bazaga muvaffaqiyatli joylandi!</blockquote> 

🔄 kino ID: <code>$kino_id</code>",
        'parse_mode' => 'html',
        'reply_to_message_id' => $mid,
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [['text' => "📢 kinoni Ko'rish", 'url' => "https://t.me/" . str_replace("@", "", $kanalcha) . "/$msg"]]
            ]
        ])
    ]);
    
    // Qadamlarni va vaqtinchalik ma'lumotlarni tozalash
    unlink("step/$cid.step");
    unlink("step/$cid.part_kino_id");
    unlink("step/$cid.part_number");
    unlink("step/$cid.part_name");
    unlink("step/$cid.part_rasm");
    unlink("step/$cid.part_video");

    bot('sendMessage', [
        'chat_id' => $cid,
        'text' => "<b>✅ Admin paneliga qaytdingiz!</b>",
        'parse_mode' => 'html',
        'reply_markup' => $panel,
    ]);
    exit();
}

// Buyruqni boshlash
if ($data == "deletekino" and in_array($cid, $admin)) {
    bot('SendMessage', [
        'chat_id' => $cid2,
        'text' => "<b>🗑 O'chirmoqchi bo'lgan kinoning ID sini kiriting:</b>\n\n<i>(Masalan: <code>123</code>)</i>",
        'parse_mode' => 'html',
        'reply_markup' => $bosh, // Bosh menyu tugmasi
    ]);
    file_put_contents("step/$cid2.step", "delete_kino_id_sorash");
    exit();
}

// ID ni qabul qilish va kinoni o'chirish
if ($step == "delete_kino_id_sorash" and is_numeric($text)) {
    if (in_array($cid, $admin)) {
        $kino_id = $text;
        $kino_dir = "kino/$kino_id";

        // Yordamchi funksiya: Katalog ichidagi barcha fayllar va pastki kataloglarni o'chirish
        function deleteDirectory($dir) {
            if (!is_dir($dir)) {
                return false;
            }
            $items = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($items as $item) {
                if ($item->isDir()) {
                    rmdir($item->getRealPath());
                } else {
                    unlink($item->getRealPath());
                }
            }
            rmdir($dir);
            return true;
        }

        if (is_dir($kino_dir)) {
            // kino katalogini o'chirish
            if (deleteDirectory($kino_dir)) {
                $response_text = "<b>✅ <code>$kino_id</code> ID raqamli kino muvaffaqiyatli o'chirildi!</b>";
            } else {
                $response_text = "<b>❌ Katalog o'chirishda xatolik yuz berdi.</b>";
            }
        } else {
            $response_text = "<b>⚠️ Uzr, <code>$kino_id</code> ID raqamli kino topilmadi.</b>";
        }
        
        // Yakuniy xabar yuborish
        bot('sendMessage', [
            'chat_id' => $cid,
            'text' => $response_text,
            'parse_mode' => 'html',
        ]);
        
        // Qadamni tozalash va Admin paneliga qaytarish
        unlink("step/$cid.step");
        bot('sendMessage', [
            'chat_id' => $cid,
            'text' => "<b>✅ Admin paneliga qaytdingiz!</b>",
            'parse_mode' => 'html',
            'reply_markup' => $panel,
        ]);
        exit();
    }
}

/*
@AniFineBot kodi!

Manba: @DavlatyorUz & @axmedovs01 (Chopmanglar ancha mehnat ketgan)
Tarqatildi: @TexnoPHPuz kanalida
*/




?>