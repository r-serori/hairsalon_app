import './bootstrap';

import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';
window.Alpine = Alpine;

Alpine.plugin(focus);

Alpine.start();

const express = require('express');
const cors = require('cors');

const app = express();

app.use(cors()); // これにより、全てのオリジンからのリクエストが許可されます

// その他のルートやミドルウェアの設定

app.listen(3000, () => {
  console.log('サーバーが起動しました');
});
