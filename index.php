 <!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WSRB Track Stats | Neon Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-dark: #090512;
            --bg-card: rgba(22, 13, 41, 0.75);
            --neon-purple: #b026ff;
            --neon-violet: #7b2cbf;
            --neon-pink: #ff2a85;
            --text-main: #f3e8ff;
            --text-muted: #a78bfa;
            --border-glow: rgba(176, 38, 255, 0.35);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-dark);
            background-image: 
                radial-gradient(circle at 15% 20%, rgba(176, 38, 255, 0.18) 0%, transparent 40%),
                radial-gradient(circle at 85% 80%, rgba(123, 44, 191, 0.2) 0%, transparent 45%);
            color: var(--text-main);
            min-height: 100vh;
            padding: 2rem 1rem;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Header */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 1.5rem;
            margin-bottom: 2rem;
            border-bottom: 1px solid var(--border-glow);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-dot {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: var(--neon-purple);
            box-shadow: 0 0 12px var(--neon-purple), 0 0 24px var(--neon-purple);
        }

        h1 {
            font-size: 1.6rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            background: linear-gradient(90deg, #fff, #d8b4fe, #b026ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .status-badge {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.8rem;
            padding: 6px 14px;
            border-radius: 999px;
            background: rgba(176, 38, 255, 0.12);
            border: 1px solid var(--neon-purple);
            color: #e9d5ff;
            box-shadow: 0 0 15px rgba(176, 38, 255, 0.2);
        }

        /* Tabs */
        .tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .tab-btn {
            background: var(--bg-card);
            border: 1px solid var(--border-glow);
            color: var(--text-muted);
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.25s ease;
        }

        .tab-btn:hover, .tab-btn.active {
            color: #fff;
            border-color: var(--neon-purple);
            background: rgba(176, 38, 255, 0.2);
            box-shadow: 0 0 20px rgba(176, 38, 255, 0.35);
        }

        /* Glass Card */
        .card {
            background: var(--bg-card);
            backdrop-filter: blur(14px);
            border: 1px solid var(--border-glow);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.45);
        }

        /* Search & Filter Forms */
        .input-group {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        input, select {
            flex: 1;
            min-width: 200px;
            background: rgba(10, 5, 20, 0.85);
            border: 1px solid rgba(176, 38, 255, 0.4);
            color: #fff;
            padding: 0.8rem 1rem;
            border-radius: 10px;
            font-size: 0.95rem;
            outline: none;
            transition: 0.2s;
        }

        input:focus, select:focus {
            border-color: var(--neon-purple);
            box-shadow: 0 0 14px rgba(176, 38, 255, 0.4);
        }

        .btn-neon {
            background: linear-gradient(135deg, #9333ea, #b026ff);
            color: #fff;
            border: none;
            padding: 0.8rem 1.8rem;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.25s;
            box-shadow: 0 0 18px rgba(176, 38, 255, 0.45);
        }

        .btn-neon:hover {
            transform: translateY(-1px);
            box-shadow: 0 0 28px rgba(176, 38, 255, 0.8);
        }

        /* Account Result List */
        .account-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 1rem;
            margin-top: 1.2rem;
        }

        .account-item {
            background: rgba(15, 8, 30, 0.9);
            border: 1px solid rgba(176, 38, 255, 0.25);
            padding: 1rem;
            border-radius: 12px;
            cursor: pointer;
            transition: 0.2s;
        }

        .account-item:hover {
            border-color: var(--neon-purple);
            box-shadow: 0 0 16px rgba(176, 38, 255, 0.3);
            transform: translateY(-2px);
        }

        .account-item h4 {
            color: #fff;
            margin-bottom: 4px;
        }

        .account-item span {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .stat-box {
            background: rgba(176, 38, 255, 0.08);
            border: 1px solid rgba(176, 38, 255, 0.3);
            border-radius: 12px;
            padding: 1rem;
        }

        .stat-label {
            font-size: 0.8rem;
            color: var(--text-muted);
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .stat-value {
            font-family: 'JetBrains Mono', monospace;
            font-size: 1.25rem;
            font-weight: 700;
            color: #fff;
            text-shadow: 0 0 10px rgba(176, 38, 255, 0.6);
        }

        /* Inventory Tags */
        .items-wrap {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 1rem;
        }

        .item-tag {
            background: rgba(30, 15, 55, 0.9);
            border: 1px solid var(--neon-violet);
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .rarity-Mythical {
            border-color: #ff2a85;
            box-shadow: 0 0 10px rgba(255, 42, 133, 0.3);
            color: #ff9ec7;
        }

        .loader {
            display: none;
            color: var(--neon-purple);
            font-family: 'JetBrains Mono', monospace;
            margin: 1rem 0;
        }

        pre.raw-json {
            background: #05020a;
            border: 1px solid rgba(176, 38, 255, 0.25);
            padding: 1rem;
            border-radius: 10px;
            overflow-x: auto;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.82rem;
            color: #d8b4fe;
            max-height: 350px;
            margin-top: 1rem;
        }

        .hidden { display: none; }
    </style>
</head>
<body>

<div class="container">
    <header>
        <div class="brand">
            <div class="brand-dot"></div>
            <h1>WSRB // TRACK STATS</h1>
        </div>
        <div class="status-badge">API: CONNECTED (cURL)</div>
    </header>

    <div class="tabs">
        <button class="tab-btn active" onclick="switchTab('single')">🔍 Tra cứu 1 Tài khoản</button>
        <button class="tab-btn" onclick="switchTab('multi')">📊 Lọc Nhiều Tài khoản</button>
    </div>

    <!-- TAB 1: ONE ACCOUNT -->
    <div id="tab-single">
        <div class="card">
            <h3 style="margin-bottom: 1rem; color: #e9d5ff;">Tìm kiếm tài khoản theo tên</h3>
            <div class="input-group">
                <input type="text" id="searchQuery" placeholder="Nhập tên tài khoản (VD: Grenlyn8040)..." onkeypress="if(event.key==='Enter') searchAccount()">
                <button class="btn-neon" onclick="searchAccount()">Tìm kiếm</button>
            </div>
            <div id="searchLoader" class="loader">⚡ Đang gọi cURL tới wsrb.org...</div>
            <div id="searchResults" class="account-list"></div>
        </div>

        <div id="accountDetailCard" class="card hidden">
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
                <h2 id="detailTitle" style="color:#fff;">Chi tiết tài khoản</h2>
                <span id="detailId" class="status-badge">ID: #0</span>
            </div>

            <h4 style="margin-top: 1.5rem; color: var(--text-muted);">Live Stats (Chỉ số hiện tại)</h4>
            <div id="liveStatsGrid" class="stats-grid"></div>

            <h4 style="margin-top: 1.5rem; color: var(--text-muted);">Inventories (Kho đồ - Hiếm nhất xếp trước)</h4>
            <div id="inventoryContainer"></div>

            <details style="margin-top: 1.5rem;">
                <summary style="cursor:pointer; color: var(--text-muted); font-size:0.85rem;">Xem dữ liệu JSON gốc từ API</summary>
                <pre id="rawSingleJson" class="raw-json"></pre>
            </details>
        </div>
    </div>

    <!-- TAB 2: MANY ACCOUNTS -->
    <div id="tab-multi" class="hidden">
        <div class="card">
            <h3 style="margin-bottom: 1rem; color: #e9d5ff;">Bộ lọc danh sách theo Game & Chỉ số</h3>
            <div class="input-group" style="margin-bottom: 12px;">
                <input type="text" id="filterGame" value="blox-fruits" placeholder="Tên game (VD: blox-fruits)">
                <select id="filterStatus">
                    <option value="">Tất cả trạng thái (Status)</option>
                    <option value="faceid">Bị khóa FaceID (status=faceid)</option>
                </select>
            </div>
            <div class="input-group">
                <input type="text" id="filterStat" placeholder="Lọc chỉ số (VD: Level:2800:)">
                <input type="text" id="filterHas" placeholder="Có vật phẩm (VD: Godhuman)">
                <input type="text" id="filterSort" placeholder="Sắp xếp (VD: stat.Beli)">
                <button class="btn-neon" onclick="filterAccounts()">Lọc dữ liệu</button>
            </div>
            <div id="multiLoader" class="loader">⚡ Đang tải danh sách tài khoản...</div>
            <pre id="multiResultJson" class="raw-json hidden"></pre>
        </div>
    </div>
</div>

<script>
    function switchTab(tab) {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        event.target.classList.add('active');
        document.getElementById('tab-single').classList.toggle('hidden', tab !== 'single');
        document.getElementById('tab-multi').classList.toggle('hidden', tab !== 'multi');
    }

    async function searchAccount() {
        const q = document.getElementById('searchQuery').value.trim();
        if (!q) return alert('Vui lòng nhập tên tài khoản!');

        const loader = document.getElementById('searchLoader');
        const resultsDiv = document.getElementById('searchResults');
        loader.style.display = 'block';
        resultsDiv.innerHTML = '';

        try {
            const res = await fetch(`api.php?action=search&q=${encodeURIComponent(q)}`);
            const data = await res.json();
            loader.style.display = 'none';

            const accounts = Array.isArray(data) ? data : (data.accounts || data.data || []);
            if (accounts.length === 0) {
                resultsDiv.innerHTML = `<p style="color:#a78bfa;">Không tìm thấy tài khoản nào khớp với "${q}".</p>`;
                return;
            }

            accounts.forEach(acc => {
                const div = document.createElement('div');
                div.className = 'account-item';
                div.innerHTML = `
                    <h4>${acc.name || acc.username || 'Unknown'}</h4>
                    <span>ID: ${acc.id} • Click để xem Inventory</span>
                `;
                div.onclick = () => loadInventory(acc.id, acc.name || acc.username);
                resultsDiv.appendChild(div);
            });
        } catch (err) {
            loader.style.display = 'none';
            alert('Lỗi kết nối tới server: ' + err.message);
        }
    }

    async function loadInventory(id, name) {
        const card = document.getElementById('accountDetailCard');
        const statsGrid = document.getElementById('liveStatsGrid');
        const invContainer = document.getElementById('inventoryContainer');
        const rawJson = document.getElementById('rawSingleJson');

        card.classList.remove('hidden');
        document.getElementById('detailTitle').innerText = name || `Account #${id}`;
        document.getElementById('detailId').innerText = `ID: #${id}`;
        statsGrid.innerHTML = '<p style="color:#a78bfa;">Đang tải chỉ số...</p>';
        invContainer.innerHTML = '';

        try {
            const res = await fetch(`api.php?action=inventory&id=${id}`);
            const data = await res.json();
            rawJson.textContent = JSON.stringify(data, null, 2);

            // Render live.stats
            const stats = data.live?.stats || data.stats || {};
            statsGrid.innerHTML = '';
            if (Object.keys(stats).length === 0) {
                statsGrid.innerHTML = '<p style="color:#a78bfa;">Không có dữ liệu live.stats</p>';
            } else {
                for (const [key, val] of Object.entries(stats)) {
                    statsGrid.innerHTML += `
                        <div class="stat-box">
                            <div class="stat-label">${key}</div>
                            <div class="stat-value">${typeof val === 'number' ? val.toLocaleString() : val}</div>
                        </div>
                    `;
                }
            }

            // Render inventories
            const inventories = data.inventories || [];
            if (inventories.length === 0) {
                invContainer.innerHTML = '<p style="color:#a78bfa; margin-top:8px;">Chưa có dữ liệu kho đồ.</p>';
            } else {
                inventories.forEach(inv => {
                    const gameTitle = document.createElement('h5');
                    gameTitle.style.cssText = 'color:#d8b4fe; margin-top:1rem; font-size:0.95rem;';
                    gameTitle.innerText = `Game: ${inv.game || 'Unknown'}`;
                    invContainer.appendChild(gameTitle);

                    const wrap = document.createElement('div');
                    wrap.className = 'items-wrap';
                    (inv.items || []).forEach(item => {
                        const tag = document.createElement('div');
                        tag.className = `item-tag rarity-${item.rarity || ''}`;
                        tag.innerHTML = `<span>${item.name}</span> ${item.count > 1 ? `<strong>x${item.count}</strong>` : ''}`;
                        wrap.appendChild(tag);
                    });
                    invContainer.appendChild(wrap);
                });
            }
        } catch (err) {
            statsGrid.innerHTML = `<p style="color:#ff2a85;">Lỗi: ${err.message}</p>`;
        }
    }

    async function filterAccounts() {
        const game = document.getElementById('filterGame').value.trim();
        const status = document.getElementById('filterStatus').value;
        const stat = document.getElementById('filterStat').value.trim();
        const has = document.getElementById('filterHas').value.trim();
        const sort = document.getElementById('filterSort').value.trim();

        const loader = document.getElementById('multiLoader');
        const out = document.getElementById('multiResultJson');
        loader.style.display = 'block';
        out.classList.add('hidden');

        const params = new URLSearchParams({ action: 'game_accounts', game, status, stat, has, sort });

        try {
            const res = await fetch(`api.php?${params.toString()}`);
            const data = await res.json();
            loader.style.display = 'none';
            out.classList.remove('hidden');
            out.textContent = JSON.stringify(data, null, 2);
        } catch (err) {
            loader.style.display = 'none';
            alert('Lỗi: ' + err.message);
        }
    }
</script>

</body>
</html>
