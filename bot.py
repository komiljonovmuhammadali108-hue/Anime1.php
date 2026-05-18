import os
import json
import sqlite3
import requests
from datetime import datetime
from flask import Flask, request

# ========================
# SOZLAMALAR
# ========================
TOKEN = os.environ.get("TOKEN", "8376174975:AAFyAKIWwbKUUBiK2MJkZiuV1m8DwNDMpXQ")
OWNER_ID = 5775346497
KANAL = "@animelar_dunyosiN"
WEBHOOK_URL = os.environ.get("WEBHOOK_URL", "")
API = f"https://api.telegram.org/bot{TOKEN}"

app = Flask(__name__)

# ========================
# DATABASE
# ========================
def get_db():
    db = sqlite3.connect("bot.db")
    db.row_factory = sqlite3.Row
    return db

def init_db():
    db = get_db()
    cur = db.cursor()
    
    # Foydalanuvchilar
    cur.execute("""CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY,
        name TEXT,
        username TEXT,
        joined_date TEXT
    )""")
    
    # Adminlar
    cur.execute("""CREATE TABLE IF NOT EXISTS admins (
        id INTEGER PRIMARY KEY
    )""")
    
    # Kanallar (majburiy obuna)
    cur.execute("""CREATE TABLE IF NOT EXISTS channels (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT UNIQUE
    )""")
    
    # Kinolar
    cur.execute("""CREATE TABLE IF NOT EXISTS movies (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        movie_id TEXT UNIQUE,
        name TEXT,
        photo_id TEXT
    )""")
    
    # Kino qismlari
    cur.execute("""CREATE TABLE IF NOT EXISTS parts (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        movie_id TEXT,
        part_num INTEGER,
        name TEXT,
        video_id TEXT,
        caption TEXT,
        download_count INTEGER DEFAULT 0
    )""")
    
    # Foydalanuvchi qadamlari
    cur.execute("""CREATE TABLE IF NOT EXISTS steps (
        user_id INTEGER PRIMARY KEY,
        step TEXT,
        data TEXT
    )""")
    
    # Bloklangan foydalanuvchilar
    cur.execute("""CREATE TABLE IF NOT EXISTS blocked (
        user_id INTEGER PRIMARY KEY
    )""")
    
    # Bot holati
    cur.execute("""CREATE TABLE IF NOT EXISTS settings (
        key TEXT PRIMARY KEY,
        value TEXT
    )""")
    
    # Owner ni admin qilib qo'shamiz
    cur.execute("INSERT OR IGNORE INTO admins (id) VALUES (?)", (OWNER_ID,))
    cur.execute("INSERT OR IGNORE INTO settings (key, value) VALUES ('holat', 'Yoqilgan')")
    cur.execute("INSERT OR IGNORE INTO settings (key, value) VALUES ('kino_kanal', '@animelar_dunyosiN')")
    
    db.commit()
    db.close()

# ========================
# TELEGRAM FUNKSIYALAR
# ========================
def bot(method, data=None):
    try:
        r = requests.post(f"{API}/{method}", json=data or {}, timeout=10)
        return r.json()
    except:
        return {}

def send_message(chat_id, text, parse_mode="html", reply_markup=None, **kwargs):
    data = {"chat_id": chat_id, "text": text, "parse_mode": parse_mode}
    if reply_markup:
        data["reply_markup"] = json.dumps(reply_markup)
    data.update(kwargs)
    return bot("sendMessage", data)

def send_photo(chat_id, photo, caption="", parse_mode="html", reply_markup=None):
    data = {"chat_id": chat_id, "photo": photo, "caption": caption, "parse_mode": parse_mode}
    if reply_markup:
        data["reply_markup"] = json.dumps(reply_markup)
    return bot("sendPhoto", data)

def send_video(chat_id, video, caption="", parse_mode="html", reply_markup=None):
    data = {"chat_id": chat_id, "video": video, "caption": caption, "parse_mode": parse_mode}
    if reply_markup:
        data["reply_markup"] = json.dumps(reply_markup)
    return bot("sendVideo", data)

def edit_message(chat_id, message_id, text, parse_mode="html", reply_markup=None):
    data = {"chat_id": chat_id, "message_id": message_id, "text": text, "parse_mode": parse_mode}
    if reply_markup:
        data["reply_markup"] = json.dumps(reply_markup)
    return bot("editMessageText", data)

def delete_message(chat_id, message_id):
    bot("deleteMessage", {"chat_id": chat_id, "message_id": message_id})

