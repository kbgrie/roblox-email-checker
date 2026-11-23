const express = require('express');
const axios = require('axios');
const path = require('path');

const app = express();
app.use(express.json({ limit: '50kb' }));
app.use(express.static(path.join(__dirname, 'public')));

app.post('/api/check', async (req, res) => {
  let { cookie } = req.body;
  if (!cookie || typeof cookie !== 'string') {
    return res.status(400).json({ success: false, error: 'Cookie là bắt buộc' });
  }

  cookie = cookie.trim();
  if (cookie.startsWith('.ROBLOSECURITY=')) {
    cookie = cookie.substring('.ROBLOSECURITY='.length);
  }

  const headers = { Cookie: `.ROBLOSECURITY=${cookie}` };

  try {
    // 1. Lấy thông tin user
    const userResp = await axios.get(
      'https://users.roblox.com/v1/users/authenticated',
      { headers, timeout: 10000 }
    );
    const user = userResp.data;

    // 2. Lấy CSRF token (Roblox yêu cầu cho các request có auth)
    let csrfToken = '';
    try {
      await axios.post('https://auth.roblox.com/v2/logout', {}, { headers, timeout: 10000 });
    } catch (e) {
      csrfToken = e.response?.headers?.['x-csrf-token'] || '';
    }

    // 3. Check email
    let hasEmail = false, verified = false, email = null;
    try {
      const emailResp = await axios.get(
        'https://accountsettings.roblox.com/v1/email',
        { headers: { ...headers, 'X-CSRF-TOKEN': csrfToken }, timeout: 10000 }
      );
      hasEmail = true;
      email = emailResp.data.email;
      verified = emailResp.data.verified;
    } catch (e) {
      // 404 = chưa có email
      if (e.response?.status !== 404 && e.response?.status !== 403) {
        throw e;
      }
    }

    return res.json({
      success: true,
      user: { id: user.id, name: user.name, displayName: user.displayName },
      email: { hasEmail, verified, email }
    });
  } catch (error) {
    const status = error.response?.status || 500;
    return res.status(status).json({
      success: false,
      error:
        status === 401
          ? 'Cookie không hợp lệ hoặc đã hết hạn'
          : 'Không thể gọi API Roblox',
      details: error.response?.data || error.message
    });
  }
});

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => console.log(`✅ Server running on port ${PORT}`));
