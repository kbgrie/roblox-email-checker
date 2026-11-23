const express = require('express');
const axios = require('axios');
const path = require('path');

const app = express();

// Render chạy sau proxy → cần trust proxy để req.ip đúng
app.set('trust proxy', 1);

app.use(express.json({ limit: '50kb' }));
app.use(express.static(path.join(__dirname, 'public')));

// === Health check cho Render ===
app.get('/health', (req, res) => res.status(200).json({ ok: true, ts: Date.now() }));

// === API check email ===
app.post('/api/check', async (req, res) => {
  let { cookie } = req.body || {};
  if (!cookie || typeof cookie !== 'string') {
    return res.status(400).json({ success: false, error: 'Cookie là bắt buộc' });
  }

  cookie = cookie.trim().replace(/^\.ROBLOSECURITY=/, '');
  if (cookie.length < 50) {
    return res.status(400).json({ success: false, error: 'Cookie không hợp lệ (quá ngắn)' });
  }

  const headers = {
    Cookie: `.ROBLOSECURITY=${cookie}`,
    'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0 Safari/537.36',
    'Accept': 'application/json'
  };

  try {
    // 1. Lấy user info
    const userResp = await axios.get('https://users.roblox.com/v1/users/authenticated', {
      headers, timeout: 15000
    });
    const user = userResp.data;

    // 2. Lấy CSRF token (Roblox bắt buộc cho endpoint accountsettings)
    let csrfToken = '';
    try {
      await axios.post('https://auth.roblox.com/v2/logout', {}, { headers, timeout: 15000 });
    } catch (e) {
      csrfToken = e.response?.headers?.['x-csrf-token'] || '';
    }

    // 3. Check email
    let hasEmail = false;
    let verified = false;
    let email = null;

    try {
      const emailResp = await axios.get('https://accountsettings.roblox.com/v1/email', {
        headers: { ...headers, 'X-CSRF-TOKEN': csrfToken },
        timeout: 15000
      });
      hasEmail = true;
      email = emailResp.data.email;
      verified = emailResp.data.verified;
    } catch (e) {
      // 404 = không có email. Các lỗi khác thì throw
      const st = e.response?.status;
      if (st !== 404 && st !== 403) throw e;
    }

    console.log(`[OK] user=${user.name} (${user.id}) hasEmail=${hasEmail}`);

    return res.json({
      success: true,
      user: { id: user.id, name: user.name, displayName: user.displayName },
      email: { hasEmail, verified, email }
    });
  } catch (error) {
    const status = error.response?.status || 500;
    console.error(`[ERR] status=${status} msg=${error.message}`);

    return res.status(status).json({
      success: false,
      error:
        status === 401
          ? 'Cookie không hợp lệ hoặc đã hết hạn'
          : status === 403
          ? 'Roblox từ chối request (cookie sai/đã logout)'
          : 'Không thể gọi API Roblox',
      details: error.response?.data || error.message
    });
  }
});

// === 404 fallback ===
app.use((req, res) => res.status(404).json({ error: 'Not found' }));

// === Start ===
const PORT = process.env.PORT || 3000;
app.listen(PORT, '0.0.0.0', () => {
  console.log(`✅ Server running on 0.0.0.0:${PORT}`);
  console.log(`   NODE_ENV=${process.env.NODE_ENV || 'development'}`);
});