def answer_callback(qid, text="", show_alert=False):
    bot("answerCallbackQuery", {"callback_query_id": qid, "text": text, "show_alert": show_alert})

# ========================
# YORDAMCHI FUNKSIYALAR
# ========================
def is_admin(user_id):
    if user_id == OWNER_ID:
        return True
    db = get_db()
    result = db.execute("SELECT id FROM admins WHERE id=?", (user_id,)).fetchone()
    db.close()
    return result is not None

def is_blocked(user_id):
    db = get_db()
    result = db.execute("SELECT user_id FROM blocked WHERE user_id=?", (user_id,)).fetchone()
    db.close()
    return result is not None

def add_user(user_id, name, username):
    db = get_db()
    db.execute("INSERT OR IGNORE INTO users (id, name, username, joined_date) VALUES (?,?,?,?)",
               (user_id, name, username, datetime.now().strftime("%d.m.Y")))
    db.commit()
    db.close()

def get_step(user_id):
    db = get_db()
    result = db.execute("SELECT step, data FROM steps WHERE user_id=?", (user_id,)).fetchone()
    db.close()
    if result:
        return result["step"], result["data"]
    return None, None

def set_step(user_id, step, data=""):
    db = get_db()
    db.execute("INSERT OR REPLACE INTO steps (user_id, step, data) VALUES (?,?,?)", (user_id, step, data))
    db.commit()
    db.close()

def clear_step(user_id):
    db = get_db()
    db.execute("DELETE FROM steps WHERE user_id=?", (user_id,))
    db.commit()
    db.close()

def get_setting(key):
    db = get_db()
    result = db.execute("SELECT value FROM settings WHERE key=?", (key,)).fetchone()
    db.close()
    return result["value"] if result else None

def get_bot_username():
    result = bot("getMe")
    return result.get("result", {}).get("username", "")

def get_channels():
    db = get_db()
    channels = db.execute("SELECT username FROM channels").fetchall()
    db.close()
    return [c["username"] for c in channels]

def check_subscription(user_id):
    channels = get_channels()
    not_joined = []
    for ch in channels:
        username = ch.replace("@", "")
        result = bot("getChatMember", {"chat_id": f"@{username}", "user_id": user_id})
        status = result.get("result", {}).get("status", "left")
        if status not in ["creator", "administrator", "member"]:
            not_joined.append(ch)
    return not_joined

def joinchat(user_id, name=""):
    not_joined = check_subscription(user_id)
    if not_joined:
        keyboard = [[{"text": f"❌ {ch}", "url": f"https://t.me/{ch.replace('@','')}"}] for ch in not_joined]
        keyboard.append([{"text": "🔄 Tekshirish", "callback_data": "checksuv"}])
        send_message(user_id, "⚠️ <b>Botdan foydalanish uchun majburiy kanallarga obuna bo'ling!</b>",
                    reply_markup={"inline_keyboard": keyboard})
        return False
    return True

def get_movie(movie_id):
    db = get_db()
    movie = db.execute("SELECT * FROM movies WHERE movie_id=?", (str(movie_id),)).fetchone()
    db.close()
    return dict(movie) if movie else None

def get_parts(movie_id):
    db = get_db()
    parts = db.execute("SELECT * FROM parts WHERE movie_id=? ORDER BY part_num", (str(movie_id),)).fetchall()
    db.close()
    return [dict(p) for p in parts]

# ========================
# KLAVIATURALAR
# ========================
def panel_kb():
    return {
        "keyboard": [
            [{"text": "📢 Kanallar"}, {"text": "📥 Kino Yuklash"}],
            [{"text": "✉ Xabarnoma"}, {"text": "📊 Statistika"}],
            [{"text": "🤖 Bot holati"}, {"text": "👥 Adminlar"}],
            [{"text": "◀️ Orqaga"}]
        ],
        "resize_keyboard": True
    }

def back_kb():
    return {
        "keyboard": [[{"text": "◀️ Orqaga"}]],
        "resize_keyboard": True
    }

def main_kb():
    return {
        "keyboard": [[{"text": "🗄 Boshqaruv paneli"}]],
        "resize_keyboard": True
    }

