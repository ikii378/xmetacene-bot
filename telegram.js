const TelegramBot = require('node-telegram-bot-api');
const { exec } = require('child_process');
require('dotenv').config();

// Token bot dari environment atau variabel konfigurasi
const token = process.env.TELEGRAM_BOT_TOKEN;
// ID grup atau chat di Telegram
const chatId = process.env.TELEGRAM_CHAT_ID;

// Inisialisasi bot Telegram
const bot = new TelegramBot(token, { polling: true });

// Fungsi untuk mengeksekusi skrip PHP dan mengirim pesan ke Telegram
function executePHPCommand(command, chatId) {
    exec(command, (error, stdout, stderr) => {
        if (error) {
            console.error(`Kesalahan: ${error.message}`);
            bot.sendMessage(chatId, `Kesalahan: ${error.message}`);
            return;
        }
        if (stderr) {
            console.error(`Stderr: ${stderr}`);
            bot.sendMessage(chatId, `Stderr: ${stderr}`);
            return;
        }
        console.log(`Stdout: ${stdout}`);
        bot.sendMessage(chatId, `Perintah PHP berhasil dieksekusi`);
    });
}

// Mendengarkan perintah dari grup atau chat
bot.onText(/\/upgrade/, (msg) => {
    const chatId = msg.chat.id;
    executePHPCommand('php /root/pet.php', chatId);
});

bot.onText(/\/detail/, (msg) => {
    const chatId = msg.chat.id;
    executePHPCommand('php /root/detail.php', chatId);
});

bot.onText(/\/battle/, (msg) => {
    const chatId = msg.chat.id;
    executePHPCommand('php /root/charge.php', chatId);
});

bot.onText(/\/bangun/, (msg) => {
    const chatId = msg.chat.id;
    executePHPCommand('php /root/wake.php', chatId);
});

bot.onText(/\/drink/, (msg) => {
    const chatId = msg.chat.id;
    executePHPCommand('php /root/drink.php', chatId);
    executePHPCommand('php /root/drink-2.php', chatId);
});

bot.onText(/\/push-pet/, (msg) => {
    const chatId = msg.chat.id;
    executePHPCommand('php /root/update-raven.php', chatId);
});

// Handle kesalahan koneksi
bot.on('polling_error', (error) => {
    console.error(`Kesalahan polling: ${error}`);
});

// Tampilkan pesan bahwa bot sudah siap
console.log('Bot Telegram sedang berjalan...');