# ========================
# XABAR YUBORISH (KINO)
# ========================
def send_movie(chat_id, movie_id, bot_username):
    movie = get_movie(movie_id)
    if not movie:
        send_message(chat_id, f"<b>❌ Uzr, <code>{movie_id}</code> ID raqamli kino topilmadi.</b>")
        return

    parts = get_parts(movie_id)
    if not parts:
        send_message(chat_id, "<b>❌ Bu kinoga hali hech qanday qism joylanmagan.</b>")
        return

    kanalcha = get_setting("kino_kanal") or KANAL

    if len(parts) == 1:
        p = parts[0]
        db = get_db()
        db.execute("UPDATE parts SET download_count=download_count+1 WHERE id=?", (p["id"],))
        db.commit()
        db.close()
        caption = (f"<b>🍿 Kino nomi: {movie['name']} (1-qism)</b>\n\n"
                   f"Kino haqida: <blockquote>{p['caption']}</blockquote>\n\n"
                   f"Id: <code>{movie_id}</code>\n"
                   f"#️⃣ Qism: <code>1</code>\n"
                   f"🗂 Yuklashlar soni: {p['download_count']+1}\n\n"
                   f"🤖 Bizning bot: @{bot_username}")
        send_video(chat_id, p["video_id"], caption,
                  reply_markup={"inline_keyboard": [[
                      {"text": "📋 Ulashish", "url": f"https://t.me/share/url?url=https://t.me/{bot_username}?start={movie_id}"}
                  ]]})
    else:
        # Ko'p qism
        part1 = parts[0]
        inline_parts = []
        row = []
        for i, p in enumerate(parts):
            row.append({"text": str(p["part_num"]), "callback_data": f"get_part|{movie_id}|{p['part_num']}"})
            if len(row) == 5:
                inline_parts.append(row)
                row = []
        if row:
            inline_parts.append(row)

        caption = (f"<b>🍿 Kino nomi: {movie['name']}</b>\n\n"
                   f"Kino haqida: <blockquote>{part1['caption']}</blockquote>\n\n"
                   f"Id: <code>{movie_id}</code>\n"
                   f"<b>Qismlari:</b>")
        send_photo(chat_id, movie["photo_id"], caption,
                  reply_markup={"inline_keyboard": inline_parts})

# ========================
# WEBHOOK HANDLER
# ========================
@app.route(f"/{TOKEN}", methods=["POST"])
def webhook():
    try:
        update = request.get_json()
        process_update(update)
    except Exception as e:
        print(f"Error: {e}")
    return "OK"

@app.route("/")
def index():
    return "Bot ishlayapti! ✅"

def process_update(update):
    bot_username = get_bot_username()
    
    # ========================
    # MESSAGE
    # ========================
    if "message" in update:
        msg = update["message"]
        chat_id = msg["chat"]["id"]
        user_id = msg["from"]["id"]
        text = msg.get("text", "")
        name = msg["from"].get("first_name", "")
        last = msg["from"].get("last_name", "")
        username = msg["from"].get("username", "")
        full_name = f"{name} {last}".strip()
        nameru = f"<a href='tg://user?id={user_id}'>{full_name}</a>"
        mid = msg["message_id"]

        # Bloklangan foydalanuvchi
        if is_blocked(user_id):
            return

        # Foydalanuvchini saqlash
        add_user(user_id, name, username)

        holat = get_setting("holat")
        step, step_data = get_step(user_id)
        kanalcha = get_setting("kino_kanal") or KANAL

        # Bot o'chirilgan
        if holat == "O'chirilgan" and not is_admin(user_id):
            send_message(chat_id, "⛔️ <b>Bot vaqtinchalik o'chirilgan!</b>\n\n<i>Botda ta'mirlash ishlari olib borilayotgan bo'lishi mumkin!</i>")
            return

        # /start
        if text == "/start" or text.startswith("/start "):
            parts_text = text.split()
            
            if len(parts_text) > 1:
                movie_id = parts_text[1]
                if joinchat(user_id):
                    send_movie(chat_id, movie_id, bot_username)
                else:
                    set_step(user_id, "wait_join", movie_id)
                return

            if not joinchat(user_id):
                return

            kb = {"inline_keyboard": [
                [{"text": "🔎 Kino kodlari", "url": f"https://t.me/{kanalcha.replace('@','')}"}]
            ]}
            if is_admin(user_id):
                kb["inline_keyboard"].append([{"text": "🗄 Boshqaruv paneli", "callback_data": "boshqar"}])

            send_photo(chat_id, "https://t.me/Rasmlarkod/37",
                      f"😊 Assalomu Alaykum! {nameru}\n<blockquote>Agar foydalanishda qiyinchilikka duch kelsangiz /help buyrug'ini yuboring.</blockquote>\n\n<b>🎬 Iltimos, kino kodingizni yuboring:</b>",
                      reply_markup=kb)
            return

        # /help
        if text == "/help":
            if not joinchat(user_id):
                return
            send_photo(chat_id, "https://t.me/Rasmlarkod/37",
                      "<blockquote>💻 <b>Savol va Takliflaringiz bo'lsa qo'llab-quvvatlash manzilimizga murojaat qiling!</b>\n❗<b>Botga faqat Kino kodini kiriting!</b></blockquote>",
                      reply_markup={"inline_keyboard": [[{"text": "☎️ Qo'llab-quvvatlash", "url": "https://t.me/DavlatyorUZ"}]]})
            return

        # Orqaga
        if text == "◀️ Orqaga":
            if not joinchat(user_id):
                return
            clear_step(user_id)
            send_message(chat_id, f"Assalomu Alaykum {nameru}\nKino Kodini Yuboring:", reply_markup={"force_reply": True})
            return

        # Admin panel
        if text in ["🗄 Boshqaruv paneli", "/panel"]:
            if is_admin(user_id):
                clear_step(user_id)
                send_message(chat_id, "<b>Admin paneliga xush kelibsiz!</b>", reply_markup=panel_kb())
            return

        # ========================
        # ADMIN BUYRUQLARI
        # ========================

        # Kanallar
        if text == "📢 Kanallar" and is_admin(user_id):
            db = get_db()
            chs = db.execute("SELECT username FROM channels").fetchall()
            db.close()
            ch_list = "\n".join([c["username"] for c in chs]) if chs else "Hozircha yo'q"
            send_message(chat_id, f"<b>📢 Majburiy kanallar:</b>\n{ch_list}",
                        reply_markup={"inline_keyboard": [
                            [{"text": "➕ Kanal qo'shish", "callback_data": "add_channel"}],
                            [{"text": "🗑 Kanal o'chirish", "callback_data": "remove_channel"}]
                        ]})
            return

        # Statistika
        if text == "📊 Statistika" and is_admin(user_id):
            db = get_db()
            users_count = db.execute("SELECT COUNT(*) as c FROM users").fetchone()["c"]
            movies_count = db.execute("SELECT COUNT(*) as c FROM movies").fetchone()["c"]
            db.close()
            send_message(chat_id,
                f"<b>📊 Statistika:</b>\n\n"
                f"👥 Foydalanuvchilar: <b>{users_count}</b>\n"
                f"🎬 Kinolar: <b>{movies_count}</b>")
            return

        # Bot holati
        if text == "🤖 Bot holati" and is_admin(user_id):
            holat = get_setting("holat")
            btn_text = "🔴 O'chirish" if holat == "Yoqilgan" else "🟢 Yoqish"
            send_message(chat_id, f"<b>🤖 Bot holati: {holat}</b>",
                        reply_markup={"inline_keyboard": [[{"text": btn_text, "callback_data": "toggle_bot"}]]})
            return

        # Adminlar
        if text == "👥 Adminlar" and is_admin(user_id):
            kb = [
                [{"text": "📑 Ro'yxat", "callback_data": "admin_list"}]
            ]
            if user_id == OWNER_ID:
                kb.insert(0, [{"text": "➕ Admin qo'shish", "callback_data": "add_admin"}])
                kb[1].append({"text": "🗑 O'chirish", "callback_data": "remove_admin"})
            send_message(chat_id, "🔰 <b>Quyidagilardan birini tanlang:</b>", reply_markup={"inline_keyboard": kb})
            return

        # Xabarnoma
        if text == "✉ Xabarnoma" and is_admin(user_id):
            set_step(user_id, "broadcast")
            send_message(chat_id, "<b>📢 Xabarnoma matnini yuboring:</b>", reply_markup=back_kb())
            return

        # Kino yuklash
        if text == "📥 Kino Yuklash" and is_admin(user_id):
            db = get_db()
            count = db.execute("SELECT COUNT(*) as c FROM movies").fetchone()["c"]
            db.close()
            new_id = str(count + 1)
            set_step(user_id, "kino_id_sorash", new_id)
            send_message(chat_id, f"<b>🎬 Yangi kino uchun ID:</b> <code>{new_id}</code>\n\nBu ID ni tasdiqlaysizmi?",
                        reply_markup={"inline_keyboard": [
                            [{"text": "✅ Tasdiqlash", "callback_data": f"confirm_kino_id|{new_id}"}],
                            [{"text": "✏️ O'zgartirish", "callback_data": "change_kino_id"}]
                        ]})
            return

        # ========================
        # STEP HANDLERS
        # ========================

        # Broadcast
        if step == "broadcast" and is_admin(user_id):
            db = get_db()
            users = db.execute("SELECT id FROM users").fetchall()
            db.close()
            sent = 0
            for u in users:
                try:
                    send_message(u["id"], text)
                    sent += 1
                except:
                    pass
            send_message(chat_id, f"<b>✅ Xabarnoma {sent} ta foydalanuvchiga yuborildi!</b>", reply_markup=panel_kb())
            clear_step(user_id)
            return

        # Admin qo'shish
        if step == "add_admin_step" and user_id == OWNER_ID:
            if text.isdigit():
                db = get_db()
                db.execute("INSERT OR IGNORE INTO admins (id) VALUES (?)", (int(text),))
                db.commit()
                db.close()
                send_message(chat_id, f"✅ <code>{text}</code> admin qilindi!", reply_markup=panel_kb())
                clear_step(user_id)
            else:
                send_message(chat_id, "❌ Faqat ID raqam yuboring!")
            return

        # Admin o'chirish
        if step == "remove_admin_step" and user_id == OWNER_ID:
            if text.isdigit():
                db = get_db()
                db.execute("DELETE FROM admins WHERE id=?", (int(text),))
                db.commit()
                db.close()
                send_message(chat_id, f"✅ <code>{text}</code> adminlikdan olindi!", reply_markup=panel_kb())
                clear_step(user_id)
            else:
                send_message(chat_id, "❌ Faqat ID raqam yuboring!")
            return

        # Kanal qo'shish
        if step == "add_channel_step" and is_admin(user_id):
            ch = text if text.startswith("@") else f"@{text}"
            db = get_db()
            db.execute("INSERT OR IGNORE INTO channels (username) VALUES (?)", (ch,))
            db.commit()
            db.close()
            send_message(chat_id, f"✅ <b>{ch} kanali qo'shildi!</b>", reply_markup=panel_kb())
            clear_step(user_id)
            return

        # Kanal o'chirish
        if step == "remove_channel_step" and is_admin(user_id):
            ch = text if text.startswith("@") else f"@{text}"
            db = get_db()
            db.execute("DELETE FROM channels WHERE username=?", (ch,))
            db.commit()
            db.close()
            send_message(chat_id, f"✅ <b>{ch} kanali o'chirildi!</b>", reply_markup=panel_kb())
            clear_step(user_id)
            return

        # Kino ID o'zgartirish
        if step == "change_kino_id_step" and is_admin(user_id):
            if text.isdigit():
                db = get_db()
                existing = db.execute("SELECT movie_id FROM movies WHERE movie_id=?", (text,)).fetchone()
                db.close()
                if existing:
                    send_message(chat_id, f"❌ <b>{text} ID allaqachon mavjud! Boshqa ID kiriting:</b>")
                    return
                set_step(user_id, "kino_name_sorash", text)
                send_message(chat_id, f"✅ ID <code>{text}</code> saqlandi!\n\n<b>1️⃣ Endi kino nomini kiriting:</b>", reply_markup=main_kb())
            else:
                send_message(chat_id, "❌ Faqat raqam kiriting!")
            return

        # Kino nomi
        if step == "kino_name_sorash" and is_admin(user_id):
            set_step(user_id, "kino_rasm_sorash", json.dumps({"id": step_data, "name": text}))
            send_message(chat_id, "<b>🖼️ Endi kino posterini (rasm) yuboring.</b>", reply_markup=main_kb())
            return

        # Kino rasmi
        if step == "kino_rasm_sorash" and is_admin(user_id):
            if "photo" in msg:
                photo_id = msg["photo"][-1]["file_id"]
                data_obj = json.loads(step_data)
                data_obj["photo"] = photo_id
                set_step(user_id, "kino_video_sorash", json.dumps(data_obj))
                send_message(chat_id, "<b>🎬 Rasm saqlandi. Endi 1-qismning videosini yuboring.</b>", reply_markup=main_kb())
            else:
                send_message(chat_id, "❌ Rasm yuboring!")
            return

        # Kino video
        if step == "kino_video_sorash" and is_admin(user_id):
            if "video" in msg:
                video_id = msg["video"]["file_id"]
                data_obj = json.loads(step_data)
                data_obj["video"] = video_id
                set_step(user_id, "kino_malumot_sorash", json.dumps(data_obj))
                send_message(chat_id, "<b>📝 Video saqlandi. Endi kino haqida ma'lumot kiriting:</b>", reply_markup=main_kb())
            else:
                send_message(chat_id, "❌ Video yuboring!")
            return

        # Kino ma'lumot
        if step == "kino_malumot_sorash" and is_admin(user_id):
            data_obj = json.loads(step_data)
            movie_id = data_obj["id"]
            movie_name = data_obj["name"]
            photo_id = data_obj["photo"]
            video_id = data_obj["video"]
            caption = text

            db = get_db()
            db.execute("INSERT OR IGNORE INTO movies (movie_id, name, photo_id) VALUES (?,?,?)",
                      (movie_id, movie_name, photo_id))
            db.execute("INSERT INTO parts (movie_id, part_num, name, video_id, caption) VALUES (?,1,?,?,?)",
                      (movie_id, movie_name, video_id, caption))
            db.commit()
            db.close()

            # Kanalga e'lon
            kanalcha = get_setting("kino_kanal") or KANAL
            ch_clean = kanalcha.replace("@", "")
            channel_caption = (f"<b>🍿 Botga yangi kino joylandi!</b>\n\n"
                             f"🎬 Kino nomi: <b>{movie_name} (1-qism)</b>\n\n"
                             f"📄 Kino haqida:\n<blockquote>{caption}</blockquote>\n\n"
                             f"🔢 Kino ID: <code>{movie_id}</code>\n\n"
                             f"‼️ Bot manzili: @{bot_username}\n\n"
                             f"<i>❗ Quyidagi tugmani bosish orqali kinoni tomosha qiling.</i>")

            bot("sendPhoto", {
                "chat_id": kanalcha,
                "photo": photo_id,
                "caption": channel_caption,
                "parse_mode": "html",
                "reply_markup": json.dumps({"inline_keyboard": [[
                    {"text": "✨️ Kinoni tomosha qilish", "url": f"https://t.me/{bot_username}?start={movie_id}"}
                ]]})
            })

            send_message(chat_id,
                f"<blockquote>✅ Kino (1-qism) bazaga muvaffaqiyatli joylandi!</blockquote>\n\n🔄 Kino ID: <code>{movie_id}</code>",
                reply_markup=panel_kb())
            clear_step(user_id)
            return

        # Qism qo'shish - video
        if step == "part_video_sorash" and is_admin(user_id):
            if "video" in msg:
                video_id = msg["video"]["file_id"]
                data_obj = json.loads(step_data)
                data_obj["video"] = video_id
                set_step(user_id, "part_malumot_sorash", json.dumps(data_obj))
                send_message(chat_id, "<b>📝 Video saqlandi. Endi qism haqida ma'lumot kiriting:</b>", reply_markup=main_kb())
            else:
                send_message(chat_id, "❌ Video yuboring!")
            return

        # Qism ma'lumot
        if step == "part_malumot_sorash" and is_admin(user_id):
            data_obj = json.loads(step_data)
            movie_id = data_obj["id"]
            part_num = data_obj["part_num"]
            video_id = data_obj["video"]

            db = get_db()
            movie = db.execute("SELECT name FROM movies WHERE movie_id=?", (movie_id,)).fetchone()
            db.execute("INSERT INTO parts (movie_id, part_num, name, video_id, caption) VALUES (?,?,?,?,?)",
                      (movie_id, part_num, movie["name"] if movie else "", video_id, text))
            db.commit()
            db.close()

            send_message(chat_id, f"✅ <code>{movie_id}</code> kinoga <b>{part_num}-qism</b> qo'shildi!", reply_markup=panel_kb())
            clear_step(user_id)
            return

        # Kino o'chirish
        if step == "delete_kino_step" and is_admin(user_id):
            if text.isdigit():
                db = get_db()
                movie = db.execute("SELECT movie_id FROM movies WHERE movie_id=?", (text,)).fetchone()
                if movie:
                    db.execute("DELETE FROM movies WHERE movie_id=?", (text,))
                    db.execute("DELETE FROM parts WHERE movie_id=?", (text,))
                    db.commit()
                    send_message(chat_id, f"✅ <code>{text}</code> ID raqamli kino o'chirildi!", reply_markup=panel_kb())
                else:
                    send_message(chat_id, f"❌ <code>{text}</code> ID raqamli kino topilmadi!")
                db.close()
                clear_step(user_id)
            else:
                send_message(chat_id, "❌ Faqat raqam kiriting!")
            return

        # ========================
        # KINO QIDIRISH (raqam)
        # ========================
        if text and text.isdigit() and not step:
            if not joinchat(user_id):
                set_step(user_id, "wait_join", text)
                return
            send_movie(chat_id, text, bot_username)
            return

    # ========================
    # CALLBACK QUERY
    # ========================
    elif "callback_query" in update:
        cb = update["callback_query"]
        qid = cb["id"]
        data = cb.get("data", "")
        user_id = cb["from"]["id"]
        chat_id = cb["message"]["chat"]["id"]
        mid = cb["message"]["message_id"]
        name = cb["from"].get("first_name", "")
        last = cb["from"].get("last_name", "")
        full_name = f"{name} {last}".strip()
        bot_username = get_bot_username()
        kanalcha = get_setting("kino_kanal") or KANAL

        holat = get_setting("holat")
        if holat == "O'chirilgan" and not is_admin(user_id):
            answer_callback(qid, "⛔️ Bot vaqtinchalik o'chirilgan!", True)
            return

        # Yopish
        if data == "yopish":
            delete_message(chat_id, mid)
            return

        # Obuna tekshirish
        if data == "checksuv":
            delete_message(chat_id, mid)
            not_joined = check_subscription(user_id)
            if not not_joined:
                step, step_data = get_step(user_id)
                send_message(chat_id, "<b>✅ Obunangiz tasdiqlandi!</b>")
                nameru = f"<a href='tg://user?id={user_id}'>{full_name}</a>"
                
                kb = {"inline_keyboard": [
                    [{"text": "🔎 Kino kodlari", "url": f"https://t.me/{kanalcha.replace('@','')}"}]
                ]}
                if is_admin(user_id):
                    kb["inline_keyboard"].append([{"text": "🗄 Boshqaruv paneli", "callback_data": "boshqar"}])
                
                send_message(chat_id, f"Assalomu Alaykum {nameru}\nKino Kodini Yuboring:", reply_markup=kb)
                
                if step == "wait_join" and step_data:
                    send_movie(chat_id, step_data, bot_username)
                    clear_step(user_id)
            else:
                keyboard = [[{"text": f"❌ {ch}", "url": f"https://t.me/{ch.replace('@','')}"}] for ch in not_joined]
                keyboard.append([{"text": "🔄 Tekshirish", "callback_data": "checksuv"}])
                send_message(chat_id, "⚠️ <b>Hali ham barcha kanallarga obuna bo'lmadingiz!</b>",
                            reply_markup={"inline_keyboard": keyboard})
            return

        # Boshqaruv paneli
        if data == "boshqar":
            delete_message(chat_id, mid)
            send_message(chat_id, "<b>🖥️ Boshqaruv panelidasiz!</b>", reply_markup=panel_kb())
            return

        if data == "bosh":
            delete_message(chat_id, mid)
            send_message(chat_id, "<b>Admin paneliga xush kelibsiz!</b>", reply_markup=panel_kb())
            return

        # Bot holati
        if data == "toggle_bot" and is_admin(user_id):
            holat = get_setting("holat")
            new_holat = "O'chirilgan" if holat == "Yoqilgan" else "Yoqilgan"
            db = get_db()
            db.execute("UPDATE settings SET value=? WHERE key='holat'", (new_holat,))
            db.commit()
            db.close()
            answer_callback(qid, f"Bot {new_holat}!", True)
            btn_text = "🔴 O'chirish" if new_holat == "Yoqilgan" else "🟢 Yoqish"
            edit_message(chat_id, mid, f"<b>🤖 Bot holati: {new_holat}</b>",
                        reply_markup={"inline_keyboard": [[{"text": btn_text, "callback_data": "toggle_bot"}]]})
            return

        # Admin ro'yxati
        if data == "admin_list" and is_admin(user_id):
            db = get_db()
            admins = db.execute("SELECT id FROM admins").fetchall()
            db.close()
            admin_list = "\n".join([str(a["id"]) for a in admins]) if admins else "Yo'q"
            edit_message(chat_id, mid, f"👮‍♂️ <b>Adminlar ro'yxati:</b>\n{admin_list}",
                        reply_markup={"inline_keyboard": [[{"text": "🔙 Orqaga", "callback_data": "bosh"}]]})
            return

        # Admin qo'shish
        if data == "add_admin" and user_id == OWNER_ID:
            delete_message(chat_id, mid)
            set_step(user_id, "add_admin_step")
            send_message(chat_id, "🔢 <b>Yangi admin ID sini yuboring:</b>", reply_markup=main_kb())
            return

        # Admin o'chirish
        if data == "remove_admin" and user_id == OWNER_ID:
            delete_message(chat_id, mid)
            set_step(user_id, "remove_admin_step")
            send_message(chat_id, "🔢 <b>O'chirmoqchi bo'lgan admin ID sini yuboring:</b>", reply_markup=main_kb())
            return

        # Kanal qo'shish
        if data == "add_channel" and is_admin(user_id):
            set_step(user_id, "add_channel_step")
            send_message(chat_id, "<b>📢 Kanal username ini yuboring (masalan: @MyChannel):</b>", reply_markup=back_kb())
            return

        # Kanal o'chirish
        if data == "remove_channel" and is_admin(user_id):
            set_step(user_id, "remove_channel_step")
            send_message(chat_id, "<b>📢 O'chirmoqchi bo'lgan kanal username ini yuboring:</b>", reply_markup=back_kb())
            return

        # Kino ID tasdiqlash
        if data.startswith("confirm_kino_id|") and is_admin(user_id):
            movie_id = data.split("|")[1]
            delete_message(chat_id, mid)
            set_step(user_id, "kino_name_sorash", movie_id)
            send_message(chat_id, f"✅ ID <code>{movie_id}</code> tasdiqlandi!\n\n<b>1️⃣ Endi kino nomini kiriting:</b>", reply_markup=main_kb())
            return

        # Kino ID o'zgartirish
        if data == "change_kino_id" and is_admin(user_id):
            delete_message(chat_id, mid)
            set_step(user_id, "change_kino_id_step")
            send_message(chat_id, "<b>✏️ Yangi kino ID sini kiriting:</b>", reply_markup=main_kb())
            return

        # Qism olish
        if data.startswith("get_part|"):
            if not check_subscription(user_id):
                answer_callback(qid, "⚠️ Kinoni olish uchun kanalga a'zo bo'lishingiz kerak!", True)
                return
            
            parts = data.split("|")
            movie_id = parts[1]
            part_num = int(parts[2])

            db = get_db()
            part = db.execute("SELECT * FROM parts WHERE movie_id=? AND part_num=?", (movie_id, part_num)).fetchone()
            if part:
                part = dict(part)
                db.execute("UPDATE parts SET download_count=download_count+1 WHERE id=?", (part["id"],))
                db.commit()
                caption = (f"<b>🍿 Kino nomi: {part['name']} ({part_num}-qism)</b>\n\n"
                          f"Kino haqida: <blockquote>{part['caption']}</blockquote>\n\n"
                          f"Id: <code>{movie_id}</code>\n"
                          f"#️⃣ Qism: <code>{part_num}</code>\n"
                          f"🗂 Yuklashlar soni: {part['download_count']+1}\n\n"
                          f"🤖 Bizning bot: @{bot_username}")
                send_video(chat_id, part["video_id"], caption,
                          reply_markup={"inline_keyboard": [[
                              {"text": "📋 Ulashish", "url": f"https://t.me/share/url?url=https://t.me/{bot_username}?start={movie_id}"}
                          ]]})
                answer_callback(qid, f"{part_num}-qism yuborildi.")
            else:
                answer_callback(qid, f"❌ {part_num}-qism topilmadi!", True)
            db.close()
            return

        # Kino o'chirish callback
        if data == "deletekino" and is_admin(user_id):
            delete_message(chat_id, mid)
            set_step(user_id, "delete_kino_step")
            send_message(chat_id, "<b>🗑 O'chirmoqchi bo'lgan kinoning ID sini kiriting:</b>", reply_markup=main_kb())
            return

        # Qism qo'shish callback
        if data.startswith("add_part|") and is_admin(user_id):
            movie_id = data.split("|")[1]
            db = get_db()
            count = db.execute("SELECT COUNT(*) as c FROM parts WHERE movie_id=?", (movie_id,)).fetchone()["c"]
            db.close()
            new_part = count + 1
            set_step(user_id, "part_video_sorash", json.dumps({"id": movie_id, "part_num": new_part}))
            send_message(chat_id, f"<b>🎬 {new_part}-qism videosini yuboring:</b>", reply_markup=main_kb())
            return

        answer_callback(qid)

# ========================
# MAIN
# ========================
if __name__ == "__main__":
    init_db()
    
    if WEBHOOK_URL:
        bot("setWebhook", {"url": f"{WEBHOOK_URL}/{TOKEN}"})
        print(f"Webhook set: {WEBHOOK_URL}/{TOKEN}")
    
    port = int(os.environ.get("PORT", 5000))
    app.run(host="0.0.0.0", port=port)
